<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\JournalText;
use App\Models\DailyCheckin;
use App\Models\Mood;
use App\Models\Feeling;
use App\Models\JadwalKonseling;
use App\Models\Konselor;

class CounselorController extends Controller
{
    public function index()
    {
        // OPTIMASI PERFORMA MongoDB:
        if (!Schema::hasTable('students')) {
            \Log::warning('CounselorController@index: table "students" not found. Returning empty collection.');
            $students = collect();
            $lastScan = null;
        } else {
            $students = Student::with('journalTexts')
                ->orderBy('mental_level', 'desc')
                ->orderBy('name')
                ->get()
                ->map(function ($student) {
                    $student->journal_texts_count = $student->journalTexts->count();
                    return $student;
                });

            $lastScan = $students->whereNotNull('mental_scanned_at')->max('mental_scanned_at');
        }

        // Get current konselor
        $user = Auth::user();
        $konselor = Konselor::where('user_id', $user->id)->first();

        // Get jadwal statistics
        $baseQuery = JadwalKonseling::where('konselor_id', optional($konselor)->id);

        $totalPenjadwalan   = (clone $baseQuery)->count();
        $menunggu       = (clone $baseQuery)->where('status', 'menunggu')->count();
        $disetujui      = (clone $baseQuery)->where('status', 'disetujui')->count();
        $ditolak        = (clone $baseQuery)->where('status', 'ditolak')->count();
        $totalSesiSelesai = (clone $baseQuery)->whereNotNull('laporan')->orWhere('status', 'selesai')->count();
        $totalDiterima  = $disetujui;
        $totalDitolak   = $ditolak;
        $mahasiswaAktif = (clone $baseQuery)->distinct('mahasiswa_id')->count('mahasiswa_id');
        $approvalRate   = $totalPenjadwalan > 0 ? round(($disetujui / $totalPenjadwalan) * 100) : 0;

        // Monthly statistics (6 bulan terakhir)
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

        // Topic statistics - Ambil langsung dari field topik di JadwalKonseling
        // Ini adalah topik yang dipilih mahasiswa saat membuat penjadwalan
        $topikStats = (clone $baseQuery)
            ->whereNotNull('topik')
            ->pluck('topik')
            ->map(fn($topic) => trim((string)$topic))
            ->filter(fn($topic) => !empty($topic))
            ->countBy()
            ->sortDesc()
            ->take(8); // Ambil max 8 topik untuk doughnut chart

        $topikLabels = $topikStats->keys()->values();
        $topikCounts = $topikStats->values()->values();
        $totalTopik = $topikCounts->sum();

        // Today's Counseling Statistics
        $today = Carbon::today();
        $todayQuery = (clone $baseQuery)->whereDate('tanggal', $today);
        
        $todayScheduled = (clone $todayQuery)
            ->whereIn('status', ['disetujui', 'menunggu'])
            ->count();
        
        $todayInProgress = (clone $todayQuery)
            ->where('status', 'disetujui')
            ->whereTime('waktu', '<=', now()->format('H:i:s'))
            ->where('waktu', '>', now()->subHours(2)->format('H:i:s'))
            ->count();
        
        $todayCompleted = (clone $todayQuery)
            ->whereNotNull('laporan')
            ->orWhere('status', 'selesai')
            ->count();
        
        $todayWaiting = (clone $todayQuery)
            ->where('status', 'menunggu')
            ->count();

        // Daftar jadwal untuk hari ini (untuk ditampilkan di dashboard)
        $todayJadwals = (clone $todayQuery)
            ->with('mahasiswa')
            ->orderBy('waktu')
            ->get();
        return view('admin.dashboard', compact(
            'students',
            'lastScan',
            'konselor',
            'totalPenjadwalan',
            'menunggu',
            'disetujui',
            'ditolak',
            'totalSesiSelesai',
            'totalDiterima',
            'totalDitolak',
            'mahasiswaAktif',
            'approvalRate',
            'monthlyLabels',
            'monthlyCounts',
            'topikStats',
            'topikLabels',
            'topikCounts',
            'totalTopik',
            'todayScheduled',
            'todayInProgress',
            'todayCompleted',
            'todayWaiting',
            'todayJadwals',
        ));
    }

