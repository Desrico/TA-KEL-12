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
use Illuminate\Validation\ValidationException;

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
            'angkatanMahasiswa' => $mahasiswa->angkatan ?? '-',
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

        $validated = $request->validate($rules, [
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

        $this->ensureSlotHasNotStarted($validated['tanggal'], $validated['waktu']);

        return $validated;
    }

    private function ensureSlotHasNotStarted(string $tanggal, string $waktu): void
    {
        $slotAt = Carbon::createFromFormat('Y-m-d H:i', $tanggal . ' ' . $waktu, 'Asia/Jakarta');

        // Booking untuk slot hari ini harus dilakukan sebelum jam sesi dimulai, bukan saat atau sesudahnya.
        if (Carbon::now('Asia/Jakarta')->greaterThanOrEqualTo($slotAt)) {
            throw ValidationException::withMessages([
                'waktu' => 'Slot waktu ini sudah dimulai. Silakan pilih jam lain yang masih sebelum waktu sesi dimulai.',
            ]);
        }
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
        return $this->findSlotConflict($tanggal, $waktu, $konselorId) !== null;
    }

    private function findSlotConflict(string $tanggal, string $waktu, int $konselorId): ?JadwalKonseling
    {
        return JadwalKonseling::whereDate('tanggal', $tanggal)
            ->whereTime('waktu', $waktu)
            ->where('konselor_id', $konselorId)
            ->where('status', '!=', 'ditolak')
            ->orderByRaw("
                CASE
                    WHEN status = 'disetujui' THEN 1
                    WHEN status = 'berlangsung' THEN 2
                    WHEN status = 'menunggu' THEN 3
                    WHEN status = 'selesai' THEN 4
                    ELSE 5
                END
            ")
            ->first();
    }

    private function formatSlotConflictResponse(?JadwalKonseling $jadwal): array
    {
        $status = strtolower(trim((string) optional($jadwal)->status));

        if (in_array($status, ['disetujui', 'berlangsung'], true)) {
            return [
                'success' => false,
                'message' => 'Jadwal ini telah terjadwal. Silakan pilih waktu lain.',
                'slot_status' => $status,
                'slot_label' => 'Telah Terjadwal',
            ];
        }

        return [
            'success' => false,
            'message' => 'Jadwal ini sudah terisi. Silakan pilih waktu lain.',
            'slot_status' => $status ?: 'menunggu',
            'slot_label' => 'Sudah Terisi',
        ];
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

        $conflict = $this->findSlotConflict($validated['tanggal'], $jamMulai, $konselor->id);

        if ($conflict) {
            return response()->json($this->formatSlotConflictResponse($conflict), 409);
        }

        return response()->json([
            'success' => true,
            'is_available' => true,
            'message' => 'Jadwal tersedia.',
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

        $normalizedWaktu = Carbon::createFromFormat('H:i', $validated['waktu'])
            ->format('H:i:s');

        $isAnonim    = $user->isAnonim();
        $namaDisplay = $user->getNamaDisplay();

        $identitasUntukKonselor = $isAnonim
            ? trim($user->getAnonimDisplayName())
            : $user->nama;

        try {
            $jadwal = DB::transaction(function () use (
                $mahasiswa,
                $konselor,
                $validated,
                $normalizedWaktu,
                $isAnonim,
                $user,
                $identitasUntukKonselor
            ) {
                /**
                 * 1. Cek apakah slot sudah dibooking mahasiswa lain
                 */
                $existingBooking = JadwalKonseling::whereDate('tanggal', $validated['tanggal'])
                    ->whereTime('waktu', $normalizedWaktu)
                    ->where('konselor_id', $konselor->id)
                    ->where('status', '!=', 'ditolak')
                    ->lockForUpdate()
                    ->orderByRaw("
                    CASE
                        WHEN status = 'disetujui' THEN 1
                        WHEN status = 'berlangsung' THEN 2
                        WHEN status = 'menunggu' THEN 3
                        WHEN status = 'selesai' THEN 4
                        ELSE 5
                    END
                ")
                    ->first();

                if ($existingBooking) {
                    abort(response()->json($this->formatSlotConflictResponse($existingBooking), 409));
                }

                /**
                 * 2. Cek apakah konselor tidak tersedia pada tanggal dan jam tersebut
                 */
                $waktuMulai = $normalizedWaktu;

                // Sesuaikan durasi sesi konseling di sini.
                // Jika 1 sesi = 60 menit, biarkan 60.
                // Jika 30 menit, ubah menjadi 30.
                $durasiSesiMenit = 60;

                $waktuSelesai = Carbon::createFromFormat('H:i:s', $normalizedWaktu)
                    ->addMinutes($durasiSesiMenit)
                    ->format('H:i:s');

                $jadwalTidakTersedia = KetidaktersediaanKonselor::where('konselor_id', $konselor->id)
                    ->whereDate('tanggal_mulai', $validated['tanggal'])
                    ->where(function ($query) use ($waktuMulai, $waktuSelesai) {
                        $query->whereTime('jam_mulai', '<', $waktuSelesai)
                            ->whereTime('jam_selesai', '>', $waktuMulai);
                    })
                    ->lockForUpdate()
                    ->first();

                if ($jadwalTidakTersedia) {
                    $tanggalTidakTersedia = Carbon::parse($jadwalTidakTersedia->tanggal_mulai)->format('d/m/Y');

                    $jamMulaiTidakTersedia = Carbon::parse($jadwalTidakTersedia->jam_mulai)->format('H:i');
                    $jamSelesaiTidakTersedia = Carbon::parse($jadwalTidakTersedia->jam_selesai)->format('H:i');

                    abort(response()->json([
                        'success' => false,
                        'type'    => 'konselor_tidak_tersedia',
                        'title'   => 'Konselor Tidak Tersedia',
                        'message' => 'Jadwal tidak dapat dipilih karena konselor tidak tersedia pada tanggal dan waktu tersebut.',
                        'detail'  => [
                            'tanggal' => $tanggalTidakTersedia,
                            'waktu'   => $jamMulaiTidakTersedia . ' - ' . $jamSelesaiTidakTersedia,
                            'alasan'  => $jadwalTidakTersedia->alasan ?: 'Tidak ada alasan tambahan.',
                        ]
                    ], 409));
                }

                /**
                 * 3. Jika aman, simpan jadwal konseling
                 */
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
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $exception) {
            $response = $exception->getResponse();

            if ($response) {
                return $response;
            }

            throw $exception;
        }

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

    public function editJadwalUlang($id)
    {
        $mahasiswa = \App\Models\Mahasiswa::where('user_id', auth()->id())->firstOrFail();

        $jadwalUlang = \App\Models\JadwalKonseling::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'perlu_penjadwalan_ulang')
            ->firstOrFail();

        return view('Pages.konseling', compact('jadwalUlang'));
    }

    public function updateJadwalUlang(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu' => 'required',
            'jenis' => 'required|string',
            'topik' => 'required|string|max:255',
        ]);

        $user = auth()->user();

        $mahasiswa = \App\Models\Mahasiswa::where('user_id', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.',
            ], 404);
        }

        $jadwal = \App\Models\JadwalKonseling::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Data jadwal tidak ditemukan.',
            ], 404);
        }

        if ($jadwal->status !== 'perlu_penjadwalan_ulang') {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ini tidak dalam status perlu penjadwalan ulang.',
            ], 409);
        }

        $jadwal->update([
            'tanggal' => $validated['tanggal'],
            'waktu' => $validated['waktu'],
            'jenis' => $validated['jenis'],
            'topik' => $validated['topik'],
            'status' => 'menunggu',
            'updated_at' => now(),
        ]);

        \App\Models\Notifikasi::create([
            'user_id' => $user->id,
            'pesan' => 'Jadwal konseling ulang Anda berhasil diajukan dan sedang menunggu konfirmasi konselor.',
            'status' => 'belum_dibaca',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dijadwalkan ulang dan sedang menunggu konfirmasi konselor.',
            'redirect_url' => url('/riwayat/' . $jadwal->id),
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
            ->whereDate('tanggal', '>=', Carbon::today()->toDateString())
            ->get(['tanggal', 'waktu', 'status'])
            ->map(function ($j) {
                $status = strtolower(trim((string) $j->status));
                $tanggal = $j->tanggal instanceof Carbon
                    ? $j->tanggal->format('Y-m-d')
                    : Carbon::parse($j->tanggal)->format('Y-m-d');

                return [
                    'slot' => $tanggal . '-' . Carbon::parse($j->waktu)->format('H:i'),
                    'status' => $status,
                    'label' => in_array($status, ['disetujui', 'berlangsung'], true)
                        ? 'Telah Terjadwal'
                        : 'Sudah Terisi',
                ];
            })
            ->toArray();

        return response()->json($booked);
    }
}
