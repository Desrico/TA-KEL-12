<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKonseling;

class RiwayatController extends Controller
{
    public function riwayatMahasiswa()
    {
        // Ambil data mahasiswa yang sedang login
        $mahasiswa = Auth::user()->mahasiswa;
        
        // Ambil riwayat jadwal konseling berdasarkan mahasiswa yang login
        $riwayat = JadwalKonseling::where('mahasiswa_id', optional($mahasiswa)->id)
                        ->orderBy('tanggal', 'desc')
                        ->get();

        // Kirim data riwayat ke view
        return view('mahasiswa.riwayat', compact('riwayat'));
    }
}
