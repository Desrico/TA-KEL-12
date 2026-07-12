<?php

// CisAdminLoginTest.php adalah file untuk menjaga supaya bug login admin CIS ini tidak muncul lagi. Sebagai log testing saja dan tidak pengaruh ke fungsi yang penting dalam sistem.

use App\Models\Konselor;
use App\Models\User;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.kampus_api.base_url' => 'https://cis.test/api',
        'services.kampus_api.timeout' => 5,
        'services.kampus_api.admin.usernames' => 'malino.sihotang',
        'services.kampus_api.admin.emails' => '',
        'services.kampus_api.admin.names' => 'Malino Win Krisnando Sihotang, S.Tr.Kom',
        'services.kampus_api.admin.pegawai_ids' => '577',
        'services.kampus_api.admin.jabatan' => 'Asisten Akademik D4 TRPL',
        'services.kampus_api.admin.specialization' => 'Asisten Akademik D4 TRPL',
    ]);
});

test('admin CIS divalidasi menggunakan pegawai id dan jabatan', function () {
    $existingCounselorUser = User::create([
        'nama' => 'Konselor Lama',
        'email' => 'konselor.lama@example.test',
        'username_cis' => 'konselor.lama',
        'password' => bcrypt('password-lokal'),
        'role' => 'konselor',
    ]);
    $existingCounselor = Konselor::create([
        'user_id' => $existingCounselorUser->id,
        'spesialisasi' => 'Konselor Lama',
    ]);

    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 577,
                    'username' => 'malino.sihotang',
                    'nama' => 'Malino Win Krisnando Sihotang, S.Tr.Kom',
                    'jabatan' => 'Asisten Akademik D4 TRPL',
                ]],
            ],
        ]),
    ]);

    $response = $this->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
    expect(auth()->user()->role)->toBe('konselor');

    $admin = User::where('username_cis', 'malino.sihotang')->firstOrFail();
    $this->assertDatabaseHas('konselor', [
        'user_id' => $admin->id,
        'spesialisasi' => 'Asisten Akademik D4 TRPL',
    ]);
    $this->assertDatabaseHas('konselor', [
        'id' => $existingCounselor->id,
        'user_id' => $existingCounselorUser->id,
    ]);

    Http::assertSent(fn(HttpRequest $request) => str_starts_with(
        $request->url(),
        'https://cis.test/api/library-api/list-pejabat'
    ) && $request['pegawai_id'] === '577'
        && $request['jabatan'] === 'Asisten Akademik D4 TRPL'
        && $request->hasHeader('Authorization', 'Bearer token-cis'));
});

test('session konselor yang belum lengkap diarahkan kembali ke login', function () {
    $user = User::create([
        'nama' => 'Konselor Terputus',
        'email' => 'konselor.terputus@example.test',
        'username_cis' => 'malino.sihotang',
        'password' => bcrypt('password-lokal'),
        'role' => 'konselor',
    ]);
    Konselor::create([
        'user_id' => $user->id,
        'spesialisasi' => 'Asisten Akademik D4 TRPL',
    ]);

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

test('session konselor yang belum lengkap saat membuka beranda juga diputus', function () {
    $user = User::create([
        'nama' => 'Konselor Terputus',
        'email' => 'konselor.terputus@example.test',
        'username_cis' => 'malino.sihotang',
        'password' => bcrypt('password-lokal'),
        'role' => 'konselor',
    ]);
    Konselor::create([
        'user_id' => $user->id,
        'spesialisasi' => 'Asisten Akademik D4 TRPL',
    ]);

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('username');
    $this->assertGuest();
});

test('hasil pejabat CIS yang tidak cocok tidak diberi akses admin', function () {
    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 999,
                    'username' => 'malino.sihotang',
                    'nama' => 'Pegawai Lain',
                    'jabatan' => 'Jabatan Lain',
                ]],
            ],
        ]),
        'https://cis.test/api/library-api/mahasiswa*' => Http::response([
            'data' => ['mahasiswa' => []],
        ]),
    ]);

    $response = $this->from('/login')->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('username');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', [
        'username_cis' => 'malino.sihotang',
        'role' => 'konselor',
    ]);
});

test('admin CIS tetap valid jika row pejabat tidak punya username tetapi nama cocok longgar', function () {
    config([
        'services.kampus_api.admin.names' => 'Malino Win Krisnando',
    ]);

    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 577,
                    'nama' => 'Malino Win Krisnando Sihotang, S.Tr.Kom.',
                    'jabatan_id' => 256,
                    'jabatan' => 'Asisten Akademik D4 TRPL',
                ]],
            ],
        ]),
    ]);

    $response = $this->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'username_cis' => 'malino.sihotang',
        'nama' => 'Malino Win Krisnando Sihotang, S.Tr.Kom.',
        'role' => 'konselor',
    ]);
});

