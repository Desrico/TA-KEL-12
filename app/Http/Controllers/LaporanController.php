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

        return view('Pages.detail-riwayat', compact(
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

    public function laporanAdmin(\Illuminate\Http\Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = $this->laporanRiwayatQuery();

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->whereHas('mahasiswa.user', function ($qb) use ($q) {
                    $qb->where('nama', 'like', "%{$q}%");
                })->orWhereHas('mahasiswa', function ($qb2) use ($q) {
                    $qb2->where('nim', 'like', "%{$q}%");
                })->orWhere('ringkasan_masalah', 'like', "%{$q}%")
                  ->orWhere('laporan', 'like', "%{$q}%")
                  ->orWhere('topik', 'like', "%{$q}%");
            });
        }

        $riwayat = $query->paginate(10)->withQueryString();

        return view('admin.laporan', compact('riwayat', 'q'));
    }

    public function search(\Illuminate\Http\Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = $this->laporanRiwayatQuery();

        if ($q !== '') {
            $query->where(function ($builder) use ($q) {
                $builder->whereHas('mahasiswa.user', function ($qb) use ($q) {
                    $qb->where('nama', 'like', "%{$q}%");
                })->orWhereHas('mahasiswa', function ($qb2) use ($q) {
                    $qb2->where('nim', 'like', "%{$q}%");
                })->orWhere('ringkasan_masalah', 'like', "%{$q}%")
                  ->orWhere('laporan', 'like', "%{$q}%")
                  ->orWhere('topik', 'like', "%{$q}%");
            });
        }

        $results = $query->limit(10)->get()->map(function ($jadwal) {
            $nama = optional(optional($jadwal->mahasiswa)->user)->nama ?? 'Anonim';
            $nim = optional($jadwal->mahasiswa)->nim ?? '-';
            $tanggal = $jadwal->tanggal ? \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') : '-';
            $waktu = $jadwal->waktu ? substr($jadwal->waktu, 0, 5) . ' WIB' : '-';
            $jenis = ucfirst($jadwal->jenis ?? 'Online');
            $topik = $jadwal->topik ?? '-';

            $statusInfo = $this->formatRiwayatStatus($jadwal->status ?? '');

            return [
                'id' => $jadwal->id,
                'nama' => $nama,
                'nim' => $nim,
                'tanggal' => $tanggal,
                'waktu' => $waktu,
                'jenis' => $jenis,
                'topik' => $topik,
                'status_label' => $statusInfo['label'],
                'status_class' => $statusInfo['class'],
                'laporan_available' => (! empty($jadwal->laporan) || strtolower((string) $jadwal->status) === 'selesai'),
            ];
        });

        return response()->json($results->values());
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
        // Allow fallback: if 'isi_laporan' not provided, compose it from other fields
        $isi = trim((string) $request->input('isi_laporan', ''));
        if ($isi === '') {
            $ringkasan = trim((string) $request->input('ringkasan_masalah', ''));
            $observasi = trim((string) $request->input('observasi_konselor', ''));
            $combined = trim(($ringkasan ? $ringkasan : '') . ($observasi ? "\n\nObservasi: {$observasi}" : ''));
            $isi = $combined;
        }

        if ($isi === '') {
            return back()->withErrors(['isi_laporan' => 'Isi laporan diperlukan.'])->withInput();
        }

        $jadwal = JadwalKonseling::findOrFail($id);
        $sesi = $this->resolveSesiKonseling($jadwal);
        $konselorId = optional(auth()->user()->konselor)->id ?? $jadwal->konselor_id;

        Laporan::updateOrCreate([
            'sesi_id' => $sesi->id,
        ], [
            'konselor_id' => $konselorId,
            'isi_laporan' => $isi,
        ]);

        $sesi->update([
            'status' => 'selesai',
        ]);

        // Update jadwal with status and form fields for traceability
        $jadwal->update([
            'status' => 'selesai',
            'ringkasan_masalah' => $request->input('ringkasan_masalah'),
            'observasi_konselor' => $request->input('observasi_konselor'),
            'progress' => $request->input('progress'),
            'tindak_lanjut_tipe' => $request->has('perlu_lanjut') ? 'perlu_lanjut' : ($request->input('tindak_lanjut') ?? $jadwal->tindak_lanjut_tipe),
            'tanggal_lanjut' => $request->input('tanggal_lanjut') ?: null,
            'laporan' => $isi,
        ]);

        return redirect()
            ->route('admin.laporan', ['scroll_to' => $jadwal->id])
            ->with('success', 'Laporan berhasil disimpan.');
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