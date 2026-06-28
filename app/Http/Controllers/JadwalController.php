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
    public function create(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        $followUpJadwal = null;

        if ($mahasiswa && $request->filled('follow_up_from')) {
            // Sesi lanjutan membuat jadwal baru; jadwal lama tetap selesai sebagai riwayat.
            $followUpJadwal = JadwalKonseling::where('id', $request->integer('follow_up_from'))
                ->where('mahasiswa_id', $mahasiswa->id)
                ->whereIn('status', ['selesai', 'perlu_sesi_lanjutan'])
                ->first();
        }

        return view('Pages.konseling', [
            'namaMahasiswa'    => $user->nama ?? '-',
            'nimMahasiswa'     => $mahasiswa->nim ?? '-',
            'jurusanMahasiswa' => $mahasiswa->jurusan ?? '-',
            'angkatanMahasiswa' => $mahasiswa->angkatan ?? '-',
            'isAnonim'         => method_exists($user, 'isAnonim') ? $user->isAnonim() : false,
            'followUpJadwal'   => $followUpJadwal,
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
            'follow_up_from' => 'nullable|integer',
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

    private function isSlotBooked(string $tanggal, string $waktu, int $konselorId, ?int $excludeJadwalId = null): bool
    {
        return $this->findSlotConflict($tanggal, $waktu, $konselorId, $excludeJadwalId) !== null;
    }

    private function findSlotConflict(string $tanggal, string $waktu, int $konselorId, ?int $excludeJadwalId = null): ?JadwalKonseling
    {
        return JadwalKonseling::whereDate('tanggal', $tanggal)
            ->whereTime('waktu', $waktu)
            ->where('konselor_id', $konselorId)
            ->when($excludeJadwalId, fn ($query) => $query->whereKeyNot($excludeJadwalId))
            ->whereNotIn('status', ['ditolak', 'dibatalkan'])
            ->orderByRaw("
                CASE
                    WHEN status = 'diterima' THEN 1
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

        if (in_array($status, ['disetujui', 'diterima', 'berlangsung'], true)) {
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

    private function findKetidaktersediaanKonselor(int $konselorId, string $tanggal, string $waktu): ?KetidaktersediaanKonselor
{
    $slotMulai = Carbon::createFromFormat('H:i', substr($waktu, 0, 5));
    $slotSelesai = $slotMulai->copy()->addHour();

    $jamMulai = $slotMulai->format('H:i:s');
    $jamSelesai = $slotSelesai->format('H:i:s');

    return KetidaktersediaanKonselor::where('konselor_id', $konselorId)
        ->whereDate('tanggal_mulai', '<=', $tanggal)
        ->where(function ($query) use ($tanggal) {
            $query->whereNull('tanggal_selesai')
                ->orWhereDate('tanggal_selesai', '>=', $tanggal);
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
}

private function formatKetidaktersediaanResponse(KetidaktersediaanKonselor $data): array
{
    $tanggal = Carbon::parse($data->tanggal_mulai)->format('d/m/Y');

    $waktu = 'Seharian';

    if ($data->jam_mulai && $data->jam_selesai) {
        $waktu = Carbon::parse($data->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($data->jam_selesai)->format('H:i');
    }

    return [
        'tanggal' => $tanggal,
        'waktu'   => $waktu,
        'alasan'  => $data->alasan ?: 'Tidak ada alasan tambahan.',
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

        $jadwalTidakTersedia = $this->findKetidaktersediaanKonselor(
        $konselor->id,
        $validated['tanggal'],
        $validated['waktu']
    );

    if ($jadwalTidakTersedia) {
        return response()->json([
            'success'      => false,
            'is_available' => false,
            'type'         => 'konselor_tidak_tersedia',
            'title'        => 'Konselor Tidak Tersedia',
            'message'      => 'Jadwal tidak dapat dipilih karena konselor tidak tersedia pada tanggal dan waktu tersebut.',
            'slot_status'  => 'tidak_tersedia',
            'slot_label'   => 'Tidak tersedia',
            'detail'       => $this->formatKetidaktersediaanResponse($jadwalTidakTersedia),
        ], 409);
    }

        $excludeJadwalId = null;

        if ($request->filled('exclude_jadwal_id') && Auth::user()?->mahasiswa) {
            // Saat penjadwalan ulang, jadwal yang sedang diedit tidak boleh mengunci slotnya sendiri.
            $excludeJadwalId = JadwalKonseling::whereKey($request->integer('exclude_jadwal_id'))
                ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->where('status', 'perlu_penjadwalan_ulang')
                ->value('id');
        }

        $conflict = $this->findSlotConflict($validated['tanggal'], $jamMulai, $konselor->id, $excludeJadwalId);

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

        $normalizedWaktu = Carbon::createFromFormat('H:i', $validated['waktu'])
            ->format('H:i:s');

        $isAnonim = $validated['jenis'] === 'online' && $user->isAnonim();
        $namaDisplay = $isAnonim
            ? trim($user->getAnonimDisplayName())
            : $user->nama;

        $identitasUntukKonselor = $isAnonim
            ? $namaDisplay
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
                    ->whereNotIn('status', ['ditolak', 'dibatalkan'])
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

                $jadwalTidakTersedia = $this->findKetidaktersediaanKonselor(
                    $konselor->id,
                    $validated['tanggal'],
                    $validated['waktu']
                );

                if ($jadwalTidakTersedia) {
                    abort(response()->json([
                        'success' => false,
                        'type'    => 'konselor_tidak_tersedia',
                        'title'   => 'Konselor Tidak Tersedia',
                        'message' => 'Jadwal tidak dapat dipilih karena konselor tidak tersedia pada tanggal dan waktu tersebut.',
                        'detail'  => $this->formatKetidaktersediaanResponse($jadwalTidakTersedia),
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

                $konselorUserId = $konselor->user_id;

                if ($konselorUserId) {
                    Notifikasi::create([
                        'user_id' => $konselorUserId,
                        'pesan'   => 'Penjadwalan baru dari ' . $identitasUntukKonselor . ' pada ' . $validated['tanggal'] . ' pukul ' . $validated['waktu'] . '.',
                        'status'  => 'belum',
                    ]);
                }

                if (! empty($validated['follow_up_from'])) {
                    // Setelah sesi lanjutan diajukan, sesi lama kembali tampil sebagai selesai di riwayat mahasiswa.
                    JadwalKonseling::where('id', $validated['follow_up_from'])
                        ->where('mahasiswa_id', $mahasiswa->id)
                        ->where('status', 'selesai')
                        ->where('tindak_lanjut_tipe', 'perlu lanjut')
                        ->update([
                            'tindak_lanjut_tipe' => 'sesi lanjutan diajukan',
                            'updated_at' => now(),
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
            'waktu' => 'required|date_format:H:i',
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

        $this->ensureSlotHasNotStarted($validated['tanggal'], $validated['waktu']);

        $normalizedWaktu = Carbon::createFromFormat('H:i', $validated['waktu'])->format('H:i:s');
        $konselorId = $jadwal->konselor_id ?: optional($this->resolveActiveKonselor())->id;

        if (! $konselorId) {
            return response()->json([
                'success' => false,
                'message' => 'Konselor belum tersedia.',
            ], 404);
        }

        $jadwalTidakTersedia = $this->findKetidaktersediaanKonselor(
            $konselorId,
            $validated['tanggal'],
            $validated['waktu']
        );

        if ($jadwalTidakTersedia) {
            return response()->json([
                'success' => false,
                'type' => 'konselor_tidak_tersedia',
                'title' => 'Konselor Tidak Tersedia',
                'message' => 'Jadwal tidak dapat dipilih karena konselor tidak tersedia pada tanggal dan waktu tersebut.',
                'detail' => $this->formatKetidaktersediaanResponse($jadwalTidakTersedia),
            ], 409);
        }

        $conflict = $this->findSlotConflict(
            $validated['tanggal'],
            $normalizedWaktu,
            $konselorId,
            $jadwal->id
        );

        if ($conflict) {
            return response()->json($this->formatSlotConflictResponse($conflict), 409);
        }

        $jadwal->update([
            'tanggal' => $validated['tanggal'],
            'waktu' => $normalizedWaktu,
            'jenis' => $validated['jenis'],
            'topik' => $validated['topik'],
            'status' => 'menunggu',
            'updated_at' => now(),
        ]);

        \App\Models\Notifikasi::create([
            'user_id' => $user->id,
            'pesan' => 'Jadwal konseling ulang Anda berhasil diajukan dan sedang menunggu konfirmasi konselor.',
            'status' => 'belum',
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
            return response()->json([])->header('Cache-Control', 'no-store, max-age=0');
        }

        $result = [];
        $excludeJadwalId = null;

        if ($request->filled('exclude_jadwal_id') && Auth::user()?->mahasiswa) {
            // Response slot dibuat dinamis per user agar halaman penjadwalan ulang tetap ringan dan akurat.
            $excludeJadwalId = JadwalKonseling::whereKey($request->integer('exclude_jadwal_id'))
                ->where('mahasiswa_id', Auth::user()->mahasiswa->id)
                ->where('status', 'perlu_penjadwalan_ulang')
                ->value('id');
        }

        $booked = JadwalKonseling::where('konselor_id', $konselor->id)
            ->when($excludeJadwalId, fn ($query) => $query->whereKeyNot($excludeJadwalId))
            ->whereNotIn('status', ['ditolak', 'dibatalkan'])
            ->whereDate('tanggal', '>=', Carbon::today()->toDateString())
            ->get(['tanggal', 'waktu', 'status'])
            ->map(function ($j) {
                $status = strtolower(trim((string) $j->status));

                $tanggal = $j->tanggal instanceof Carbon
                    ? $j->tanggal->format('Y-m-d')
                    : Carbon::parse($j->tanggal)->format('Y-m-d');

                return [
                    'slot'   => $tanggal . '-' . Carbon::parse($j->waktu)->format('H:i'),
                    'status' => $status,
                    'label'  => in_array($status, ['disetujui', 'diterima', 'berlangsung'], true)
                        ? 'Telah Terjadwal'
                        : 'Sudah Terisi',
                ];
            })
            ->toArray();

        foreach ($booked as $slot) {
            $result[$slot['slot']] = $slot;
        }

        $serviceTimes = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'];

        $tanggalMulai = Carbon::today();
        $tanggalAkhir = Carbon::today()->copy()->addMonths(3);

        for ($date = $tanggalMulai->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
            if (!$date->isWeekday()) {
                continue;
            }

            $tanggal = $date->format('Y-m-d');

            foreach ($serviceTimes as $time) {
                $tidakTersedia = $this->findKetidaktersediaanKonselor(
                    $konselor->id,
                    $tanggal,
                    $time
                );

                if (!$tidakTersedia) {
                    continue;
                }

                $slotKey = $tanggal . '-' . $time;

                $result[$slotKey] = [
                    'slot'   => $slotKey,
                    'status' => 'tidak_tersedia',
                    'label'  => 'Tidak tersedia',
                    'detail' => $this->formatKetidaktersediaanResponse($tidakTersedia),
                ];
            }
        }

        return response()
            ->json(array_values($result))
            ->header('Cache-Control', 'no-store, max-age=0');
    }
}
