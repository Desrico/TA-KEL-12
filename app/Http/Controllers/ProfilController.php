<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\JadwalKonseling;
use App\Models\Profil;
use App\Models\Notifikasi;
use App\Models\User;

class ProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $totalKonseling = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)->count();
        $sesiBerlangsung = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
            ->where('status', 'disetujui')
            ->count();

        return view('pages.profil', compact('user', 'mahasiswa', 'totalKonseling', 'sesiBerlangsung'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;
        $profil = Profil::firstOrCreate(
            ['user_id' => $user->id],
            ['bio' => null, 'anonim' => false]
        );

        $request->validate([
            'nama'     => 'required|string|max:100',
            'nim'      => 'required|string|max:20|unique:mahasiswa,nim,' . optional($mahasiswa)->id,
            'bio'      => 'nullable|string|max:500',
            'jurusan'  => 'required|string|max:100',
            'angkatan' => 'required|digits:4',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Update data user
        $user->update([
            'nama' => $request->nama,
        ]);

        // Update data mahasiswa
        if ($mahasiswa) {
            $mahasiswa->update([
                'nim'      => $request->nim,
                'jurusan'  => $request->jurusan,
                'angkatan' => $request->angkatan,
            ]);
        }

        // Update bio profil
        $profil->bio = $request->bio;

        // Update foto profil jika ada file baru
        if ($request->hasFile('foto')) {
            // hapus foto lama jika ada
            if ($profil->foto && Storage::disk('public')->exists($profil->foto)) {
                Storage::disk('public')->delete($profil->foto);
            }

            // simpan foto baru
            $path = $request->file('foto')->store('profil', 'public');
            $profil->foto = $path;
        }

        $profil->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function toggleAnonim(Request $request)
    {
        $validated = $request->validate([
            'anonim' => 'required|boolean',
        ]);

        $isAnonim = (bool) $validated['anonim'];

        $profil = Profil::updateOrCreate(
            ['user_id' => Auth::id()],
            ['anonim' => $isAnonim]
        );

        if ($isAnonim) {
            $konselorUsers = User::where('role', 'konselor')->get(['id']);

            foreach ($konselorUsers as $konselorUser) {
                Notifikasi::create([
                    'user_id' => $konselorUser->id,
                    'pesan'   => 'Terdapat pembaruan data anonim dari mahasiswa. Silakan cek jadwal terbaru.',
                    'status'  => 'belum',
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'anonim'  => (bool) $profil->anonim,
            'message' => $isAnonim ? 'Mode anonim aktif' : 'Mode anonim nonaktif',
        ]);
    }

    public function riwayat()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $riwayat = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
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