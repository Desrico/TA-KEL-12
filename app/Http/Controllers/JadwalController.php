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

    private function validateSchedulingPayload(Request $request): array
    {
        return $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu'   => 'required|date_format:H:i',
            'jenis'   => 'required|in:online,offline',
            'topik'   => 'required|string|min:3|max:255',
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
                'success'  => false,
                'message'  => 'Silakan login terlebih dahulu untuk membuat jadwal.',
                'redirect' => '/login'
            ], 401);
        }

        $validated = $this->validateSchedulingPayload($request);
        $normalizedWaktu = Carbon::createFromFormat('H:i', $validated['waktu'])->format('H:i:s');

        $konselor = $this->resolveActiveKonselor();
        if (!$konselor) {
            return response()->json([
                'success' => false,
                'message' => 'Konselor belum tersedia.'
            ], 404);
        }

        $isAvailable = !$this->isSlotBooked($validated['tanggal'], $normalizedWaktu, $konselor->id);

        return response()->json([
            'success'      => true,
            'is_available' => $isAvailable,
            'message'      => $isAvailable
                ? 'Jadwal tersedia dan bisa dikonfirmasi.'
                : 'Jadwal ini sudah terisi. Silakan pilih waktu lain.',
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

        $validated = $this->validateSchedulingPayload($request);

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
        $namaDisplay = $isAnonim ? 'Mahasiswa Anonim' : $user->nama;
        $prodi       = $mahasiswa->jurusan ?? '-';
        $angkatan    = $mahasiswa->angkatan ?? '-';

        $identitasUntukKonselor = $isAnonim
            ? 'Mahasiswa Prodi ' . $prodi . ' Angkatan ' . $angkatan
            : $user->nama;

        $jadwal = DB::transaction(function () use ($mahasiswa, $konselor, $validated, $normalizedWaktu, $isAnonim, $user, $identitasUntukKonselor) {
            $jadwal = JadwalKonseling::create([
                'mahasiswa_id' => $mahasiswa->id,
                'konselor_id'  => $konselor->id,
                'tanggal'      => $validated['tanggal'],
                'waktu'        => $normalizedWaktu,
                'status'       => 'menunggu',
                'jenis'        => $validated['jenis'],
                'anonim'       => $isAnonim,
                'catatan'      => ($isAnonim ? '[ANONIM] ' : '') . 'Topik: ' . $validated['topik'] . ' | Jenis: ' . $validated['jenis'],
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
            'success'      => true,
            'message'      => 'Jadwal berhasil dibuat!',
            'kode_jadwal'  => 'JD-' . strtoupper(base_convert($jadwal->id, 10, 36)),
            'nama_display' => $namaDisplay,
            'is_anonim'    => $isAnonim,
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