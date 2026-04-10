<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\JadwalKonseling;
use App\Models\Mahasiswa;
use App\Models\Konselor;
use App\Models\User;
use App\Models\Notifikasi;

class BookingController extends Controller
{
    private function resolveActiveKonselor(): ?Konselor
    {
        $konselor = Konselor::first();
        if ($konselor) {
            return $konselor;
        }

        $konselorUser = User::where('role', 'konselor')->first();
        if (!$konselorUser) {
            return null;
        }

        return Konselor::firstOrCreate(
            ['user_id' => $konselorUser->id],
            ['spesialisasi' => 'Umum']
        );
    }

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

        $konselor = $this->resolveActiveKonselor();
        if (!$konselor) {
            return response()->json([
                'success' => false,
                'message' => 'Konselor belum tersedia.'
            ], 404);
        }
        $konselor->loadMissing('user');

        $normalizedWaktu = Carbon::createFromFormat('H:i', $request->waktu)->format('H:i:s');

        $sudahAda = JadwalKonseling::whereDate('tanggal', $request->tanggal)
            ->whereTime('waktu', $normalizedWaktu)
            ->where('konselor_id', $konselor->id)
            ->where('status', '!=', 'ditolak')
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
        $prodi       = $mahasiswa->jurusan ?? '-';
        $angkatan    = $mahasiswa->angkatan ?? '-';

        // Untuk konselor, booking anonim hanya menampilkan prodi dan angkatan.
        $identitasUntukKonselor = $isAnonim
            ? 'Mahasiswa Prodi ' . $prodi . ' Angkatan ' . $angkatan
            : $user->nama;

        // Simpan booking dengan catatan topik + status anonim
        $jadwal = JadwalKonseling::create([
            'mahasiswa_id' => $mahasiswa->id,
            'konselor_id'  => $konselor->id,
            'tanggal'      => $request->tanggal,
            'waktu'        => $normalizedWaktu,
            'status'       => 'menunggu',
            'catatan'      => ($isAnonim ? '[ANONIM] ' : '') . 'Topik: ' . $request->topik . ' | Jenis: ' . $request->jenis,
        ]);

        Notifikasi::create([
            'user_id' => $user->id,
            'pesan'   => 'Booking #' . $jadwal->id . ' berhasil dibuat dan menunggu persetujuan konselor.',
            'status'  => 'belum',
        ]);

        $konselorUserId = optional($konselor->user)->id;
        if ($konselorUserId) {
            Notifikasi::create([
                'user_id' => $konselorUserId,
                'pesan'   => 'Booking baru dari ' . $identitasUntukKonselor . ' pada ' . $request->tanggal . ' pukul ' . $request->waktu . '.',
                'status'  => 'belum',
            ]);
        }

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
        $konselor = $this->resolveActiveKonselor();
        if (!$konselor) {
            return response()->json([]);
        }

        $booked = JadwalKonseling::where('konselor_id', $konselor->id)
            ->where('status', '!=', 'ditolak')
            ->get(['tanggal', 'waktu'])
            ->map(function ($j) {
                return $j->tanggal . '-' . Carbon::parse($j->waktu)->format('H:i');
            })
            ->toArray();

        return response()->json($booked);
    }
}