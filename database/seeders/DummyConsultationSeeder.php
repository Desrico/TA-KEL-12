<?php

namespace Database\Seeders;

use App\Models\JadwalKonseling;
use App\Models\Konselor;
use App\Models\Laporan;
use App\Models\Mahasiswa;
use App\Models\SesiKonseling;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

// ADDED: Seeder baru untuk membuat dummy laporan konseling dengan berbagai topik konseling
class DummyConsultationSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = Mahasiswa::with('user')->orderBy('id')->first();
        $konselor = Konselor::orderBy('id')->first();

        if (! $mahasiswa || ! $konselor) {
            $this->command?->warn('Seeder membutuhkan minimal satu mahasiswa dan satu konselor.');
            return;
        }

        // ADDED: Berbagai topik konseling sesuai dengan pilihan di sistem
        $items = [
            // ADDED: Dummy laporan dengan topik Keluarga
            [
                'days_ago' => 35,
                'time' => '11:00:00',
                'topik' => 'Keluarga',
                'ringkasan' => 'Mahasiswa mengalami tekanan dari orang tua terkait pilihan jurusan dan ekspektasi akademik yang tinggi. Merasa kurang mendapat dukungan emosional dari keluarga.',
                'observasi' => 'Mahasiswa menunjukkan ekspresi sedih saat membahas komunikasi dengan orang tua. Mampu mengidentifikasi kebutuhan untuk membangun dialog yang lebih baik.',
                'progress' => 'memburuk',
                'tindak_lanjut' => 'perlu lanjut',
                'tanggal_lanjut_offset' => 21,
            ],
            // ADDED: Dummy laporan dengan topik Akademik
            [
                'days_ago' => 28,
                'time' => '14:00:00',
                'topik' => 'Akademik (TA, Kuliah, KP, MBKM, others)',
                'ringkasan' => 'Mahasiswa merasa kehilangan semangat belajar setelah mengalami nilai rendah di ujian tengah semester. Kepercayaan diri terhadap kemampuan akademik menurun signifikan.',
                'observasi' => 'Mahasiswa menunjukkan gejala depresi ringan dengan pola pikir negatif tentang masa depan. Namun masih memiliki motivasi untuk berkembang setelah diberikan perspektif positif.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'perlu lanjut',
                'tanggal_lanjut_offset' => 14,
            ],
            // ADDED: Dummy laporan dengan topik Kehidupan di Kampus
            [
                'days_ago' => 21,
                'time' => '10:30:00',
                'topik' => 'Kehidupan di Kampus',
                'ringkasan' => 'Mahasiswa merasa terasing di kampus dan sulit bergabung dengan organisasi mahasiswa. Merasa tidak memiliki teman dekat di lingkungan universitas.',
                'observasi' => 'Mahasiswa menunjukkan kekhawatiran tentang isolasi sosial. Konselor membantu mengidentifikasi potensi aktivitas dan komunitas yang sesuai dengan minat.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'monitoring',
                'tanggal_lanjut_offset' => null,
            ],
            // ADDED: Dummy laporan dengan topik Masalah di asrama
            [
                'days_ago' => 14,
                'time' => '13:00:00',
                'topik' => 'Masalah di asrama',
                'ringkasan' => 'Mahasiswa mengalami konflik dengan teman sekamar terkait kebiasaan tidur dan kebersihan ruangan. Akibatnya tidur menjadi terganggu dan konsentrasi belajar menurun.',
                'observasi' => 'Mahasiswa mampu mengartikulasikan masalah dengan jelas dan terbuka untuk komunikasi. Konselor membantu merancang strategi komunikasi asertif dengan roommate.',
                'progress' => 'memburuk',
                'tindak_lanjut' => 'perlu lanjut',
                'tanggal_lanjut_offset' => 10,
            ],
            // ADDED: Dummy laporan dengan topik Relasi
            [
                'days_ago' => 7,
                'time' => '15:30:00',
                'topik' => 'Relasi (pertemanan, pacaran, ketidaknyamanan di asrama, kesalahpahaman)',
                'ringkasan' => 'Mahasiswa sedang dalam hubungan pacaran yang mulai menunjukkan tanda-tanda tidak sehat dengan pasangan yang posesif dan mengontrol. Merasa tertekan dan kehilangan kebebasan pribadi.',
                'observasi' => 'Mahasiswa menunjukkan keraguan dan kekhawatiran namun masih mencintai pasangan. Konselor membantu mengidentifikasi batasan sehat dalam hubungan dan opsi yang tersedia.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'perlu lanjut',
                'tanggal_lanjut_offset' => 7,
            ],
            // ADDED: Dummy laporan dengan topik Intrapersonal
            [
                'days_ago' => 2,
                'time' => '09:30:00',
                'topik' => 'Intrapersonal (Kecemasan, Kejenuhan, Motivasi Belajar, dll)',
                'ringkasan' => 'Mahasiswa mengalami kecemasan sosial yang signifikan dengan gejala fobia berbicara di depan kelas. Hal ini berdampak pada performa presentasi dan partisipasi di kelas.',
                'observasi' => 'Mahasiswa menunjukkan gejala cemas fisik saat membahas presentasi. Konselor mulai mengajarkan teknik relaksasi dan exposure gradual untuk mengatasi fobia.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'perlu lanjut',
                'tanggal_lanjut_offset' => 7,
            ],
        ];

        foreach ($items as $item) {
            $date = Carbon::now('Asia/Jakarta')->subDays($item['days_ago'])->toDateString();
            $laporanText = $this->composeLaporan($item);

            // ADDED: Cek apakah data sudah ada, jika belum buat baru
            $jadwal = JadwalKonseling::updateOrCreate(
                [
                    'mahasiswa_id' => $mahasiswa->id,
                    'konselor_id' => $konselor->id,
                    'tanggal' => $date,
                    'waktu' => $item['time'],
                ],
                [
                    'status' => 'selesai',
                    'jenis' => 'online',
                    'topik' => $item['topik'],
                    'catatan' => 'Dummy Consultation | Topik: ' . $item['topik'],
                    'ringkasan_masalah' => $item['ringkasan'],
                    'observasi_konselor' => $item['observasi'],
                    'progress' => $item['progress'],
                    'tindak_lanjut_tipe' => $item['tindak_lanjut'],
                    'tanggal_lanjut' => $item['tanggal_lanjut_offset']
                        ? Carbon::parse($date)->addDays($item['tanggal_lanjut_offset'])->toDateString()
                        : null,
                    'laporan' => $laporanText,
                ]
            );

            // ADDED: Create sesi konseling terkait
            $foreignKey = SesiKonseling::jadwalForeignKey();
            $sesiValues = [
                'status' => 'selesai',
                'catatan' => 'Dummy sesi untuk pengujian. ' . $item['observasi'],
            ];

            $sesi = $jadwal->sesiKonseling()->updateOrCreate(
                [$foreignKey => $jadwal->id],
                $sesiValues,
            );

            // ADDED: Create laporan terkait
            Laporan::updateOrCreate(
                ['sesi_id' => $sesi->id],
                [
                    'konselor_id' => $konselor->id,
                    'isi_laporan' => $laporanText,
                ]
            );

            $this->command?->info("✓ Dummy konseling created: {$item['topik']}");
        }

        $this->command?->info('Dummy consultation data berhasil dibuat!');
    }

    // ADDED: Helper function untuk membuat teks laporan yang terstruktur
    private function composeLaporan(array $item): string
    {
        return implode("\n\n", [
            "RINGKASAN MASALAH:\n{$item['ringkasan']}",
            "OBSERVASI KONSELOR:\n{$item['observasi']}",
            "PROGRESS:\n{$item['progress']}",
            "TINDAK LANJUT:\n{$item['tindak_lanjut']}",
        ]);
    }
}
