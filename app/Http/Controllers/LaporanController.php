<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalKonseling;
use App\Models\Laporan;
use App\Models\SesiKonseling;
use Illuminate\Support\Facades\Schema;

class LaporanController extends Controller
{
    private function jadwalHasColumn(string $column): bool
    {
        static $cache = [];

        return $cache[$column] ??= Schema::hasColumn('jadwal_konseling', $column);
    }

    private function extractTopik(?string $catatan, ?string $fallback = null): string
    {
        if (!empty($fallback)) {
            return $fallback;
        }

        if (empty($catatan)) {
            return '-';
        }

        if (preg_match('/Topik:\s*([^|]+)/i', $catatan, $match)) {
            return trim($match[1]) ?: '-';
        }

        return '-';
    }

    private function formatRiwayatStatus(?string $status): array
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'selesai' => ['label' => 'Selesai', 'class' => 'status-selesai'],
            'ditolak' => ['label' => 'Ditolak', 'class' => 'status-ditolak'],
            'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'status-dibatalkan'],
            'disetujui', 'diterima' => ['label' => 'Diterima', 'class' => 'status-diterima'],
            'menunggu', 'menunggu konfirmasi' => ['label' => 'Menunggu Konfirmasi', 'class' => 'status-menunggu'],
            'berlangsung', 'sedang berlangsung' => ['label' => 'Sedang Berlangsung', 'class' => 'status-berlangsung'],
            default => ['label' => ucfirst($normalized ?: '-'), 'class' => 'status-default'],
        };
    }

    public function riwayat()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        $riwayat = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])
            ->where('mahasiswa_id', optional($mahasiswa)->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();

        return view('Pages.riwayat', compact('riwayat'));

        $riwayat = JadwalKonseling::with(['mahasiswa.user'])
            ->where('mahasiswa_id', optional($mahasiswa)->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $selectedJadwal = request()->query('jadwal');

        return view('Pages.riwayat', compact('riwayat', 'selectedJadwal'));

    }

    public function detailRiwayat($id)
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (! $mahasiswa) {
            abort(403, 'Data mahasiswa tidak ditemukan.');
        }

        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'konselor.user', 'sesiKonseling'])
            ->where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $laporan = null;
        if ($jadwal->sesiKonseling) {
            $laporan = Laporan::where('sesi_id', $jadwal->sesiKonseling->id)->first();
        }

        $statusInfo = $this->formatRiwayatStatus($jadwal->status);
        $topik = $this->extractTopik($jadwal->catatan, $jadwal->topik ?? null);
        $metode = strtolower(trim((string) $jadwal->jenis)) === 'offline'
            ? 'Tatap Muka'
            : 'Video Call';

        $catatanRingkasan = trim((string) ($jadwal->ringkasan_masalah ?? ''));
        if ($catatanRingkasan === '') {
            $catatanRingkasan = trim((string) ($laporan->isi_laporan ?? $jadwal->catatan ?? ''));
        }

        $observasi = trim((string) ($jadwal->observasi_konselor ?? ''));
        $progress = trim((string) ($jadwal->progress ?? ''));
        $tindakLanjut = trim((string) ($jadwal->tindak_lanjut ?? $jadwal->tindak_lanjut_tipe ?? ''));
        $tanggalLanjut = trim((string) ($jadwal->tanggal_lanjut ?? ''));

        return view('Pages.riwayat-detail', compact(
            'jadwal',
            'laporan',
            'statusInfo',
            'topik',
            'metode',
            'catatanRingkasan',
            'observasi',
            'progress',
            'tindakLanjut',
            'tanggalLanjut'
        ));
    }

    public function laporanAdmin()
    {
        $riwayat = JadwalKonseling::orderBy('tanggal', 'desc')->get();
        $statusDoneSql = "LOWER(COALESCE(status, '')) = 'selesai'";
        $prioritySql = $this->jadwalHasColumn('laporan')
            ? "CASE WHEN laporan IS NOT NULL OR {$statusDoneSql} THEN 0 ELSE 1 END"
            : "CASE WHEN {$statusDoneSql} THEN 0 ELSE 1 END";

        $riwayat = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])
            ->orderByRaw($prioritySql)
            ->orderByDesc('updated_at')
            ->orderByDesc('tanggal')
            ->get();

        return view('admin.laporan', compact('riwayat'));
    }

    public function createLaporan($id)
    {
        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'konselor.user', 'sesiKonseling.laporan'])->findOrFail($id);
        $foreignKey = (new SesiKonseling())->jadwalKonseling()->getForeignKeyName();
        $statusSelesai = strtolower($jadwal->status ?? '') === 'selesai';

        $sudahAdaLaporan = Laporan::whereHas('sesi', function ($query) use ($id, $foreignKey) {
            $query->where($foreignKey, $id);
        })->exists() || $statusSelesai;

        return view('admin.laporan_form', compact('jadwal', 'sudahAdaLaporan'));
        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])->findOrFail($id);

        return view('admin.laporan', compact('jadwal'));
    }

    public function storeLaporan(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string',
            'progress' => 'nullable|in:Membaik,Memburuk',
            'tanggal_tindak_lanjut' => 'nullable|date',
        ]);

        $jadwal = JadwalKonseling::findOrFail($id);

        $ringkasanMasalah = trim((string) $request->catatan);
        $catatanLama = (string) ($jadwal->catatan ?? '');
        $perluLanjut = $request->boolean('tindak_lanjut');

        $updates = [
            'status' => 'selesai',
        ];

        if ($this->jadwalHasColumn('ringkasan_masalah')) {
            $updates['ringkasan_masalah'] = $ringkasanMasalah;
        } else {
            // Fallback lama: simpan ringkasan tanpa menghilangkan bagian topik pada catatan.
            if (preg_match('/Topik:\s*([^|]+)/i', $catatanLama, $match)) {
                $updates['catatan'] = 'Topik: '.trim($match[1]).' | Laporan: '.$ringkasanMasalah;
            } else {
                $updates['catatan'] = $ringkasanMasalah;
            }
        }

        if ($this->jadwalHasColumn('observasi_konselor')) {
            $updates['observasi_konselor'] = $request->observasi_konselor;
        }

        if ($this->jadwalHasColumn('progress')) {
            $updates['progress'] = $request->progress;
        }

        if ($this->jadwalHasColumn('tindak_lanjut_tipe')) {
            $updates['tindak_lanjut_tipe'] = $perluLanjut ? 'perlu_lanjut' : null;
        }

        if ($this->jadwalHasColumn('tanggal_lanjut')) {
            $updates['tanggal_lanjut'] = $perluLanjut ? $request->tanggal_tindak_lanjut : null;
        }

        if ($this->jadwalHasColumn('tindak_lanjut')) {
            $updates['tindak_lanjut'] = $perluLanjut ? 'perlu_lanjut' : null;
        }

        if ($this->jadwalHasColumn('laporan')) {
            $updates['laporan'] = ($ringkasanMasalah !== '' || (string) $request->observasi_konselor !== '') ? 'ada' : null;
        }

        $jadwal->update($updates);

        return redirect()
            ->route('admin.laporan', ['scroll_to' => $jadwal->id])
            ->with('success', 'Laporan berhasil disimpan!');
    }
}
