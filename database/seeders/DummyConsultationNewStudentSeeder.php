<?php

namespace Database\Seeders;

use App\Models\JadwalKonseling;
use App\Models\Konselor;
use App\Models\Laporan;
use App\Models\Mahasiswa;
use App\Models\SesiKonseling;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyConsultationNewStudentSeeder extends Seeder
{
    public function run(): void
    {
        // Cari mahasiswa yang belum memiliki jadwal konseling
        $mahasiswa = Mahasiswa::whereDoesntHave('jadwalKonseling')->first();
        
        // Jika semua sudah punya, ambil mahasiswa urutan ke-4 (agar berbeda dari 3 sebelumnya)
        if (!$mahasiswa) {
            $mahasiswa = Mahasiswa::with('user')->orderBy('id')->skip(3)->first();
        }

        $konselor = Konselor::orderBy('id')->first();

        if (! $mahasiswa || ! $konselor) {
            $this->command?->warn('Tidak dapat menemukan mahasiswa baru atau konselor.');
            return;
        }

        // Tiga topik berbeda yang dipilih
        $items = [
            [
                'days_ago' => 20,
                'time' => '09:00:00',
                'topik' => 'Akademik (TA, Kuliah, KP, MBKM, others)',
                'ringkasan' => 'Mahasiswa merasa terbebani dengan tugas akhir (TA) yang tidak kunjung selesai. Terdapat kendala komunikasi dengan dosen pembimbing.',
                'observasi' => 'Kelihatan sangat lelah dan kurang tidur. Mahasiswa menyadari perlu memperbaiki jadwal bimbingan.',
                'progress' => 'tetap',
                'tindak_lanjut' => 'perlu lanjut',
                'tanggal_lanjut_offset' => 7,
            ],
            [
                'days_ago' => 13,
                'time' => '10:00:00',
                'topik' => 'Intrapersonal (Kecemasan, Kejenuhan, Motivasi Belajar, dll)',
                'ringkasan' => 'Mengalami kejenuhan luar biasa dan kehilangan motivasi untuk menyusun TA. Mulai muncul rasa cemas berlebihan akan masa depan.',
                'observasi' => 'Lebih terbuka dalam bercerita, namun masih sering menunduk saat ditanya tentang progress spesifik TA.',
                'progress' => 'membaik',
                'tindak_lanjut' => 'perlu lanjut',
                'tanggal_lanjut_offset' => 7,
            ],
            [
                'days_ago' => 6,
                'time' => '13:00:00',
                'topik' => 'Keluarga',
                'ringkasan' => 'Merasa ditekan oleh keluarga di kampung halaman karena target lulus yang meleset dari harapan orang tua.',
                'observasi' => 'Sempat menangis ketika menceritakan harapan orang tua, tetapi sudah menemukan titik terang cara mengkomunikasikan kendalanya ke keluarga.',
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
                    'catatan' => 'Dummy Consultation New Student | Topik: ' . $item['topik'],
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
                'catatan' => 'Dummy sesi untuk pengujian student baru. ' . $item['observasi'],
            ];

            $sesi = $jadwal->sesiKonseling()->updateOrCreate(
                [$foreignKey => $jadwal->id],
                $sesiValues,
            );

            Laporan::updateOrCreate(
                ['sesi_id' => $sesi->id],
                [
                    'konselor_id' => $konselor->id,
                    'isi_laporan' => $laporanText,
                ]
            );

            $this->command?->info("✓ Dummy konseling student baru created: {$item['topik']}");
        }

        $this->command?->info('Dummy consultation new student data berhasil dibuat!');
    }

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
