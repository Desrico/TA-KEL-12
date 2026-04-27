<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\KampusApiController;
use App\Http\Controllers\CounselorController;
use App\Http\Controllers\EducationController;


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

// ═══════════════════════════════
// KONSELOR (PA3)
// ═══════════════════════════════
Route::get('/konselor/dashboard', [CounselorController::class, 'index'])->name('counselor.dashboard');
Route::get('/konselor/prioritas', [CounselorController::class, 'prioritas'])->name('counselor.prioritas');
Route::get('/konselor/semua-mahasiswa', [CounselorController::class, 'semuaMahasiswa'])->name('counselor.semua-mahasiswa');
Route::post('/konselor/update-status/{nim}', [CounselorController::class, 'updateStatus'])->name('counselor.update-status');
Route::get('/konselor/chart-data', [CounselorController::class, 'getChartData'])->name('counselor.chart-data');
Route::get('/konselor/top-students', [CounselorController::class, 'getStudentPreview'])->name('counselor.top-students');
Route::get('/konselor/notifications', [CounselorController::class, 'getUrgentNotifications'])->name('counselor.notifications');
Route::get('/konselor/feeling-distribution', [CounselorController::class, 'getFeelingDistribution'])->name('counselor.feeling-distribution');
Route::get('/konselor/detail/{nim}', [CounselorController::class, 'showDetail'])->name('counselor.detail');
Route::post('/konselor/scan', [CounselorController::class, 'scanLevel3'])->name('counselor.scan');
Route::post('/konselor/summary', [CounselorController::class, 'getSummary'])->name('counselor.summary');

// --- FITUR EDUKASI ---
Route::prefix('konselor/edukasi')->name('counselor.education.')->group(function () {
    Route::get('/', [EducationController::class, 'index'])->name('index');
    
    // Modules
    Route::get('/modules', [EducationController::class, 'moduleIndex'])->name('modules.index');
    Route::get('/modules/create', [EducationController::class, 'moduleCreate'])->name('modules.create');
    Route::post('/modules', [EducationController::class, 'moduleStore'])->name('modules.store');
    Route::get('/modules/{module}/edit', [EducationController::class, 'moduleEdit'])->name('modules.edit');
    Route::put('/modules/{module}', [EducationController::class, 'moduleUpdate'])->name('modules.update');
    Route::delete('/modules/{module}', [EducationController::class, 'moduleDestroy'])->name('modules.destroy');

    // Challenges
    Route::get('/challenges', [EducationController::class, 'challengeIndex'])->name('challenges.index');
    Route::get('/challenges/create', [EducationController::class, 'challengeCreate'])->name('challenges.create');
    Route::post('/challenges', [EducationController::class, 'challengeStore'])->name('challenges.store');
    Route::get('/challenges/{challenge}/edit', [EducationController::class, 'challengeEdit'])->name('challenges.edit');
    Route::put('/challenges/{challenge}', [EducationController::class, 'challengeUpdate'])->name('challenges.update');
    Route::delete('/challenges/{challenge}', [EducationController::class, 'challengeDestroy'])->name('challenges.destroy');
});

// --- DEBUG ROUTE UNTUK PENGETESAN (HAPUS SETELAH DIGUNAKAN) ---
Route::get('/debug/seed-if001', function () {
    $nim = 'IF-001';
    
    // 1. Buat/Update Mahasiswa
    \App\Models\Student::updateOrCreate(['nim' => $nim], [
        'name' => 'Mahasiswa Test Predictive',
        'gender' => 'Laki-laki',
        'prodi' => 'IF',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'mental_level' => null, 
    ]);

    // 2. Buat Check-in Negatif SEBANYAK 14 HARI (Untuk memicu Predictive Level 3)
    // Kita hapus data lama dulu agar bersih
    \App\Models\DailyCheckin::where('nim', $nim)->delete();
    \App\Models\JournalText::where('nim', $nim)->delete();

    for ($i = 13; $i >= 0; $i--) {
        $date = now()->subDays($i);
        \App\Models\DailyCheckin::create([
            'nim' => $nim,
            'mood_id' => 2, // Sedih
            'feeling_id' => 11, // Cemas
            'created_at' => $date,
            'updated_at' => $date
        ]);
    }

    // 3. Buat Jurnal AMAN (Agar terbukti Level 3 berasal dari TREN, bukan dari teks)
    \App\Models\JournalText::create([
        'nim' => $nim,
        'description' => 'Hari ini saya hanya makan nasi goreng di kantin. Suasananya biasa saja.',
        'created_at' => now(),
    ]);

    // 4. Jalankan AI Klasifikasi
    \App\Http\Controllers\CounselorController::classifyAndSave($nim);

    return "Data dummy if001 (14 Hari Negatif) berhasil dibuat. Jurnal diset 'Aman'. Silakan cek Dashboard Konselor.";
});