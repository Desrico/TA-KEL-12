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

class AdminController extends Controller
{
    private function createApprovalNotificationIfMissing(JadwalKonseling $jadwal): void
    {
        $mahasiswaUserId = optional(optional($jadwal->mahasiswa)->user)->id;
        if (!$mahasiswaUserId) {
            return;
        }

        $pesan = 'Booking #' . $jadwal->id . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . ' telah disetujui oleh konselor.';

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

    // Halaman login admin
    public function showLogin()
    {
        // Kalau sudah login sebagai konselor, langsung ke dashboard
        if (Auth::check() && Auth::user()->role === 'konselor') {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    // Proses login admin
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Cek apakah role-nya konselor
            if (Auth::user()->role !== 'konselor') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Anda tidak memiliki akses sebagai admin.',
                ])->withInput();
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }

    // Halaman dashboard admin
   public function dashboard()
    {
        $user = Auth::user();

        // Pastikan yang login adalah konselor
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

    public function booking()
    {
        $konselor = Konselor::where('user_id', Auth::id())->first();
        $bookings = JadwalKonseling::where('konselor_id', optional($konselor)->id)
                        ->with('mahasiswa.user')
                        ->orderBy('tanggal', 'desc')
                        ->get();

        // Backfill notifikasi booking masuk untuk admin.
        $bookings->each(function ($jadwal) {
            $isAnonimBooking = str_starts_with((string) ($jadwal->catatan ?? ''), '[ANONIM]');
            $mahasiswa = $jadwal->mahasiswa;

            $identitas = $isAnonimBooking
                ? 'Mahasiswa Prodi ' . (optional($mahasiswa)->jurusan ?? '-') . ' Angkatan ' . (optional($mahasiswa)->angkatan ?? '-')
                : (optional(optional($mahasiswa)->user)->nama ?? 'Mahasiswa');

            $pesan = 'Booking baru dari ' . $identitas . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . '.';
            Notifikasi::firstOrCreate(
                ['user_id' => Auth::id(), 'pesan' => $pesan],
                ['status' => 'belum']
            );
        });

        // Backfill notifikasi untuk booking lama yang sudah disetujui.
        $bookings->where('status', 'disetujui')->each(function ($jadwal) {
            $this->createApprovalNotificationIfMissing($jadwal);
        });

        return view('admin.penjadwalan', compact('bookings'));
    }

    public function setujui($id)
    {
        $jadwal = JadwalKonseling::with('mahasiswa.user')->findOrFail($id);
        $jadwal->update(['status' => 'disetujui']);

        $this->createApprovalNotificationIfMissing($jadwal);

        return back()->with('success', 'Booking berhasil disetujui!');
    }

    public function tolak($id)
    {
        JadwalKonseling::findOrFail($id)->update(['status' => 'ditolak']);
        return back()->with('success', 'Booking berhasil ditolak.');
    }

    public function sesi()
    {
        $konselor = $this->getKonselor();
        $sesi     = SesiKonseling::whereHas('jadwal', function($q) use ($konselor) {
                        $q->where('konselor_id', optional($konselor)->id);
                    })->with('jadwal.mahasiswa.user')
                      ->orderBy('created_at', 'desc')
                      ->get();

        return view('konselor.sesi', compact('konselor', 'sesi'));
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

    public function laporan()
    {
        $konselor = Konselor::where('user_id', Auth::id())->first();
        $laporan  = JadwalKonseling::where('konselor_id', optional($konselor)->id)
                        ->where('status', 'disetujui')
                        ->with('mahasiswa.user')
                        ->orderBy('tanggal', 'desc')
                        ->get();

        return view('admin.laporan', compact('laporan'));
    }

    public function pengaturan()
    {
        return view('admin.pengaturan');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

}