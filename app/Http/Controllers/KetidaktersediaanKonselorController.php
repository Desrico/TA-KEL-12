<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Konselor;
use App\Models\KetidaktersediaanKonselor;

class KetidaktersediaanKonselorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|required_with:jam_selesai|date_format:H:i',
            'jam_selesai' => 'nullable|required_with:jam_mulai|date_format:H:i|after:jam_mulai',
            'alasan' => 'nullable|string|max:255',
        ]);

        $konselor = Auth::user()->konselor ?? Konselor::where('user_id', Auth::id())->first();

        if (!$konselor) {
            return back()->with('error', 'Data konselor tidak ditemukan.');
        }

        KetidaktersediaanKonselor::create([
            'konselor_id' => $konselor->id,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'alasan' => $request->alasan,
        ]);

        return back()->with('success', 'Jadwal ketidaktersediaan berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $konselor = Auth::user()->konselor ?? Konselor::where('user_id', Auth::id())->first();

        if (!$konselor) {
            return back()->with('error', 'Data konselor tidak ditemukan.');
        }

        $data = KetidaktersediaanKonselor::where('id', $id)
            ->where('konselor_id', $konselor->id)
            ->firstOrFail();

        $data->delete();

        return back()->with('success', 'Jadwal ketidaktersediaan berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|required_with:jam_selesai|date_format:H:i',
            'jam_selesai' => 'nullable|required_with:jam_mulai|date_format:H:i|after:jam_mulai',
            'alasan' => 'nullable|string|max:255',
        ]);

        $konselor = Auth::user()->konselor ?? Konselor::where('user_id', Auth::id())->first();

        if (!$konselor) {
            return back()->with('error', 'Data konselor tidak ditemukan.');
        }

        $data = KetidaktersediaanKonselor::where('id', $id)
            ->where('konselor_id', $konselor->id)
            ->firstOrFail();

        $data->update([
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'alasan' => $request->alasan,
        ]);

        return back()->with('success', 'Ketidaktersediaan berhasil diperbarui.');
    }
}