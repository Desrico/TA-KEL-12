<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use PragmaRX\Google2FA\Google2FA;

function studentForTwoFactor(): User
{
    return User::create([
        'nama' => 'Mahasiswa MFA',
        'email' => fake()->unique()->safeEmail(),
        'username_cis' => fake()->unique()->userName(),
        'password' => Hash::make('password-cis'),
        'role' => 'mahasiswa',
    ]);
}

test('mahasiswa dapat mengonfirmasi authenticator dengan OTP yang valid', function () {
    $user = studentForTwoFactor();
    $google2fa = app(Google2FA::class);
    $secret = $google2fa->generateSecretKey(32);

    $response = $this->actingAs($user)
        ->withSession(['two_factor_setup_secret' => $secret])
        ->post(route('two-factor.confirm'), [
            'code' => $google2fa->getCurrentOtp($secret),
        ]);

    $response->assertRedirect(route('dashboard'));
    $user->refresh();
    expect($user->hasTwoFactorAuthentication())->toBeTrue()
        ->and($user->two_factor_recovery_codes)->toBeNull();
});

test('halaman setup menampilkan QR authenticator secara lokal', function () {
    $user = studentForTwoFactor();

    $this->actingAs($user)
        ->get(route('two-factor.setup'))
        ->assertOk()
        ->assertSee('Hubungkan Authenticator')
        ->assertSee('Google LLC yang tersedia di Play Store')
        ->assertSee('img/google-authenticator.png')
        ->assertSee('Amankan akun Anda')
        ->assertSee('Aktifkan Google Authenticator sebagai verifikasi tambahan untuk membantu melindungi akun dan data konseling Anda.')
        ->assertDontSee('PIN keamanan lama')
        ->assertSee('<svg', false);
});

test('QR setup dapat diganti dan secret lama dibuang dari session', function () {
    $user = studentForTwoFactor();

    $this->actingAs($user)
        ->withSession(['two_factor_setup_secret' => 'JBSWY3DPEHPK3PXP'])
        ->post(route('two-factor.setup.regenerate'))
        ->assertRedirect(route('two-factor.setup'))
        ->assertSessionMissing('two_factor_setup_secret');
});

test('password saja tidak menyediakan endpoint reset faktor kedua', function () {
    expect(Route::has('security-pin.reset.show'))->toBeFalse()
        ->and(Route::has('security-pin.reset.submit'))->toBeFalse();
});

test('route recovery code tidak tersedia', function () {
    expect(Route::has('two-factor.recover'))->toBeFalse()
        ->and(Route::has('two-factor.recovery-codes'))->toBeFalse();
});
