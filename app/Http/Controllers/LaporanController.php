<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKonseling;
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
    }

    public function laporanAdmin()
    {
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
        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])->findOrFail($id);

        return view('admin.laporan', compact('jadwal'));
    }

    public function storeLaporan(Request $request, $id)
    {
        $request->validate([
            'ringkasan_masalah' => 'nullable|string',
            'observasi_konselor' => 'nullable|string',
            'progress' => 'nullable|string',
            'perlu_lanjut' => 'nullable',
            'tanggal_lanjut' => 'nullable|date',
        ]);

        $jadwal = JadwalKonseling::findOrFail($id);
        $updates = [
            'status' => 'selesai',
        ];

        if ($this->jadwalHasColumn('ringkasan_masalah')) {
            $updates['ringkasan_masalah'] = $request->ringkasan_masalah;
        }

        if ($this->jadwalHasColumn('observasi_konselor')) {
            $updates['observasi_konselor'] = $request->observasi_konselor;
        }

        if ($this->jadwalHasColumn('progress')) {
            $updates['progress'] = $request->progress;
        }

        if ($this->jadwalHasColumn('tindak_lanjut_tipe')) {
            $updates['tindak_lanjut_tipe'] = $request->perlu_lanjut ? 'perlu_lanjut' : null;
        }

        if ($this->jadwalHasColumn('tanggal_lanjut')) {
            $updates['tanggal_lanjut'] = $request->perlu_lanjut ? $request->tanggal_lanjut : null;
        }

        if ($this->jadwalHasColumn('tindak_lanjut')) {
            $updates['tindak_lanjut'] = $request->perlu_lanjut ? 'perlu_lanjut' : null;
        }

        if ($this->jadwalHasColumn('laporan')) {
            $updates['laporan'] = ($request->ringkasan_masalah || $request->observasi_konselor) ? 'ada' : null;
        }

        $jadwal->update($updates);

        return redirect()
            ->route('admin.laporan', ['scroll_to' => $jadwal->id])
            ->with('success', 'Laporan berhasil disimpan!');
    }
}
