<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalKonseling;
use App\Models\Mahasiswa;
use App\Models\Konselor;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success'  => false,
                'message'  => 'Silakan login terlebih dahulu untuk melakukan booking.',
                'redirect' => '/login'
            ], 401);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'waktu'   => 'required',
            'jenis'   => 'required|in:online,offline',
            'topik'   => 'required|string',
        ]);

        $user      = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan.'
            ], 404);
        }

        $konselor = Konselor::first();
        if (!$konselor) {
            return response()->json([
                'success' => false,
                'message' => 'Konselor belum tersedia.'
            ], 404);
        }

        $sudahAda = JadwalKonseling::where('tanggal', $request->tanggal)
            ->where('waktu', $request->waktu)
            ->where('konselor_id', $konselor->id)
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal ini sudah dibooking. Silakan pilih waktu lain.'
            ], 409);
        }

        // Cek mode anonim
        $isAnonim    = $user->isAnonim();
        $namaDisplay = $isAnonim ? 'Mahasiswa Anonim' : $user->nama;

        // Simpan booking dengan catatan topik + status anonim
        $jadwal = JadwalKonseling::create([
            'mahasiswa_id' => $mahasiswa->id,
            'konselor_id'  => $konselor->id,
            'tanggal'      => $request->tanggal,
            'waktu'        => $request->waktu,
            'status'       => 'menunggu',
            'catatan'      => ($isAnonim ? '[ANONIM] ' : '') . 'Topik: ' . $request->topik . ' | Jenis: ' . $request->jenis,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Booking berhasil!',
            'kode_booking' => 'BK-' . strtoupper(base_convert($jadwal->id, 10, 36)),
            'nama_display' => $namaDisplay,
            'is_anonim'    => $isAnonim,
        ]);
    }

    public function getBookedSlots(Request $request)
    {
        $konselor = Konselor::first();
        if (!$konselor) {
            return response()->json([]);
        }

        $booked = JadwalKonseling::where('konselor_id', $konselor->id)
            ->where('status', '!=', 'ditolak')
            ->get(['tanggal', 'waktu'])
            ->map(fn($j) => $j->tanggal . '-' . $j->waktu)
            ->toArray();

        return response()->json($booked);
    }
}