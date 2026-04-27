@extends('layouts.master')

@push('styles')
<style>
  .home-page {
    --primary: #0A523A;
    --primary-700: #063B2A;
    --primary-600: #0D6A4B;
    --primary-500: #0FB87A;
    --primary-soft: #E5F7EF;
    --mint: #7EF0A0;
    --mint-soft: #EEF8F2;
    --warm: #FDBA5A;
    --warm-dark: #5C3200;
    --ink: #26312D;
    --muted: #66736E;
    --line: #DDEAE4;
    background: #F8FCF9;
    color: var(--ink);
  }

  .home-page a {
    text-decoration: none;
  }

  .home-hero {
    position: relative;
    overflow: hidden;
    padding: 4.8rem 0 4.2rem;
    background:
      linear-gradient(90deg, rgba(248, 252, 249, .98) 0%, rgba(248, 252, 249, .9) 48%, rgba(232, 244, 236, .78) 100%),
      url("{{ asset('img/bg.png') }}") center bottom / cover no-repeat;
  }

  .hero-kicker,
  .section-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    border-radius: 999px;
    background: #F8D59E;
    color: #7B4E05;
    font-size: .7rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: .42rem .82rem;
  }

  .hero-title {
    max-width: 11.5ch;
    margin: 1.25rem 0 1rem;
    color: var(--ink);
    font-size: clamp(2.45rem, 5.2vw, 4.8rem);
    font-weight: 800;
    letter-spacing: -.03em;
    line-height: .96;
  }

  .hero-title span {
    display: block;
    color: var(--primary);
  }

  .hero-desc {
    max-width: 470px;
    margin-bottom: 1.5rem;
    color: var(--muted);
    font-size: .95rem;
    line-height: 1.8;
  }

  .hero-actions,
  .cta-row {
    display: flex;
    flex-wrap: wrap;
    gap: .85rem;
  }

  .btn-home-primary,
  .btn-home-secondary,
  .btn-dark-soft,
  .btn-warm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    border-radius: 999px;
    padding: .78rem 1.25rem;
    border: 0;
    font-size: .86rem;
    font-weight: 800;
    transition: all .2s ease;
  }

  .btn-home-primary,
  .btn-dark-soft {
    background: var(--primary);
    color: #fff;
  }

  .btn-home-primary:hover,
  .btn-dark-soft:hover {
    background: var(--primary-700);
    color: #fff;
    transform: translateY(-1px);
  }

  .btn-home-secondary {
    background: #DDE5E0;
    color: var(--ink);
  }

  .btn-home-secondary:hover {
    background: #CCD8D1;
    color: var(--ink);
  }

  .hero-visual {
    position: relative;
    max-width: 520px;
    margin-left: auto;
  }

  .hero-illustration-card {
    position: relative;
    overflow: hidden;
    min-height: 455px;
    border-radius: 34px;
    background: linear-gradient(135deg, #C5D5B9 0%, #E4D7AF 100%);
    box-shadow: 0 28px 70px rgba(10, 82, 58, .18);
  }

  .hero-illustration-card::before {
    content: "";
    position: absolute;
    inset: 1.4rem;
    border: 2px solid rgba(255, 255, 255, .38);
    border-radius: 999px 999px 28px 28px;
  }

  .hero-illustration-card::after {
    content: "";
    position: absolute;
    right: 2.2rem;
    top: 3.2rem;
    width: 135px;
    height: 185px;
    border-radius: 72px 72px 12px 12px;
    background:
      linear-gradient(90deg, rgba(255,255,255,.92) 49%, rgba(255,255,255,.45) 50%),
      linear-gradient(180deg, #93C9B0 0%, #DDF4E8 100%);
    border: 10px solid rgba(255, 255, 255, .92);
  }

  .hero-counselor-img {
    position: absolute;
    left: 50%;
    bottom: -.4rem;
    z-index: 2;
    width: min(78%, 380px);
    transform: translateX(-50%);
    filter: drop-shadow(0 18px 24px rgba(37, 47, 43, .14));
  }

  .hero-student-card {
    position: absolute;
    left: 2rem;
    bottom: 2.4rem;
    z-index: 3;
    max-width: 180px;
    padding: .9rem;
    border-radius: 18px;
    background: rgba(255, 255, 255, .9);
    box-shadow: 0 18px 42px rgba(43, 55, 50, .14);
  }

  .hero-student-card strong {
    display: block;
    color: var(--primary);
    font-size: 1.35rem;
    line-height: 1;
  }

  .hero-student-card span {
    display: block;
    margin-top: .25rem;
    color: var(--ink);
    font-size: .62rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .hero-dot {
    position: absolute;
    z-index: 3;
    width: 15px;
    height: 15px;
    border: 3px solid #F08B39;
    border-radius: 50%;
  }

  .hero-dot.dot-1 { left: 42%; top: 16%; }
  .hero-dot.dot-2 { right: 8%; top: 35%; }
  .hero-dot.dot-3 { left: 12%; top: 42%; border-color: rgba(255,255,255,.82); }

  .section-block {
    padding: 4.5rem 0;
  }

  .section-title {
    color: var(--ink);
    font-size: clamp(1.85rem, 3vw, 3rem);
    font-weight: 800;
    letter-spacing: -.025em;
    line-height: 1.08;
  }

  .section-title span {
    color: var(--primary);
  }

  .section-desc {
    max-width: 580px;
    color: var(--muted);
    font-size: .92rem;
    line-height: 1.75;
  }

  .safe-space {
    overflow: hidden;
    border-radius: 44px;
    background: #EFF5F2;
    padding: 4.4rem 0;
  }

  .support-card {
    position: relative;
    overflow: hidden;
    min-height: 280px;
    height: 100%;
    border-radius: 28px;
    padding: 2rem;
  }

  .support-card h3 {
    margin: .95rem 0 .55rem;
    color: #114735;
    font-size: 1.45rem;
    font-weight: 800;
  }

  .support-card p {
    max-width: 330px;
    margin-bottom: 2rem;
    color: rgba(17, 71, 53, .78);
    font-size: .9rem;
    line-height: 1.7;
  }

  .mood-card {
    background: var(--mint);
  }

  .schedule-card {
    background: var(--warm);
  }

  .schedule-card h3,
  .schedule-card p {
    color: var(--warm-dark);
  }

  .support-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .28);
    color: #075F3F;
    font-size: 1.2rem;
  }

  .mood-dots {
    position: absolute;
    right: 1.8rem;
    top: 1.6rem;
    display: grid;
    grid-template-columns: repeat(2, 34px);
    gap: .55rem;
  }

  .mood-dot {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,.92);
    color: var(--primary);
    font-size: .95rem;
  }

  .schedule-card::after {
    content: "";
    position: absolute;
    top: -48px;
    right: -40px;
    width: 150px;
    height: 150px;
    border: 6px solid rgba(255, 244, 219, .45);
    border-radius: 50%;
  }

  .stats-card {
    margin-top: 2rem;
    padding: 1rem;
    border: 1px solid var(--line);
    border-radius: 24px;
    background: rgba(255, 255, 255, .72);
  }

  .stat-tile {
    display: flex;
    align-items: center;
    gap: .8rem;
    height: 100%;
    padding: .95rem;
    border-radius: 18px;
  }

  .stat-tile i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    border-radius: 50%;
    background: var(--primary-soft);
    color: var(--primary);
  }

  .stat-tile h4 {
    margin: 0;
    color: var(--ink);
    font-size: 1rem;
    font-weight: 800;
  }

  .stat-tile p {
    margin: .18rem 0 0;
    color: var(--muted);
    font-size: .78rem;
    line-height: 1.45;
  }

  .trust-strip {
    background: #F8FCF9;
  }

  .trust-list {
    display: grid;
    gap: 1.7rem;
  }

  .trust-item {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
  }

  .trust-item i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    flex: 0 0 48px;
    border-radius: 50%;
    background: var(--primary-soft);
    color: var(--primary);
  }

  .trust-item:nth-child(3) i {
    background: #FFE3B7;
    color: #9A5B05;
  }

  .trust-item h4 {
    margin-bottom: .3rem;
    color: var(--ink);
    font-size: 1.1rem;
    font-weight: 800;
  }

  .trust-item p {
    margin: 0;
    color: var(--muted);
    font-size: .88rem;
    line-height: 1.65;
  }

  .trust-image {
    position: relative;
    overflow: hidden;
    border-radius: 28px;
    aspect-ratio: 3456 / 1216;
    background: #EAF3EE;
    box-shadow: 0 24px 60px rgba(10, 82, 58, .12);
  }

  .trust-image img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .steps-section {
    position: relative;
    overflow: hidden;
    border-radius: 44px;
    background:
      linear-gradient(135deg, rgba(232, 246, 237, .96) 0%, rgba(201, 231, 214, .94) 52%, rgba(244, 250, 246, .98) 100%);
  }

  .steps-section::before,
  .steps-section::after {
    content: "";
    position: absolute;
    inset-inline: 0;
    pointer-events: none;
  }

  .steps-section::before {
    top: 0;
    height: 150px;
    background:
      url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 180' preserveAspectRatio='none'%3E%3Cpath d='M0 78L80 65C160 52 320 26 480 43C640 60 800 120 960 126C1120 132 1280 84 1360 60L1440 36V0H0Z' fill='%23f8fcf9' fill-opacity='.92'/%3E%3Cpath d='M0 132L90 118C180 104 360 76 540 92C720 108 900 168 1080 160C1260 152 1350 96 1440 72V0H0Z' fill='%230a523a' fill-opacity='.08'/%3E%3C/svg%3E") top center / 100% 100% no-repeat;
  }

  .steps-section::after {
    right: 0;
    bottom: -18px;
    left: auto;
    width: min(62%, 760px);
    height: 210px;
    border-radius: 120px 0 44px 0;
    background:
      linear-gradient(145deg, rgba(10, 82, 58, .12), rgba(15, 184, 122, .07)),
      repeating-linear-gradient(-8deg, rgba(255,255,255,.18) 0 12px, rgba(255,255,255,0) 12px 28px);
    transform: skewY(-4deg);
  }

  .steps-section .container {
    position: relative;
    z-index: 1;
  }

  .step-card {
    height: 100%;
    min-height: 172px;
    padding: 1.5rem 1.2rem;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, .74);
    background: rgba(255, 255, 255, .86);
    box-shadow: 0 18px 38px rgba(10, 82, 58, .08);
    backdrop-filter: blur(8px);
    text-align: center;
  }

  .step-icon {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    margin-bottom: 1rem;
    border-radius: 50%;
    background: var(--primary-soft);
    color: var(--primary);
  }

  .step-icon.gold { background: #F8D59E; color: #875807; }
  .step-icon.red { background: #FEE2E2; color: #B91C1C; }

  .step-card small {
    display: block;
    color: var(--primary);
    font-size: .68rem;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .step-card h4 {
    max-width: 150px;
    margin: .35rem auto 0;
    color: var(--ink);
    font-size: .95rem;
    font-weight: 800;
    line-height: 1.35;
  }

  .dmhi-section {
    background: #EAF8F1;
  }

  .dmhi-card {
    height: 100%;
    padding: 1rem;
    border: 1px solid var(--line);
    border-radius: 22px;
    background: rgba(255,255,255,.78);
  }

  .dmhi-thumb {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 76px;
    height: 76px;
    margin-bottom: 1rem;
    overflow: hidden;
    border-radius: 18px;
    background: var(--primary-soft);
  }

  .dmhi-thumb img {
    width: 82px;
    height: 82px;
    object-fit: contain;
  }

  .dmhi-card h4 {
    color: var(--ink);
    font-size: 1rem;
    font-weight: 800;
  }

  .dmhi-card p {
    margin: 0;
    color: var(--muted);
    font-size: .86rem;
    line-height: 1.7;
  }

  .impact-section {
    background: #F8FCF9;
  }

  .impact-card {
    height: 100%;
    padding: 1.4rem;
    border: 1px solid var(--line);
    border-radius: 20px;
    background: #fff;
  }

  .impact-card i {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    margin-bottom: 1rem;
    border-radius: 14px;
    background: var(--primary-soft);
    color: var(--primary);
  }

  .impact-card h4 {
    color: var(--ink);
    font-size: 1.05rem;
    font-weight: 800;
  }

  .impact-card p {
    margin: 0;
    color: var(--muted);
    font-size: .86rem;
    line-height: 1.75;
  }

  .final-cta {
    padding: 2rem 0 4.5rem;
  }

  .final-cta-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    padding: 1.6rem;
    border-radius: 28px;
    background: var(--primary);
    color: #fff;
  }

  .final-cta-box p {
    max-width: 620px;
    margin: 0;
    color: rgba(255,255,255,.78);
    line-height: 1.7;
  }

  .btn-warm {
    background: var(--warm);
    color: var(--warm-dark);
  }

  .btn-warm:hover {
    background: #F6A83B;
    color: var(--warm-dark);
  }

  .schedule-card .btn-warm {
    background: #FFF8E9;
    color: var(--warm-dark);
    box-shadow: 0 10px 22px rgba(92, 50, 0, .12);
  }

  .schedule-card .btn-warm:hover {
    background: var(--warm-dark);
    color: #FFF8E9;
    box-shadow: 0 14px 28px rgba(92, 50, 0, .18);
    transform: translateY(-1px);
  }

  @media (max-width: 991.98px) {
    .hero-visual {
      margin: 2rem auto 0;
    }

    .hero-title {
      max-width: 100%;
    }

    .safe-space,
    .steps-section {
      border-radius: 30px;
    }

    .final-cta-box {
      align-items: flex-start;
      flex-direction: column;
    }
  }

  @media (max-width: 575.98px) {
    .home-hero {
      padding-top: 3.8rem;
    }

    .hero-illustration-card {
      min-height: 370px;
      border-radius: 26px;
    }

    .hero-student-card {
      left: 1rem;
      bottom: 1rem;
    }

    .hero-actions a,
    .support-card .btn-dark-soft,
    .support-card .btn-warm,
    .final-cta-box a {
      width: 100%;
    }

    .support-card {
      min-height: 250px;
      padding: 1.5rem;
    }

    .mood-dots {
      right: 1.2rem;
      top: 1.2rem;
    }

    .section-block,
    .safe-space {
      padding: 3.5rem 0;
    }
  }
</style>
@endpush

@section('konten')
<div class="home-page">
  <section class="home-hero">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="hero-kicker">Kesehatan Mental Siswa</div>
          <h1 class="hero-title">
            Konsultasikan Dirimu, <span>Percaya Mental Sehat</span>
          </h1>
          <p class="hero-desc">
            Langkah berani untuk masa depan yang lebih cerah. Campus Care hadir sebagai ruang bimbingan dan konseling yang aman, tenang, dan mudah diakses oleh mahasiswa IT Del.
          </p>
          <div class="hero-actions">
            <a href="/tentang" class="btn-home-primary">Tentang Campus Care</a>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="hero-visual">
            <div class="hero-illustration-card">
              <span class="hero-dot dot-1"></span>
              <span class="hero-dot dot-2"></span>
              <span class="hero-dot dot-3"></span>
              <img src="{{ asset('img/dokter.png') }}" alt="Ilustrasi konselor Campus Care" class="hero-counselor-img">
              <div class="hero-student-card">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="safe-space" id="ruang-aman">
    <div class="container">
      <div class="mb-4">
        <h2 class="section-title">Ruang Aman <span>Untukmu</span></h2>
        <p class="section-desc">
          Kami menyediakan fitur sederhana untuk membantu kamu memantau kondisi harian dan mulai mencari bantuan saat dibutuhkan.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="support-card mood-card">
            <span class="support-icon"><i class="bi bi-emoji-smile"></i></span>
            <div class="mood-dots">
              <span class="mood-dot"><i class="bi bi-emoji-smile"></i></span>
              <span class="mood-dot"><i class="bi bi-emoji-neutral"></i></span>
              <span class="mood-dot"><i class="bi bi-emoji-frown"></i></span>
              <span class="mood-dot"><i class="bi bi-emoji-expressionless"></i></span>
            </div>
            <h3>Daily Mood Tracker</h3>
            <p>Pahami pola emosi harian untuk kesehatan mental yang lebih stabil dan terukur.</p>
            <a href="#ruang-aman" class="btn-dark-soft">Mulai Tracking <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="support-card schedule-card">
            <span class="support-icon"><i class="bi bi-calendar2-week"></i></span>
            <h3>Penjadwalan Konseling</h3>
            <p>Pilih konselor profesional dan tentukan waktu konseling tatap muka yang paling nyaman untukmu.</p>
            <a href="{{ route('konseling') }}" class="btn-warm">Cari Jadwal</a>
          </div>
        </div>
      </div>

      <div class="stats-card">
        <div class="row g-2">
          <div class="col-md-6 col-lg-3">
            <div class="stat-tile">
              <i class="bi bi-laptop"></i>
              <div>
                <h4>Online</h4>
                <p>Layanan digital yang fleksibel.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="stat-tile">
              <i class="bi bi-incognito"></i>
              <div>
                <h4>Anonim</h4>
                <p>Opsi menjaga kenyamanan awal.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="stat-tile">
              <i class="bi bi-calendar-check"></i>
              <div>
                <h4>Terjadwal</h4>
                <p>Pengajuan sesi lebih rapi.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="stat-tile">
              <i class="bi bi-journal-check"></i>
              <div>
                <h4>Terpantau</h4>
                <p>Riwayat layanan tersimpan.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-block trust-strip">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-5">
          <div class="trust-list">
            <div class="trust-item">
              <i class="bi bi-lock"></i>
              <div>
                <h4>100% Privasi Terjamin</h4>
                <p>Ceritamu aman bersama kami. Data dan pilihan layanan diperlakukan secara hati-hati.</p>
              </div>
            </div>
            <div class="trust-item">
              <i class="bi bi-shield-check"></i>
              <div>
                <h4>Konselor Berlisensi</h4>
                <p>Tim bimbingan dan konseling siap mendengarkan dengan profesional dan tidak menghakimi.</p>
              </div>
            </div>
            <div class="trust-item">
              <i class="bi bi-headset"></i>
              <div>
                <h4>Respon Cepat</h4>
                <p>Alur layanan dibuat jelas agar kamu tahu langkah berikutnya setelah mengajukan jadwal.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="trust-image">
            <img src="{{ asset('img/therapy-session2.png') }}" alt="Pendampingan konseling mahasiswa">
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-block steps-section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title">Tahapan Sebelum Konseling</h2>
        <p class="section-desc mx-auto">
          Ikuti langkah-langkah mudah berikut untuk memulai perjalanan kesehatan mentalmu bersama Campus Care.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="step-card">
            <div class="step-icon"><i class="bi bi-person-plus"></i></div>
            <small>Langkah 1</small>
            <h4>Melakukan Pendaftaran Akun</h4>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="step-card">
            <div class="step-icon gold"><i class="bi bi-box-arrow-in-right"></i></div>
            <small>Langkah 2</small>
            <h4>Masuk ke Akun yang Terdaftar</h4>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="step-card">
            <div class="step-icon"><i class="bi bi-clipboard2-pulse"></i></div>
            <small>Langkah 3</small>
            <h4>Mengikuti Survey atau Mood Tracker</h4>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="step-card">
            <div class="step-icon red"><i class="bi bi-calendar2-check"></i></div>
            <small>Langkah 4</small>
            <h4>Memilih Menu Jadwal Konseling</h4>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-block dmhi-section">
    <div class="container">
      <div class="row align-items-end g-4 mb-4">
        <div class="col-lg-7">
          <div class="section-kicker mb-3">Konsep DMHI</div>
          <h2 class="section-title">Mengarah pada pendekatan <span>Digital Mental Health Intervention</span></h2><br>
        <p class="section-desc">
            Campus Care menjadi pintu masuk digital untuk membantu mahasiswa memahami kebutuhan, mengatur sesi, dan menjaga keberlanjutan dukungan psikologis.
          </p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="dmhi-card">
            <div class="dmhi-thumb"><img src="{{ asset('img/ac.png') }}" alt="Akses konseling digital"></div>
            <h4>Akses Lebih Mudah</h4>
            <p>Mahasiswa dapat memulai proses bantuan dari platform tanpa harus datang langsung lebih dulu.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="dmhi-card">
            <div class="dmhi-thumb"><img src="{{ asset('img/privacy.png') }}" alt="Privasi layanan konseling"></div>
            <h4>Privasi dan Rasa Aman</h4>
            <p>Mode anonim, akun pengguna, dan riwayat layanan membantu pengalaman terasa lebih aman.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="dmhi-card">
            <div class="dmhi-thumb"><img src="{{ asset('img/alur_terstruktur.png') }}" alt="Alur konseling terstruktur"></div>
            <h4>Alur Terstruktur</h4>
            <p>Dari registrasi hingga jadwal konseling, setiap langkah dibuat jelas dan mudah dipahami.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-block impact-section">
    <div class="container">
      <div class="mb-4">
        <h2 class="section-title">Mengapa pendekatan ini relevan untuk mahasiswa?</h2>
        <p class="section-desc">
          Mahasiswa sering menghadapi tekanan akademik, adaptasi sosial, dan tantangan personal secara bersamaan. Akses bantuan yang sederhana dapat menjadi langkah awal yang realistis.
        </p>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="impact-card">
            <i class="bi bi-people"></i>
            <h4>Relasi Sosial</h4>
            <p>Membantu mahasiswa yang sedang menghadapi konflik, rasa kesepian, atau kesulitan beradaptasi.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="impact-card">
            <i class="bi bi-person-heart"></i>
            <h4>Personal</h4>
            <p>Memberi ruang untuk memahami kecemasan, tekanan diri, dan kebutuhan emosi secara lebih tenang.</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="impact-card">
            <i class="bi bi-mortarboard"></i>
            <h4>Akademik</h4>
            <p>Mendukung mahasiswa yang mengalami stres akademik, kehilangan motivasi, atau kesulitan fokus.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
