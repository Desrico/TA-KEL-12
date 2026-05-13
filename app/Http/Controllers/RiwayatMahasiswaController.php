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

        $status = strtolower($jadwal->status ?? '');

        if (!in_array($status, ['menunggu', 'menunggu konfirmasi'])) {
            return redirect()->back()->with('error', 'Jadwal tidak bisa diubah.');
        }

        return view('Pages.konseling', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $mahasiswa = auth()->user()->mahasiswa;

        $jadwal = JadwalKonseling::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->firstOrFail();

        $status = strtolower($jadwal->status ?? '');

        if (!in_array($status, ['menunggu', 'menunggu konfirmasi'])) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak bisa diubah.'
            ], 403);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'waktu' => 'required',
            'jenis' => 'required|in:online,offline',
            'topik' => 'required|string',
        ]);

        $jadwal->update([
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'jenis' => $request->jenis,
            'catatan' => 'Topik: ' . $request->topik . ' | Jenis: ' . $request->jenis,
            'status' => 'menunggu',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diubah.',
            'redirect' => route('riwayat'),
        ]);
    }
}