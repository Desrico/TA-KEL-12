<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfilController;

// ═══════════════════════════════
// HALAMAN PUBLIK
// ═══════════════════════════════
Route::get('/', function () { return view('pages.beranda'); });
Route::get('/about', function () { return view('pages.about'); })->name('about');
Route::get('/layanan', function () { return view('pages.layanan'); })->name('layanan');

// ═══════════════════════════════
// AUTH
// ═══════════════════════════════
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ═══════════════════════════════
// MAHASISWA (harus login)
// ═══════════════════════════════
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('Pages.beranda'); })->name('dashboard');

    // Profil
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/anonim', [ProfilController::class, 'toggleAnonim'])->name('profil.anonim');
    Route::get('/riwayat', [ProfilController::class, 'riwayat'])->name('riwayat');
    Route::post('/notifikasi/baca', [ProfilController::class, 'markNotificationsAsRead'])->name('notifikasi.baca');

    // Booking
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/booking/booked-slots', [BookingController::class, 'getBookedSlots'])->name('booking.booked');
});

// ═══════════════════════════════
// ADMIN / KONSELOR
// ═══════════════════════════════
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard',  [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking',    [AdminController::class, 'booking'])->name('booking');
    Route::post('/booking/{id}/setujui', [AdminController::class, 'setujui'])->name('booking.setujui');
    Route::post('/booking/{id}/tolak',   [AdminController::class, 'tolak'])->name('booking.tolak');
    Route::get('/mahasiswa',  [AdminController::class, 'mahasiswa'])->name('mahasiswa');
    Route::get('/laporan',    [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/pengaturan', [AdminController::class, 'pengaturan'])->name('pengaturan');
    Route::post('/logout',    [AdminController::class, 'logout'])->name('logout');
});