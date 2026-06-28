<?php

namespace App\Http\Controllers;

use App\Models\JadwalKonseling;
use App\Models\KetidaktersediaanKonselor;
use App\Models\Konselor;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class KetidaktersediaanKonselorController extends Controller
{
 public function store(Request $request)
{
    $validated = $request->validate([
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|required_with:jam_selesai|date_format:H:i',
            'jam_selesai' => 'nullable|required_with:jam_mulai|date_format:H:i|after:jam_mulai',
            'alasan' => 'required|string|max:200',
            'ulang_mingguan' => 'nullable|boolean',
        ]);

    $konselor = $this->getKonselorAktif();

    if (!$konselor) {
        return back()->with('error', 'Data konselor tidak ditemukan.');
    }

    $validated['tanggal_selesai'] = $validated['tanggal_selesai'] ?? $validated['tanggal_mulai'];
    // Normalisasi input jam dari picker custom agar tersimpan konsisten dan tetap terbaca JadwalController.
    $validated['jam_mulai'] = isset($validated['jam_mulai']) ? Carbon::createFromFormat('H:i', $validated['jam_mulai'])->format('H:i:s') : null;
    $validated['jam_selesai'] = isset($validated['jam_selesai']) ? Carbon::createFromFormat('H:i', $validated['jam_selesai'])->format('H:i:s') : null;

    $ulangMingguan = $request->boolean('ulang_mingguan');

    $jumlahJadwalTerdampak = 0;
    $jumlahKetidaktersediaan = 0;

    DB::transaction(function () use (
        $validated,
        $konselor,
        $ulangMingguan,
        &$jumlahJadwalTerdampak,
        &$jumlahKetidaktersediaan
    ) {
        $tanggalList = $ulangMingguan
            ? $this->buatTanggalUlangMingguan($validated['tanggal_mulai'], 12)
            : [$validated['tanggal_mulai']];

        foreach ($tanggalList as $tanggal) {
            $dataKetidaktersediaan = [
                'konselor_id' => $konselor->id,
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => $tanggal,
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
            ];

            KetidaktersediaanKonselor::updateOrCreate(
                $dataKetidaktersediaan,
                [
                    'alasan' => $validated['alasan'] ?? null,
                ]
            );

            $jumlahKetidaktersediaan++;

            $jumlahJadwalTerdampak += $this->tandaiJadwalTerdampak(
                $konselor->id,
                [
                    'tanggal_mulai' => $tanggal,
                    'tanggal_selesai' => $tanggal,
                    'jam_mulai' => $validated['jam_mulai'],
                    'jam_selesai' => $validated['jam_selesai'],
                ]
            );
        }
    });

    $pesan = $ulangMingguan
        ? 'Jadwal ketidaktersediaan mingguan berhasil ditambahkan untuk ' . $jumlahKetidaktersediaan . ' minggu.'
        : 'Jadwal ketidaktersediaan berhasil ditambahkan.';

    if ($jumlahJadwalTerdampak > 0) {
        $pesan .= ' ' . $jumlahJadwalTerdampak .
            ' jadwal yang bentrok telah ditandai perlu penjadwalan ulang dan mahasiswa telah diberi notifikasi.';
    }

    return back()->with('success', $pesan);
}

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|required_with:jam_selesai|date_format:H:i',
            'jam_selesai' => 'nullable|required_with:jam_mulai|date_format:H:i|after:jam_mulai',
            'alasan' => 'required|string|max:200',
        ]);

        $konselor = $this->getKonselorAktif();

        if (!$konselor) {
            return back()->with('error', 'Data konselor tidak ditemukan.');
        }

        $validated['tanggal_selesai'] = $validated['tanggal_selesai'] ?? $validated['tanggal_mulai'];
        $validated['jam_mulai'] = isset($validated['jam_mulai']) ? Carbon::createFromFormat('H:i', $validated['jam_mulai'])->format('H:i:s') : null;
        $validated['jam_selesai'] = isset($validated['jam_selesai']) ? Carbon::createFromFormat('H:i', $validated['jam_selesai'])->format('H:i:s') : null;

        $jumlahJadwalTerdampak = 0;

        DB::transaction(function () use ($validated, $id, $konselor, &$jumlahJadwalTerdampak) {
            $data = KetidaktersediaanKonselor::where('id', $id)
                ->where('konselor_id', $konselor->id)
                ->firstOrFail();

            $data->update([
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'] ?? $validated['tanggal_mulai'],
                'jam_mulai' => $validated['jam_mulai'] ?? null,
                'jam_selesai' => $validated['jam_selesai'] ?? null,
                'alasan' => $validated['alasan'] ?? null,
            ]);

            $jumlahJadwalTerdampak = $this->tandaiJadwalTerdampak(
                $konselor->id,
                $validated
            );
        });

        if ($jumlahJadwalTerdampak > 0) {
            return back()->with(
                'success',
                'Ketidaktersediaan berhasil diperbarui. ' .
                $jumlahJadwalTerdampak .
                ' jadwal yang sudah disetujui telah ditandai perlu penjadwalan ulang.'
            );
        }

        return back()->with('success', 'Ketidaktersediaan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $konselor = $this->getKonselorAktif();

        if (!$konselor) {
            return back()->with('error', 'Data konselor tidak ditemukan.');
        }

        $data = KetidaktersediaanKonselor::where('id', $id)
            ->where('konselor_id', $konselor->id)
            ->firstOrFail();

        $data->delete();

        return back()->with('success', 'Jadwal ketidaktersediaan berhasil dihapus.');
    }

    private function getKonselorAktif()
    {
        return Auth::user()->konselor ?? Konselor::where('user_id', Auth::id())->first();
    }

    private function buatTanggalUlangMingguan(string $tanggalAwal, int $jumlahMinggu = 12): array
{
    $tanggal = Carbon::parse($tanggalAwal);
    $tanggalList = [];

    for ($i = 0; $i < $jumlahMinggu; $i++) {
        $tanggalList[] = $tanggal->copy()->addWeeks($i)->toDateString();
    }

    return $tanggalList;
}

