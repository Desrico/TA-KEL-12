<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKonseling;

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
    }

    public function laporanAdmin()
    {
        $riwayat = JadwalKonseling::orderBy('tanggal', 'desc')->get();
        return view('admin.laporan', compact('riwayat'));
    }

    public function createLaporan($id)
    {
        $jadwal = JadwalKonseling::findOrFail($id);
        return view('admin.laporan', compact('jadwal'));
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

        return redirect()->route('admin.laporan')->with('success', 'Laporan berhasil disimpan!');
    }
}
