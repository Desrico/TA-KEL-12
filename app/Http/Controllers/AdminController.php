<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\JadwalKonseling;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Konselor;
use App\Models\Notifikasi;
use App\Models\SesiKonseling;

class AdminController extends Controller
{
    public function notifications()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 401);
        }

        $items = $user->notifikasi()
            ->latest()
            ->take(6)
            ->get(['id', 'pesan', 'status', 'created_at']);

        return response()->json([
            'success' => true,
            'unread_count' => $user->notifikasi()->where('status', 'belum')->count(),
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'pesan' => $item->pesan,
                    'status' => $item->status,
                    'created_at_human' => $item->created_at?->diffForHumans() ?? 'Baru saja',
                ];
            })->values(),
        ]);
    }

    public function markNotificationsAsRead()
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 401);
        }

        Notifikasi::where('user_id', $userId)
            ->where('status', 'belum')
            ->update(['status' => 'dibaca']);

        return response()->json([
            'success' => true,
        ]);
    }

    private function createApprovalNotificationIfMissing(JadwalKonseling $jadwal): void
    {
        $mahasiswaUserId = optional(optional($jadwal->mahasiswa)->user)->id;
        if (!$mahasiswaUserId) {
            return;
        }

        $pesan = 'Jadwal #' . $jadwal->id . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . ' telah disetujui oleh konselor.';

        $exists = Notifikasi::where('user_id', $mahasiswaUserId)
            ->where('pesan', $pesan)
            ->exists();

        if (!$exists) {
            Notifikasi::create([
                'user_id' => $mahasiswaUserId,
                'pesan'   => $pesan,
                'status'  => 'belum',
            ]);
        }
    }


    public function jadwal()
    {
        return view('admin.jadwal');
    }

    public function setujui($id)
    {
        $jadwal = JadwalKonseling::with('mahasiswa.user')->findOrFail($id);
        $jadwal->update(['status' => 'disetujui']);

        $this->createApprovalNotificationIfMissing($jadwal);

        return back()->with('success', 'Jadwal berhasil disetujui!');
    }

    public function tolak($id)
    {
        JadwalKonseling::findOrFail($id)->update(['status' => 'ditolak']);
        return back()->with('success', 'Jadwal berhasil ditolak.');
    }

   public function sesi()
    {
        $konselor = auth()->user()->konselor;

        $jadwal = \App\Models\JadwalKonseling::with(['mahasiswa.user', 'mahasiswa.user.profil'])
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

    public function detailSesi($id)
    {
        $konselor = auth()->user()->konselor;

        $jadwal = \App\Models\JadwalKonseling::with(['mahasiswa.user'])
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        return view('admin.detail_sesi', compact('jadwal'));
    }

    public function terimaSesi($id)
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

    public function tolakSesi($id)
    {
        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::with(['mahasiswa.user'])
            ->where('konselor_id', $konselor->id)
            ->findOrFail($id);

        return view('admin.tolak_sesi', compact('jadwal'));
    }

    public function kirimTolakSesi(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
        ]);

        return redirect()->route('admin.sesi.detail', $jadwal->id)
            ->with('success', 'Jadwal berhasil ditolak.');
    }
    private function getKonselor()
    {
        $user = auth()->user();

        if (!$user || !$user->konselor) {
            abort(403, 'Data konselor tidak ditemukan.');
        }

        return $user->konselor;
    }

    public function mahasiswa()
    {
        $konselor   = Konselor::where('user_id', Auth::id())->first();
        $mahasiswas = Mahasiswa::with([
                            'user',
                            'jadwalKonseling' => function($q) use ($konselor) {
                                $q->where('konselor_id', optional($konselor)->id)
                                  ->orderBy('tanggal', 'desc')
                                  ->orderBy('waktu', 'desc');
                            }
                        ])
                        ->whereHas('jadwalKonseling', function($q) use ($konselor) {
                            $q->where('konselor_id', optional($konselor)->id);
                        })->get();

        return view('admin.mahasiswa', compact('mahasiswas'));
    }

    public function jadwalEvents()
{
    $jadwals = JadwalKonseling::with(['mahasiswa.user'])
        ->orderBy('tanggal')
        ->orderBy('waktu')
        ->get();

    $events = $jadwals->map(function ($jadwal) {
        $statusColor = match ($jadwal->status) {
            'menunggu' => '#E9D98B',
            'disetujui' => '#B8EEC0',
            'berlangsung' => '#C9B8F5',
            'selesai' => '#8EC9F5',
            'ditolak' => '#F4A6A6',
            default => '#D9D9D9',
        };

        $namaMahasiswa = optional(optional($jadwal->mahasiswa)->user)->nama ?? 'Mahasiswa';
        $jenis = ucfirst($jadwal->jenis ?? '-');
        $topik = $jadwal->topik ?? '-';
        $waktu = $jadwal->waktu ? substr($jadwal->waktu, 0, 5) : '-';

        return [
            'id' => $jadwal->id,
            'title' => $namaMahasiswa . ' - ' . $waktu,
            'start' => $jadwal->tanggal,
            'allDay' => true,
            'backgroundColor' => $statusColor,
            'borderColor' => $statusColor,
            'textColor' => '#1F2937',
            'extendedProps' => [
                'nama' => $namaMahasiswa,
                'waktu' => $waktu,
                'jenis' => $jenis,
                'topik' => $topik,
                'status' => ucfirst($jadwal->status),
            ],
        ];
    });

    return response()->json($events);
}

    public function pengaturan()
    {
        return view('admin.pengaturan');
    }

    public function chat()
    {
        return view('admin.chat');
    }


}