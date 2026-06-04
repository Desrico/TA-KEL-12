<?php

namespace App\Http\Controllers;

use App\Models\Konselor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\JadwalKonseling;
use App\Models\SesiKonseling;

class SesiKonselingController extends Controller
{
    private function resolveAuthenticatedKonselor(): Konselor
    {
        $user = auth()->user();

        if (! $user || ! $user->konselor) {
            abort(403, 'Data konselor tidak ditemukan.');
        }

        return $user->konselor;
    }

    public function index(Request $request)
    {
        $konselor = $this->resolveAuthenticatedKonselor();
        $search = trim((string) $request->query('search', ''));

        $jadwalQuery = JadwalKonseling::with(['mahasiswa.user', 'mahasiswa.user.profil', 'sesiKonseling.laporan'])
            ->where('konselor_id', $konselor->id);

        if ($search !== '') {
            $jadwalQuery->where(function ($query) use ($search) {
                $query->whereHas('mahasiswa.user', function ($userQuery) use ($search) {
                    $userQuery->where('nama', 'like', '%' . $search . '%');
                })->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                    $mahasiswaQuery->where('nim', 'like', '%' . $search . '%')
                        ->orWhere('jurusan', 'like', '%' . $search . '%');
                });
            });
        }

        $jadwal = $jadwalQuery
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
            ->paginate(10)
            ->withQueryString();

        $jadwal->getCollection()->each(function (JadwalKonseling $item) {
            $item->syncExpiredSessionStatus();

            // If a sesi exists and is active, ensure the jadwal status reflects it.
            if ($item->relationLoaded('sesiKonseling') && $item->sesiKonseling?->status === 'berlangsung' && ($item->status ?? '') !== 'berlangsung') {
                $item->forceFill(['status' => 'berlangsung'])->save();
            }
        });

        return view('admin.sesi', compact('jadwal'));
    }

    public function detail($id)
    {
        $konselor = $this->resolveAuthenticatedKonselor();

        $jadwal = JadwalKonseling::with(['mahasiswa.user', 'sesiKonseling.laporan'])
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        return view('admin.detail_sesi', compact('jadwal'));
    }

    public function terima($id)
    {
        $konselor = $this->resolveAuthenticatedKonselor();

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
        $konselor = $this->resolveAuthenticatedKonselor();

        $jadwal = JadwalKonseling::with(['mahasiswa.user'])
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        return view('admin.tolak_sesi', compact('jadwal'));
    }

    public function kirimTolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string',
        ]);

        $konselor = $this->resolveAuthenticatedKonselor();

        $jadwal = JadwalKonseling::where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return redirect()
            ->route('admin.sesi')
            ->with('success', 'Penjadwalan berhasil ditolak.');
    }
    
    public function selesai($id)
    {
        $konselor = $this->resolveAuthenticatedKonselor();

        $jadwal = JadwalKonseling::where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->update(['status' => 'selesai']);

        $sesi = $jadwal->sesiKonseling ?: $jadwal->sesiKonseling()->create([
            'status' => 'selesai',
            'waktu_mulai' => now()->subHour(),
        ]);

        $sesi->update(['status' => 'selesai']);

        if (Schema::hasColumn('sesi_konseling', 'waktu_selesai')) {
            $sesi->update(['waktu_selesai' => now()]);
        }

        // Broadcast ke mahasiswa agar chat otomatis terkunci
        broadcast(new \App\Events\SesiSelesai($sesi))->toOthers();

        // Redirect kembali ke halaman chat, BUKAN detail sesi
        return redirect()
            ->route('admin.chat', ['jadwal' => $jadwal->id])
            ->with('success', 'Sesi konseling telah diselesaikan.');
    }
}
