<?php

namespace Database\Seeders;

use App\Models\JadwalKonseling;
use App\Models\Konselor;
use App\Models\Mahasiswa;
use App\Models\SesiKonseling;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class FransCompletedSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan konselor admin tersedia.
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama' => 'Admin',
                'password' => Hash::make('admin123'),
                'role' => 'konselor',
            ]
        );

        $konselor = Konselor::updateOrCreate(
            ['user_id' => $admin->id],
            ['spesialisasi' => 'Konselor Utama']
        );

        // Gunakan data Frans yang sudah ada, atau buat jika belum tersedia.
        $fransUser = User::updateOrCreate(
            ['email' => 'if422031@students.del.ac.id'],
            [
                'nama' => 'Frans Elo Hansen Panjaitan',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
            ]
        );

        $frans = Mahasiswa::updateOrCreate(
            ['nim' => '11422031'],
            [
                'user_id' => $fransUser->id,
                'jurusan' => 'D IV Sarjana Terapan Teknologi Rekayasa Perangkat Lunak',
                'angkatan' => 2022,
            ]
        );

        $sessions = [
            [
                'date' => Carbon::now('Asia/Jakarta')->subDays(5),
                'time' => '09:00:00',
                'topik' => 'Akademik (TA, Kuliah, KP, MBKM, others)',
                'catatan' => 'Frans membutuhkan arahan untuk mengatur prioritas tugas akhir, kuliah, dan jadwal bimbingan.',
            ],
            [
                'date' => Carbon::now('Asia/Jakarta')->subDays(3),
                'time' => '13:00:00',
                'topik' => 'Intrapersonal (Kecemasan, Kejenuhan, Motivasi Belajar, dll)',
                'catatan' => 'Frans menyampaikan kejenuhan belajar dan kecemasan saat target akademik belum tercapai.',
            ],
            [
                'date' => Carbon::now('Asia/Jakarta')->subDay(),
                'time' => '15:00:00',
                'topik' => 'Kehidupan di Kampus',
                'catatan' => 'Frans ingin menyesuaikan rutinitas kampus agar aktivitas akademik dan sosial lebih seimbang.',
            ],
        ];

        foreach ($sessions as $item) {
            $tanggal = $item['date']->toDateString();

            $jadwalValues = [
                'status' => 'selesai',
                'tanggal' => $tanggal,
                'waktu' => $item['time'],
            ];

            if (Schema::hasColumn('jadwal_konseling', 'jenis')) {
                $jadwalValues['jenis'] = 'online';
            }

            if (Schema::hasColumn('jadwal_konseling', 'topik')) {
                $jadwalValues['topik'] = $item['topik'];
            }

            if (Schema::hasColumn('jadwal_konseling', 'catatan')) {
                $jadwalValues['catatan'] = 'Topik: ' . $item['topik'] . ' | ' . $item['catatan'];
            }

            if (Schema::hasColumn('jadwal_konseling', 'started_at')) {
                $jadwalValues['started_at'] = Carbon::parse($tanggal . ' ' . $item['time'], 'Asia/Jakarta');
            }

            if (Schema::hasColumn('jadwal_konseling', 'expires_at')) {
                $jadwalValues['expires_at'] = Carbon::parse($tanggal . ' ' . $item['time'], 'Asia/Jakarta')->addDay();
            }

            $jadwal = JadwalKonseling::firstOrNew([
                'mahasiswa_id' => $frans->id,
                'konselor_id' => $konselor->id,
                'tanggal' => $tanggal,
                'waktu' => $item['time'],
            ]);

            // Kosongkan laporan hanya saat data contoh pertama kali dibuat.
            if (! $jadwal->exists) {
                foreach (['laporan', 'ringkasan_masalah', 'observasi_konselor', 'progress', 'tindak_lanjut', 'tindak_lanjut_tipe', 'tanggal_lanjut'] as $column) {
                    if (Schema::hasColumn('jadwal_konseling', $column)) {
                        $jadwalValues[$column] = null;
                    }
                }
            }

            $jadwal->fill($jadwalValues);
            $jadwal->save();

            $sesiValues = ['status' => 'selesai'];

            // Isi hanya kolom sesi yang tersedia.
            if (Schema::hasColumn('sesi_konseling', 'catatan_sesi')) {
                $sesiValues['catatan_sesi'] = 'Sesi selesai untuk topik ' . $item['topik'] . '.';
            }

            if (Schema::hasColumn('sesi_konseling', 'catatan')) {
                $sesiValues['catatan'] = 'Sesi selesai untuk topik ' . $item['topik'] . '.';
            }

            if (Schema::hasColumn('sesi_konseling', 'waktu_mulai')) {
                $sesiValues['waktu_mulai'] = Carbon::parse($tanggal . ' ' . $item['time'], 'Asia/Jakarta');
            }

            if (Schema::hasColumn('sesi_konseling', 'waktu_selesai')) {
                $sesiValues['waktu_selesai'] = Carbon::parse($tanggal . ' ' . $item['time'], 'Asia/Jakarta')->addHour();
            }

            SesiKonseling::updateOrCreate(
                [SesiKonseling::jadwalForeignKey() => $jadwal->id],
                $sesiValues
            );
        }

        $this->replaceEmptyTopics();
    }

    private function replaceEmptyTopics(): void
    {
        if (! Schema::hasColumn('jadwal_konseling', 'topik')) {
            return;
        }

        // Ganti topik kosong atau '-' dengan opsi valid.
        JadwalKonseling::query()
            ->whereNull('topik')
            ->orWhere('topik', '')
            ->orWhere('topik', '-')
            ->update(['topik' => "Opsi 'Lainnya'"]);
    }
}
