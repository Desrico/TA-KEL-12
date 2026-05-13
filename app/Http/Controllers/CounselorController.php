<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\JadwalKonseling;
use App\Models\Konselor;

class CounselorController extends Controller
{

    public function getJadwalData(Request $request)
    {
        $user = Auth::user();
        $konselor = $user ? Konselor::where('user_id', $user->id)->first() : null;
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



    public function prioritas()
    {
        $students = Student::where('mental_level', 3)
            ->orderBy('mental_scanned_at', 'desc')
            ->get();

        return view('admin.prioritas', compact('students'));
    }

    public function semuaMahasiswa()
    {
        $students = Student::orderBy('name', 'asc')->get();
        return view('admin.semua_mahasiswa', compact('students'));
    }

}
