<?php

namespace Database\Seeders;

use App\Models\JadwalKonseling;
use App\Models\Konselor;
use App\Models\Laporan;
use App\Models\Mahasiswa;
use App\Models\SesiKonseling;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AiSummaryDummySeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswa = Mahasiswa::with('user')->orderBy('id')->first();
        $konselor = Konselor::orderBy('id')->first();

        if (! $mahasiswa || ! $konselor) {
            $this->command?->warn('Seeder membutuhkan minimal satu mahasiswa dan satu konselor.');
            return;
        }

        $items = [
            [
                'days_ago' => 28,
                'time' => '09:00:00',
                'topik' => 'Tekanan akademik dan manajemen waktu',
                'ringkasan' => 'Mahasiswa merasa kewalahan menghadapi beberapa tugas besar yang tenggatnya berdekatan. Kesulitan utama adalah menyusun prioritas dan menjaga ritme belajar.',
                'observasi' => 'Mahasiswa tampak cemas saat menjelaskan beban tugas, namun mampu mengidentifikasi mata kuliah yang paling menekan setelah diarahkan.',
                'progress' => 'memburuk',
                'tindak_lanjut' => 'perlu_lanjut',
                'tanggal_lanjut_offset' => 14,
            ],
            [
                'days_ago' => 20,
                'time' => '10:00:00',
                'topik' => 'Strategi belajar dan rutinitas harian',
                'ringkasan' => 'Mahasiswa mulai mencoba membuat daftar prioritas harian, tetapi masih kesulitan konsisten karena jam tidur tidak teratur.',
                'observasi' => 'Mahasiswa lebih terbuka dibanding sesi sebelumnya dan dapat menyebutkan dua kebiasaan yang ingin diperbaiki.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'perlu_lanjut',
                'tanggal_lanjut_offset' => 10,
            ],
            [
                'days_ago' => 12,
                'time' => '13:30:00',
                'topik' => 'Kecemasan menjelang evaluasi',
                'ringkasan' => 'Mahasiswa masih merasa tegang menjelang kuis dan presentasi, tetapi sudah memakai jadwal belajar bertahap untuk mengurangi belajar mendadak.',
                'observasi' => 'Mahasiswa terlihat lebih tenang saat membahas rencana akademik. Kekhawatiran masih muncul ketika membicarakan presentasi di depan kelas.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'perlu_lanjut',
                'tanggal_lanjut_offset' => 7,
            ],
            [
                'days_ago' => 5,
                'time' => '15:00:00',
                'topik' => 'Evaluasi perkembangan dan tindak lanjut',
                'ringkasan' => 'Mahasiswa melaporkan beban akademik masih ada, tetapi lebih mampu mengatur prioritas dan meminta bantuan teman saat menemui hambatan.',
                'observasi' => 'Mahasiswa menunjukkan peningkatan rasa percaya diri dan mampu menyusun rencana belajar untuk minggu berikutnya.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'monitoring',
                'tanggal_lanjut_offset' => null,
            ],
        ];

        foreach ($items as $item) {
            $date = Carbon::now('Asia/Jakarta')->subDays($item['days_ago'])->toDateString();
            $laporanText = $this->composeLaporan($item);

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
                    'catatan' => 'Dummy AI Summary | Topik: ' . $item['topik'],
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

            $foreignKey = SesiKonseling::jadwalForeignKey();
            $sesiValues = [
                'status' => 'selesai',
                'catatan' => 'Dummy sesi untuk pengujian ringkasan AI. ' . $item['observasi'],
            ];

            if (Schema::hasColumn('sesi_konseling', 'catatan_sesi')) {
                $sesiValues['catatan_sesi'] = $item['observasi'];
            }

            if (Schema::hasColumn('sesi_konseling', 'waktu_mulai')) {
                $sesiValues['waktu_mulai'] = Carbon::parse($date . ' ' . $item['time']);
            }

            if (Schema::hasColumn('sesi_konseling', 'waktu_selesai')) {
                $sesiValues['waktu_selesai'] = Carbon::parse($date . ' ' . $item['time'])->addHour();
            }

            $sesi = SesiKonseling::updateOrCreate([$foreignKey => $jadwal->id], $sesiValues);

            Laporan::updateOrCreate(
                ['sesi_id' => $sesi->id],
                [
                    'konselor_id' => $konselor->id,
                    'isi_laporan' => $laporanText,
                ]
            );
        }

        $this->command?->info('Dummy laporan AI dibuat untuk mahasiswa: ' . (optional($mahasiswa->user)->nama ?? $mahasiswa->nim));
    }

    private function composeLaporan(array $item): string
    {
        return implode("\n\n", [
            'Ringkasan Masalah: ' . $item['ringkasan'],
            'Observasi Konselor: ' . $item['observasi'],
            'Progress: ' . $item['progress'],
            'Tindak Lanjut: ' . $item['tindak_lanjut'],
        ]);
    }
}
