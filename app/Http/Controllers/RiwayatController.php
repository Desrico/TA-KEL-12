<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKonseling;

class RiwayatController extends Controller
{
    public function riwayatMahasiswa()
    {
        $mahasiswa = Auth::user()->mahasiswa;

        $riwayat = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])
            ->where('mahasiswa_id', optional($mahasiswa)->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();

        return view('Pages.riwayat', compact('riwayat'));
    }
}
