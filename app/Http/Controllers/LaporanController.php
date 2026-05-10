<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalKonseling;
use App\Models\Laporan;
use App\Models\SesiKonseling;

class LaporanController extends Controller
{
    public function riwayat()
    {
        $mahasiswa = auth()->user()->mahasiswa;
        $riwayat = JadwalKonseling::with(['mahasiswa.user', 'konselor.user'])
            ->where('mahasiswa_id', optional($mahasiswa)->id)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc')
            ->get();

        return view('Pages.riwayat', compact('riwayat'));
    }

    public function laporanAdmin()
    {
        $riwayat = $this->laporanRiwayatQuery()->get();

        return view('admin.laporan', compact('riwayat'));
    }

    public function createLaporan($id)
    {
        $jadwal = $this->laporanRiwayatQuery()->whereKey($id)->firstOrFail();
        $riwayat = $this->laporanRiwayatQuery()->get();
        $sesi = $this->resolveSesiKonseling($jadwal);
        $laporan = $sesi->laporan;

        return view('admin.laporan', compact('jadwal', 'riwayat', 'sesi', 'laporan'));
    }

    public function storeLaporan(Request $request, $id)
    {
        $request->validate([
            'isi_laporan' => 'required|string',
        ]);

        $jadwal = JadwalKonseling::findOrFail($id);
        $sesi = $this->resolveSesiKonseling($jadwal);
        $konselorId = optional(auth()->user()->konselor)->id ?? $jadwal->konselor_id;

        Laporan::updateOrCreate([
            'sesi_id' => $sesi->id,
        ], [
            'konselor_id' => $konselorId,
            'isi_laporan' => trim($request->isi_laporan),
        ]);

        $sesi->update([
            'status' => 'selesai',
        ]);

        $jadwal->update([
            'status' => 'selesai',
        ]);

        return redirect()
            ->route('admin.laporan.laporan', $jadwal->id)
            ->with('success', 'Laporan berhasil disimpan.');
    }

    private function laporanRiwayatQuery()
    {
        return JadwalKonseling::with([
            'mahasiswa.user',
            'konselor.user',
            'sesiKonseling.laporan',
        ])->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'desc');
    }

    private function resolveSesiKonseling(JadwalKonseling $jadwal): SesiKonseling
    {
        $foreignKey = SesiKonseling::jadwalForeignKey();

        return SesiKonseling::firstOrCreate([
            $foreignKey => $jadwal->id,
        ], [
            'status' => strtolower((string) ($jadwal->status ?: 'berlangsung')),
        ]);
    }
}
