<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="Cache-Control" content="no-store">
<title>Verifikasi Authenticator - Campus Care</title>
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
.codes{display:grid;grid-template-columns:1fr 1fr;gap:9px;margin:18px 0}.code{padding:10px;border:1px solid #dce9e2;border-radius:9px;text-align:center;font-family:monospace;font-weight:700}
.switch{margin-top:18px;padding-top:16px;border-top:1px solid #e8efeb}.switch summary{cursor:pointer;color:#075f47;text-align:center;font-weight:600;font-size:14px}
@media(max-width:420px){.codes{grid-template-columns:1fr}.brand span{display:none}.auth-page{padding-top:82px}.card{border-radius:18px}}
</style>
</head>
<body>
<div class="brand"><img src="{{ asset('img/logo.png') }}" alt=""><div><strong>Campus Care</strong><span>Bimbingan & Konseling Digital</span></div></div>
<main class="auth-page"><section class="card">
<h1>Verifikasi Authenticator</h1>
<p class="lead">Buka Google Authenticator yang terhubung ke akun Google Anda, lalu masukkan kode 6 digit Campus Care.</p>
@if($errors->any())<div class="error">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('two-factor.verify') }}">@csrf
<label for="code">Kode Authenticator</label>
<input class="input" id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" autofocus required>
<button class="btn" type="submit">Verifikasi dan Masuk</button>
</form>
<p class="lead" style="margin-top:18px;margin-bottom:0">Kehilangan akses ke Authenticator? Hubungi administrator kampus untuk verifikasi identitas.</p>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="muted-btn" style="border:0;background:none;width:100%;cursor:pointer" type="submit">Keluar dari akun ini</button></form>
</section></main></body></html>
