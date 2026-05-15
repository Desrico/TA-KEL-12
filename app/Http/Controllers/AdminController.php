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
use App\Models\Student;
use App\Models\KetidaktersediaanKonselor;

class AdminController extends Controller
{
    private function formatKonselingDateTime(?string $tanggal, ?string $waktu): string
    {
        if (!$tanggal) {
            return 'jadwal yang ditentukan';
        }

        $dateTime = Carbon::parse($tanggal, 'Asia/Jakarta')
            ->startOfDay()
            ->setTimeFromTimeString($waktu ?: '00:00:00');

        return $dateTime->translatedFormat('j F Y') . ' pukul ' . $dateTime->format('H:i');
    }

    private function formatJenisKonseling(?string $jenis): string
    {
        $jenis = strtolower(trim((string) $jenis));

        return match ($jenis) {
            'offline' => 'offline',
            default => 'online',
        };
    }

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

        $pesan = 'Konseling ' . $this->formatJenisKonseling($jadwal->jenis) . ' pada ' . $this->formatKonselingDateTime($jadwal->tanggal, $jadwal->waktu) . ' telah disetujui oleh konselor.';
        $legacyMessages = [
            'Jadwal #' . $jadwal->id . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . ' telah disetujui oleh konselor.',
            'Booking #' . $jadwal->id . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . ' telah disetujui oleh konselor.',
        ];

        $existingNotification = Notifikasi::where('user_id', $mahasiswaUserId)
            ->where(function ($query) use ($pesan, $legacyMessages) {
                $query->where('pesan', $pesan)
                    ->orWhereIn('pesan', $legacyMessages);
            })
            ->latest()
            ->first();

        if ($existingNotification) {
            if ($existingNotification->pesan !== $pesan) {
                $existingNotification->pesan = $pesan;
                $existingNotification->save();
            }

            return;
        }

        Notifikasi::create([
            'user_id' => $mahasiswaUserId,
            'pesan'   => $pesan,
            'status'  => 'belum',
        ]);
    }

