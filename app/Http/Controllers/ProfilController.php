<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKonseling;
use App\Models\Konselor;
use App\Models\Notifikasi;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['mahasiswa', 'konselor', 'profil']);

        if ($user->role === 'konselor') {
            $konselor = $user->konselor ?: Konselor::where('user_id', $user->id)->first();

            return view('admin.profil', compact('user', 'konselor'));
        }

        $mahasiswa = $user->mahasiswa;

        $totalKonseling = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
            ->count();

        $sesiBerlangsung = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
            ->where('status', 'disetujui')
            ->count();

        return view('Pages.profil', compact(
            'user',
            'mahasiswa',
            'totalKonseling',
            'sesiBerlangsung'
        ));
    }

    public function update(Request $request)
    {
        return back()->with('info', 'Profil menggunakan data akun CIS, sehingga tidak dapat diubah dari aplikasi ini.');
    }

    public function toggleAnonim(Request $request)
    {
        $request->validate([
            'anonim' => 'required|boolean',
        ]);

        $user = auth()->user();

        if (! $user || $user->role !== 'mahasiswa') {
            abort(403);
        }

        $user->update([
            'is_anonim' => $request->boolean('anonim'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mode anonim berhasil diperbarui.',
            'anonim' => (bool) $user->fresh()->is_anonim,
        ]);
    }

    public function riwayat()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $riwayat = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])
            ->where('mahasiswa_id', optional($mahasiswa)->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();

        return view('Pages.riwayat', compact('riwayat'));
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
