<?php

namespace App\Http\Controllers;

use App\Models\Konselor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\JadwalKonseling;
use App\Models\Notifikasi;
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

    private function getIdentitasMahasiswaTampil(JadwalKonseling $jadwal): array
    {
        $mahasiswa = $jadwal->mahasiswa;
        $userMahasiswa = $mahasiswa?->user;

        $isAnonim = filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

        $namaAnonim = 'Anonim';

        if ($userMahasiswa && method_exists($userMahasiswa, 'getAnonimDisplayName')) {
            $namaAnonim = trim($userMahasiswa->getAnonimDisplayName()) ?: 'Anonim';
        }

        return [
            'is_anonim' => $isAnonim,
            'nama' => $isAnonim
                ? $namaAnonim
                : ($userMahasiswa->nama ?? '-'),
            'nim' => $isAnonim
                ? '-'
                : ($mahasiswa->nim ?? '-'),
        ];
    }

    public function index(Request $request)
    {
        $konselor = $this->resolveAuthenticatedKonselor();
        $search = trim((string) $request->query('search', ''));
        $selectedJadwalId = $request->integer('jadwal');

        $jadwalQuery = JadwalKonseling::with(['mahasiswa.user', 'mahasiswa.user.profil', 'sesiKonseling.laporan'])
            ->where('konselor_id', $konselor->id);

        if ($search !== '') {
            $jadwalQuery
                ->where(function ($query) {
                    $query->whereNull('anonim')
                        ->orWhere('anonim', false);
                })
                ->where(function ($query) use ($search) {
                    $query->whereHas('mahasiswa.user', function ($userQuery) use ($search) {
                        $userQuery->where('nama', 'like', '%' . $search . '%');
                    })->orWhereHas('mahasiswa', function ($mahasiswaQuery) use ($search) {
                        $mahasiswaQuery->where('nim', 'like', '%' . $search . '%')
                            ->orWhere('jurusan', 'like', '%' . $search . '%');
                    });
                });
        }

        if ($selectedJadwalId > 0) {
            $jadwalQuery->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$selectedJadwalId]);
        }

        $jadwal = $jadwalQuery
            // Riwayat berfungsi sebagai aktivitas: keputusan terbaru konselor tampil paling atas.
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $jadwal->getCollection()->each(function (JadwalKonseling $item) {
            $item->syncExpiredSessionStatus();

            if ($item->relationLoaded('sesiKonseling') && $item->sesiKonseling?->status === 'berlangsung' && ($item->status ?? '') !== 'berlangsung') {
                $item->forceFill(['status' => 'berlangsung'])->save();
            }

            $identitas = $this->getIdentitasMahasiswaTampil($item);

            $item->nama_tampil = $identitas['nama'];
            $item->nim_tampil = $identitas['nim'];
            $item->is_anonim_tampil = $identitas['is_anonim'];
        });
        return view('admin.riwayat', compact('jadwal'));
    }

    public function detail($id)
    {
        $konselor = $this->resolveAuthenticatedKonselor();

        $jadwal = JadwalKonseling::with(['mahasiswa.user.profil', 'sesiKonseling.laporan'])
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->syncExpiredSessionStatus();

        if ($jadwal->sesiKonseling?->status === 'berlangsung' && ($jadwal->status ?? '') !== 'berlangsung') {
            // Detail riwayat harus mengikuti status sesi aktif dari ruang chat.
            $jadwal->forceFill(['status' => 'berlangsung'])->save();
        }

        $identitasMahasiswa = $this->getIdentitasMahasiswaTampil($jadwal);

        return view('admin.detail_riwayat', compact('jadwal', 'identitasMahasiswa'));
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
            ->route('admin.riwayat.detail', $jadwal->id)
            ->with('terima_success', true);
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
        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        $konselor = $this->resolveAuthenticatedKonselor();

        $jadwal = JadwalKonseling::with('mahasiswa.user')
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $validated['alasan_penolakan'],
        ]);

        $mahasiswaUserId = $jadwal->mahasiswa?->user?->id;

        if ($mahasiswaUserId) {
            $target = route('detail.riwayat', $jadwal->id);

            Notifikasi::updateOrCreate(
                [
                    'user_id' => $mahasiswaUserId,
                    'cta_target' => $target,
                    'cta_label' => 'Lihat Alasan Penolakan',
                ],
                [
                    'pesan' => 'Permintaan penjadwalan konseling Anda telah ditolak. Alasan: '
                        . $validated['alasan_penolakan'],
                    'status' => 'belum',
                ]
            );
        }

        return redirect()
            ->route('admin.riwayat', ['jadwal' => $jadwal->id])
            ->with('success', 'Penjadwalan berhasil ditolak.');
    }
    
    public function selesai(\Illuminate\Http\Request $request, $id)
    {
        $jadwal = JadwalKonseling::with('sesiKonseling')->findOrFail($id);

        $jadwal->update([
            'status' => 'selesai',
        ]);

        if ($jadwal->sesiKonseling) {
            $jadwal->sesiKonseling->update([
                'status' => 'selesai',
                'waktu_selesai' => now(),
            ]);
        }

        $jenisLayanan = strtolower((string) ($jadwal->jenis ?? 'online'));
        $isOfflineSession = str_contains($jenisLayanan, 'offline');

        if ($isOfflineSession) {
            return redirect()
                ->route('admin.riwayat')
                ->with('success', 'Sesi offline berhasil ditandai selesai.');
        }

        if ($request->query('from') === 'chat') {
            return redirect()
                ->route('admin.chat', ['jadwal' => $jadwal->id])
                ->with('success', 'Sesi konseling berhasil ditandai selesai.');
        }

        return redirect()
            ->route('admin.riwayat.detail', $jadwal->id)
            ->with('success', 'Sesi konseling berhasil ditandai selesai.');
    }
}
