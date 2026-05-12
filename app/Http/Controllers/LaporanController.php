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
}
