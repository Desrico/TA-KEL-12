<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\JadwalKonseling;
use App\Models\Laporan;
use App\Models\SesiKonseling;

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
        $riwayat = $this->laporanRiwayatQuery()->get();

        return view('admin.laporan', compact('riwayat'));
    }

    public function createLaporan($id)
    {
        $jadwal = $this->laporanRiwayatQuery()->whereKey($id)->firstOrFail();
        $riwayat = $this->laporanRiwayatQuery()->get();
        $sesi = $this->resolveSesiKonseling($jadwal);
        $laporan = $sesi->laporan;

        return view('admin.laporan', compact('jadwal', 'riwayat', 'sesi', 'laporan'));
    }

    public function storeLaporan(Request $request, $id)
    {
        $request->validate([
            'isi_laporan' => 'required|string',
        ]);

        $jadwal = JadwalKonseling::findOrFail($id);
        $sesi = $this->resolveSesiKonseling($jadwal);
        $konselorId = optional(auth()->user()->konselor)->id ?? $jadwal->konselor_id;

        Laporan::updateOrCreate([
            'sesi_id' => $sesi->id,
        ], [
            'konselor_id' => $konselorId,
            'isi_laporan' => trim($request->isi_laporan),
        ]);

        $sesi->update([
            'status' => 'selesai',
        ]);

        $jadwal->update([
            'status' => 'selesai',
        ]);

        return redirect()
            ->route('admin.laporan.laporan', $jadwal->id)
            ->with('success', 'Laporan berhasil disimpan.');
    }

    public function detailRiwayat($id)
    {
        $jadwal = JadwalKonseling::with([
            'mahasiswa.user.profil',
            'konselor.user',
            'sesiKonseling'
        ])->findOrFail($id);

        $mahasiswa = $jadwal->mahasiswa;
        $user = optional($mahasiswa)->user;
        $profil = optional($user)->profil;

        $totalKonseling = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)->count();

        $sesiBerlangsung = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
            ->whereIn('status', ['disetujui', 'diterima', 'berlangsung', 'sedang berlangsung'])
            ->count();

        return view('Pages.detail-riwayat', compact(
            'jadwal',
            'mahasiswa',
            'user',
            'profil',
            'totalKonseling',
            'sesiBerlangsung'
        ));
    }

    private function laporanRiwayatQuery()
    {
        return JadwalKonseling::with([
            'mahasiswa.user',
            'konselor.user',
            'sesiKonseling.laporan',
        ])->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc');
    }

    private function resolveSesiKonseling(JadwalKonseling $jadwal): SesiKonseling
    {
        $foreignKey = SesiKonseling::jadwalForeignKey();

        return SesiKonseling::firstOrCreate([
            $foreignKey => $jadwal->id,
        ], [
            'status' => strtolower((string) ($jadwal->status ?: 'berlangsung')),
        ]);
    }
}