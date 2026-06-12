<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\JournalText;
use App\Models\DailyCheckin;
use App\Models\Feeling;
use App\Models\User;
use App\Models\JadwalKonseling;
use App\Models\Konselor;
use App\Models\Feedback;

class DashboardController extends Controller
{
    public function index()
    {
        // Data utama dashboard konselor (MongoDB)
        $students = Student::with('journalTexts')
            ->whereNotNull('mental_level')
            ->orderBy('mental_level', 'desc')
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $student->journal_texts_count = $student->journalTexts->count();
                return $student;
            });

        $user = Auth::user();
        $konselor = $user ? Konselor::where('user_id', $user->id)->first() : null;

        $baseQuery = $konselor
            ? JadwalKonseling::where('konselor_id', $konselor->id)
            : JadwalKonseling::query();

        $todayJadwals = (clone $baseQuery)
            ->whereDate('tanggal', Carbon::today())
            ->with('mahasiswa.user')
            ->orderBy('waktu')
            ->get();

        $lastScan = $students->whereNotNull('mental_scanned_at')->max('mental_scanned_at');

        // Daftar angkatan unik dari MongoDB untuk filter dropdown
        $angkatanList = Student::pluck('angkatan')->filter()->unique()->sort()->values();$angkatanList = Student::pluck('angkatan')->filter()->unique()->sort()->values();

        $feedbacks = Feedback::with(['mahasiswa.user'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', [
            'students'     => $students,
            'lastScan'     => $lastScan,
            'todayJadwals' => $todayJadwals,
            'angkatanList' => $angkatanList,
            'feedbacks'    => $feedbacks,
        ]);
    }

    public function getChartData(Request $request)
    {
        $range = $request->query('range', '14d');
        // ✅ Eager load 'feeling' dan 'mood' sekaligus untuk menghindari N+1 query
        $query = DailyCheckin::with(['feeling', 'mood']);
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
                    7 => 1, 6 => 2, 5 => 3, 4 => 4, 3 => 5, 2 => 6, 1 => 7, default => 5,
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
        $feelingsCount = $rawEntries->groupBy('feeling_id')->map->count();
        $totalCheckins = $rawEntries->count();
        $distribution = [];
        if ($totalCheckins > 0) {
            $topFeelingIds = $feelingsCount->sortDesc()->take(5)->keys();
            // ✅ Feelings sudah ter-eager-load, ambil dari koleksi yang ada (tanpa query baru)
            $feelings = $rawEntries->pluck('feeling')->filter()->keyBy('_id');
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
        $feelingsTrendData = $grouped->map(function ($entries) {
            $scores = $entries->map(function ($entry) {
                if (!$entry->feeling) return 3;
                $fName = $entry->feeling->feeling_name;
                return match (true) {
                    in_array($fName, ['Aktif', 'Enerjik', 'Antusias', 'Bersemangat']) => 5,
                    in_array($fName, ['Santai', 'Kalem', 'Damai', 'Tenang']) => 4,
                    in_array($fName, ['Bosan', 'Jemu', 'Letih', 'Malas']) => 2,
                    in_array($fName, ['Takut', 'Marah', 'Cemas', 'Gugup']) => 1,
                    default => 3,
                };
            });
            return round($scores->average(), 2);
        });
        $feelingsTrend = array_values($feelingsTrendData->toArray());

        // ✅ Gunakan $rawEntries yang sudah ada, bukan query baru seluruh tabel
        $moodDist = [];
        if ($totalCheckins > 0) {
            $counts = $rawEntries->groupBy('mood.mood_name')->map->count();
            foreach ($counts as $moodName => $count) {
                $moodDist[$moodName] = round(($count / $totalCheckins) * 100);
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

    public function showDetail(string $nim)
    {
        $student = Student::with(['journalTexts' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }, 'dailyCheckins' => function ($query) {
            $query->with(['mood', 'feeling'])->orderBy('created_at', 'desc');
        }])->where('nim', $nim)->firstOrFail();

        $logs = collect();
        
        foreach ($student->journalTexts as $journal) {
            $date = $journal->created_at->format('Y-m-d');
            if (!$logs->has($date)) {
                $logs->put($date, [
                    'created_at' => $journal->created_at,
                    'journals'   => collect([$journal]),
                    'checkin'    => null
                ]);
            } else {
                $item = $logs->get($date);
                if (!isset($item['journals'])) {
                    $item['journals'] = collect();
                }
                $item['journals']->push($journal);
                $logs->put($date, $item);
            }
        }

        foreach ($student->dailyCheckins as $checkin) {
            $date = $checkin->created_at->format('Y-m-d');
            if (!$logs->has($date)) {
                $logs->put($date, [
                    'created_at' => $checkin->created_at,
                    'journals'   => collect(),
                    'checkin'    => $checkin
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
            $distribution = DailyCheckin::whereIn('nim', $activeNims)
                ->select('feeling_id')
                ->get()
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
                ->select('feeling_id')
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

    public function getStudentPreview(Request $request)
    {
        $prodi    = $request->query('prodi', 'Semua');
        $angkatan = $request->query('angkatan', 'Semua');

        // Pemetaan Fakultas → daftar Prodi yang termasuk di dalamnya
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
            ->when($angkatan !== 'Semua', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            })
            ->when($prodi !== 'Semua', function ($q) use ($prodi, $fakultasMap) {
                if (array_key_exists($prodi, $fakultasMap)) {
                    $prodiList = $fakultasMap[$prodi];
                    $q->where(function ($sub) use ($prodiList) {
                        foreach ($prodiList as $p) {
                            $sub->orWhere('prodi', 'like', "%{$p}%");
                        }
                    });
                } else {
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
            'mental_updated_manual_at' => now(),
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

        $saved    = 0;
        $skipped  = 0;

        foreach ($students as $student) {
            if ($student->mental_scanned_at) {
                $lastScanned = \Carbon\Carbon::parse($student->mental_scanned_at);

                $lastJournalDate  = $student->journalTexts->first()?->created_at;
                $lastCheckinDate  = DailyCheckin::where('nim', $student->nim)
                    ->orderBy('created_at', 'desc')
                    ->value('created_at');

                $hasNewJournal  = $lastJournalDate && \Carbon\Carbon::parse($lastJournalDate)->gt($lastScanned);
                $hasNewCheckin  = $lastCheckinDate && \Carbon\Carbon::parse($lastCheckinDate)->gt($lastScanned);

                // Jika tidak ada aktivitas baru sejak scan terakhir, lewati mahasiswa ini
                if (!$hasNewJournal && !$hasNewCheckin) {
                    $skipped++;
                    continue;
                }
            }
            // ─────────────────────────────────────────────────────────────────

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

            $journals = $student->journalTexts;

            if ($student->mental_updated_manual_at) {
                $manualUpdateDate = \Carbon\Carbon::parse($student->mental_updated_manual_at);
                $journals = $journals->filter(function($j) use ($manualUpdateDate) {
                    return \Carbon\Carbon::parse($j->created_at)->gt($manualUpdateDate);
                });
            }

            // Jika jurnal kosong setelah difilter (tidak ada jurnal baru sejak update manual),
            // maka TIDAK PERLU di-scan lagi. Lewati.
            if ($student->mental_updated_manual_at && $journals->isEmpty()) {
                $skipped++;
                continue;
            }

            $lastJournal = $journals->first();
            $daysSinceLastJournal = $lastJournal ? now()->diffInDays($lastJournal->created_at) : 99;
            $allText = $journals->pluck('description')->implode(' ');

            try {
                $aiUrl = env('AI_ENGINE_URL', 'http://127.0.0.1:8001');
                $response = Http::timeout(30)->post("{$aiUrl}/api/classify", [
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
                        $counselors = User::where('role', 'konselor')->get();
                        Notification::send($counselors, new \App\Notifications\HighRiskStudentDetected($student));

                        foreach ($counselors as $counselor) {
                            \App\Models\Notifikasi::create([
                                'user_id' => $counselor->id,
                                'pesan'   => "⚠️ Peringatan Risiko Tinggi! Mahasiswa {$student->name} ({$student->nim}) terdeteksi berada di Level 3 (Krisis).",
                                'status'  => 'belum',
                            ]);
                        }
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
            'message' => "Scan selesai: {$saved} mahasiswa diproses, {$skipped} dilewati (tidak ada data baru).",
            'saved'   => $saved,
            'skipped' => $skipped,
        ]);
    }

    public function getSummary(Request $request)
    {
        $nim = $request->nim;

        $journals = JournalText::where('nim', $nim)
            ->orderBy('created_at', 'asc')
            ->get()
            ->pluck('description')
            ->toArray();

        if (empty($journals)) {
            return response()->json([
                'status'  => 'success',
                'nim'     => $nim,
                'summary' => 'Mahasiswa ini belum memiliki jurnal yang dapat diringkas.',
            ]);
        }

        $aiUrl = env('AI_ENGINE_URL', 'http://127.0.0.1:8001');
        $response = Http::timeout(120)->post("{$aiUrl}/api/summarize", [
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

    public function prioritas()
    {
        $students = Student::with('journalTexts')
            ->whereIn('mental_level', [3, '3'])
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $student->journal_texts_count = $student->journalTexts->count();
                return $student;
            });

        // 1. Hitung statistik dasar
        $totalStudents = Student::count();
        $totalScanned = Student::whereNotNull('mental_level')->count();
        $l3Count = $students->count();
        $ratio = $totalScanned > 0 ? round(($l3Count / $totalScanned) * 100, 1) : 0;

        // 2. Distribusi kasus per Program Studi (Prodi)
        $prodiBreakdown = $students->groupBy(function ($s) {
            return $s->prodi ?: 'Tidak Diketahui';
        })->map(function ($group) {
            return $group->count();
        })->sortDesc();

        // 3. Distribusi kasus per Angkatan
        $angkatanBreakdown = $students->groupBy('angkatan')->map(function ($group) {
            return $group->count();
        })->sort();

        // 4. Tren Mood Bulanan Mahasiswa Prioritas (Level 3)
        $nims = $students->pluck('nim')->toArray();
        $fourMonthsAgo = now()->subMonths(4)->startOfMonth();
        
        $checkins = DailyCheckin::whereIn('nim', $nims)
            ->where('created_at', '>=', $fourMonthsAgo)
            ->get();

        $monthlyMoodTrend = $checkins->groupBy(function ($checkin) {
            $date = $checkin->created_at instanceof \Carbon\Carbon 
                ? $checkin->created_at 
                : \Carbon\Carbon::parse($checkin->created_at);
            return $date->format('Y-m');
        })->map(function ($group) {
            $avg = $group->map(function ($entry) {
                return match ((int)$entry->mood_id) {
                    7 => 1, 6 => 2, 5 => 3, 4 => 4, 3 => 5, 2 => 6, 1 => 7, default => 4
                };
            })->average();
            return [
                'avg_score' => round($avg, 2),
                'count' => $group->count()
            ];
        })->sortKeys();

        return view('admin.prioritas', compact(
            'students',
            'totalStudents',
            'totalScanned',
            'l3Count',
            'ratio',
            'prodiBreakdown',
            'angkatanBreakdown',
            'monthlyMoodTrend'
        ));
    }

    public function semuaMahasiswa(Request $request)
    {
        $search   = $request->query('search');
        $angkatan = $request->query('angkatan', 'Semua');
        $prodi    = $request->query('prodi', 'Semua');
        $level    = $request->query('level', 'Semua');

        // Daftar angkatan unik dari MongoDB
        $angkatanList = Student::pluck('angkatan')->filter()->unique()->sort()->values();

        // Pemetaan Fakultas → daftar Prodi (sama dengan getStudentPreview)
        $fakultasMap = [
            'FAK:Vokasi'                  => ['Teknologi Rekayasa Perangkat Lunak', 'Teknologi Informasi', 'Teknologi Komputer'],
            'Semua Vokasi'                => ['Teknologi Rekayasa Perangkat Lunak', 'Teknologi Informasi', 'Teknologi Komputer'],
            'FAK:Informatika & Elektro'   => ['Informatika', 'Teknik Elektro'],
            'Semua Informatika & Elektro' => ['Informatika', 'Teknik Elektro'],
            'FAK:Bioteknologi'            => ['Bioproses', 'Bioteknologi'],
            'Semua Bioteknologi'          => ['Bioproses', 'Bioteknologi'],
            'FAK:Teknik Industri'         => ['Managemen Rekayasa', 'Metalurgi'],
        ];

        $students = Student::with('journalTexts')
            ->when($level !== 'Semua', function ($q) use ($level) {
                $q->whereIn('mental_level', [(int)$level, (string)$level]);
            })
            ->when($angkatan !== 'Semua', function ($q) use ($angkatan) {
                $q->where('angkatan', $angkatan);
            })
            ->when($prodi !== 'Semua', function ($q) use ($prodi, $fakultasMap) {
                if (array_key_exists($prodi, $fakultasMap)) {
                    $prodiList = $fakultasMap[$prodi];
                    $q->where(function ($sub) use ($prodiList) {
                        foreach ($prodiList as $p) {
                            $sub->orWhere('prodi', 'like', "%{$p}%");
                        }
                    });
                } else {
                    $q->where('prodi', 'like', "%{$prodi}%");
                }
            })
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            })
            ->orderBy('mental_level', 'desc')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($student) {
                $student->journal_texts_count = $student->journalTexts->count();
                return $student;
            });

        return view('admin.semua_mahasiswa', compact('students', 'search', 'angkatanList', 'angkatan', 'prodi', 'level'));
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

            $journals = $student->journalTexts;

            if ($student->mental_updated_manual_at) {
                $manualUpdateDate = \Carbon\Carbon::parse($student->mental_updated_manual_at);
                $journals = $journals->filter(function($j) use ($manualUpdateDate) {
                    return \Carbon\Carbon::parse($j->created_at)->gt($manualUpdateDate);
                });
            }

            if ($student->mental_updated_manual_at && $journals->isEmpty()) {
                return;
            }

            $lastJournal = $journals->first();
            $daysSinceLastJournal = $lastJournal ? now()->diffInDays($lastJournal->created_at) : 99;
            $allText = $journals->pluck('description')->implode(' ');

            $aiUrl = env('AI_ENGINE_URL', 'http://127.0.0.1:8001');
            $response = Http::timeout(20)->post("{$aiUrl}/api/classify", [
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

    public function laporanTren(Request $request)
    {
        $range = $request->query('range', '14d');
        $query = DailyCheckin::with(['mood', 'feeling']);

        if ($range === '14d') {
            $query->where('created_at', '>=', now()->subDays(14));
            $rangeName = '14 Hari Terakhir';
        } elseif ($range === '1m') {
            $query->where('created_at', '>=', now()->subMonths(1));
            $rangeName = '1 Bulan Terakhir';
        } elseif ($range === '4m') {
            $query->where('created_at', '>=', now()->subMonths(4));
            $rangeName = '4 Bulan Terakhir';
        } elseif ($range === '1y') {
            $query->where('created_at', '>=', now()->subYears(1));
            $rangeName = '1 Tahun Terakhir';
        } else {
            $query->where('created_at', '>=', now()->subDays(14));
            $rangeName = '14 Hari Terakhir';
            $range = '14d';
        }

        $rawEntries = $query->orderBy('created_at', 'asc')->get();
        $totalCheckins = $rawEntries->count();

        // 1. Kelompokkan data tren mood
        if ($range === '1y' || $range === '4m') {
            $grouped = $rawEntries->groupBy(function ($d) {
                return $d->created_at->format('Y-m');
            });
        } else {
            $grouped = $rawEntries->groupBy(function ($d) {
                return $d->created_at->format('Y-m-d');
            });
        }

        $moodTrendData = $grouped->map(function ($entries) {
            $scores = $entries->map(function ($entry) {
                return match ((int)$entry->mood_id) {
                    7 => 1, 6 => 2, 5 => 3, 4 => 4, 3 => 5, 2 => 6, 1 => 7, default => 5,
                };
            });
            return round($scores->average(), 2);
        });

        $labels = [];
        $chartData = [];
        foreach ($moodTrendData as $key => $val) {
            if ($range === '1y' || $range === '4m') {
                $labels[] = \Carbon\Carbon::createFromFormat('Y-m', $key)->isoFormat('MMMM YYYY');
            } else {
                $labels[] = \Carbon\Carbon::parse($key)->isoFormat('D MMM YYYY');
            }
            $chartData[] = $val;
        }

        // Buat tabel data tren
        $trendTable = [];
        $i = 0;
        foreach ($moodTrendData as $key => $val) {
            $trendTable[] = [
                'label' => $labels[$i] ?? $key,
                'score' => $val,
                'count' => $grouped[$key]->count()
            ];
            $i++;
        }

        // 2. Hitung Top 5 Suasana Perasaan (Feelings)
        $distribution = [];
        if ($totalCheckins > 0) {
            $feelingsCount = $rawEntries->groupBy('feeling_id')->map->count();
            $topFeelingIds = $feelingsCount->sortDesc()->take(5)->keys();
            $feelings = Feeling::whereIn('_id', $topFeelingIds)->get()->keyBy('_id');
            foreach ($topFeelingIds as $fid) {
                $count = $feelingsCount[$fid];
                $feeling = $feelings[$fid] ?? null;
                if (!$feeling) continue;
                $fName = $feeling->feeling_name;
                $meta = $this->getFeelingMeta($fName);
                $distribution[] = [
                    'name' => $fName,
                    'percentage' => round(($count / $totalCheckins) * 100, 1),
                    'count' => $count,
                    'desc' => $meta['desc']
                ];
            }
        }

        // 3. Hitung Distribusi Mood
        $moodDist = [];
        if ($totalCheckins > 0) {
            $counts = $rawEntries->groupBy('mood.mood_name')->map->count();
            foreach ($counts as $moodName => $count) {
                $moodDist[] = [
                    'name' => $moodName ?: 'Biasa Saja',
                    'count' => $count,
                    'percentage' => round(($count / $totalCheckins) * 100, 1)
                ];
            }
            usort($moodDist, function ($a, $b) {
                return $b['percentage'] <=> $a['percentage'];
            });
        }

        return view('admin.laporan_tren', compact(
            'range',
            'rangeName',
            'totalCheckins',
            'labels',
            'chartData',
            'trendTable',
            'distribution',
            'moodDist'
        ));
    }

    public function sendCustomNotification(Request $request, string $nim)
    {
        $request->validate([
            'pesan' => 'required|string|max:1000',
        ]);

        $student = Student::where('nim', $nim)->firstOrFail();

        // Selalu simpan langsung ke MongoDB collection "notifications" agar mobile dapat membacanya.
        // Mobile backend (EMORA-APP-backend) membaca dari collection ini via GET /api/notifications.
        \App\Models\NotifikasiMahasiswa::create([
            'nim'    => (string) $nim, // Pastikan nim disimpan sebagai string untuk konsistensi
            'pesan'  => $request->pesan,
            'status' => 'belum',
        ]);

        // Jika mahasiswa juga terdaftar di SQL (user web), sinkronisasi juga ke tabel SQL
        // agar notifikasi muncul di dashboard web konselor.
        $mahasiswa = \App\Models\Mahasiswa::where('nim', $nim)->first();
        if ($mahasiswa && $mahasiswa->user_id) {
            \App\Models\Notifikasi::create([
                'user_id' => $mahasiswa->user_id,
                'pesan'   => $request->pesan,
                'status'  => 'belum',
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Notifikasi berhasil dikirim ke mahasiswa.',
        ]);
    }

}
