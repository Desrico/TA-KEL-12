<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KampusApiController;


// ═══════════════════════════════
// HALAMAN PUBLIK
// ═══════════════════════════════
Route::get('/', function () {
    return view('Pages.beranda');
})->name('beranda');

Route::get('/tentang', function () {    
    return view('Pages.tentang');
})->name('tentang');

// halaman konseling
Route::get('/konseling', [JadwalController::class, 'create'])->name('konseling');

// ═══════════════════════════════
// AUTH
// ═══════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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

    Route::get('/riwayat', [LaporanController::class, 'riwayat'])->name('riwayat');
    Route::post('/notifikasi/baca', [ProfilController::class, 'markNotificationsAsRead'])->name('notifikasi.baca');

    // flow penjadwalan
    Route::get('/detail-penjadwalan', [JadwalController::class, 'detail'])->name('jadwal.detail');
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
        Route::get('/sesi/{id}', [AdminController::class, 'detailSesi'])->name('sesi.detail');
        Route::post('/sesi/{id}/terima', [AdminController::class, 'terimaSesi'])->name('sesi.terima');
        Route::get('/sesi/{id}/tolak', [AdminController::class, 'tolakSesi'])->name('sesi.tolak');
        Route::post('/sesi/{id}/tolak', [AdminController::class, 'kirimTolakSesi'])->name('sesi.tolak.kirim');

        Route::get('/laporan', [LaporanController::class, 'laporanAdmin'])->name('laporan');
        Route::get('/laporan/{id}/laporan', [LaporanController::class, 'createLaporan'])->name('laporan.laporan');
        Route::post('/laporan/{id}/laporan', [LaporanController::class, 'storeLaporan'])->name('laporan.laporan.store');

        Route::get('/mahasiswa', [AdminController::class, 'mahasiswa'])->name('mahasiswa');
        Route::get('/jadwal/events', [AdminController::class, 'jadwalEvents'])->name('jadwal.events');


        Route::get('/kampus-api/mahasiswa', [KampusApiController::class, 'mahasiswa']);
        Route::get('/kampus-api/mahasiswa/{nim}', [KampusApiController::class, 'mahasiswaByNim']);
    });


Route::get('/test-mongodb', function () {
    try {
        $client = new MongoDB\Client(env('MONGODB_URI'));
        $database = $client->{env('MONGODB_DATABASE', 'monitoring')};
        $collections = $database->listCollections();
        
        $collectionList = [];
        foreach ($collections as $collection) {
            $collectionList[] = $collection->getName();
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Connected to MongoDB successfully',
            'database' => env('MONGODB_DATABASE', 'monitoring'),
            'collections' => $collectionList,
            'connection_uri' => preg_replace('/([a-zA-Z0-9]+):([^@]+)@/', '$1:****@', env('MONGODB_URI'))
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'MongoDB Connection Failed',
            'error' => $e->getMessage()
        ], 500);
    }
});

