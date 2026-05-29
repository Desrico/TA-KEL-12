<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Notifikasi;
use App\Models\NotifikasiMahasiswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== VERIFIKASI SINKRONISASI NOTIFIKASI ===\n\n";

// 1. Tampilkan total notifikasi hasil migrasi di MongoDB
$totalMongo = NotifikasiMahasiswa::count();
echo "1. Total notifikasi di MongoDB saat ini (hasil migrasi): $totalMongo\n";
if ($totalMongo > 0) {
    $sample = NotifikasiMahasiswa::latest()->first();
    echo "   Sample MongoDB Notif:\n";
    echo "   - NIM: " . $sample->nim . "\n";
    echo "   - Pesan: " . $sample->pesan . "\n";
    echo "   - Status: " . $sample->status . "\n";
    echo "   - SQL Notif ID: " . $sample->sql_notifikasi_id . "\n\n";
}

// 2. Cari user mahasiswa untuk pengetesan
$studentUser = User::where('role', 'mahasiswa')->first();
if (!$studentUser) {
    echo "Gagal: Tidak ditemukan user mahasiswa di database untuk pengetesan.\n";
    exit(1);
}
$mahasiswa = $studentUser->mahasiswa;
if (!$mahasiswa || !$mahasiswa->nim) {
    echo "Gagal: User mahasiswa tidak memiliki data profil mahasiswa/NIM.\n";
    exit(1);
}

$nim = $mahasiswa->nim;
echo "Menggunakan mahasiswa: " . $studentUser->nama . " (NIM: $nim) untuk pengetesan sync.\n\n";

// 3. Tes CREATE (Buat Notifikasi Baru di SQL)
echo "2. Mengetes CREATE...\n";
$newNotif = Notifikasi::create([
    'user_id' => $studentUser->id,
    'pesan'   => 'TESTING SYNC: Sesi konseling Anda telah dijadwalkan.',
    'status'  => 'belum',
]);

echo "   Notifikasi SQL dibuat dengan ID: " . $newNotif->id . "\n";

// Cek di MongoDB
$mongoNotif = NotifikasiMahasiswa::where('sql_notifikasi_id', $newNotif->id)->first();
if ($mongoNotif) {
    echo "   [BERHASIL] Notifikasi terdeteksi di MongoDB!\n";
    echo "   - NIM: " . $mongoNotif->nim . "\n";
    echo "   - Pesan: " . $mongoNotif->pesan . "\n";
    echo "   - Status: " . $mongoNotif->status . "\n\n";
} else {
    echo "   [GAGAL] Notifikasi tidak ditemukan di MongoDB.\n\n";
}

// 4. Tes UPDATE (Ubah Status di SQL)
if ($mongoNotif) {
    echo "3. Mengetes UPDATE (Ubah status di SQL jadi 'dibaca')...\n";
    $newNotif->update(['status' => 'dibaca']);
    
    // Cek di MongoDB lagi
    $mongoNotifUpdated = NotifikasiMahasiswa::where('sql_notifikasi_id', $newNotif->id)->first();
    if ($mongoNotifUpdated && $mongoNotifUpdated->status === 'dibaca') {
        echo "   [BERHASIL] Status di MongoDB terupdate menjadi: " . $mongoNotifUpdated->status . "\n\n";
    } else {
        echo "   [GAGAL] Status di MongoDB tidak berubah atau document tidak ditemukan.\n\n";
    }
}

// 5. Tes DELETE (Hapus di SQL)
if ($mongoNotif) {
    echo "4. Mengetes DELETE (Hapus notifikasi di SQL)...\n";
    $newNotif->delete();
    
    // Cek di MongoDB
    $mongoNotifDeleted = NotifikasiMahasiswa::where('sql_notifikasi_id', $newNotif->id)->first();
    if (!$mongoNotifDeleted) {
        echo "   [BERHASIL] Notifikasi berhasil terhapus dari MongoDB!\n\n";
    } else {
        echo "   [GAGAL] Notifikasi masih ada di MongoDB.\n\n";
    }
}

echo "=== VERIFIKASI SELESAI ===\n";
