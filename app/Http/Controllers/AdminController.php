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

 public function dashboard()
{
    $user = Auth::user();
    
    if ($user->role !== 'konselor') {
        return redirect('/')->with('error', 'Akses ditolak.');
    }

    $konselor = Konselor::where('user_id', $user->id)->first();

    $baseQuery = JadwalKonseling::where('konselor_id', optional($konselor)->id);

    $totalBooking   = (clone $baseQuery)->count();
    $menunggu       = (clone $baseQuery)->where('status', 'menunggu')->count();
    $disetujui      = (clone $baseQuery)->where('status', 'disetujui')->count();
    $ditolak        = (clone $baseQuery)->where('status', 'ditolak')->count();
    $mahasiswaAktif = (clone $baseQuery)->distinct('mahasiswa_id')->count('mahasiswa_id');
    $approvalRate   = $totalBooking > 0 ? round(($disetujui / $totalBooking) * 100) : 0;

    $monthlyLabels = [];
    $monthlyCounts = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = Carbon::now()->subMonths($i);
        $monthlyLabels[] = $month->translatedFormat('M');
        $monthlyCounts[] = (clone $baseQuery)
            ->whereYear('tanggal', $month->year)
            ->whereMonth('tanggal', $month->month)
            ->count();
    }

    $statusDistribution = [
        'Menunggu' => $menunggu,
        'Disetujui' => $disetujui,
        'Ditolak' => $ditolak,
    ];

    $topikStats = collect();
    if (Schema::hasColumn('jadwal_konseling', 'catatan')) {
        $topikStats = (clone $baseQuery)
            ->whereNotNull('catatan')
            ->pluck('catatan')
            ->map(function ($catatan) {
                if (preg_match('/Topik:\s*([^|]+)/i', (string) $catatan, $match)) {
                    return trim($match[1]);
                }
                return null;
            })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5);
    }

    $bookingTerbaru = (clone $baseQuery)
        ->with('mahasiswa.user')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    return view('admin.dashboard', compact(
        'konselor',
        'totalBooking',
        'menunggu',
        'disetujui',
        'ditolak',
        'mahasiswaAktif',
        'approvalRate',
        'monthlyLabels',
        'monthlyCounts',
        'statusDistribution',
        'topikStats',
        'bookingTerbaru'
    ));
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

        $sesi = SesiKonseling::with('jadwal.mahasiswa.user')
            ->when($konselor, function ($q) use ($konselor) {
                $q->whereHas('jadwal', function ($sub) use ($konselor) {
                    $sub->where('konselor_id', $konselor->id);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.sesi', compact('sesi', 'konselor'));
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