test('pegawai lain dengan jabatan sama tidak dapat dipasangkan ke username admin', function () {
    config([
        'services.kampus_api.admin.pegawai_ids' => '627',
        'services.kampus_api.admin.jabatan' => 'Asisten Akademik D4 TRPL',
        'services.kampus_api.admin.usernames' => 'malino.sihotang',
        'services.kampus_api.admin.names' => 'Malino Win Krisnando Sihotang, S.Tr.Kom',
    ]);

    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 627,
                    'nama' => 'Indah Chris Sarah Sinurat, S.Tr.Kom.',
                    'jabatan_id' => 256,
                    'jabatan' => 'Asisten Akademik D4 TRPL',
                ]],
            ],
        ]),
        'https://cis.test/api/library-api/mahasiswa*' => Http::response([
            'data' => ['mahasiswa' => []],
        ]),
    ]);

    $response = $this->from('/login')->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('username');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', [
        'username_cis' => 'malino.sihotang',
        'role' => 'konselor',
    ]);
});

test('pegawai id dari response login CIS harus sama dengan pegawai id admin', function () {
    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
            'data' => [
                'pegawai_id' => 627,
                'username' => 'malino.sihotang',
            ],
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 577,
                    'username' => 'malino.sihotang',
                    'nama' => 'Malino Win Krisnando Sihotang, S.Tr.Kom',
                    'jabatan' => 'Asisten Akademik D4 TRPL',
                ]],
            ],
        ]),
        'https://cis.test/api/library-api/mahasiswa*' => Http::response([
            'data' => ['mahasiswa' => []],
        ]),
    ]);

    $response = $this->from('/login')->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('username');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', [
        'username_cis' => 'malino.sihotang',
        'role' => 'konselor',
    ]);
});

test('admin CIS ditolak jika pegawai id atau sumber jabatan tidak dikonfigurasi', function () {
    config([
        'services.kampus_api.admin.pegawai_ids' => '',
        'services.kampus_api.admin.jabatan' => '',
        'services.kampus_api.admin.specialization' => '',
    ]);

    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 577,
                    'username' => 'malino.sihotang',
                    'nama' => 'Malino Win Krisnando Sihotang, S.Tr.Kom',
                    'jabatan' => 'Asisten Akademik D4 TRPL',
                ]],
            ],
        ]),
        'https://cis.test/api/library-api/mahasiswa*' => Http::response([
            'data' => ['mahasiswa' => []],
        ]),
    ]);

    $response = $this->from('/login')->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('username');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', [
        'username_cis' => 'malino.sihotang',
        'role' => 'konselor',
    ]);

    Http::assertNotSent(fn(HttpRequest $request) => str_starts_with(
        $request->url(),
        'https://cis.test/api/library-api/list-pejabat'
    ));
});

test('admin CIS memakai specialization sebagai fallback jika jabatan tidak dikonfigurasi', function () {
    config([
        'services.kampus_api.admin.jabatan' => '',
        'services.kampus_api.admin.specialization' => 'Asisten Akademik D4 TRPL',
    ]);

    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 577,
                    'username' => 'malino.sihotang',
                    'nama' => 'Malino Win Krisnando Sihotang, S.Tr.Kom',
                    'jabatan' => 'Asisten Akademik D4 TRPL',
                ]],
            ],
        ]),
    ]);

    $response = $this->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'username_cis' => 'malino.sihotang',
        'role' => 'konselor',
    ]);

    Http::assertSent(fn(HttpRequest $request) => str_starts_with(
        $request->url(),
        'https://cis.test/api/library-api/list-pejabat'
    ) && $request['pegawai_id'] === '577'
        && $request['jabatan'] === 'Asisten Akademik D4 TRPL');
});

test('admin CIS ditolak jika jabatan dan specialization berbeda', function () {
    config([
        'services.kampus_api.admin.jabatan' => 'Asisten Akademik D4 TRPL',
        'services.kampus_api.admin.specialization' => 'Staf Kemahasiswaan',
    ]);

    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::response([
            'data' => [
                'pejabat' => [[
                    'pegawai_id' => 577,
                    'username' => 'malino.sihotang',
                    'nama' => 'Malino Win Krisnando Sihotang, S.Tr.Kom',
                    'jabatan' => 'Asisten Akademik D4 TRPL',
                ]],
            ],
        ]),
        'https://cis.test/api/library-api/mahasiswa*' => Http::response([
            'data' => ['mahasiswa' => []],
        ]),
    ]);

    $response = $this->from('/login')->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('username');
    $this->assertGuest();

    Http::assertNotSent(fn(HttpRequest $request) => str_starts_with(
        $request->url(),
        'https://cis.test/api/library-api/list-pejabat'
    ));
});

test('admin terkonfigurasi tetap dapat login ketika endpoint pejabat CIS timeout', function () {
    Http::fake([
        'https://cis.test/api/jwt-api/do-auth' => Http::response([
            'token' => 'token-cis',
        ]),
        'https://cis.test/api/library-api/list-pejabat*' => Http::failedConnection(),
    ]);

    $response = $this->post('/login', [
        'username' => 'malino.sihotang',
        'password' => 'password-cis',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'username_cis' => 'malino.sihotang',
        'role' => 'konselor',
    ]);
    $this->assertDatabaseHas('konselor', [
        'spesialisasi' => 'Asisten Akademik D4 TRPL',
    ]);
});
