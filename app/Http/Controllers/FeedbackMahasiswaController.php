<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\SesiKonseling;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class FeedbackMahasiswaController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sesi_id' => ['required', 'integer'],
            'isi_feedback' => ['required', 'string', 'max:1000'],
        ]);

        $user = auth()->user();

        $mahasiswa = Mahasiswa::where('user_id', $user->id)->firstOrFail();

        $sesi = SesiKonseling::query()
            ->with('jadwalKonseling.mahasiswa')
            ->where('id', $validated['sesi_id'])
            ->where('status', 'selesai')
            ->whereHas('jadwalKonseling.mahasiswa', function ($query) use ($mahasiswa) {
                $query->where('id', $mahasiswa->id);
            })
            ->firstOrFail();

        Feedback::updateOrCreate(
            [
                'sesi_id' => $sesi->id,
                'mahasiswa_id' => $mahasiswa->id,
            ],
            [
                'isi_feedback' => $validated['isi_feedback'],
            ]
        );

        return back()->with('success', 'Terima kasih, feedback kamu berhasil dikirim.');
    }
}