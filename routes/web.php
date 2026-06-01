<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ChatMahasiswaController;
use App\Http\Controllers\ChatAdminController;
use App\Http\Controllers\GroupChatAdminController;
use App\Http\Controllers\GroupChatMahasiswaController;
use App\Http\Controllers\KampusApiController;
use App\Http\Controllers\CounselorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SesiKonselingController;
use App\Http\Controllers\KetidaktersediaanKonselorController;
use App\Http\Controllers\PushSubscriptionController;

// ═══════════════════════════════
// NOTIFIKASI WEB PUSH
// ═══════════════════════════════
Route::post('/subscriptions', [PushSubscriptionController::class, 'update']);
Route::post('/subscriptions/delete', [PushSubscriptionController::class, 'destroy']);

// HALAMAN PUBLIK
// ═══════════════════════════════
Route::get('/', function () {
    return view('Pages.beranda');
})->name('beranda');

Route::get('/edukasi-mental', [EducationController::class, 'show'])
    ->name('edukasi.mental');

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
    Route::get('/riwayat/{id}', [LaporanController::class, 'detailRiwayat'])->name('detail.riwayat');
    Route::post('/notifikasi/baca', [ProfilController::class, 'markNotificationsAsRead'])->name('notifikasi.baca');
    Route::get('/chat', [ChatMahasiswaController::class, 'index'])->name('mahasiswa.chat');
    Route::post('/chat/mulai', [ChatMahasiswaController::class, 'start'])->name('mahasiswa.chat.start');
    Route::get('/chat/pesan', [ChatMahasiswaController::class, 'messages'])->name('mahasiswa.chat.messages');
    Route::post('/chat/pesan', [ChatMahasiswaController::class, 'store'])->name('mahasiswa.chat.store');
    Route::patch('/chat/pesan/{chat}', [ChatMahasiswaController::class, 'update'])->name('mahasiswa.chat.update');
    Route::delete('/chat/pesan/{chat}', [ChatMahasiswaController::class, 'destroy'])->name('mahasiswa.chat.destroy');
    Route::get('/group-chat', [GroupChatMahasiswaController::class, 'index'])->name('mahasiswa.group-chat');
    Route::get('/group-chat/buat', [GroupChatMahasiswaController::class, 'create'])->name('mahasiswa.group-chat.create');
    Route::post('/group-chat/gabung', [GroupChatMahasiswaController::class, 'join'])->name('mahasiswa.group-chat.join');
    Route::get('/group-chat/room/{group}', [GroupChatMahasiswaController::class, 'room'])->name('mahasiswa.group-chat.room');
    Route::get('/group-chat/pesan', [GroupChatMahasiswaController::class, 'messages'])->name('mahasiswa.group-chat.messages');
    Route::post('/group-chat/pesan', [GroupChatMahasiswaController::class, 'store'])->name('mahasiswa.group-chat.store');
    Route::patch('/group-chat/pesan/{message}', [GroupChatMahasiswaController::class, 'update'])->name('mahasiswa.group-chat.update');
    Route::delete('/group-chat/pesan/{message}', [GroupChatMahasiswaController::class, 'destroy'])->name('mahasiswa.group-chat.destroy');

    // Chat - Mahasiswa dapat melakukan chat dengan konselor
    Route::get('/chat/{jadwalId}', [ChatController::class, 'studentSession'])->name('chat.student');
    Route::post('/chat/{jadwalId}', [ChatController::class, 'studentStore'])->name('chat.student.store');

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
        Route::get('/', fn() => redirect()->route('admin.dashboard'));

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notifikasi', [AdminController::class, 'notifications'])->name('notifikasi.list');
        Route::post('/notifikasi/baca', [AdminController::class, 'markNotificationsAsRead'])->name('notifikasi.baca');

        Route::get('/jadwal', [AdminController::class, 'jadwal'])->name('jadwal');
        Route::post('/jadwal/{id}/setujui', [AdminController::class, 'setujui'])->name('jadwal.setujui');
        Route::post('/jadwal/{id}/tolak', [AdminController::class, 'tolak'])->name('jadwal.tolak');

        Route::get('/chat', [ChatAdminController::class, 'index'])->name('chat');
        Route::post('/chat/mulai', [ChatAdminController::class, 'start'])->name('chat.start');
        Route::get('/chat/pesan', [ChatAdminController::class, 'messages'])->name('chat.messages');
        Route::post('/chat/pesan', [ChatAdminController::class, 'store'])->name('chat.store');
        Route::get('/chat/{sessionId}', [ChatController::class, 'session'])->name('chat.session');
        Route::post('/chat/{sessionId}', [ChatController::class, 'store'])->name('chat.session.store');
        Route::patch('/chat/pesan/{chat}', [ChatAdminController::class, 'update'])->name('chat.update');
        Route::delete('/chat/pesan/{chat}', [ChatAdminController::class, 'destroy'])->name('chat.destroy');
        Route::get('/group-chat', [GroupChatAdminController::class, 'index'])->name('group-chat');
        Route::get('/group-chat/pesan', [GroupChatAdminController::class, 'messages'])->name('group-chat.messages');
        Route::post('/group-chat/pesan', [GroupChatAdminController::class, 'store'])->name('group-chat.store');
        Route::patch('/group-chat/pesan/{message}', [GroupChatAdminController::class, 'update'])->name('group-chat.update');
        Route::delete('/group-chat/pesan/{message}', [GroupChatAdminController::class, 'destroy'])->name('group-chat.destroy');

        Route::get('/sesi', [SesiKonselingController::class, 'index'])->name('sesi');
        Route::get('/sesi/{id}', [SesiKonselingController::class, 'detail'])->name('sesi.detail');
        Route::post('/sesi/{id}/terima', [SesiKonselingController::class, 'terima'])->name('sesi.terima');
        Route::get('/sesi/{id}/tolak', [SesiKonselingController::class, 'tolak'])->name('sesi.tolak');
        Route::post('/sesi/{id}/tolak', [SesiKonselingController::class, 'kirimTolak'])->name('sesi.tolak.kirim');
        Route::post('/sesi/{id}/selesai', [SesiKonselingController::class, 'selesai'])->name('sesi.selesai');

        Route::get('/laporan', [LaporanController::class, 'laporanAdmin'])->name('laporan');
        Route::get('/laporan/search', [LaporanController::class, 'search'])->name('laporan.search');
        Route::get('/laporan/mahasiswa/{mahasiswa}', [LaporanController::class, 'showMahasiswaLaporan'])->name('laporan.mahasiswa');
        Route::post('/laporan/mahasiswa/{mahasiswa}/ai-summary', [LaporanController::class, 'generateAiSummary'])->name('laporan.ai-summary');
        Route::get('/laporan/{id}/laporan', [LaporanController::class, 'createLaporan'])->name('laporan.laporan');
        Route::post('/laporan/{id}/laporan', [LaporanController::class, 'storeLaporan'])->name('laporan.laporan.store');

        Route::get('/mahasiswa', [AdminController::class, 'mahasiswa'])->name('mahasiswa');

        Route::get('/jadwal/events', [AdminController::class, 'jadwalEvents'])->name('jadwal.events');
        Route::get('/jadwal/data', [CounselorController::class, 'getJadwalData'])->name('jadwal.data');

        Route::get('/kampus-api/mahasiswa', [KampusApiController::class, 'mahasiswa']);
        Route::get('/kampus-api/mahasiswa/{nim}', [KampusApiController::class, 'mahasiswaByNim']);
    });



