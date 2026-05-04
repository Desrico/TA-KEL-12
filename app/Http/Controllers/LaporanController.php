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
        $riwayat = JadwalKonseling::orderByRaw("CASE WHEN laporan IS NOT NULL OR LOWER(status) = 'selesai' THEN 0 ELSE 1 END")
            ->orderByDesc('updated_at')
            ->orderByDesc('tanggal')
            ->get();
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
            'ringkasan_masalah' => 'nullable|string',
            'observasi_konselor' => 'nullable|string',
            'progress' => 'nullable|string',
            'perlu_lanjut' => 'nullable',
            'tanggal_lanjut' => 'nullable|date',
        ]);

        $jadwal = JadwalKonseling::findOrFail($id);
        $jadwal->update([
            'ringkasan_masalah' => $request->ringkasan_masalah,
            'observasi_konselor' => $request->observasi_konselor,
            'progress' => $request->progress,
            'tindak_lanjut_tipe' => $request->perlu_lanjut ? 'perlu_lanjut' : null,
            'tanggal_lanjut' => $request->perlu_lanjut ? $request->tanggal_lanjut : null,
            'laporan' => $request->ringkasan_masalah || $request->observasi_konselor ? 'ada' : null,
            'status' => 'Selesai',
        ]);

        return redirect()
            ->route('admin.laporan', ['scroll_to' => $jadwal->id])
            ->with('success', 'Laporan berhasil disimpan!');
    }
}