<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Student;
use App\Models\JournalText;
use App\Models\DailyCheckin;
use App\Models\Mood;
use App\Models\Feeling;

class CounselorController extends Controller
{
    public function index()
    {
        // Ganti journals menjadi journalTexts sesuai relasi model Student terbaru
        $students = Student::with('journalTexts')
            ->orderBy('mental_level', 'desc')
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $student->journal_texts_count = $student->journalTexts->count();
                return $student;
            });

        $lastScan = $students->whereNotNull('mental_scanned_at')->max('mental_scanned_at');

        return view('admin.dashboard', [
            'students' => $students,
            'lastScan' => $lastScan,
        ]);
    }

    public function prioritas()
    {
        $students = Student::with('journalTexts')
            ->where('mental_level', 3)
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $student->journal_texts_count = $student->journalTexts->count();
                return $student;
            });

        return view('admin.prioritas', compact('students'));
    }

    public function semuaMahasiswa(Request $request)
    {
        $search = $request->query('search');

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

        return view('admin.semua_mahasiswa', compact('students', 'search'));
    }

    public function getChartData(Request $request)
    {
        $range = $request->query('range', '14d');
        // DailyCheckin menggantikan JournalEntry untuk kebutuhan grafik trend mood
        $query = DailyCheckin::query();

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
                // Mapping Mood ID ke skor 1-7 (Berdasarkan Data Master MongoDB)
                return match ((int)$entry->mood_id) {
                    7 => 1, // Marah
                    6 => 2, // Takut
                    5 => 3, // Sedih
                    4 => 4, // Terkejut
                    3 => 5, // Netral
                    2 => 6, // Antusias
                    1 => 7, // Senang
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

        // Hitung Distribusi & Trend Feeling (Sebaran Emosi)
        $feelingsCount = $rawEntries->groupBy('feeling_id')->map->count();
        $totalCheckins = $rawEntries->count();
        
        $distribution = [];
        $feelingsTrend = [];
        
        if ($totalCheckins > 0) {
            $topFeelingIds = $feelingsCount->sortDesc()->take(5)->keys();
            $feelings = Feeling::whereIn('_id', $topFeelingIds)->get()->keyBy('_id');

            foreach ($topFeelingIds as $fid) {
                $count = $feelingsCount[$fid];
                $feeling = $feelings[$fid] ?? null;
                if (!$feeling) continue;
                
                $fName = $feeling->feeling_name;
                $meta = $this->getFeelingMeta($fName);

                // For Distribution (Percentage Cards)
                $distribution[] = [
                    'name' => $fName,
                    'percentage' => round(($count / $totalCheckins) * 100),
                    'count' => $count,
                    'icon' => $meta['icon'],
                    'color' => $meta['color'],
                    'desc' => $meta['desc']
                ];
            }
        }

        // For Trend Chart (Time Series) - Single Line Average Feeling
        $feelingsTrendData = $grouped->map(function ($entries) {
            $scores = $entries->map(function ($entry) {
                if (!$entry->feeling) return 3; // Default
                $fName = $entry->feeling->feeling_name;
                
                return match (true) {
                    in_array($fName, ['Aktif', 'Enerjik', 'Antusias', 'Bersemangat']) => 5, // Semangat
                    in_array($fName, ['Santai', 'Kalem', 'Damai', 'Tenang']) => 4, // Tenang
                    in_array($fName, ['Bosan', 'Jemu', 'Letih', 'Malas']) => 2, // Lelah
                    in_array($fName, ['Takut', 'Marah', 'Cemas', 'Gugup']) => 1, // Cemas
                    default => 3, // Netral
                };
            });
            return round($scores->average(), 2);
        });

        $feelingsTrend = [];
        foreach ($feelingsTrendData as $key => $val) {
            $feelingsTrend[] = $val; // Array statik untuk single line chart
        }

        // Hitung Distribusi Mood (Senang, Antusias, Netral, dll)

        // Mood Distribution dinamis sesuai data di database
        $allCheckins = DailyCheckin::with('mood')->get();
        $totalAll = $allCheckins->count();
        $moodDist = [];
        if ($totalAll > 0) {
            $counts = $allCheckins->groupBy('mood.mood_name')->map->count();
            foreach ($counts as $moodName => $count) {
                $moodDist[$moodName] = round(($count / $totalAll) * 100);
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'distribution' => $distribution,
            'feelingsTrend' => $feelingsTrend,
            'mood_distribution' => $moodDist
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
            $allFeelings = Feeling::all()->keyBy('_id');
            $distribution = DailyCheckin::all()
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
            $matchingFeelings = Feeling::whereIn('feeling_name', $feelingNames)->get()->keyBy('_id');
            $feelingIds = $matchingFeelings->keys()->toArray();
            
            $distribution = DailyCheckin::whereIn('feeling_id', $feelingIds)
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
                
            $totalAll = DailyCheckin::count();
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

            $count = DailyCheckin::where('feeling_id', $feeling->_id)->count();
            $total = DailyCheckin::count();
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
        // Ambil mahasiswa dengan mental_level >= 2 (Waspada/Bahaya)
        // Diurutkan berdasarkan level tertinggi dan scan terbaru
        $urgentStudents = Student::where('mental_level', '>=', 2)
            ->orderBy('mental_level', 'desc')
            ->orderBy('mental_scanned_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'count' => $urgentStudents->count(),
            'notifications' => $urgentStudents
        ]);
    }

    public function getStudentPreview(Request $request)
    {
        $prodi = $request->query('prodi', 'Semua');

        $students = Student::with('journalTexts')
            ->whereNotNull('mental_level')
            ->orderBy('mental_level', 'desc')
            ->orderBy('mental_confidence', 'desc')
            ->when($prodi !== 'Semua', function ($q) use ($prodi) {
                $q->where('prodi', $prodi);
            })
            ->take(5)
            ->get()
            ->map(function ($student) {
                $student->journal_texts_count = $student->journalTexts->count();
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

        return view('admin.detail', compact('student'));
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

                    $student->update([
                        'mental_level'      => $data['level']      ?? null,
                        'mental_label'      => $data['label']      ?? null,
                        'mental_confidence' => $data['confidence'] ?? null,
                        'mental_red_flag'   => $data['red_flag']   ?? null,
                        'mental_scanned_at' => now(),
                    ]);

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

        return response()->json($response->json());
    }
}
