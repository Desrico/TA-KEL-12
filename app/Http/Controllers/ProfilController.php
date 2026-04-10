<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKonseling;
use App\Models\Mahasiswa;
use App\Models\Profil;
use App\Models\Notifikasi;

class ProfilController extends Controller
{
    public function index()
    {
        $user            = Auth::user();
        $mahasiswa       = $user->mahasiswa;
        $totalKonseling  = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)->count();
        $sesiBerlangsung = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
                            ->where('status', 'disetujui')->count();

        return view('pages.profil', compact('user', 'mahasiswa', 'totalKonseling', 'sesiBerlangsung'));
    }

   public function update(Request $request)
{
    $request->validate([
        'nama'     => 'required|string|max:100',
        'nim'      => 'required|string|max:20|unique:mahasiswa,nim,' . optional(Auth::user()->mahasiswa)->id,
        'bio'      => 'nullable|string|max:500',
        'jurusan'  => 'required|string|max:100',
        'angkatan' => 'required|digits:4',
    ]);

    // Update user
    Auth::user()->update(['nama' => $request->nama]);

    // Update mahasiswa
    if (Auth::user()->mahasiswa) {
        Auth::user()->mahasiswa->update([
            'nim'      => $request->nim,
            'jurusan'  => $request->jurusan,
            'angkatan' => $request->angkatan,
        ]);
    }

    // Update profil bio
    Profil::updateOrCreate(
        ['user_id' => Auth::id()],
        ['bio'     => $request->bio]
    );

    return back()->with('success', 'Profil berhasil diperbarui!');
}

    public function toggleAnonim(Request $request)
    {
        $profil = Profil::firstOrCreate(['user_id' => Auth::id()]);
        $profil->update(['anonim' => $request->anonim]);

        return response()->json([
            'success' => true,
            'anonim'  => (bool) $request->anonim,
            'message' => $request->anonim ? 'Mode anonim aktif' : 'Mode anonim nonaktif',
        ]);
    }

    public function riwayat()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $riwayat   = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
                        ->orderBy('tanggal', 'desc')
                        ->get();

        return view('pages.riwayat', compact('riwayat'));
    }

    public function markNotificationsAsRead()
    {
        Notifikasi::where('user_id', Auth::id())
            ->where('status', 'belum')
            ->update(['status' => 'dibaca']);

        return response()->json([
            'success' => true,
        ]);
    }

    
}