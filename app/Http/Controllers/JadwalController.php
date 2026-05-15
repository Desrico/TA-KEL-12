<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\JadwalKonseling;
use App\Models\Mahasiswa;
use App\Models\Konselor;
use App\Models\User;
use App\Models\Notifikasi;
use App\Models\KetidaktersediaanKonselor;

class JadwalController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        return view('Pages.konseling', [
            'namaMahasiswa'    => $user->nama ?? '-',
            'nimMahasiswa'     => $mahasiswa->nim ?? '-',
            'jurusanMahasiswa' => $mahasiswa->jurusan ?? '-',
            'angkatanMahasiswa'=> $mahasiswa->angkatan ?? '-',
            'isAnonim'         => method_exists($user, 'isAnonim') ? $user->isAnonim() : false,
        ]);
    }

    public function detail(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu'   => 'required|date_format:H:i',
            'jenis'   => 'required|in:online,offline',
            'topik'   => 'required|string|min:3|max:255',
        ]);

        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        return view('Pages.detail-penjadwalan', [
            'namaMahasiswa'     => $user->nama ?? '-',
            'nimMahasiswa'      => $mahasiswa->nim ?? '-',
            'jurusanMahasiswa'  => $mahasiswa->jurusan ?? '-',
            'angkatanMahasiswa' => $mahasiswa->angkatan ?? '-',
            'isAnonim'          => method_exists($user, 'isAnonim') ? $user->isAnonim() : false,
            'tanggal'           => $validated['tanggal'],
            'waktu'             => $validated['waktu'],
            'jenis'             => $validated['jenis'],
            'topik'             => $validated['topik'],
        ]);
    }

    private function validateSchedulingPayload(Request $request, bool $requireConfirmation = false): array
    {
        $rules = [
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu'   => 'required|date_format:H:i',
            'jenis'   => 'required|in:online,offline',
            'topik'   => 'required|string|min:3|max:255',
        ];

        if ($requireConfirmation) {
            $rules['konfirmasi'] = 'accepted';
        }

        return $request->validate($rules, [
            'tanggal.required'         => 'Tanggal konseling wajib diisi.',
            'tanggal.date'             => 'Format tanggal konseling tidak valid.',
            'tanggal.after_or_equal'   => 'Tanggal konseling tidak boleh sebelum hari ini.',
            'waktu.required'           => 'Waktu konseling wajib dipilih.',
            'waktu.date_format'        => 'Format waktu konseling tidak valid.',
            'jenis.required'           => 'Jenis layanan konseling wajib dipilih.',
            'jenis.in'                 => 'Jenis layanan konseling tidak valid.',
            'topik.required'           => 'Topik konseling wajib diisi.',
            'topik.min'                => 'Topik konseling minimal 3 karakter.',
            'topik.max'                => 'Topik konseling maksimal 255 karakter.',
            'konfirmasi.accepted'      => 'Centang pernyataan konfirmasi sebelum menjadwalkan konseling.',
        ]);
    }

    private function resolveActiveKonselor(): ?Konselor
    {
        $konselor = Konselor::first();
        if ($konselor) {
            return $konselor;
        }

        $konselorUser = User::where('role', 'konselor')->first();
        if (!$konselorUser) {
            return null;
        }

        return Konselor::firstOrCreate(
            ['user_id' => $konselorUser->id],
            ['spesialisasi' => 'Umum']
        );
    }

    private function isSlotBooked(string $tanggal, string $waktu, int $konselorId): bool
    {
        return JadwalKonseling::whereDate('tanggal', $tanggal)
            ->whereTime('waktu', $waktu)
            ->where('konselor_id', $konselorId)
            ->where('status', '!=', 'ditolak')
            ->exists();
    }

   public function checkAvailability(Request $request)
{
    if (!Auth::check()) {
        return response()->json([
            'success' => false,
            'is_available' => false,
            'message' => 'Silakan login terlebih dahulu.'
        ], 401);
    }

    $validated = $this->validateSchedulingPayload($request);

    $slotMulai = Carbon::createFromFormat('H:i', $validated['waktu']);
    $slotSelesai = $slotMulai->copy()->addHour();

    $jamMulai = $slotMulai->format('H:i:s');
    $jamSelesai = $slotSelesai->format('H:i:s');

    $konselor = $this->resolveActiveKonselor();

    if (!$konselor) {
        return response()->json([
            'success' => false,
            'is_available' => false,
            'message' => 'Konselor belum tersedia.'
        ], 404);
    }

    $jadwalTidakTersedia = DB::table('ketidaktersediaan_konselor')
        ->where('konselor_id', $konselor->id)
        ->whereDate('tanggal_mulai', '<=', $validated['tanggal'])
        ->where(function ($query) use ($validated) {
            $query->whereNull('tanggal_selesai')
                ->orWhereDate('tanggal_selesai', '>=', $validated['tanggal']);
        })
        ->where(function ($query) use ($jamMulai, $jamSelesai) {
            $query->where(function ($q) {
                $q->whereNull('jam_mulai')
                  ->whereNull('jam_selesai');
            })
            ->orWhere(function ($q) use ($jamMulai, $jamSelesai) {
                $q->whereNotNull('jam_mulai')
                  ->whereNotNull('jam_selesai')
                  ->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
            });
        })
        ->first();

    if ($jadwalTidakTersedia) {
        return response()->json([
            'success' => false,
            'is_available' => false,
            'message' => 'Maaf, konselor tidak tersedia pada jadwal yang kamu pilih.',
            'alasan' => $jadwalTidakTersedia->alasan ?? 'Konselor tidak tersedia pada waktu tersebut.'
        ]);
    }

    return response()->json([
        'success' => true,
        'message' => 'Penjadwalan berhasil dibuat.',
        'kode_jadwal' => $jadwal->kode_jadwal ?? '-',
        'nama_display' => Auth::user()->nama ?? '-',
        'tanggal' => $jadwal->tanggal,
        'waktu' => $jadwal->waktu,
        'topik' => $jadwal->topik,
        'jenis' => $jadwal->jenis,
    ]);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Silakan login terlebih dahulu untuk membuat jadwal.',
                'redirect' => '/login'
            ], 401);
        }

        $validated = $this->validateSchedulingPayload($request, true);

        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $konselor = $this->resolveActiveKonselor();
        if (!$konselor) {
            return response()->json([
                'success' => false,
                'message' => 'Konselor belum tersedia.'
            ], 404);
        }
        $konselor->loadMissing('user');

        $normalizedWaktu = Carbon::createFromFormat('H:i', $validated['waktu'])->format('H:i:s');

        $sudahAda = $this->isSlotBooked($validated['tanggal'], $normalizedWaktu, $konselor->id);

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ini sudah terisi. Silakan pilih waktu lain.'
            ], 409);
        }

        $isAnonim    = $user->isAnonim();
        $namaDisplay = $user->getNamaDisplay();
        $prodi       = $mahasiswa->jurusan ?? '-';
        $angkatan    = $mahasiswa->angkatan ?? '-';

        $identitasUntukKonselor = $isAnonim
            ? trim($user->getAnonimDisplayName())
            : $user->nama;

        $jadwal = DB::transaction(function () use ($mahasiswa, $konselor, $validated, $normalizedWaktu, $isAnonim, $user, $identitasUntukKonselor) {
            $jadwal = JadwalKonseling::create([
                'mahasiswa_id' => $mahasiswa->id,
                'konselor_id'  => $konselor->id,
                'tanggal'      => $validated['tanggal'],
                'waktu'        => $normalizedWaktu,
                'status'       => 'menunggu',
                'jenis'        => $validated['jenis'],
                'topik'        => $validated['topik'],
                'anonim'       => $isAnonim,
                'catatan'      => null,
            ]);

            Notifikasi::create([
                'user_id' => $user->id,
                'pesan'   => 'Penjadwalan #' . $jadwal->id . ' berhasil dibuat dan menunggu persetujuan konselor.',
                'status'  => 'belum',
            ]);

            $konselorUserId = optional($konselor->user)->id;
            if ($konselorUserId) {
                Notifikasi::create([
                    'user_id' => $konselorUserId,
                    'pesan'   => 'Penjadwalan baru dari ' . $identitasUntukKonselor . ' pada ' . $validated['tanggal'] . ' pukul ' . $validated['waktu'] . '.',
                    'status'  => 'belum',
                ]);
            }

            return $jadwal;
        });

        return response()->json([
            'success'       => true,
            'message'       => 'Jadwal berhasil dibuat.',
            'kode_jadwal'   => '#' . $jadwal->id,
            'nama_display'  => $namaDisplay,
            'tanggal'       => $validated['tanggal'],
            'waktu'         => $validated['waktu'],
            'topik'         => $validated['topik'],
            'jenis'         => $validated['jenis'],
            'id'            => $jadwal->id,
        ]);
    }

    public function getBookedSlots(Request $request)
    {
        $konselor = $this->resolveActiveKonselor();
        if (!$konselor) {
            return response()->json([]);
        }

        $booked = JadwalKonseling::where('konselor_id', $konselor->id)
            ->where('status', '!=', 'ditolak')
            ->get(['tanggal', 'waktu'])
            ->map(function ($j) {
                return $j->tanggal . '-' . Carbon::parse($j->waktu)->format('H:i');
            })
            ->toArray();

        return response()->json($booked);
    }
}