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

class ExampleCompletedSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin/konselor contoh jika belum ada.
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

        // Buat mahasiswa contoh untuk sesi selesai.
        $studentUser = User::updateOrCreate(
            ['email' => 'contoh.selesai@campus.test'],
            [
                'nama' => 'Nadia Putri Contoh',
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
            ]
        );

        $mahasiswa = Mahasiswa::updateOrCreate(
            ['nim' => '11426001'],
            [
                'user_id' => $studentUser->id,
                'jurusan' => 'D IV Sarjana Terapan Teknologi Rekayasa Perangkat Lunak',
                'angkatan' => 2022,
            ]
        );

        $tanggal = Carbon::now('Asia/Jakarta')->subDay()->toDateString();
        $waktu = '09:00:00';

        $jadwalValues = [
            'tanggal' => $tanggal,
            'waktu' => $waktu,
            'status' => 'selesai',
        ];

        if (Schema::hasColumn('jadwal_konseling', 'jenis')) {
            $jadwalValues['jenis'] = 'online';
        }

        if (Schema::hasColumn('jadwal_konseling', 'topik')) {
            $jadwalValues['topik'] = 'Manajemen stres akademik';
        }

        if (Schema::hasColumn('jadwal_konseling', 'catatan')) {
            $jadwalValues['catatan'] = 'Contoh sesi selesai untuk uji pembuatan laporan admin.';
        }

        if (Schema::hasColumn('jadwal_konseling', 'started_at')) {
            $jadwalValues['started_at'] = Carbon::parse($tanggal . ' ' . $waktu, 'Asia/Jakarta');
        }

        if (Schema::hasColumn('jadwal_konseling', 'expires_at')) {
            $jadwalValues['expires_at'] = Carbon::parse($tanggal . ' ' . $waktu, 'Asia/Jakarta')->addDay();
        }

        $jadwal = JadwalKonseling::firstOrNew([
            'mahasiswa_id' => $mahasiswa->id,
            'konselor_id' => $konselor->id,
            'tanggal' => $tanggal,
            'waktu' => $waktu,
        ]);

        // Kosongkan kolom laporan hanya saat data contoh pertama kali dibuat.
        if (! $jadwal->exists) {
            foreach (['laporan', 'ringkasan_masalah', 'observasi_konselor', 'progress', 'tindak_lanjut', 'tindak_lanjut_tipe', 'tanggal_lanjut'] as $column) {
                if (Schema::hasColumn('jadwal_konseling', $column)) {
                    $jadwalValues[$column] = null;
                }
            }
        }

        $jadwal->fill($jadwalValues);
        $jadwal->save();

        $sesiValues = [
            'status' => 'selesai',
        ];

        // Isi hanya kolom sesi yang tersedia di database lokal.
        if (Schema::hasColumn('sesi_konseling', 'catatan_sesi')) {
            $sesiValues['catatan_sesi'] = 'Contoh sesi sudah selesai, laporan belum dibuat.';
        }

        if (Schema::hasColumn('sesi_konseling', 'catatan')) {
            $sesiValues['catatan'] = 'Contoh sesi sudah selesai, laporan belum dibuat.';
        }

        if (Schema::hasColumn('sesi_konseling', 'waktu_mulai')) {
            $sesiValues['waktu_mulai'] = Carbon::parse($tanggal . ' ' . $waktu, 'Asia/Jakarta');
        }

        if (Schema::hasColumn('sesi_konseling', 'waktu_selesai')) {
            $sesiValues['waktu_selesai'] = Carbon::parse($tanggal . ' ' . $waktu, 'Asia/Jakarta')->addHour();
        }

        SesiKonseling::updateOrCreate(
            [SesiKonseling::jadwalForeignKey() => $jadwal->id],
            $sesiValues
        );
    }
}
