<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKonseling;
use App\Models\Mahasiswa;
use App\Models\Notifikasi;

class RiwayatController extends Controller
{
    public function riwayatMahasiswa()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $riwayat = JadwalKonseling::with([
                'mahasiswa.user',
                'konselor.user',
                'sesiKonseling.feedback',
            ])
            ->where('mahasiswa_id', optional($mahasiswa)->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('Pages.riwayat', compact('riwayat'));
    }

    public function editJadwalUlang($id)
{
    $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

    $jadwal = JadwalKonseling::where('id', $id)
        ->where('mahasiswa_id', $mahasiswa->id)
        ->where('status', 'perlu_penjadwalan_ulang')
        ->firstOrFail();

    return view('Pages.jadwal_ulang', compact('jadwal'));
}

public function updateJadwalUlang(Request $request, $id)
{
    $validated = $request->validate([
        'tanggal' => 'required|date|after_or_equal:today',
        'waktu' => 'required|date_format:H:i',
        'jenis' => 'required|string',
        'topik' => 'required|string|max:255',
    ]);

    $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

    $jadwal = JadwalKonseling::where('id', $id)
        ->where('mahasiswa_id', $mahasiswa->id)
        ->where('status', 'perlu_penjadwalan_ulang')
        ->firstOrFail();

    $jadwal->update([
        'tanggal' => $validated['tanggal'],
        'waktu' => $validated['waktu'],
        'jenis' => $validated['jenis'],
        'topik' => $validated['topik'],
        'status' => 'menunggu',
        'updated_at' => now(),
    ]);

    Notifikasi::create([
        'user_id' => Auth::id(),
        'pesan' => 'Jadwal konseling ulang Anda berhasil diajukan dan sedang menunggu konfirmasi konselor.',
        'status' => 'belum_dibaca',
    ]);

    return redirect('/riwayat/' . $jadwal->id)
        ->with('success', 'Jadwal berhasil diajukan ulang dan menunggu konfirmasi konselor.');
}
}
