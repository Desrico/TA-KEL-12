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
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\FeedbackMahasiswaController;
use App\Http\Controllers\TwoFactorAuthenticationController;
use App\Models\Feedback;

// ═══════════════════════════════
// NOTIFIKASI WEB PUSH
// ═══════════════════════════════
Route::post('/subscriptions', [PushSubscriptionController::class, 'update']);
Route::post('/subscriptions/delete', [PushSubscriptionController::class, 'destroy']);

// HALAMAN PUBLIK
// ═══════════════════════════════
Route::get('/', function () {
    $feedbacks = Feedback::with(['mahasiswa.user'])
    ->where('is_published', true)
    ->latest()
    ->take(12)
    ->get();

    return view('Pages.beranda', [
        'feedbacks' => $feedbacks,
    ]);
})->middleware('student.public')->name('beranda');

Route::get('/edukasi-mental', [EducationController::class, 'show'])
    ->middleware('student.public')
    ->name('edukasi.mental');

Route::get('/edukasi-mental/konten/{id}/materi', [EducationController::class, 'showMaterial'])
    ->middleware('student.public')
    ->name('edukasi.mental.material');

Route::get('/konseling', [JadwalController::class, 'create'])
    ->middleware('student.public')
    ->name('konseling');


// ═══════════════════════════════
// AUTH
// ═══════════════════════════════
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/two-factor/setup', [TwoFactorAuthenticationController::class, 'setup'])->name('two-factor.setup');
    Route::post('/two-factor/setup', [TwoFactorAuthenticationController::class, 'confirm'])
        ->middleware('throttle:5,1')->name('two-factor.confirm');
    Route::post('/two-factor/setup/regenerate', [TwoFactorAuthenticationController::class, 'regenerateSetup'])
        ->middleware('throttle:3,1')->name('two-factor.setup.regenerate');
    Route::get('/two-factor/challenge', [TwoFactorAuthenticationController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/two-factor/challenge', [TwoFactorAuthenticationController::class, 'verify'])
        ->middleware('throttle:5,1')->name('two-factor.verify');

});

Route::post('/notifikasi/{notifikasi}/baca', function (\App\Models\Notifikasi $notifikasi) {
    if ((int) $notifikasi->user_id !== (int) auth()->id()) {
        abort(403);
    }

    $notifikasi->status = 'dibaca';
    $notifikasi->save();

    return response()->json([
        'success' => true,
    ]);
})->middleware('auth')->name('notifikasi.baca');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
    Route::post('/profil', [ProfilController::class, 'update'])->name('profil.update');
});


