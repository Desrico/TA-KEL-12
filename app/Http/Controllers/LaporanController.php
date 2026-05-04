<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalKonseling;
use App\Models\Laporan;
use App\Models\SesiKonseling;

class LaporanController extends Controller
{
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
        return view('admin.laporan', compact('riwayat'));
    }

    public function createLaporan($id)
    {
        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'konselor.user', 'sesiKonseling.laporan'])->findOrFail($id);
        $foreignKey = SesiKonseling::jadwalForeignKey();
        $statusSelesai = strtolower($jadwal->status ?? '') === 'selesai';

        $sudahAdaLaporan = Laporan::whereHas('sesi', function ($query) use ($id, $foreignKey) {
            $query->where($foreignKey, $id);
        })->exists() || $statusSelesai;

        return view('admin.laporan_form', compact('jadwal', 'sudahAdaLaporan'));
    }

    public function storeLaporan(Request $request, $id)
    {
        $request->validate([
            'catatan' => 'required|string',
        ]);
        $jadwal = JadwalKonseling::findOrFail($id);
        $jadwal->update([
            'catatan' => $request->catatan,
            'status' => 'Selesai',
        ]);

        return redirect()
            ->route('admin.laporan', ['scroll_to' => $jadwal->id])
            ->with('success', 'Laporan berhasil disimpan!');
    }
}