    public function prioritas()
    {
        if (!Schema::hasTable('students')) {
            \Log::warning('CounselorController@prioritas: table "students" not found. Returning empty collection.');
            $students = collect();
        } else {
            $students = Student::with('journalTexts')
                ->where('mental_level', 3)
                ->orderBy('name')
                ->get()
                ->map(function ($student) {
                    $student->journal_texts_count = $student->journalTexts->count();
                    return $student;
                });
        }

        return view('admin.prioritas', compact('students'));
    }

    public function semuaMahasiswa(Request $request)
    {
        $search = $request->query('search');

        if (!Schema::hasTable('students')) {
            \Log::warning('CounselorController@semuaMahasiswa: table "students" not found. Returning empty collection.');
            $students = collect();
        } else {
            $students = Student::with('journalTexts')
                ->whereNotNull('mental_level')
                ->when($search, function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('nim', 'like', "%{$search}%");
                })
                ->orderBy('mental_level', 'desc')
                ->orderBy('mental_confidence', 'desc')
                ->get()
                ->map(function ($student) {
                    $student->journal_texts_count = $student->journalTexts->count();
                    return $student;
                });
        }

        return view('admin.semua_mahasiswa', compact('students', 'search'));
    }

    public function getChartData(Request $request)
    {
        $activeNims = Student::pluck('nim')->toArray();
        $range = $request->query('range', '14d');
        $query = DailyCheckin::with(['mood', 'feeling'])->whereIn('nim', $activeNims); // Filter by active users

        if ($range === '14d') {
            $query->where('created_at', '>=', now()->subDays(14));
        } elseif ($range === '1m') {
            $query->where('created_at', '>=', now()->subMonths(1));
        } elseif ($range === '4m') {
            $query->where('created_at', '>=', now()->subMonths(4));
        } elseif ($range === '1y') {
            $query->where('created_at', '>=', now()->subYears(1));
        }

        $rawEntries = $query->orderBy('created_at', 'asc')->get();

        if ($range === '1y' || $range === '4m') {
            $grouped = $rawEntries->groupBy(function ($d) {
                return $d->created_at->format('Y-m');
            });
        } else {
            $grouped = $rawEntries->groupBy(function ($d) {
                return $d->created_at->format('Y-m-d');
            });
        }

        $moodTrend = $grouped->map(function ($entries) {
            $scores = $entries->map(function ($entry) {
                return match ((int)$entry->mood_id) {
                    7 => 1, 6 => 2, 5 => 3, 4 => 4, 3 => 5, 2 => 6, 1 => 7,
                    default => 5,
                };
            });
            return round($scores->average(), 2);
        });

        $labels = [];
        $data = [];
        foreach ($moodTrend as $key => $val) {
            if ($range === '1y' || $range === '4m') {
                $labels[] = \Carbon\Carbon::createFromFormat('Y-m', $key)->isoFormat('MMMM YYYY');
            } else {
                $labels[] = \Carbon\Carbon::parse($key)->isoFormat('D MMM');
            }
            $data[] = $val;
        }

        // Hitung Distribusi & Trend Feeling — reuse $rawEntries, relasi sudah di-eager-load
        $feelingsCount = $rawEntries->groupBy('feeling_id')->map->count();
        $totalCheckins = $rawEntries->count();

        $distribution = [];
        if ($totalCheckins > 0) {
            $topFeelingIds = $feelingsCount->sortDesc()->take(5)->keys();
            $feelings = Feeling::whereIn('feeling_id', $topFeelingIds)->get()->keyBy('feeling_id');

            foreach ($topFeelingIds as $fid) {
                $count = $feelingsCount[$fid];
                $feeling = $feelings[$fid] ?? null;
                if (!$feeling) continue;

                $fName = $feeling->feeling_name;
                $meta = $this->getFeelingMeta($fName);
                $distribution[] = [
                    'name'       => $fName,
                    'percentage' => round(($count / $totalCheckins) * 100),
                    'count'      => $count,
                    'icon'       => $meta['icon'],
                    'color'      => $meta['color'],
                    'desc'       => $meta['desc']
                ];
            }
        }

        // Feelings Trend — gunakan relasi yang sudah di-load (tidak ada query baru)
        $feelingsTrendData = $grouped->map(function ($entries) {
            $scores = $entries->map(function ($entry) {
                $fName = $entry->feeling->feeling_name ?? null;
                if (!$fName) return 3;
                return match (true) {
                    in_array($fName, ['Aktif', 'Enerjik', 'Antusias', 'Bersemangat']) => 5,
                    in_array($fName, ['Santai', 'Kalem', 'Damai', 'Tenang'])           => 4,
                    in_array($fName, ['Bosan', 'Jemu', 'Letih', 'Malas'])              => 2,
                    in_array($fName, ['Takut', 'Marah', 'Cemas', 'Gugup'])             => 1,
                    default => 3,
                };
            });
            return round($scores->average(), 2);
        });

        $feelingsTrend = array_values($feelingsTrendData->toArray());

        // Mood Distribution — reuse $rawEntries, tidak perlu fetch ulang dari database
        $moodDist = [];
        $totalAll = $rawEntries->count();
        if ($totalAll > 0) {
            $counts = $rawEntries->groupBy(function ($e) {
                return $e->mood->mood_name ?? 'Tidak Diketahui';
            })->map->count();
            foreach ($counts as $moodName => $count) {
                $moodDist[$moodName] = round(($count / $totalAll) * 100);
            }
        }

        // Get JadwalKonseling statistics
        $konselor_id = auth()->guard('konselor')->id();
        $jadwalQuery = \App\Models\JadwalKonseling::where('konselor_id', $konselor_id);
        
        // Apply date range filter
        if ($range === '14d') {
            $jadwalQuery->where('created_at', '>=', now()->subDays(14));
        } elseif ($range === '1m') {
            $jadwalQuery->where('created_at', '>=', now()->subMonths(1));
        } elseif ($range === '4m') {
            $jadwalQuery->where('created_at', '>=', now()->subMonths(4));
        } elseif ($range === '1y') {
            $jadwalQuery->where('created_at', '>=', now()->subYears(1));
        }
        
        $totalPenjadwalan = $jadwalQuery->count();
        $selesaiCount = \App\Models\JadwalKonseling::where('konselor_id', $konselor_id)
            ->whereNotNull('laporan')
            ->orWhere('status', 'selesai')
            ->count();
        $diterimaPenjadwalan = \App\Models\JadwalKonseling::where('konselor_id', $konselor_id)
            ->where('status', 'diterima')
            ->count();
        $ditolakPenjadwalan = \App\Models\JadwalKonseling::where('konselor_id', $konselor_id)
            ->where('status', 'ditolak')
            ->count();

        // Get problem distribution from JadwalKonseling (ringkasan_masalah field)
        $problemDist = [];
        $problemCategories = [
            'Akademik' => ['nilai', 'ujian', 'mata kuliah', 'beasiswa', 'akademik', 'tugas'],
            'Finansial' => ['uang', 'biaya', 'tuition', 'finansial', 'utang', 'duit'],
            'Relasi' => ['hubungan', 'pacar', 'teman', 'keluarga', 'orang tua', 'relasi'],
            'Keluarga' => ['keluarga', 'orang tua', 'saudara', 'ayah', 'ibu', 'keluarga'],
            'Pribadi' => ['diri sendiri', 'kepribadian', 'identitas', 'harga diri', 'percaya diri'],
            'Karir' => ['karir', 'pekerjaan', 'magang', 'lowongan', 'profesi'],
            'Kesehatan' => ['kesehatan', 'sakit', 'medis', 'fisik', 'kesehatan']
        ];
        
        $allJadwal = \App\Models\JadwalKonseling::where('konselor_id', $konselor_id)
            ->whereNotNull('ringkasan_masalah')
            ->get();
        
        foreach ($problemCategories as $category => $keywords) {
            $count = 0;
            foreach ($allJadwal as $jadwal) {
                $summary = strtolower($jadwal->ringkasan_masalah ?? '');
                foreach ($keywords as $keyword) {
                    if (strpos($summary, strtolower($keyword)) !== false) {
                        $count++;
                        break;
                    }
                }
            }
            if ($count > 0) {
                $problemDist[$category] = $count;
            }
        }

        // Calculate trend data (konsultasi per period)
        $trendData = [];
        $jadwalGrouped = \App\Models\JadwalKonseling::where('konselor_id', $konselor_id);
        
        if ($range === '1y' || $range === '4m') {
            $jadwalGrouped = $jadwalGrouped->where('created_at', '>=', now()->subtract($range === '4m' ? 4 : 12, 'months'))->get()
                ->groupBy(function ($d) {
                    return $d->created_at->format('Y-m');
                });
        } else {
            $daysBack = $range === '14d' ? 14 : 30;
            $jadwalGrouped = $jadwalGrouped->where('created_at', '>=', now()->subDays($daysBack))->get()
                ->groupBy(function ($d) {
                    return $d->created_at->format('Y-m-d');
                });
        }
        
        foreach ($jadwalGrouped as $period => $jadwals) {
            $trendData[] = $jadwals->count();
        }

        return response()->json([
            'labels'           => $labels,
            'data'             => $data,
            'trend_data'       => $trendData,
            'distribution'     => $distribution,
            'feelingsTrend'    => $feelingsTrend,
            'mood_distribution' => $moodDist,
            'problem_distribution' => $problemDist,
            'total_penjadwalan' => $totalPenjadwalan,
            'selesai_count'    => $selesaiCount,
            'diterima_count'   => $diterimaPenjadwalan,
            'ditolak_count'    => $ditolakPenjadwalan
        ]);
    }




    public function getJadwalData(Request $request)
    {
        $konselor = auth()->user()->konselor;
        $konselor_id = optional($konselor)->id;
        $range = $request->query('range', '14d');

        // Get stat counters
        $totalPenjadwalan = JadwalKonseling::where('konselor_id', $konselor_id)->count();
        $selesaiCount = JadwalKonseling::where('konselor_id', $konselor_id)
            ->where(function($q) {
                $q->where('status', 'berlangsung')
                  ->orWhere('status', 'selesai');
            })
            ->count();
        $diterimaPenjadwalan = JadwalKonseling::where('konselor_id', $konselor_id)
            ->where('status', 'disetujui')
            ->count();
        $ditolakPenjadwalan = JadwalKonseling::where('konselor_id', $konselor_id)
            ->where('status', 'ditolak')
            ->count();

        // Get trend data (jumlah konseling per period)
        $daysBack = 14;
        if ($range === '1m') {
            $daysBack = 30;
        } elseif ($range === '4m') {
            $daysBack = 120;
        } elseif ($range === '1y') {
            $daysBack = 365;
        }

        $jadwalRaw = JadwalKonseling::where('konselor_id', $konselor_id)
            ->where('created_at', '>=', now()->subDays($daysBack))
            ->orderBy('created_at', 'asc')
            ->get();

        $labels = [];
        $data = [];
        
        if ($range === '1y' || $range === '4m') {
            $grouped = $jadwalRaw->groupBy(function ($d) {
                return $d->created_at->format('Y-m');
            });
            foreach ($grouped as $month => $items) {
                $labels[] = \Carbon\Carbon::createFromFormat('Y-m', $month)->isoFormat('MMM YYYY');
                $data[] = $items->count();
            }
        } else {
            $grouped = $jadwalRaw->groupBy(function ($d) {
                return $d->created_at->format('Y-m-d');
            });
            foreach ($grouped as $day => $items) {
                $labels[] = \Carbon\Carbon::parse($day)->isoFormat('D MMM');
                $data[] = $items->count();
            }
        }

        // Get problem distribution
        $problemDist = [];
        $problemCategories = [
            'Akademik' => ['nilai', 'ujian', 'mata kuliah', 'beasiswa', 'akademik', 'tugas'],
            'Finansial' => ['uang', 'biaya', 'tuition', 'finansial', 'utang', 'duit'],
            'Relasi' => ['hubungan', 'pacar', 'teman', 'keluarga', 'orang tua', 'relasi'],
            'Keluarga' => ['keluarga', 'orang tua', 'saudara', 'ayah', 'ibu'],
            'Pribadi' => ['diri sendiri', 'kepribadian', 'identitas', 'harga diri', 'percaya diri'],
            'Karir' => ['karir', 'pekerjaan', 'magang', 'lowongan', 'profesi'],
            'Kesehatan' => ['kesehatan', 'sakit', 'medis', 'fisik']
        ];
        
        $allJadwal = JadwalKonseling::where('konselor_id', $konselor_id)
            ->whereNotNull('ringkasan_masalah')
            ->get();
        
        foreach ($problemCategories as $category => $keywords) {
            $count = 0;
            foreach ($allJadwal as $jadwal) {
                $summary = strtolower($jadwal->ringkasan_masalah ?? '');
                foreach ($keywords as $keyword) {
                    if (strpos($summary, strtolower($keyword)) !== false) {
                        $count++;
                        break;
                    }
                }
            }
            if ($count > 0) {
                $problemDist[$category] = $count;
            }
        }

        return response()->json([
            'total_count' => $totalPenjadwalan,
            'status_counts' => [
                'selesai' => $selesaiCount,
                'diterima' => $diterimaPenjadwalan,
                'ditolak' => $ditolakPenjadwalan,
                'menunggu' => $totalPenjadwalan - $selesaiCount - $diterimaPenjadwalan - $ditolakPenjadwalan
            ],
            'trend_data' => [
                'labels' => $labels,
                'data' => $data
            ],
            'problem_distribution' => $problemDist
        ]);
    }

    public function getFeelingDistribution(Request $request)
    {
        $name = $request->query('name', 'all');
        
        $categories = [
            'CAT:Positif'   => ['Gembira', 'Bangga', 'Bersyukur', 'Ceria', 'Semangat', 'Energik', 'Kagum', 'Bergairah'],
            'CAT:Netral'    => ['Biasa Saja', 'Stabil', 'Tenang', 'Santai'],
            'CAT:Penasaran' => ['Tercengang', 'Penasaran', 'Tertarik', 'Gelagapan'],
            'CAT:Sedih'     => ['Pilu', 'Depresi', 'Kesepian', 'Putus Asa'],
            'CAT:Cemas'     => ['Cemas', 'Khawatir', 'Panik', 'Gelisah'],
            'CAT:Kesal'     => ['Kesal', 'Jengkel', 'Benci', 'Kecewa'],
        ];

        if ($name === 'all') {
            $allFeelings = Feeling::all()->keyBy('feeling_id');
            $activeNims = Student::pluck('nim')->toArray();
            $distribution = DailyCheckin::whereIn('nim', $activeNims)->get()
                ->groupBy('feeling_id')
                ->map(function ($checkins, $feelingId) use ($allFeelings) {
                    $feeling = $allFeelings->get($feelingId);
                    return [
                        'name'       => $feeling ? $feeling->feeling_name : 'Tidak Ada',
                        'count'      => $checkins->count(),
                        'percentage' => 0
                    ];
                })
                ->values();
            
            $total = $distribution->sum('count');
            if ($total > 0) {
                $distribution = $distribution->map(function ($item) use ($total) {
                    $item['percentage'] = round(($item['count'] / $total) * 100);
                    return $item;
                })->sortByDesc('count')->take(10)->values();
            }

            return response()->json(['items' => $distribution]);
        } elseif (str_starts_with($name, 'CAT:')) {
            $feelingNames = $categories[$name] ?? [];
            $matchingFeelings = Feeling::whereIn('feeling_name', $feelingNames)->get()->keyBy('feeling_id');
            $feelingIds = $matchingFeelings->keys()->toArray();
            $activeNims = Student::pluck('nim')->toArray();
            
            $distribution = DailyCheckin::whereIn('feeling_id', $feelingIds)
                ->whereIn('nim', $activeNims)
                ->get()
                ->groupBy('feeling_id')
                ->map(function ($checkins, $feelingId) use ($matchingFeelings) {
                    $feeling = $matchingFeelings->get($feelingId);
                    return [
                        'name'       => $feeling ? $feeling->feeling_name : 'Tidak Ada',
                        'count'      => $checkins->count(),
                        'percentage' => 0
                    ];
                })
                ->values();
                
            $totalAll = DailyCheckin::whereIn('nim', $activeNims)->count();
            if ($totalAll > 0) {
                $distribution = $distribution->map(function ($item) use ($totalAll) {
                    $item['percentage'] = round(($item['count'] / $totalAll) * 100);
                    return $item;
                })->sortByDesc('count')->values();
            }
            
            return response()->json(['items' => $distribution]);
        } else {
            $feeling = Feeling::where('feeling_name', $name)->first();
            if (!$feeling) {
                return response()->json(['items' => []]);
            }

            $activeNims = Student::pluck('nim')->toArray();
            $count = DailyCheckin::where('feeling_id', $feeling->_id)->whereIn('nim', $activeNims)->count();
            $total = DailyCheckin::whereIn('nim', $activeNims)->count();
            $percentage = $total > 0 ? round(($count / $total) * 100) : 0;

            return response()->json([
                'items' => [[
                    'name'       => $feeling->feeling_name,
                    'count'      => $count,
                    'percentage' => $percentage
                ]]
            ]);
        }
    }

    /**
     * Helper untuk icon & warna feeling di dashboard
     */
    private function getFeelingMeta($name) {
        $map = [
            'Bahagia' => ['icon' => '😊', 'color' => 'rgba(52,211,153,0.15)', 'desc' => 'Kondisi emosional yang sangat positif.'],
            'Senang' => ['icon' => '😁', 'color' => 'rgba(52,211,153,0.15)', 'desc' => 'Menunjukkan kepuasan dan keceriaan.'],
            'Biasa' => ['icon' => '😐', 'color' => 'rgba(255,255,255,0.08)', 'desc' => 'Kondisi stabil tanpa gejolak emosi.'],
            'Cemas' => ['icon' => '😰', 'color' => 'rgba(251,191,36,0.15)', 'desc' => 'Perasaan khawatir yang butuh perhatian.'],
            'Sedih' => ['icon' => '😢', 'color' => 'rgba(96,165,250,0.15)', 'desc' => 'Kondisi murung atau kehilangan semangat.'],
            'Marah' => ['icon' => '😡', 'color' => 'rgba(248,113,113,0.15)', 'desc' => 'Indikasi frustrasi atau tekanan tinggi.'],
            'Bosan' => ['icon' => '🥱', 'color' => 'rgba(255,255,255,0.08)', 'desc' => 'Kurangnya stimulasi atau minat.'],
            'Letih' => ['icon' => '😫', 'color' => 'rgba(124,111,247,0.15)', 'desc' => 'Kelelahan fisik atau mental yang menumpuk.'],
            'Cemas' => ['icon' => '😰', 'color' => 'rgba(251,191,36,0.15)', 'desc' => 'Sering muncul saat tekanan tugas tinggi.'],
        ];

        return $map[$name] ?? ['icon' => '✨', 'color' => 'rgba(255,255,255,0.08)', 'desc' => 'Emosi yang terekam dalam jurnal.'];
    }

    public function getUrgentNotifications()
    {
        // Ambil mahasiswa dengan mental_level = 3 (Krisis / Bahaya) yang belum dibaca notifnya
        $urgentStudents = Student::where('mental_level', 3)
            ->where('mental_notif_read', '!=', true)
            ->orderBy('mental_level', 'desc')
            ->orderBy('mental_scanned_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'count' => $urgentStudents->count(),
            'notifications' => $urgentStudents
        ]);
    }

    public function markUrgentRead(string $nim)
    {
        Student::where('nim', $nim)->update(['mental_notif_read' => true]);
        return response()->json(['success' => true]);
    }

    public function getStudentPreview(Request $request)
    {
        $prodi = $request->query('prodi', 'Semua');

        // Pemetaan Fakultas → daftar Prodi yang termasuk di dalamnya
        // Mendukung dua format key: "FAK:X" (dari dropdown fakultas) dan "Semua X" (dari opsi pertama dropdown prodi)
        $fakultasMap = [
            'FAK:Vokasi'                  => ['Teknologi Rekayasa Perangkat Lunak', 'Teknologi Informasi', 'Teknologi Komputer'],
            'Semua Vokasi'                => ['Teknologi Rekayasa Perangkat Lunak', 'Teknologi Informasi', 'Teknologi Komputer'],
            'FAK:Informatika & Elektro'   => ['Informatika', 'Teknik Elektro'],
            'Semua Informatika & Elektro' => ['Informatika', 'Teknik Elektro'],
            'FAK:Bioteknologi'            => ['Bioproses', 'Bioteknologi'],
            'Semua Bioteknologi'          => ['Bioproses', 'Bioteknologi'],
            'FAK:Teknik Industri'         => ['Managemen Rekayasa', 'Metalurgi'],
            'Semua Teknik Industri'       => ['Managemen Rekayasa', 'Metalurgi'],
        ];

        // OPTIMASI PERFORMA MongoDB: Hanya memanggil kolom _id dari relasi untuk menghemat RAM
        $students = Student::with(['journalTexts' => function ($q) {
                $q->select('_id', 'nim');
            }])
            ->whereNotNull('mental_level')
            ->orderBy('mental_level', 'desc')
            ->orderBy('mental_confidence', 'desc')
            ->when($prodi !== 'Semua', function ($q) use ($prodi, $fakultasMap) {
                if (array_key_exists($prodi, $fakultasMap)) {
                    // Filter per-Fakultas: cocokkan semua prodi yang termasuk
                    $prodiList = $fakultasMap[$prodi];
                    $q->where(function ($sub) use ($prodiList) {
                        foreach ($prodiList as $p) {
                            $sub->orWhere('prodi', 'like', "%{$p}%");
                        }
                    });
                } else {
                    // Filter per-Prodi: fuzzy match agar toleran terhadap variasi penulisan di DB
                    $q->where('prodi', 'like', "%{$prodi}%");
                }
            })
            ->take(5)
            ->get()
            ->map(function ($student) {
                $student->journal_texts_count = $student->journalTexts ? $student->journalTexts->count() : 0;
                unset($student->journalTexts);
                return $student;
            });

        return response()->json([
            'students' => $students
        ]);
    }

    public function showDetail(string $nim)
    {
        $student = Student::with(['journalTexts' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'dailyCheckins' => function ($query) {
            $query->with(['mood', 'feeling'])->orderBy('created_at', 'desc');
        }])->where('nim', $nim)->firstOrFail();

        // Merge journals and checkins by date to show unified history
        $logs = collect();
        
        foreach ($student->journalTexts as $journal) {
            $date = $journal->created_at->format('Y-m-d');
            if (!$logs->has($date)) {
                $logs->put($date, [
                    'created_at' => $journal->created_at,
                    'journal' => $journal,
                    'checkin' => null
                ]);
            } else {
                $item = $logs->get($date);
                if (!$item['journal']) {
                    $item['journal'] = $journal;
                    $logs->put($date, $item);
                }
            }
        }

        foreach ($student->dailyCheckins as $checkin) {
            $date = $checkin->created_at->format('Y-m-d');
            if (!$logs->has($date)) {
                $logs->put($date, [
                    'created_at' => $checkin->created_at,
                    'journal' => null,
                    'checkin' => $checkin
                ]);
            } else {
                $item = $logs->get($date);
                if (!$item['checkin']) {
                    $item['checkin'] = $checkin;
                    $logs->put($date, $item);
                }
            }
        }

        $sortedLogs = $logs->sortByDesc('created_at');

        return view('admin.detail', compact('student', 'sortedLogs'));
    }

    public function updateStatus(Request $request, string $nim)
    {
        $request->validate([
            'mental_level' => 'required|integer|in:0,1,2,3'
        ]);

        $levelMap = [
            0 => 'Level 0 (Positif / Baik)',
            1 => 'Level 1 (Ekspresi Emosi Ringan)',
            2 => 'Level 2 (Perlu Pemantauan)',
            3 => 'Level 3 (Krisis / Butuh Penanganan Cepat)',
        ];

        $student = Student::where('nim', $nim)->firstOrFail();
        
        $level = $request->mental_level;
        $student->update([
            'mental_level'      => $level,
            'mental_label'      => $levelMap[$level],
            'mental_confidence' => 100, // Manual override
            'mental_red_flag'   => $level == 3 ? '[KOREKSI KONSELOR] Diperbarui secara manual' : null,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Status klasifikasi mahasiswa berhasil diperbarui.',
        ]);
    }

    public function scanLevel3()
    {
        $students = Student::with(['journalTexts' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }])->get();

        $saved   = 0;
        $skipped = 0;

        foreach ($students as $student) {
            // Ambil histori mood & feeling 14 hari terakhir
            $historyInput = DailyCheckin::with(['mood', 'feeling'])
                ->where('nim', $student->nim)
                ->orderBy('created_at', 'desc')
                ->take(14)
                ->get()
                ->map(function($checkin) {
                    return [
                        'mood'    => $checkin->mood->mood_name    ?? 'Biasa',
                        'feeling' => $checkin->feeling->feeling_name ?? 'Kalem'
                    ];
                })
                ->toArray();

            $lastJournal = $student->journalTexts->first();
            $daysSinceLastJournal = $lastJournal ? now()->diffInDays($lastJournal->created_at) : 99;
            $allText = $student->journalTexts->pluck('description')->implode(' ');

            try {
                $response = Http::timeout(30)->post('http://127.0.0.1:8001/api/classify', [
                    'nim'                     => $student->nim,
                    'text'                    => $allText,
                    'mood_history'            => $historyInput,
                    'days_since_last_journal' => (int)$daysSinceLastJournal,
                ]);

                if ($response->successful()) {
                    $data = $response->json('data');
                    $oldLevel = $student->mental_level;

                    $updateData = [
                        'mental_level'      => $data['level']      ?? null,
                        'mental_label'      => $data['label']      ?? null,
                        'mental_confidence' => $data['confidence'] ?? null,
                        'mental_red_flag'   => $data['red_flag']   ?? null,
                        'mental_scanned_at' => now(),
                    ];

                    if (($data['level'] ?? 0) == 3 && $oldLevel != 3) {
                        $updateData['mental_notif_read'] = false;
                    }

                    $student->update($updateData);

                    // Kirim notifikasi HANYA JIKA sebelumnya bukan Level 3
                    // untuk mencegah spam notifikasi yang sama setiap kali dipindai
                    if (($data['level'] ?? 0) == 3 && $oldLevel != 3) {
                        $counselors = \App\Models\User::all();
                        \Illuminate\Support\Facades\Notification::send($counselors, new \App\Notifications\HighRiskStudentDetected($student));
                    }

                    $saved++;
                }
            } catch (\Exception $e) {
                $skipped++;
                continue;
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => "Scan selesai: {$saved} mahasiswa diperbarui, {$skipped} dilewati.",
            'saved'   => $saved,
            'skipped' => $skipped,
        ]);
    }

    public static function classifyAndSave(string $nim): void
    {
        try {
            $student = Student::with(['journalTexts' => function ($q) {
                $q->orderBy('created_at', 'desc');
            }])->where('nim', $nim)->first();

            if (!$student) return;

            // Histori mood & feeling 14 hari terakhir
            $historyInput = DailyCheckin::with(['mood', 'feeling'])
                ->where('nim', $nim)
                ->orderBy('created_at', 'desc')
                ->take(14)
                ->get()
                ->map(function($checkin) {
                    return [
                        'mood'    => $checkin->mood?->mood_name    ?? 'Biasa',
                        'feeling' => $checkin->feeling?->feeling_name ?? 'Biasa'
                    ];
                })
                ->toArray();

            $lastJournal = $student->journalTexts->first();
            $daysSinceLastJournal = $lastJournal ? now()->diffInDays($lastJournal->created_at) : 99;
            $allText = $student->journalTexts->pluck('description')->implode(' ');

            $response = Http::timeout(20)->post('http://127.0.0.1:8001/api/classify', [
                'nim'                     => $nim,
                'text'                    => $allText,
                'mood_history'            => $historyInput,
                'days_since_last_journal' => (int)$daysSinceLastJournal,
            ]);

            if ($response->successful()) {
                $data = $response->json('data');

                $student->update([
                    'mental_level'      => $data['level']      ?? null,
                    'mental_label'      => $data['label']      ?? null,
                    'mental_confidence' => $data['confidence'] ?? null,
                    'mental_red_flag'   => $data['red_flag']   ?? null,
                    'mental_scanned_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
        }
    }

    public function getSummary(Request $request)
    {
        $nim = $request->nim;

        $journals = JournalText::where('nim', $nim)
            ->orderBy('created_at', 'asc')
            ->pluck('description')
            ->toArray();

        if (empty($journals)) {
            return response()->json([
                'status'  => 'success',
                'nim'     => $nim,
                'summary' => 'Mahasiswa ini belum memiliki jurnal yang dapat diringkas.',
            ]);
        }

        $response = Http::timeout(120)->post('http://127.0.0.1:8001/api/summarize', [
            'nim'           => $nim,
            'journal_texts' => $journals,
        ]);

        if ($response->failed()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghubungi server AI. Pastikan Python berjalan di port 8001.',
            ], 502);
        }

        $data = $response->json();
        $summary = $data['summary'] ?? null;

        if ($summary) {
            // Simpan insight ke database agar persisten
            Student::where('nim', $nim)->update([
                'mental_insight' => $summary,
                'mental_scanned_at' => now(), // Update waktu scan terakhir
            ]);
        }

        return response()->json($data);
    }
}
