<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfilController;

 Route::get('/', function () {
     return view('pages.beranda');
 });

 Route::get('/about', function () {
     return view('pages.about');
 })->name('about');

 Route::get('/layanan', function () {
     return view('pages.layanan');
 })->name('layanan');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Halaman Profil
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::get('/riwayat', [ProfilController::class, 'riwayat'])->name('riwayat');
});

// Route::get('/', function () {
//     return view('login');
// });

 Route::get('/register', function () {
     return view('auth.register');
 });

// Route::get('/', [AuthController::class, 'showLogin'])->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
// Route::post('/register', [AuthController::class, 'register'])->name('register.post');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// // Halaman utama setelah login (sesuaikan nanti)
// Route::middleware('auth')->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });

// LOGIN (satu untuk semua role)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN — tidak perlu route login sendiri lagi
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
});

// REGISTER
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// HALAMAN SETELAH LOGIN
Route::middleware('auth')->group(function () {
    Route::get('/beranda', function () {
        return view('pages.beranda'); // atau sesuaikan dengan folder kamu
    })->name('beranda');
});

// Halaman layanan
Route::middleware('auth')->group(function () {
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
});

Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/booked-slots', [BookingController::class, 'getBookedSlots'])->name('booking.booked');

// Fitur Anonim
Route::middleware('auth')->group(function () {
    // ... route lainnya
    Route::post('/profil/anonim', function(\Illuminate\Http\Request $req) {
        $profil = \App\Models\Profil::firstOrCreate(['user_id' => \Auth::id()]);
        $profil->update(['anonim' => $req->anonim]);
        return response()->json(['success' => true]);
    })->name('profil.anonim');
});

// Anonim
Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/anonim', [ProfilController::class, 'toggleAnonim'])->name('profil.anonim');
    Route::get('/riwayat', [ProfilController::class, 'riwayat'])->name('riwayat');
});