public function dashboard()
{
    $user = Auth::user();

    if (!$user || $user->role !== 'konselor') {
        return redirect('/')->with('error', 'Akses ditolak.');
    }

    $konselor = Konselor::where('user_id', $user->id)->first();

    // DATA TAB 1 - MOBILE
    if (Schema::hasTable('students')) {
        $students = Student::with('journalTexts')
            ->orderBy('mental_level', 'desc')
            ->orderBy('name')
            ->get();

        $lastScan = $students->whereNotNull('mental_scanned_at')->max('mental_scanned_at');
    } else {
        $students = collect();
        $lastScan = null;
    }

    // DATA TAB 2 - STATISTIK KONSELING
    $baseQuery = JadwalKonseling::query();

    if ($konselor) {
        $baseQuery->where('konselor_id', $konselor->id);
    }

    $totalPenjadwalan = (clone $baseQuery)->count();

    $totalSesiSelesai = (clone $baseQuery)
        ->where('status', 'selesai')
        ->count();

    $totalDiterima = (clone $baseQuery)
        ->whereIn('status', ['disetujui', 'diterima'])
        ->count();

    $totalDitolak = (clone $baseQuery)
        ->where('status', 'ditolak')
        ->count();

    $menunggu = (clone $baseQuery)
        ->where('status', 'menunggu')
        ->count();

    $disetujui = $totalDiterima;
    $ditolak = $totalDitolak;

    $mahasiswaAktif = (clone $baseQuery)
        ->distinct('mahasiswa_id')
        ->count('mahasiswa_id');

    $approvalRate = $totalPenjadwalan > 0
        ? round(($totalDiterima / $totalPenjadwalan) * 100)
        : 0;

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

    $topikStats = collect();

    if (Schema::hasColumn('jadwal_konseling', 'topik')) {
        $topikStats = (clone $baseQuery)
            ->whereNotNull('topik')
            ->where('topik', '!=', '')
            ->selectRaw('topik, COUNT(*) as total')
            ->groupBy('topik')
            ->orderByDesc('total')
            ->take(5)
            ->pluck('total', 'topik');
    } elseif (Schema::hasColumn('jadwal_konseling', 'catatan')) {
        $topikStats = (clone $baseQuery)
            ->whereNotNull('catatan')
            ->pluck('catatan')
            ->map(function ($catatan) {
                if (preg_match('/Topik:\s*([^|]+)/i', (string) $catatan, $match)) {
                    return trim($match[1]);
                }

                return trim((string) $catatan);
            })
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(5);
    }

    $topikLabels = $topikStats->keys()->values();
    $topikCounts = $topikStats->values()->values();
    $totalTopik = $topikCounts->sum();

    $JadwalTerbaru = (clone $baseQuery)
        ->with('mahasiswa.user')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    return view('admin.dashboard', compact(
        'students',
        'lastScan',
        'konselor',
        'totalPenjadwalan',
        'totalSesiSelesai',
        'totalDiterima',
        'totalDitolak',
        'menunggu',
        'disetujui',
        'ditolak',
        'mahasiswaAktif',
        'approvalRate',
        'monthlyLabels',
        'monthlyCounts',
        'topikStats',
        'topikLabels',
        'topikCounts',
        'totalTopik',
        'JadwalTerbaru'
    ));
}
    public function jadwal()
    {
        $konselor = Konselor::where('user_id', Auth::id())->first();

        $ketidaktersediaan = KetidaktersediaanKonselor::where('konselor_id', optional($konselor)->id)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();

        return view('admin.jadwal', compact('ketidaktersediaan'));
    }

    public function setujui($id)
    {
        $jadwal = JadwalKonseling::with('mahasiswa.user')->findOrFail($id);
        $jadwal->update(['status' => 'disetujui']);

        $this->createApprovalNotificationIfMissing($jadwal);

        if (($jadwal->jenis ?? null) === 'online') {
            return redirect()
                ->route('admin.chat', ['jadwal' => $jadwal->id])
                ->with('success', 'Jadwal berhasil disetujui dan thread chat terbaru sudah dibuka.');
        }

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

        $this->createApprovalNotificationIfMissing($jadwal->fresh(['mahasiswa.user']));

        if (($jadwal->jenis ?? null) === 'online') {
            return redirect()
                ->route('admin.chat', ['jadwal' => $jadwal->id])
                ->with('success', 'Jadwal berhasil diterima dan thread chat terbaru sudah dibuka.');
        }

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
        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        $konselor = auth()->user()->konselor;

        $jadwal = JadwalKonseling::where('konselor_id', $konselor->id)
            ->findOrFail($id);

        $jadwal->status = 'ditolak';
        $jadwal->alasan_penolakan = $validated['alasan_penolakan'];
        $jadwal->save();

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
    $konselor = auth()->user()->konselor;

    $jadwal = \App\Models\JadwalKonseling::with(['mahasiswa.user'])
        ->where('konselor_id', $konselor->id)
        ->get()
        ->map(function ($item) {
            $status = strtolower($item->status ?? 'menunggu');

            $warna = match ($status) {
                'menunggu', 'menunggu konfirmasi' => '#E9D98B',
                'disetujui', 'diterima' => '#B8EEC0',
                'berlangsung', 'sedang berlangsung' => '#C9B8F5',
                'selesai' => '#8EC9F5',
                'ditolak' => '#F4A6A6',
                default => '#E9D98B',
            };

            return [
                'title' => $item->mahasiswa->user->nama ?? 'Mahasiswa',
                'start' => $item->tanggal,
                'backgroundColor' => $warna,
                'borderColor' => $warna,
                'textColor' => '#1F2937',
                'extendedProps' => [
                    'nama' => $item->mahasiswa->user->nama ?? '-',
                    'waktu' => $item->waktu ?? '-',
                    'jenis' => $item->jenis ?? '-',
                    'topik' => $item->topik ?? '-',
                    'status' => ucfirst($item->status ?? 'Menunggu'),
                ],
            ];
        });

    $tidakTersedia = \App\Models\KetidaktersediaanKonselor::where('konselor_id', $konselor->id)
        ->get()
        ->map(function ($item) {
            $jamMulai = $item->jam_mulai ? substr($item->jam_mulai, 0, 5) : null;
            $jamSelesai = $item->jam_selesai ? substr($item->jam_selesai, 0, 5) : null;

            $title = 'Tidak Tersedia';

            if ($jamMulai && $jamSelesai) {
                $title .= " {$jamMulai} - {$jamSelesai}";
            }

            return [
                'title' => $title,
                'start' => $item->tanggal_mulai,
                'end' => $item->tanggal_selesai,
                'backgroundColor' => '#D9D9D9',
                'borderColor' => '#D9D9D9',
                'textColor' => '#374151',
                'extendedProps' => [
    'id' => $item->id,
    'tanggal' => $item->tanggal_mulai,
    'nama' => 'Konselor Tidak Tersedia',
    'status' => 'Tidak Tersedia',

    'jam_mulai' => $jamMulai,
    'jam_selesai' => $jamSelesai,
    'alasan' => $item->alasan ?? '-',

    'waktu' => $jamMulai && $jamSelesai ? "{$jamMulai} - {$jamSelesai}" : 'Seharian',
    'jenis' => '-',
    'topik' => $item->alasan ?? '-',
],
            ];
        });

    return response()->json($jadwal->merge($tidakTersedia)->values());
}

public function update(Request $request, $id)
{
    $request->validate([
        'tanggal_mulai' => 'required|date',
        'jam_mulai' => 'required',
        'jam_selesai' => 'required|after:jam_mulai',
        'alasan' => 'nullable|string|max:200',
    ]);

    $data = KetidaktersediaanKonselor::findOrFail($id);

    $data->update([
        'tanggal_mulai' => $request->tanggal_mulai,
        'tanggal_selesai' => $request->tanggal_mulai,
        'jam_mulai' => $request->jam_mulai,
        'jam_selesai' => $request->jam_selesai,
        'alasan' => $request->alasan,
    ]);

    return redirect()->back()->with('success', 'Ketidaktersediaan berhasil diperbarui.');
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