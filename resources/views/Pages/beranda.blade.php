@extends('layouts.master')

@push('styles')
<style>
/* ══════════════════════════════════════
   HERO
══════════════════════════════════════ */
.hero{
  position:relative;overflow:hidden;
  min-height:88vh;padding:5rem 0 4rem;
  background:linear-gradient(180deg,#f7faf6 0%,#edf4ef 100%);
}
.hero::before{
  content:'';position:absolute;inset:0;
  background-image:
    linear-gradient(rgba(66,99,74,.05) 1px,transparent 1px),
    linear-gradient(90deg,rgba(66,99,74,.05) 1px,transparent 1px);
  background-size:76px 76px;
  opacity:.45;
}
.hero::after{
  content:'';position:absolute;inset:0;
  background:radial-gradient(circle at 82% 18%,rgba(129,235,173,.22) 0,transparent 18%),
             radial-gradient(circle at 18% 78%,rgba(255,186,92,.16) 0,transparent 14%);
}
.hero-badge{
  display:inline-flex;align-items:center;gap:.5rem;
  background:#f8c98e;border:1px solid rgba(216,150,64,.15);
  color:#6c4a14;border-radius:999px;padding:.45rem .9rem;
  font-size:.78rem;font-weight:800;letter-spacing:.03em;text-transform:uppercase;
}
.hero-title{
  font-family:'Fraunces',serif;
  font-size:clamp(2.7rem,5.5vw,4.8rem);
  line-height:.92;font-weight:800;color:#263238;
  margin:1.3rem 0 1rem;
}
.hero-title .line-accent{
  color:#067b3e;
}
.hero-desc{
  font-size:1rem;color:#6f7c85;line-height:1.8;
  max-width:520px;margin-bottom:2rem;
}
.hero-actions{display:flex;gap:1rem;flex-wrap:wrap;}
.btn-hero-primary{
  background:#0a7a46;
  color:white;font-weight:800;border:none;border-radius:999px;
  padding:.9rem 1.8rem;font-size:.95rem;
  box-shadow:0 12px 24px rgba(10,122,70,.18);
}
.btn-hero-primary:hover{color:white;transform:translateY(-1px);box-shadow:0 16px 28px rgba(10,122,70,.22);}
.btn-hero-ghost{
  background:#dfe5e2;border:1.6px solid rgba(123,140,130,.08);
  color:#3c4a4d;border-radius:999px;padding:.88rem 1.75rem;
  font-size:.95rem;font-weight:700;
}
.btn-hero-ghost:hover{background:#d5ddd9;color:#243033;}
.hero-right{position:relative;display:flex;justify-content:center;align-items:center;}
.hero-figure{
  width:min(100%,420px);position:relative;
}
.hero-figure-card{
  position:relative;border-radius:28px;padding:1.4rem 1.4rem .85rem;
  background:linear-gradient(180deg,#cfd6b8 0%,#d9d0aa 100%);
  box-shadow:0 28px 60px rgba(86,111,83,.16);
}
.hero-figure-card::before{
  content:'';position:absolute;inset:14px 14px 28px 14px;border-radius:24px;
  background:radial-gradient(circle at 18% 16%,rgba(255,255,255,.5) 0,transparent 16%),
             radial-gradient(circle at 82% 17%,rgba(255,255,255,.34) 0,transparent 12%),
             linear-gradient(180deg,#f4ecd3 0%,#f6f0df 100%);
}
.hero-figure-card img{
  position:relative;z-index:1;width:100%;display:block;border-radius:18px;
  transform:translateY(4px);
}
.hero-badge-mini{
  position:absolute;left:-1rem;bottom:-1rem;z-index:2;
  width:86px;height:86px;border-radius:18px;background:#ffbf73;
  box-shadow:0 18px 28px rgba(193,140,64,.2);display:flex;flex-direction:column;
  align-items:center;justify-content:center;color:#6a4b15;line-height:1;
}
.hero-badge-mini strong{font-size:1.05rem;font-weight:900;}
.hero-badge-mini span{font-size:.62rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em;margin-top:.25rem;text-align:center;}
.hero-float-dot{
  position:absolute;width:12px;height:12px;border-radius:50%;border:3px solid #ff8b3d;background:#f7faf6;
}
.hero-float-dot.dot-1{top:14%;left:18%;}
.hero-float-dot.dot-2{top:30%;left:8%;}
.hero-float-dot.dot-3{top:18%;right:12%;}
.hero-float-dot.dot-4{bottom:28%;right:8%;}
.hero-mini-cloud{
  position:absolute;width:28px;height:18px;border-radius:999px;background:#fff;
  box-shadow:14px 0 0 0 #fff,-12px 2px 0 0 #fff;
  opacity:.92;
}
.hero-mini-cloud.cloud-1{top:19%;right:22%;}
.hero-mini-cloud.cloud-2{top:42%;left:17%;transform:scale(.75);}
.hero-mini-cloud.cloud-3{top:17%;left:36%;transform:scale(.62);}
.hero-floating-note{
  position:absolute;top:2.6rem;left:50%;transform:translateX(-50%);
  background:#fff;border-radius:16px;padding:.85rem 1rem;box-shadow:0 14px 28px rgba(0,0,0,.1);
  font-weight:800;color:#2d3640;display:flex;align-items:center;gap:.6rem;
}
.hero-floating-note .dot{width:10px;height:10px;border-radius:50%;background:#1cc58b;}
.hero-floating-note.bottom{top:auto;right:-1.1rem;left:auto;bottom:4rem;transform:none;}
.hero-card{
  position:relative;z-index:1;background:transparent;border:none;border-radius:0;padding:0;
  backdrop-filter:none;box-shadow:none;
}
.hero-chip{
  position:absolute;display:inline-flex;align-items:center;gap:.55rem;
  background:#f6f7f6;color:#25303a;border-radius:16px;padding:.72rem 1rem;
  font-weight:700;box-shadow:0 14px 28px rgba(0,0,0,.12);
}
.hero-chip.top{top:1rem;left:50%;transform:translateX(-50%);}
.hero-chip.right{right:-2rem;bottom:2rem;}
.hero-chip .dot{width:10px;height:10px;border-radius:50%;background:#19c386;display:inline-block;}
.hero-chip .lock{color:#2f6df6;}
.hero-card-head{display:flex;align-items:center;gap:1rem;margin-bottom:2rem;}
.hero-avatar{width:56px;height:56px;border-radius:50%;background:#19a76e;display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;}
.hero-name{font-weight:800;color:#f8fbf8;font-size:1.1rem;line-height:1.1;}
.hero-role{font-size:.92rem;color:rgba(255,255,255,.56);}
.hero-status{margin-left:auto;display:flex;align-items:center;gap:.45rem;color:#1bc686;font-weight:700;}
.hero-message{background:rgba(255,255,255,.12);color:rgba(255,255,255,.92);border-radius:18px;padding:1rem 1.1rem;max-width:78%;font-size:1rem;line-height:1.6;}
.hero-message.reply{background:#10b56b;margin-left:auto;}
.hero-time{font-size:.78rem;color:rgba(255,255,255,.35);margin:1rem 0 1.2rem;}
.hero-typing{display:inline-flex;align-items:center;gap:4px;padding:.6rem 1rem;background:rgba(255,255,255,.12);border-radius:999px;margin:0 0 1.2rem .2rem;}
.hero-typing span{width:7px;height:7px;border-radius:50%;background:rgba(255,255,255,.55);animation:typingBounce 1.2s ease infinite;}
.hero-typing span:nth-child(2){animation-delay:.15s;}
.hero-typing span:nth-child(3){animation-delay:.3s;}
.hero-endnote{background:#1d6c74;color:rgba(255,255,255,.85);border-radius:14px;padding:.95rem 1rem;text-align:center;font-size:.92rem;}
@keyframes typingBounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-4px)}}

@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}

@media (max-width: 991.98px){
  .hero{padding:4rem 0 3rem;min-height:auto;}
  .hero-right{margin-top:2.5rem;}
  .hero-chip.right{right:0;}
  .hero-floating-note.bottom{right:0;}
}
@media (max-width: 575.98px){
  .hero{padding:3rem 0 2.5rem;}
  .hero-figure-card{padding:1rem 1rem .7rem;}
  .hero-message{max-width:100%;font-size:.92rem;}
  .hero-title{font-size:clamp(2.3rem,13vw,3.2rem);}
  .hero-actions .btn{width:100%;justify-content:center;}
}

/* ══════════════════════════════════════
   WAVE DIVIDER
══════════════════════════════════════ */
.wave-divider{line-height:0;overflow:hidden;margin-top:-2px;}
.wave-divider svg{display:block;width:100%;}

/* ══════════════════════════════════════
   LAYANAN CARDS
══════════════════════════════════════ */
.service-grid-card{
  background:white;border-radius:var(--radius);padding:2rem 1.8rem;
  border:1px solid rgba(26,58,92,.07);position:relative;overflow:hidden;
  transition:all .3s;height:100%;
}
.service-grid-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:4px;
  background:var(--card-color,var(--primary));
  transform:scaleX(0);transform-origin:left;transition:.3s;
}
.service-grid-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-md);}
.service-grid-card:hover::before{transform:scaleX(1);}
.service-grid-card .s-icon{
  width:58px;height:58px;border-radius:16px;
  display:flex;align-items:center;justify-content:center;font-size:1.5rem;
  margin-bottom:1.3rem;
}
.service-grid-card h5{font-weight:700;font-size:1.05rem;margin-bottom:.5rem;}
.service-grid-card p{font-size:.87rem;color:var(--text-mid);line-height:1.68;margin:0;}
.service-grid-card .s-link{
  display:inline-flex;align-items:center;gap:.3rem;
  font-size:.82rem;font-weight:700;margin-top:1rem;text-decoration:none;
  color:var(--primary-light);transition:gap .2s;
}
.service-grid-card:hover .s-link{gap:.6rem;}

/* ══════════════════════════════════════
   KONSELOR SECTION
══════════════════════════════════════ */
.konselor-showcase{
  background:linear-gradient(135deg,var(--primary) 0%,var(--primary-mid) 100%);
  border-radius:28px;overflow:hidden;position:relative;
}
.konselor-showcase::before{
  content:'';position:absolute;inset:0;
  background:url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M0 0h80v80H0z'/%3E%3C/g%3E%3C/svg%3E");
}
.ks-photo{
  width:180px;height:180px;border-radius:50%;
  background:linear-gradient(135deg,rgba(255,255,255,.15),rgba(255,255,255,.05));
  border:4px solid rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;font-size:5rem;
  box-shadow:0 0 0 20px rgba(255,255,255,.05),0 0 0 40px rgba(255,255,255,.02);
  position:relative;
  animation:portraitPulse 4s ease-in-out infinite;
}
@keyframes portraitPulse{
  0%,100%{box-shadow:0 0 0 20px rgba(255,255,255,.05),0 0 0 40px rgba(255,255,255,.02);}
  50%{box-shadow:0 0 0 28px rgba(255,255,255,.07),0 0 0 55px rgba(255,255,255,.03);}
}
.ks-badge{
  display:inline-flex;align-items:center;gap:.4rem;
  background:rgba(15,184,122,.2);border:1px solid rgba(15,184,122,.4);
  color:#52e8a6;border-radius:50px;padding:.25rem .8rem;
  font-size:.73rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;
}
.ks-spec-tag{
  display:inline-flex;background:rgba(255,255,255,.12);
  color:rgba(255,255,255,.85);border-radius:50px;
  padding:.25rem .75rem;font-size:.78rem;font-weight:500;margin:.2rem;
}

/* ══════════════════════════════════════
   CARA KERJA
══════════════════════════════════════ */
.how-card{
  background:white;border-radius:var(--radius);padding:2rem 1.5rem;
  border:1px solid rgba(26,58,92,.07);text-align:center;
  position:relative;transition:all .3s;
}
.how-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-md);}
.how-num{
  font-family:'Fraunces',serif;font-size:3.5rem;font-weight:700;
  line-height:1;color:rgba(26,58,92,.06);position:absolute;top:.8rem;left:1rem;
}
.how-icon-wrap{
  width:64px;height:64px;border-radius:18px;margin:0 auto 1.2rem;
  display:flex;align-items:center;justify-content:center;font-size:1.8rem;
}
.how-card h6{font-weight:700;margin-bottom:.4rem;}
.how-card p{font-size:.85rem;color:var(--text-mid);line-height:1.65;margin:0;}
.connector{
  position:absolute;top:40%;right:-20px;z-index:1;
  color:rgba(26,58,92,.2);font-size:1.2rem;
}

/* ══════════════════════════════════════
   FEATURE BENTO
══════════════════════════════════════ */
.bento-grid{display:grid;grid-template-columns:repeat(3,1fr);grid-template-rows:auto;gap:1rem;}
.bento-card{background:white;border-radius:var(--radius);padding:1.8rem;border:1px solid rgba(26,58,92,.07);transition:all .3s;}
.bento-card:hover{box-shadow:var(--shadow-md);transform:translateY(-3px);}
.bento-card.tall{grid-row:span 2;}
.bento-card.wide{grid-column:span 2;}
.bento-card.dark{background:var(--primary);color:white;}
.bento-card.accent{background:linear-gradient(135deg,var(--accent),#0a9c64);color:white;}
.bento-icon{font-size:2rem;margin-bottom:.8rem;display:block;}
.bento-card h5{font-weight:700;font-size:1rem;margin-bottom:.4rem;}
.bento-card p{font-size:.84rem;line-height:1.65;opacity:.75;margin:0;}

/* ══════════════════════════════════════
   TESTIMONIAL
══════════════════════════════════════ */
.testi-wrap{
  background:white;border-radius:var(--radius);padding:2rem;
  border:1px solid rgba(26,58,92,.07);height:100%;
  position:relative;transition:all .3s;
}
.testi-wrap:hover{box-shadow:var(--shadow-md);transform:translateY(-4px);}
.testi-wrap::before{
  content:'\201C';font-family:'Fraunces',serif;font-size:5rem;
  color:rgba(46,134,193,.12);line-height:.8;
  position:absolute;top:.8rem;left:1.2rem;
}
.testi-stars{color:#f5a623;font-size:.85rem;margin-bottom:.8rem;}
.testi-text{font-size:.88rem;color:var(--text-mid);line-height:1.75;padding-top:.5rem;}
.testi-author{display:flex;align-items:center;gap:.7rem;margin-top:1.2rem;padding-top:1rem;border-top:1px solid rgba(26,58,92,.07);}
.t-avatar{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.9rem;flex-shrink:0;}

/* ══════════════════════════════════════
   CTA FINAL
══════════════════════════════════════ */
.cta-final{
  background:linear-gradient(135deg,#071825 0%,#0d2d4a 50%,#0e5c3d 100%);
  border-radius:28px;padding:4rem 2rem;text-align:center;
  position:relative;overflow:hidden;
}
.cta-final::before{
  content:'';position:absolute;inset:0;
  background:radial-gradient(circle at 50% 0%,rgba(15,184,122,.15) 0%,transparent 60%);
}
.cta-ring{
  position:absolute;border-radius:50%;border:1px solid rgba(255,255,255,.05);
}
</style>
@endpush

@section('konten')

<!-- ═══════════════ HERO ═══════════════ -->
<section class="hero">
  <div class="container position-relative" style="z-index:1">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="hero-badge">
          <span style="width:10px;height:10px;border-radius:50%;background:#0c8f5a;display:inline-block"></span>
          Kesehatan mental siswa
        </div>
        <h1 class="hero-title">
          Konsultasikan<br>
          Dirimu, <span class="line-accent">Percaya</span><br>
          <span class="line-accent">Mental Sehat</span>
        </h1>
        <p class="hero-desc">
          Langkah berani untuk masa depan yang lebih cerah. Del Care hadir sebagai teman setia perjalanan kesehatan mentalmu di kampus.
        </p>
        <div class="hero-actions">
          <a href="/layanan" class="btn btn-hero-primary">
            <i class="bi bi-calendar-check me-2"></i>Mulai Konsultasi
          </a>
          <a href="/about" class="btn btn-hero-ghost">
            <i class="bi bi-play-circle me-2"></i>Pelajari Lebih Lanjut
          </a>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="hero-right">
          <div class="hero-figure">
            <div class="hero-figure-card">
              <span class="hero-float-dot dot-1"></span>
              <span class="hero-float-dot dot-2"></span>
              <span class="hero-float-dot dot-3"></span>
              <span class="hero-float-dot dot-4"></span>
              <span class="hero-mini-cloud cloud-1"></span>
              <span class="hero-mini-cloud cloud-2"></span>
              <span class="hero-mini-cloud cloud-3"></span>
              <img src="{{ asset('img/dokter.png') }}" alt="Konselor BK IT Del">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WAVE -->
<div class="wave-divider">
  <svg viewBox="0 0 1440 60" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
    <path d="M0,30 C360,60 1080,0 1440,30 L1440,60 L0,60 Z" fill="#f0f5fa"/>
  </svg>
</div>

<!-- ═══════════════ MARQUEE TRUST BAR ═══════════════ -->
<div style="background:white;padding:1rem 0;border-bottom:1px solid rgba(26,58,92,.07);overflow:hidden;">
  <div style="display:flex;gap:3rem;animation:marqueeScroll 20s linear infinite;white-space:nowrap;align-items:center;">
    @foreach(['🔒 100% Rahasia','✅ Konselor Bersertifikat','💚 Gratis untuk Mahasiswa','⚡ Respon Cepat','📱 Akses Kapan Saja','🎓 Didukung IT Del','❤️ Berbasis Empati','🌿 Pendekatan Holistik','🔒 100% Rahasia','✅ Konselor Bersertifikat','💚 Gratis untuk Mahasiswa','⚡ Respon Cepat','📱 Akses Kapan Saja'] as $item)
    <span style="font-size:.82rem;font-weight:600;color:var(--text-mid);flex-shrink:0;">{{ $item }}</span>
    @endforeach
  </div>
</div>
<style>@keyframes marqueeScroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}</style>

<!-- ═══════════════ LAYANAN UTAMA ═══════════════ -->
<section class="container" style="margin-top:5rem">
  <div class="text-center mb-5">
    <div class="section-eyebrow"><i class="bi bi-stars"></i> Apa yang Kami Tawarkan</div>
    <h2 class="section-heading">Layanan Lengkap untuk<br>Kesejahteraanmu</h2>
  </div>

  <div class="row g-4">
    <div class="col-md-6 col-lg-3">
      <div class="service-grid-card" style="--card-color:var(--primary-light)">
        <div class="s-icon" style="background:rgba(46,134,193,.1)">💻</div>
        <h5>Konseling Online</h5>
        <p>Sesi konseling via video call atau chat bersama konselor profesional. Fleksibel dari mana saja.</p>
        <a href="/layanan#online" class="s-link">Booking Sekarang <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="service-grid-card" style="--card-color:var(--accent)">
        <div class="s-icon" style="background:rgba(15,184,122,.1)">🏛️</div>
        <h5>Konseling Offline</h5>
        <p>Tatap muka langsung dengan konselor di ruang BK kampus IT Del. Lebih personal dan mendalam.</p>
        <a href="/layanan#offline" class="s-link" style="color:var(--accent)">Buat Janji <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="service-grid-card" style="--card-color:var(--warm)">
        <div class="s-icon" style="background:rgba(245,166,35,.1)">📖</div>
        <h5>Self-Help Resources</h5>
        <p>Modul interaktif, artikel kesehatan mental, dan latihan mindfulness yang bisa kamu lakukan mandiri.</p>
        <a href="#" class="s-link" style="color:var(--warm)">Jelajahi <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="service-grid-card" style="--card-color:#e74c3c">
        <div class="s-icon" style="background:rgba(231,76,60,.1)">🚨</div>
        <h5>Hotline Darurat</h5>
        <p>Layanan 24/7 untuk situasi krisis. Tim siap mendampingi kapan kamu paling membutuhkan bantuan.</p>
        <a href="tel:119" class="s-link" style="color:#e74c3c">Hubungi Sekarang <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ KONSELOR SHOWCASE ═══════════════ -->
<section class="container" style="margin-top:5rem">
  <div class="konselor-showcase p-4 p-lg-5">
    <div class="row align-items-center g-5 position-relative" style="z-index:1">

      <!-- Photo -->
      <div class="col-lg-4 text-center">
        <div class="ks-photo mx-auto">👩‍⚕️</div>
        <div class="mt-4">
          <span class="ks-badge">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--accent);display:inline-block;animation:statusPulse 2s ease infinite"></span>
            Tersedia Sekarang
          </span>
        </div>
      </div>

      <!-- Info -->
      <div class="col-lg-8">
        <div class="section-eyebrow" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.8)">
          <i class="bi bi-person-badge"></i> Konselor Kami
        </div>
        <h2 style="font-family:'Fraunces',serif;font-weight:700;color:white;font-size:clamp(1.8rem,3vw,2.4rem);margin-bottom:.5rem">
          ibu laura M.Psi
        </h2>
        <p style="color:rgba(255,255,255,.6);font-size:.9rem;margin-bottom:1.5rem">
          Konselor Bimbingan dan Konseling · Institut Teknologi Del
        </p>
        <p style="color:rgba(255,255,255,.75);font-size:.92rem;line-height:1.8;max-width:560px;margin-bottom:1.5rem">
          Memiliki pengalaman lebih dari 8 tahun mendampingi mahasiswa teknologi dalam menghadapi tekanan akademik, kecemasan, dan tantangan pribadi. Berpendekatan hangat, non-judgemental, dan berbasis bukti ilmiah.
        </p>
        <div>
          <span class="ks-spec-tag"><i class="bi bi-mortarboard me-1"></i>S1 Psikologi Klinis</span>
          <span class="ks-spec-tag"><i class="bi bi-award me-1"></i>Lisensi BK Resmi</span>
          <span class="ks-spec-tag">Stres Akademik</span>
          <span class="ks-spec-tag">Kecemasan</span>
          <span class="ks-spec-tag">Bimbingan Karir</span>
          <span class="ks-spec-tag">Mindfulness</span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ═══════════════ CARA KERJA ═══════════════ -->
<section style="background:white;padding:5rem 0;margin-top:5rem;">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-eyebrow"><i class="bi bi-lightning-charge"></i> Proses Mudah</div>
      <h2 class="section-heading">4 Langkah Mulai Konseling</h2>
    </div>
    <div class="row g-4 position-relative">
      <!-- Connector line -->
      <div style="position:absolute;top:50%;left:10%;right:10%;height:2px;background:linear-gradient(90deg,var(--primary-light),var(--accent));opacity:.2;z-index:0;display:none;" class="d-none d-lg-block"></div>

      <div class="col-md-6 col-lg-3">
        <div class="how-card">
          <div class="how-num">01</div>
          <div class="how-icon-wrap" style="background:rgba(46,134,193,.1)">🔐</div>
          <h6>Login dengan Akun IT Del</h6>
          <p>Masuk menggunakan email mahasiswa IT Del kamu. Aman dan cepat tanpa perlu registrasi.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="how-card">
          <div class="how-num">02</div>
          <div class="how-icon-wrap" style="background:rgba(15,184,122,.1)">🎯</div>
          <h6>Pilih Jenis Layanan</h6>
          <p>Tentukan apakah kamu ingin konseling online (video/chat) atau offline (tatap muka di kampus).</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="how-card">
          <div class="how-num">03</div>
          <div class="how-icon-wrap" style="background:rgba(245,166,35,.1)">📅</div>
          <h6>Pilih Jadwal</h6>
          <p>Pilih hari dan jam yang sesuai dengan jadwal tersedia dari konselor. Senin–Jumat.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-3">
        <div class="how-card">
          <div class="how-num">04</div>
          <div class="how-icon-wrap" style="background:rgba(26,58,92,.08)">💬</div>
          <h6>Mulai Konseling</h6>
          <p>Terima konfirmasi dan mulailah sesi konseling. Perjalananmu menuju kesehatan mental dimulai.</p>
        </div>
      </div>
    </div>
    <div class="text-center mt-4">
      <a href="/layanan" class="btn btn-primary rounded-pill px-5 py-2">
        <i class="bi bi-arrow-right-circle me-2"></i>Mulai Sekarang
      </a>
    </div>
  </div>
</section>

<!-- ═══════════════ BENTO FEATURES ═══════════════ -->
<section class="container" style="margin-top:5rem">
  <div class="text-center mb-5">
    <div class="section-eyebrow"><i class="bi bi-grid"></i> Kenapa BK Connect?</div>
    <h2 class="section-heading">Dirancang Khusus<br>untuk Mahasiswa IT Del</h2>
  </div>

  <div class="bento-grid">
    <div class="bento-card dark">
      <span class="bento-icon">🔒</span>
      <h5 style="color:white">Privasi 100% Terjaga</h5>
      <p style="color:rgba(255,255,255,.6)">Semua sesi dienkripsi end-to-end. Identitas dan ceritamu tidak akan pernah bocor ke pihak manapun.</p>
    </div>
    <div class="bento-card accent wide">
      <span class="bento-icon">🎓</span>
      <h5 style="color:white;font-size:1.2rem">Gratis Untuk Seluruh Mahasiswa IT Del</h5>
      <p style="color:rgba(255,255,255,.8)">Seluruh layanan konseling disediakan sepenuhnya gratis sebagai fasilitas resmi kemahasiswaan IT Del tanpa terkecuali.</p>
    </div>
    <div class="bento-card">
      <span class="bento-icon">⚡</span>
      <h5>Respon Cepat</h5>
      <p>Konfirmasi booking dalam 2 jam kerja. Konseling bisa dimulai hari yang sama.</p>
    </div>
    <div class="bento-card wide">
      <span class="bento-icon">📊</span>
      <h5>Pantau Progresmu</h5>
      <p>Dashboard pribadi untuk melihat riwayat sesi, jurnal mood, dan perkembangan kesehatan mentalmu dari waktu ke waktu.</p>
    </div>
    <div class="bento-card">
      <span class="bento-icon">📱</span>
      <h5>Akses dari Mana Saja</h5>
      <p>Konseling tetap berjalan walau kamu di asrama, pulang kampung, atau sedang KKN.</p>
    </div>
  </div>
</section>

<!-- ═══════════════ TESTIMONIAL ═══════════════ -->
<section style="background:white;padding:5rem 0;margin-top:5rem;">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-eyebrow"><i class="bi bi-chat-quote"></i> Suara Mereka</div>
      <h2 class="section-heading">Apa Kata Mahasiswa<br>yang Sudah Merasakan</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="testi-wrap">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text">Awalnya ragu mau cerita, tapi konselornya sangat suportif dan tidak menghakimi. Benar-benar membantu saya melewati masa sulit di semester 5.</p>
          <div class="testi-author">
            <div class="t-avatar" style="background:linear-gradient(135deg,var(--primary),var(--primary-light))">A</div>
            <div>
              <div style="font-size:.85rem;font-weight:600">Adinda, TI 2022</div>
              <div style="font-size:.75rem;color:var(--text-light)">Konseling Online</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testi-wrap" style="background:linear-gradient(135deg,var(--primary),var(--primary-mid))">
          <div class="testi-stars">★★★★★</div>
          <p class="testi-text" style="color:rgba(255,255,255,.8)">Fitur booking-nya mudah banget. Saya pilih jadwal sesuai kegiatan saya. Konfirmasi langsung masuk email. Sangat direkomendasikan!</p>
          <div class="testi-author" style="border-top-color:rgba(255,255,255,.1)">
            <div class="t-avatar" style="background:rgba(255,255,255,.15)">R</div>
            <div>
              <div style="font-size:.85rem;font-weight:600;color:white">Rizky, SI 2021</div>
              <div style="font-size:.75rem;color:rgba(255,255,255,.4)">Konseling Offline</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="testi-wrap">
          <div class="testi-stars">★★★★☆</div>
          <p class="testi-text">Self-help module-nya bagus! Saya rutin pakai teknik relaksasinya sebelum ujian. Stres akademik jadi jauh lebih terkendali sekarang.</p>
          <div class="testi-author">
            <div class="t-avatar" style="background:linear-gradient(135deg,#f39c12,#e67e22)">M</div>
            <div>
              <div style="font-size:.85rem;font-weight:600">Mariana, EL 2023</div>
              <div style="font-size:.75rem;color:var(--text-light)">Self-Help Resources</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ CTA FINAL ═══════════════ -->
<section class="container" style="margin-top:5rem;margin-bottom:0">
  <div class="cta-final">
    <!-- Rings -->
    <div class="cta-ring" style="width:400px;height:400px;top:-100px;right:-100px;"></div>
    <div class="cta-ring" style="width:200px;height:200px;bottom:-50px;left:10%;"></div>

    <div class="position-relative" style="z-index:1">
      <div class="section-eyebrow mx-auto mb-3" style="width:fit-content;background:rgba(15,184,122,.15);color:#52e8a6;border:1px solid rgba(15,184,122,.25)">
        Jangan Tunda Lagi
      </div>
      <h2 style="font-family:'Fraunces',serif;font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:700;color:white;margin-bottom:.8rem">
        Mulai Perjalanan Menuju<br>Kesehatan Mental yang Lebih Baik
      </h2>
      <p style="color:rgba(255,255,255,.65);font-size:.95rem;max-width:480px;margin:0 auto 2rem;line-height:1.75">
        Kesehatan mental sama pentingnya dengan nilai akademikmu. Kami ada untuk menemanimu tanpa syarat.
      </p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="/about" class="btn btn-hero-ghost">
          <i class="bi bi-info-circle me-2"></i>Kenali Platform Kami
        </a>
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
// Hero section is static on this page.
</script>
@endpush

@endsection