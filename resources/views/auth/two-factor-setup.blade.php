<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Cache-Control" content="no-store">
<title>Aktivasi Authenticator - Campus Care</title>
@vite(['resources/css/app.css','resources/js/app.js'])
<style>
*{box-sizing:border-box}body{margin:0;font-family:Poppins,Arial,sans-serif;color:#172033;background:#f4fbf7}
.auth-page{min-height:100svh;padding:88px 16px 32px;display:grid;place-items:center;background:linear-gradient(rgba(255,255,255,.72),rgba(255,255,255,.72)),url('{{ asset('img/bg.png') }}') center/cover}
.brand{position:absolute;top:24px;left:clamp(18px,4vw,48px);display:flex;align-items:center;gap:10px;color:#064e3b}.brand img{width:42px;height:42px;object-fit:contain}.brand strong{display:block;font-size:17px}.brand span{font-size:12px;color:#64748b}
.card{width:min(100%,520px);padding:clamp(20px,5vw,32px);background:#fff;border:1px solid #e3eee8;border-radius:22px;box-shadow:0 18px 45px rgba(15,77,55,.12)}
h1{margin:0 0 8px;text-align:center;font-size:clamp(21px,5vw,28px)}.lead{text-align:center;color:#64748b;line-height:1.6;margin:0 0 22px;font-size:14px}
label{display:block;font-size:14px;font-weight:600;margin:14px 0 7px}.input{width:100%;min-height:50px;border:1.5px solid #cbdad3;border-radius:12px;padding:11px 14px;font-size:18px;text-align:center;outline:0}.input:focus{border-color:#0f8c68;box-shadow:0 0 0 3px rgba(15,140,104,.12)}
.btn{width:100%;min-height:50px;margin-top:18px;border:0;border-radius:12px;background:#08795b;color:#fff;font-weight:700;font-size:15px;cursor:pointer}.btn:hover{background:#066348}.muted-btn{display:block;text-align:center;margin-top:14px;color:#64748b;text-decoration:none;font-size:14px}
.error{padding:11px 13px;margin-bottom:16px;border:1px solid #fecaca;border-radius:10px;background:#fef2f2;color:#b91c1c;font-size:13px}.note{padding:12px;border-radius:12px;background:#effaf5;color:#166044;font-size:13px;line-height:1.55}
.qr{display:grid;place-items:center;margin:16px auto;width:min(100%,260px);padding:10px;background:#fff;border:1px solid #dce9e2;border-radius:16px}.qr svg{display:block;width:100%;height:auto}.secret{overflow-wrap:anywhere;text-align:center;padding:10px;background:#f5f7f6;border-radius:9px;font-family:monospace;font-size:12px}
.authenticator-focus{margin:0 0 20px;text-align:center}
.authenticator-focus p{margin:0;color:#475569;font-size:14px;line-height:1.6}.authenticator-focus p strong{color:inherit;font-size:inherit;font-weight:700}
.auth-title-row{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:10px}.auth-title-row h1{margin:0}.authenticator-logo-link{display:inline-flex;flex:0 0 auto;text-decoration:none}.authenticator-logo{width:42px;height:42px;object-fit:contain;transition:transform .2s ease}.authenticator-logo-link:hover .authenticator-logo{transform:translateY(-2px) scale(1.04)}
.codes{display:grid;grid-template-columns:1fr 1fr;gap:9px;margin:18px 0}.code{padding:10px;border:1px solid #dce9e2;border-radius:9px;text-align:center;font-family:monospace;font-weight:700}
.switch{margin-top:18px;padding-top:16px;border-top:1px solid #e8efeb}.switch summary{cursor:pointer;color:#075f47;text-align:center;font-weight:600;font-size:14px}
.intro-overlay{position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(15,23,42,.42);backdrop-filter:blur(4px);animation:introBackdropIn .35s ease both}
.intro-box{position:relative;overflow:hidden;width:min(100%,440px);padding:24px 28px 28px;background:linear-gradient(180deg,#f8fffb 0,#fff 42%);border:1px solid rgba(255,255,255,.8);border-radius:24px;text-align:center;box-shadow:0 28px 70px rgba(15,23,42,.28);animation:introCardIn .48s cubic-bezier(.2,.8,.2,1) both}
.intro-box h2{margin:0 0 10px;font-size:22px}.intro-box p{margin:0;color:#64748b;font-size:14px;line-height:1.65}.intro-box .btn{margin-top:22px}
.security-scene{position:relative;width:220px;height:154px;margin:0 auto 5px;display:grid;place-items:center}
.security-orbit{position:absolute;width:150px;height:150px;border:1px solid rgba(8,121,91,.2);border-radius:50%;animation:securityOrbit 7s linear infinite}.security-orbit::before,.security-orbit::after{content:"";position:absolute;width:9px;height:9px;border-radius:50%;background:#60c4a4;box-shadow:0 0 0 6px rgba(96,196,164,.13)}.security-orbit::before{top:14px;right:20px}.security-orbit::after{bottom:17px;left:16px;background:#f3c85f;box-shadow:0 0 0 6px rgba(243,200,95,.14)}
.security-pulse{position:absolute;width:110px;height:110px;border-radius:50%;border:2px solid rgba(8,121,91,.24);animation:securityPulse 2.2s ease-out infinite}
.security-shield{position:relative;width:96px;height:112px;display:grid;place-items:center;background:linear-gradient(145deg,#bff1dc,#59bea0);clip-path:polygon(50% 0,92% 16%,86% 68%,70% 88%,50% 100%,30% 88%,14% 68%,8% 16%);filter:drop-shadow(0 15px 18px rgba(6,78,59,.2));animation:securityShieldIn .7s cubic-bezier(.2,.9,.2,1) .18s both,securityShieldFloat 3.2s ease-in-out 1s infinite}
.security-shield::before{content:"";position:absolute;inset:7px;background:linear-gradient(150deg,rgba(255,255,255,.5),rgba(255,255,255,.08));clip-path:inherit}
.security-lock{position:relative;z-index:2;width:42px;height:36px;margin-top:14px;border-radius:8px;background:#08795b;box-shadow:inset 0 2px 0 rgba(255,255,255,.2);animation:securityLockIn .45s ease .85s both}
.security-lock::before{content:"";position:absolute;left:9px;top:-23px;width:24px;height:27px;border:6px solid #08795b;border-bottom:0;border-radius:18px 18px 0 0;transform-origin:center bottom;animation:securityShackleClose .55s cubic-bezier(.2,.9,.2,1) .65s both}
.security-lock::after{content:"";position:absolute;left:50%;top:10px;width:6px;height:13px;border-radius:999px;background:#d9f7ea;transform:translateX(-50%)}
.data-node{position:absolute;width:24px;height:7px;border-radius:999px;background:#9edfc7;opacity:0}.data-node.one{left:3px;top:45px;animation:dataFlowLeft 2.5s ease 1s infinite}.data-node.two{right:2px;top:80px;animation:dataFlowRight 2.5s ease 1.45s infinite}.data-node.three{left:18px;bottom:22px;width:16px;animation:dataFlowLeft 2.5s ease 1.85s infinite}
.intro-overlay.is-closing{animation:introBackdropOut .25s ease both}.intro-overlay.is-closing .intro-box{animation:introCardOut .25s ease both}
@keyframes introBackdropIn{from{opacity:0}to{opacity:1}}
@keyframes introBackdropOut{from{opacity:1}to{opacity:0}}
@keyframes introCardIn{from{opacity:0;transform:translateY(28px) scale(.94)}to{opacity:1;transform:translateY(0) scale(1)}}
@keyframes introCardOut{from{opacity:1;transform:translateY(0) scale(1)}to{opacity:0;transform:translateY(16px) scale(.96)}}
@keyframes securityOrbit{to{transform:rotate(360deg)}}
@keyframes securityPulse{0%{transform:scale(.72);opacity:.8}100%{transform:scale(1.45);opacity:0}}
@keyframes securityShieldIn{from{opacity:0;transform:translateY(22px) scale(.72) rotate(-6deg)}to{opacity:1;transform:translateY(0) scale(1) rotate(0)}}
@keyframes securityShieldFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-5px)}}
@keyframes securityLockIn{from{opacity:0;transform:scale(.55)}to{opacity:1;transform:scale(1)}}
@keyframes securityShackleClose{from{transform:translateY(-7px) rotateY(55deg)}to{transform:translateY(0) rotateY(0)}}
@keyframes dataFlowLeft{0%{opacity:0;transform:translateX(-12px)}35%{opacity:1}70%,100%{opacity:0;transform:translateX(75px) scale(.55)}}
@keyframes dataFlowRight{0%{opacity:0;transform:translateX(12px)}35%{opacity:1}70%,100%{opacity:0;transform:translateX(-75px) scale(.55)}}
@media(prefers-reduced-motion:reduce){.intro-overlay,.intro-box,.intro-overlay.is-closing,.intro-overlay.is-closing .intro-box,.security-orbit,.security-pulse,.security-shield,.security-lock,.security-lock::before,.data-node{animation:none}}
@media(max-width:420px){.codes{grid-template-columns:1fr}.brand span{display:none}.auth-page{padding-top:82px}.card{border-radius:18px}}
</style>
</head>
<body>
@if($showIntro)
<div class="intro-overlay" id="authenticatorIntro" role="dialog" aria-modal="true" aria-labelledby="authenticatorIntroTitle">
    <div class="intro-box">
        <div class="security-scene" aria-hidden="true">
            <div class="security-orbit"></div>
            <div class="security-pulse"></div>
            <span class="data-node one"></span><span class="data-node two"></span><span class="data-node three"></span>
            <div class="security-shield"><div class="security-lock"></div></div>
        </div>
        <h2 id="authenticatorIntroTitle">Amankan akun Anda</h2>
        <p>Aktifkan Google Authenticator sebagai verifikasi tambahan untuk membantu melindungi akun dan data konseling Anda.</p>
        <button type="button" class="btn" onclick="closeAuthenticatorIntro()">Lanjutkan</button>
    </div>
</div>
@endif
<div class="brand"><img src="{{ asset('img/logo.png') }}" alt=""><div><strong>Campus Care</strong><span>Bimbingan & Konseling Digital</span></div></div>
<main class="auth-page"><section class="card">
<div class="auth-title-row">
    <a class="authenticator-logo-link" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" rel="noopener noreferrer" aria-label="Buka Google Authenticator resmi di Play Store">
        <img class="authenticator-logo" src="{{ asset('img/google-authenticator.png') }}" alt="Logo resmi Google Authenticator">
    </a>
    <h1>Hubungkan Authenticator</h1>
</div>
<div class="authenticator-focus">
    <p>Gunakan aplikasi resmi <strong>Google Authenticator</strong> dari Google LLC yang tersedia di Play Store.</p>
</div>
@if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
@if(session('status'))<div class="note" style="margin-bottom:16px">{{ session('status') }}</div>@endif
<div class="note"><strong>Sebelum memindai:</strong> buka Google Authenticator, masuk ke akun Google pribadi Anda, dan pastikan sinkronisasi aktif. Jangan gunakan mode “Gunakan tanpa akun” dan jangan bagikan QR atau kunci rahasia ini.</div>
<div class="qr">{!! $qrSvg !!}</div>
<p class="lead" style="margin-bottom:6px">Tidak dapat memindai? Masukkan kunci berikut secara manual:</p>
<div class="secret">{{ $secret }}</div>
<form method="POST" action="{{ route('two-factor.setup.regenerate') }}">@csrf
<button class="muted-btn" style="border:0;background:none;width:100%;cursor:pointer;color:#075f47" type="submit">Buat QR baru</button>
</form>
<form method="POST" action="{{ route('two-factor.confirm') }}">@csrf
<label for="code">Kode 6 digit dari Authenticator</label>
<input class="input" id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" autofocus required>
<button class="btn" type="submit">Aktifkan Authenticator</button>
</form><form method="POST" action="{{ route('logout') }}">@csrf<button class="muted-btn" style="border:0;background:none;width:100%;cursor:pointer" type="submit">Keluar dari akun ini</button></form>
</section></main>
<script>
function closeAuthenticatorIntro() {
    const overlay = document.getElementById('authenticatorIntro');
    if (!overlay) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        overlay.remove();
        return;
    }

    overlay.classList.add('is-closing');
    window.setTimeout(() => overlay.remove(), 260);
}
</script>
</body></html>
