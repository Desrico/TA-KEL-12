<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationController extends Controller
{
    public function setup(Request $request, Google2FA $google2fa): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role !== 'mahasiswa') {
            return redirect()->route('login');
        }

        if ($user->hasTwoFactorAuthentication()) {
            return redirect()->route('two-factor.challenge');
        }

        $secret = $request->session()->get('two_factor_setup_secret');
        if (! $secret) {
            $secret = $google2fa->generateSecretKey(32);
            $request->session()->put('two_factor_setup_secret', $secret);
        }

        $uri = $google2fa->getQRCodeUrl('Campus Care', $user->username_cis ?: $user->email, $secret);
        $renderer = new ImageRenderer(new RendererStyle(240, 1), new SvgImageBackEnd());
        $qrSvg = (new Writer($renderer))->writeString($uri);
        $showIntro = ! $request->session()->has('two_factor_intro_seen');
        $request->session()->put('two_factor_intro_seen', true);

        return view('auth.two-factor-setup', compact('secret', 'qrSvg', 'showIntro'));
    }

    public function confirm(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validate(['code' => ['required', 'digits:6']], [
            'code.digits' => 'Kode Authenticator harus terdiri dari 6 digit.',
        ]);

        $secret = $request->session()->get('two_factor_setup_secret');
        if (! $secret || ! $google2fa->verifyKey($secret, $validated['code'], 2)) {
            return back()->withErrors([
                'code' => 'Kode tidak cocok. Aktifkan tanggal dan waktu otomatis di HP, hapus entri Campus Care lama di Authenticator, lalu pindai QR baru.',
            ]);
        }

        $user->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => now(),
            'two_factor_last_used_step' => null,
        ])->save();

        $request->session()->forget('two_factor_setup_secret');
        $request->session()->put('two_factor_verified_at', now()->toIso8601String());
        return redirect()->route('dashboard')
            ->with('success', 'Authenticator berhasil diaktifkan.');
    }

    public function challenge(Request $request): View|RedirectResponse
    {
        if (! $request->user()->hasTwoFactorAuthentication()) {
            return redirect()->route('two-factor.setup');
        }

        if ($request->session()->has('two_factor_verified_at')) {
            return redirect()->route('dashboard');
        }

        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $validated = $request->validate(['code' => ['required', 'digits:6']]);
        $user = $request->user();
        $step = $google2fa->verifyKeyNewer(
            $user->two_factor_secret,
            $validated['code'],
            $user->two_factor_last_used_step,
            2
        );

        if ($step === false) {
            return back()->withErrors(['code' => 'Kode tidak valid, kedaluwarsa, atau sudah pernah digunakan.']);
        }

        $user->forceFill(['two_factor_last_used_step' => $step])->save();
        $this->markVerified($request);

        return redirect()->intended(route('dashboard'));
    }

    public function regenerateSetup(Request $request): RedirectResponse
    {
        if ($request->user()->hasTwoFactorAuthentication()) {
            return redirect()->route('two-factor.challenge');
        }

        $request->session()->forget('two_factor_setup_secret');

        return redirect()->route('two-factor.setup')
            ->with('status', 'QR baru berhasil dibuat. Hapus entri Campus Care lama sebelum memindai ulang.');
    }

    private function markVerified(Request $request): void
    {
        $request->session()->regenerate();
        $request->session()->put('two_factor_verified_at', now()->toIso8601String());
    }
}