// ═══════════════════════════════
// MAHASISWA
// ═══════════════════════════════
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', function () {
   $feedbacks = Feedback::with(['mahasiswa.user'])
    ->where('is_published', true)
    ->latest()
    ->take(10)
    ->get();

    return view('Pages.beranda', compact('feedbacks'));
})->name('dashboard');
    Route::get('/riwayat', [LaporanController::class, 'riwayat'])->name('riwayat');
    Route::post('/riwayat/{id}/batalkan', [LaporanController::class, 'batalkanPenjadwalan'])
        ->name('riwayat.batalkan');
    Route::get('/riwayat/{id}', [LaporanController::class, 'detailRiwayat'])->name('detail.riwayat');

    Route::post('/riwayat/feedback', [FeedbackMahasiswaController::class, 'store'])
    ->name('mahasiswa.feedback.store');

    Route::get('/konseling/jadwal-ulang/{id}', [JadwalController::class, 'editJadwalUlang'])
    ->name('konseling.jadwal_ulang.edit');
    Route::put('/konseling/jadwal-ulang/{id}', [JadwalController::class, 'updateJadwalUlang'])
    ->name('konseling.jadwal_ulang.update');
    
    Route::post('/notifikasi/baca-semua', [ProfilController::class, 'markNotificationsAsRead'])
    ->name('notifikasi.baca-semua');
    
    Route::get('/chat', [ChatMahasiswaController::class, 'index'])->name('mahasiswa.chat');
    Route::post('/chat/mulai', [ChatMahasiswaController::class, 'start'])->name('mahasiswa.chat.start');
    Route::get('/chat/pesan', [ChatMahasiswaController::class, 'messages'])->name('mahasiswa.chat.messages');
    Route::post('/chat/pesan', [ChatMahasiswaController::class, 'store'])->name('mahasiswa.chat.store');
    Route::patch('/chat/pesan/{chat}', [ChatMahasiswaController::class, 'update'])->name('mahasiswa.chat.update');
    Route::delete('/chat/pesan/{chat}', [ChatMahasiswaController::class, 'destroy'])->name('mahasiswa.chat.destroy');
    
    Route::get('/group-chat', [GroupChatMahasiswaController::class, 'index'])->name('mahasiswa.group-chat');
    Route::get('/group-chat/buat', [GroupChatMahasiswaController::class, 'create'])->name('mahasiswa.group-chat.create');
    Route::get('/group-chat/undangan/{token}', [GroupChatMahasiswaController::class, 'invitation'])->name('mahasiswa.group-chat.invite');
    Route::post('/group-chat/gabung', [GroupChatMahasiswaController::class, 'join'])->name('mahasiswa.group-chat.join');
    Route::get('/group-chat/room/{group}', [GroupChatMahasiswaController::class, 'room'])->name('mahasiswa.group-chat.room');
    Route::get('/group-chat/pesan', [GroupChatMahasiswaController::class, 'messages'])->name('mahasiswa.group-chat.messages');
    Route::post('/group-chat/pesan', [GroupChatMahasiswaController::class, 'store'])->name('mahasiswa.group-chat.store');
    Route::patch('/group-chat/pesan/{message}', [GroupChatMahasiswaController::class, 'update'])->name('mahasiswa.group-chat.update');
    Route::delete('/group-chat/pesan/{message}', [GroupChatMahasiswaController::class, 'destroy'])->name('mahasiswa.group-chat.destroy');
    Route::post('/group-chat/room/{group}/keluar', [GroupChatMahasiswaController::class, 'leave'])->name('mahasiswa.group-chat.leave');

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
        Route::get('/group-chat/mahasiswa/cari', [GroupChatAdminController::class, 'searchStudents'])->name('group-chat.students.search');
        Route::post('/group-chat/grup', [GroupChatAdminController::class, 'createRoom'])->name('group-chat.rooms.store');
        Route::patch('/group-chat/grup/{group}', [GroupChatAdminController::class, 'renameRoom'])->name('group-chat.rooms.update');
        Route::post('/group-chat/grup/{group}/undang', [GroupChatAdminController::class, 'inviteMembers'])->name('group-chat.rooms.invite');
        Route::post('/group-chat/grup/{group}/avatar', [GroupChatAdminController::class, 'updateRoomAvatar'])->name('group-chat.rooms.avatar');
        Route::delete('/group-chat/grup/{group}', [GroupChatAdminController::class, 'deleteRoom'])->name('group-chat.rooms.destroy');
        Route::delete('/group-chat/grup/{group}/members/{member}', [GroupChatAdminController::class, 'removeMember'])->name('group-chat.rooms.members.remove');
        Route::get('/group-chat/members', [GroupChatAdminController::class, 'members'])->name('group-chat.members');
        Route::get('/group-chat/pesan', [GroupChatAdminController::class, 'messages'])->name('group-chat.messages');
        Route::post('/group-chat/pesan', [GroupChatAdminController::class, 'store'])->name('group-chat.store');
        Route::patch('/group-chat/pesan/{message}', [GroupChatAdminController::class, 'update'])->name('group-chat.update');
        Route::delete('/group-chat/pesan/{message}', [GroupChatAdminController::class, 'destroy'])->name('group-chat.destroy');

        Route::get('/group-chat/members', [GroupChatAdminController::class, 'members'])->name('group-chat.members');
        Route::post('/group-chat/grup/{group}/avatar', [GroupChatAdminController::class, 'updateRoomAvatar'])->name('group-chat.rooms.avatar');

        Route::get('/riwayat-konseling', [SesiKonselingController::class, 'index'])->name('riwayat');
        Route::get('/riwayat-konseling/{id}', [SesiKonselingController::class, 'detail'])->name('riwayat.detail');
        Route::post('/riwayat-konseling/{id}/terima', [SesiKonselingController::class, 'terima'])->name('riwayat.terima');
        Route::get('/riwayat-konseling/{id}/tolak', [SesiKonselingController::class, 'tolak'])->name('riwayat.tolak');
        Route::post('/riwayat-konseling/{id}/tolak', [SesiKonselingController::class, 'kirimTolak'])->name('riwayat.tolak.kirim');
        Route::post('/riwayat-konseling/{id}/selesai', [SesiKonselingController::class, 'selesai'])->name('riwayat.selesai');

        Route::get('/laporan', [LaporanController::class, 'laporanAdmin'])->name('laporan');
        Route::get('/laporan/search', [LaporanController::class, 'search'])->name('laporan.search');
        Route::get('/laporan/mahasiswa/{mahasiswa}', [LaporanController::class, 'showMahasiswaLaporan'])->name('laporan.mahasiswa');
        Route::post('/laporan/mahasiswa/{mahasiswa}/ai-summary', [LaporanController::class, 'generateAiSummary'])->name('laporan.ai-summary');
        Route::get('/laporan/{id}/laporan', [LaporanController::class, 'createLaporan'])->name('laporan.laporan');
        Route::post('/laporan/{id}/laporan', [LaporanController::class, 'storeLaporan'])->name('laporan.laporan.store');

        Route::get('/mahasiswa', [AdminController::class, 'mahasiswa'])->name('mahasiswa');

        Route::get('/jadwal/events', [AdminController::class, 'jadwalEvents'])->name('jadwal.events');
        Route::get('/jadwal/data', [CounselorController::class, 'getJadwalData'])->name('jadwal.data');

        Route::patch('/feedback/{feedback}/publish', [DashboardController::class, 'publishFeedback'])
            ->name('feedback.publish');

        Route::patch('/feedback/{feedback}/unpublish', [DashboardController::class, 'unpublishFeedback'])
            ->name('feedback.unpublish');

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


//PREVIEW ERROR PAGES
Route::get('/preview-error/{code}', function ($code) {
    abort($code);
});

// TEMPORARY ROUTE TO SEED DATABASE
Route::get('/run-seed', function () {
    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
    return "Database berhasil di-seed! Silakan coba login dengan admin@gmail.com (password: admin123).";
});

// TEMPORARY ROUTE TO VIEW LOGS
Route::get('/logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (!file_exists($logFile)) {
        return "No logs found.";
    }
    // Get last 100 lines
    $lines = file($logFile);
    $lastLines = array_slice($lines, -100);
    return response("<pre>" . implode("", $lastLines) . "</pre>");
});
