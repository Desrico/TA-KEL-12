<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\JadwalKonseling;
use App\Models\SesiKonseling;

class SesiKonselingController extends Controller
{
    public function index()
    {
        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'mahasiswa.user.profil'])
            ->where('konselor_id', $konselor->id)
            ->orderByRaw("
                CASE
                    WHEN status = 'menunggu' THEN 1
                    WHEN status = 'disetujui' THEN 2
                    WHEN status = 'berlangsung' THEN 3
                    WHEN status = 'selesai' THEN 4
                    WHEN status = 'ditolak' THEN 5
                    ELSE 6
                END
            ")
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu', 'asc')
            ->paginate(10);

        return view('admin.sesi', compact('jadwal'));
    }

    public function detail($id)
    {
        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::with(['mahasiswa.user'])
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        return view('admin.detail_sesi', compact('jadwal'));
    }

    public function terima($id)
    {
        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->update([
            'status' => 'disetujui',
        ]);

        return redirect()
            ->route('admin.sesi.detail', $jadwal->id)
            ->with('success', 'Jadwal berhasil diterima.');
    }

    public function tolak($id)
    {
        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::with(['mahasiswa.user'])
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        return view('admin.tolak_sesi', compact('jadwal'));
    }

    public function kirimTolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $jadwal = Jadwal::findOrFail($id);

        $jadwal->status = 'ditolak';
        $jadwal->alasan_penolakan = $request->alasan_penolakan; 
        $jadwal->save();

        return redirect()
            ->route('admin.sesi.detail', $id)
            ->with('success', 'Penjadwalan berhasil ditolak.');
    }

    public function selesai($id)
    {
        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->update([
            'status' => 'selesai',
        ]);

        // set waktu_selesai on sesi_konseling if the column exists
        $sesi = $jadwal->sesiKonseling;
        if ($sesi && Schema::hasColumn('sesi_konseling', 'waktu_selesai')) {
            $sesi->update(['waktu_selesai' => now()]);
        }

        return redirect()
            ->route('admin.sesi.detail', $id)
            ->with('success', 'Sesi berhasil ditandai selesai.');
    }
}