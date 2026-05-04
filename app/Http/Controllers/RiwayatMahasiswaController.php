<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKonseling;

class RiwayatMahasiswaController extends Controller
{
    public function riwayat()
    {
        $mahasiswa = auth()->user()->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Data mahasiswa tidak ditemukan.');
        }

        $query = JadwalKonseling::where('mahasiswa_id', $mahasiswa->id);

        $totalSesi = (clone $query)->count();

        $sesiSelesai = (clone $query)
            ->where('status', 'selesai')
            ->count();

        $riwayat = $query
            ->orderBy('tanggal', 'desc')
            ->paginate(5);

        return view('Pages.riwayat', compact(
            'riwayat',
            'totalSesi',
            'sesiSelesai'
        ));
    }

    public function edit($id)
    {
        $mahasiswa = auth()->user()->mahasiswa;

        $jadwal = JadwalKonseling::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        if (strtolower($jadwal->status) !== 'menunggu konfirmasi') {
            return redirect()->back()->with('error', 'Jadwal tidak bisa diubah.');
        }

        return view('Pages.edit-jadwal', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = auth()->user()->mahasiswa;

        $jadwal = JadwalKonseling::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        if (strtolower($jadwal->status) !== 'menunggu konfirmasi') {
            return redirect()->back()->with('error', 'Jadwal tidak bisa diubah.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'jenis' => 'required|in:online,offline',
        ]);

        $jadwal->update([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'jenis' => $request->jenis,
            'status' => 'menunggu konfirmasi',
        ]);

        return redirect()->route('riwayat.detail', $jadwal->id)
            ->with('success', 'Jadwal berhasil diubah.');
    }
}