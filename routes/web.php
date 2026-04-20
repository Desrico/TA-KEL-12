<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LaporanController;

// ═══════════════════════════════
// HALAMAN PUBLIK
// ═══════════════════════════════
Route::get('/', function () {
    return view('Pages.beranda');
})->name('beranda');

Route::get('/tentang', function () {
    return view('Pages.tentang');
})->name('tentang');

Route::get('/layanan', function () {
    return view('Pages.layanan');
})->name('layanan');

// ═══════════════════════════════
// AUTH
// ═══════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ═══════════════════════════════
// MAHASISWA
// ═══════════════════════════════
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', function () {
        return view('Pages.beranda');
    })->name('dashboard');

    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/anonim', [ProfilController::class, 'toggleAnonim'])->name('profil.anonim');

    // Riwayat Konseling Mahasiswa
    Route::get('/riwayat', [LaporanController::class, 'riwayat'])->name('riwayat'); // Mahasiswa hanya melihat riwayat mereka sendiri

    Route::post('/notifikasi/baca', [ProfilController::class, 'markNotificationsAsRead'])->name('notifikasi.baca');

    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::post('/jadwal/cek', [JadwalController::class, 'checkAvailability'])->name('jadwal.check');
    Route::get('/jadwal/terisi', [JadwalController::class, 'getBookedSlots'])->name('jadwal.terisi');
});

// ═══════════════════════════════
// ADMIN / KONSELOR
// ═══════════════════════════════
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:konselor'])
    ->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/notifikasi', [AdminController::class, 'notifications'])->name('notifikasi.list');
        Route::post('/notifikasi/baca', [AdminController::class, 'markNotificationsAsRead'])->name('notifikasi.baca');

        Route::get('/jadwal', [AdminController::class, 'jadwal'])->name('jadwal');
        Route::post('/jadwal/{id}/setujui', [AdminController::class, 'setujui'])->name('jadwal.setujui');
        Route::post('/jadwal/{id}/tolak', [AdminController::class, 'tolak'])->name('jadwal.tolak');

        Route::get('/chat', [AdminController::class, 'chat'])->name('chat');
        Route::get('/sesi', [AdminController::class, 'sesi'])->name('sesi');

        // Laporan (Admin)
        Route::get('/laporan', [LaporanController::class, 'laporanAdmin'])->name('laporan');
        Route::get('/laporan/{id}/laporan', [LaporanController::class, 'createLaporan'])->name('laporan.laporan');
        Route::post('/laporan/{id}/laporan', [LaporanController::class, 'storeLaporan'])->name('laporan.laporan.store');

        Route::get('/mahasiswa', [AdminController::class, 'mahasiswa'])->name('mahasiswa');
        Route::get('/jadwal/events', [AdminController::class, 'jadwalEvents'])->name('jadwal.events');
    });