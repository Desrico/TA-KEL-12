<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKonseling;

class LaporanController extends Controller
{
    public function riwayat()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        $riwayat = JadwalKonseling::where('mahasiswa_id', $mahasiswa->id)->orderBy('tanggal', 'desc')->get();
        return view('mahasiswa.riwayat', compact('riwayat'));
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