private function tandaiJadwalTerdampak($konselorId, array $data)
{
    $tanggalMulai = $data['tanggal_mulai'];
    $tanggalSelesai = $data['tanggal_selesai'] ?? $data['tanggal_mulai'];
    $jamMulai = $data['jam_mulai'] ?? null;
    $jamSelesai = $data['jam_selesai'] ?? null;

    $jadwalAktif = JadwalKonseling::with(['mahasiswa.user'])
        ->where('konselor_id', $konselorId)
        ->whereDate('tanggal', '>=', $tanggalMulai)
        ->whereDate('tanggal', '<=', $tanggalSelesai)
        ->where(function ($query) {
            $query->whereNull('status')
                ->orWhereNotIn('status', [
                    'selesai',
                    'ditolak',
                    'dibatalkan',
                    'perlu_penjadwalan_ulang',
                ]);
        })
        ->get();

    $jadwalTerdampak = $jadwalAktif->filter(function ($jadwal) use ($jamMulai, $jamSelesai) {
        return $this->isWaktuBentrok(
            $jadwal->waktu,
            $jamMulai,
            $jamSelesai
        );
    });

    if ($jadwalTerdampak->isEmpty()) {
        return 0;
    }

    JadwalKonseling::whereIn('id', $jadwalTerdampak->pluck('id'))
        ->update([
            'status' => 'perlu_penjadwalan_ulang',
            'updated_at' => now(),
        ]);

    foreach ($jadwalTerdampak as $jadwal) {
        if ($jadwal->mahasiswa && $jadwal->mahasiswa->user) {
            Notifikasi::create([
                'user_id' => $jadwal->mahasiswa->user->id,
                'pesan' => 'Jadwal konseling Anda pada ' .
                    Carbon::parse($jadwal->tanggal)->translatedFormat('d F Y') .
                    ' pukul ' .
                    Carbon::parse($jadwal->waktu)->format('H:i') .
                    ' perlu dijadwalkan ulang karena konselor tidak tersedia.',
                'status' => 'belum',
                // Notifikasi penjadwalan ulang dibuka dari daftar riwayat agar status khususnya terlihat.
                'cta_target' => route('riwayat', ['jadwal' => $jadwal->id]),
                'cta_label' => 'Buka Riwayat',
            ]);
        }
    }

    return $jadwalTerdampak->count();
}

private function isWaktuBentrok($waktuJadwal, $jamMulai, $jamSelesai)
{
    // Jika ketidaktersediaan dibuat tanpa jam, berarti tidak tersedia seharian
    if (!$jamMulai || !$jamSelesai) {
        return true;
    }

    $waktuJadwal = date('H:i', strtotime($waktuJadwal));
    $jamMulai = date('H:i', strtotime($jamMulai));
    $jamSelesai = date('H:i', strtotime($jamSelesai));

    return $waktuJadwal >= $jamMulai && $waktuJadwal < $jamSelesai;
}

    private function pecahWaktuJadwal($waktuJadwal)
    {
        if (empty($waktuJadwal)) {
            return [null, null];
        }

        $waktuJadwal = trim($waktuJadwal);

        /*
         * Supaya format seperti:
         * 10.00 - 14.00
         * 10:00 - 14:00
         * 10:00–14:00
         * tetap bisa dibaca.
         */
        $waktuJadwal = str_replace(['.', '–', '—'], [':', '-', '-'], $waktuJadwal);

        if (str_contains($waktuJadwal, '-')) {
            [$mulai, $selesai] = array_map('trim', explode('-', $waktuJadwal, 2));

            $jadwalMulai = $this->jamKeMenit($mulai);
            $jadwalSelesai = $this->jamKeMenit($selesai);

            return [$jadwalMulai, $jadwalSelesai];
        }

        /*
         * Jika waktu jadwal hanya satu jam, misalnya 10:00,
         * maka diasumsikan durasi konseling 1 jam.
         */
        $jadwalMulai = $this->jamKeMenit($waktuJadwal);

        if ($jadwalMulai === null) {
            return [null, null];
        }

        $jadwalSelesai = min($jadwalMulai + 60, 1440);

        return [$jadwalMulai, $jadwalSelesai];
    }

    private function jamKeMenit($jam)
    {
        if (empty($jam)) {
            return null;
        }

        $jam = trim($jam);
        $jam = str_replace('.', ':', $jam);

        /*
         * Mendukung format:
         * 10:00
         * 10:00:00
         * 9:30
         */
        if (!preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $jam, $matches)) {
            return null;
        }

        $hour = (int) $matches[1];
        $minute = (int) $matches[2];

        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        return ($hour * 60) + $minute;
    }
}