Route::middleware(['auth', 'role:konselor'])->group(function () {
    Route::post('/konselor/ketidaktersediaan', [KetidaktersediaanKonselorController::class, 'store'])
        ->name('konselor.ketidaktersediaan.store');

    Route::delete('/konselor/ketidaktersediaan/{id}', [KetidaktersediaanKonselorController::class, 'destroy'])
        ->name('konselor.ketidaktersediaan.destroy');
    
    Route::put('/admin/ketidaktersediaan/{id}', [KetidaktersediaanKonselorController::class, 'update'])
        ->name('admin.ketidaktersediaan.update');
});


// ═══════════════════════════════
// TEST MONGODB
// ═══════════════════════════════
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
            'connection_uri' => preg_replace('/([a-zA-Z0-9]+):([^@]+)@/', '$1:****@', env('MONGODB_URI')),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'MongoDB Connection Failed',
            'error' => $e->getMessage(),
        ], 500);
    }
});

// ═══════════════════════════════
// KONSELOR Web
// ═══════════════════════════════
Route::get('/konselor/jadwal-data', [CounselorController::class, 'getJadwalData'])->name('counselor.web.jadwal-data');


// ═══════════════════════════════
// KONSELOR PA3
// ═══════════════════════════════
Route::get('/konselor/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('counselor.dashboard');
Route::get('/konselor/prioritas', [\App\Http\Controllers\DashboardController::class, 'prioritas'])->name('counselor.prioritas');
Route::get('/konselor/laporan-tren', [\App\Http\Controllers\DashboardController::class, 'laporanTren'])->name('counselor.laporan-tren');
Route::get('/konselor/semua-mahasiswa', [\App\Http\Controllers\DashboardController::class, 'semuaMahasiswa'])->name('counselor.semua-mahasiswa');
Route::post('/konselor/update-status/{nim}', [\App\Http\Controllers\DashboardController::class, 'updateStatus'])->name('counselor.update-status');
Route::post('/konselor/kirim-notifikasi/{nim}', [\App\Http\Controllers\DashboardController::class, 'sendCustomNotification'])->name('counselor.send-notification');
Route::get('/konselor/chart-data', [DashboardController::class, 'getChartData'])->name('counselor.chart-data');
Route::get('/konselor/top-students', [\App\Http\Controllers\DashboardController::class, 'getStudentPreview'])->name('counselor.top-students');
Route::get('/konselor/notifications', [\App\Http\Controllers\DashboardController::class, 'getUrgentNotifications'])->name('counselor.notifications');
Route::post('/konselor/notifications/{nim}/read', [\App\Http\Controllers\DashboardController::class, 'markUrgentRead'])->name('counselor.notifications.read');
Route::get('/konselor/feeling-distribution', [\App\Http\Controllers\DashboardController::class, 'getFeelingDistribution'])->name('counselor.feeling-distribution');
Route::get('/konselor/detail/{nim}', [\App\Http\Controllers\DashboardController::class, 'showDetail'])->name('counselor.detail');
Route::post('/konselor/scan', [\App\Http\Controllers\DashboardController::class, 'scanLevel3'])->name('counselor.scan');
Route::post('/konselor/summary', [\App\Http\Controllers\DashboardController::class, 'getSummary'])->name('counselor.summary');


// ═══════════════════════════════
// FITUR EDUKASI
// ═══════════════════════════════
Route::prefix('konselor/edukasi')
    ->name('counselor.education.')
    ->middleware(['auth', 'role:konselor'])
    ->group(function () {
        Route::get('/', [EducationController::class, 'index'])->name('index');

        // ABOUT PAGE
        Route::get('/about-page', [EducationController::class, 'aboutPageEdit'])->name('about-page.edit');
        Route::put('/about-page', [EducationController::class, 'aboutPageUpdate'])->name('about-page.update');

        // MODULES MOBILE
        Route::get('/modules', [EducationController::class, 'moduleIndex'])->name('modules.index');
        Route::get('/modules/create', [EducationController::class, 'moduleCreate'])->name('modules.create');
        Route::post('/modules', [EducationController::class, 'moduleStore'])->name('modules.store');
        Route::get('/modules/{module}/edit', [EducationController::class, 'moduleEdit'])->name('modules.edit');
        Route::put('/modules/{module}', [EducationController::class, 'moduleUpdate'])->name('modules.update');
        Route::delete('/modules/{module}', [EducationController::class, 'moduleDestroy'])->name('modules.destroy');

        // CHALLENGES MOBILE
        Route::get('/challenges', [EducationController::class, 'challengeIndex'])->name('challenges.index');
        Route::get('/challenges/create', [EducationController::class, 'challengeCreate'])->name('challenges.create');
        Route::post('/challenges', [EducationController::class, 'challengeStore'])->name('challenges.store');
        Route::get('/challenges/{challenge}/edit', [EducationController::class, 'challengeEdit'])->name('challenges.edit');
        Route::put('/challenges/{challenge}', [EducationController::class, 'challengeUpdate'])->name('challenges.update');
        Route::delete('/challenges/{challenge}', [EducationController::class, 'challengeDestroy'])->name('challenges.destroy');

        // TREND TOPIK / KONTEN EDUKASI WEB
        Route::get('/web-contents', [EducationController::class, 'webContentIndex'])->name('web-contents.index');
        Route::get('/web-contents/create', [EducationController::class, 'webContentCreate'])->name('web-contents.create');
        Route::post('/web-contents', [EducationController::class, 'webContentStore'])->name('web-contents.store');
        Route::get('/web-contents/{id}/edit', [EducationController::class, 'webContentEdit'])->name('web-contents.edit');
        Route::put('/web-contents/{id}', [EducationController::class, 'webContentUpdate'])->name('web-contents.update');
        Route::delete('/web-contents/{id}', [EducationController::class, 'webContentDestroy'])->name('web-contents.destroy');
    });


// ═══════════════════════════════
// DEBUG ROUTE UNTUK PENGETESAN
// HAPUS SETELAH TIDAK DIGUNAKAN
// ═══════════════════════════════
Route::get('/debug/seed-if001', function () {
    $nim = 'IF-001';

    \App\Models\Student::updateOrCreate(['nim' => $nim], [
        'name' => 'Mahasiswa Test Predictive',
        'gender' => 'Laki-laki',
        'prodi' => 'IF',
        'tingkatan' => '2023',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'mental_level' => null,
    ]);

    \App\Models\DailyCheckin::where('nim', $nim)->delete();
    \App\Models\JournalText::where('nim', $nim)->delete();

    for ($i = 13; $i >= 0; $i--) {
        $date = now()->subDays($i);

        \App\Models\DailyCheckin::create([
            'nim' => $nim,
            'mood_id' => 2,
            'feeling_id' => 11,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    \App\Models\JournalText::create([
        'nim' => $nim,
        'description' => 'Hari ini saya hanya makan nasi goreng di kantin. Suasananya biasa saja.',
        'created_at' => now(),
    ]);
    \App\Http\Controllers\CounselorController::classifyAndSave($nim);
    // 4. Jalankan AI Klasifikasi
    \App\Http\Controllers\DashboardController::classifyAndSave($nim);

    return "Data dummy if001 (14 Hari Negatif) berhasil dibuat. Jurnal diset 'Aman'. Silakan cek Dashboard Konselor.";
});
