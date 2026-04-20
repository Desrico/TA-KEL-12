@extends('layouts.master')

@push('styles')
<style>
  .home-hero {
    background: linear-gradient(180deg, var(--navbar-bg) 0%, #ffffff 78%);
    padding: 4.5rem 0 3.5rem;
    position: relative;
    overflow: hidden;
  }

  .home-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at 85% 20%, rgba(16, 185, 129, 0.08), transparent 20%),
      radial-gradient(circle at 10% 80%, rgba(6, 78, 59, 0.05), transparent 18%);
    pointer-events: none;
  }

  .hero-kicker {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .48rem .95rem;
    border-radius: 999px;
    background: var(--primary-soft);
    color: var(--primary);
    font-size: .78rem;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
  }

  .hero-kicker .dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: var(--primary-500);
  }

  .hero-title {
    font-family: 'Fraunces', serif;
    font-size: clamp(2.2rem, 4.5vw, 4rem);
    line-height: 1.1;
    color: var(--primary);
    margin: 1rem 0 1rem;
    max-width: 20ch;
  }

  .hero-desc {
    font-size: .98rem;
    line-height: 1.85;
    color: var(--text-mid);
    max-width: 520px;
    margin-bottom: 1.6rem;
  }

  .hero-actions {
    display: flex;
    gap: .9rem;
    flex-wrap: wrap;
    margin-bottom: 1.6rem;
  }

  .btn-home-primary {
    background: var(--primary);
    color: #fff;
    border: 1px solid var(--primary);
    border-radius: 14px;
    padding: .92rem 1.3rem;
    font-weight: 700;
    font-size: .92rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all .2s ease;
  }

  .btn-home-primary:hover {
    background: var(--primary-700);
    border-color: var(--primary-700);
    color: #fff;
    transform: translateY(-1px);
  }

  .btn-home-secondary {
    background: #fff;
    color: var(--primary);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: .92rem 1.3rem;
    font-weight: 700;
    font-size: .92rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all .2s ease;
  }

  .btn-home-secondary:hover {
    background: var(--surface-soft);
    color: var(--primary);
  }

  .hero-note {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem 1.25rem;
    color: var(--text-mid);
    font-size: .88rem;
  }

  .hero-note span {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
  }

  .hero-note i {
    color: var(--primary-600);
  }

  .hero-visual-clean {
    position: relative;
    display: flex;
    align-items: end;
    justify-content: center;
    min-height: 520px;
  }

  .hero-image-wrap {
    position: relative;
    width: 100%;
    max-width: 640px;
    display: flex;
    align-items: end;
    justify-content: center;
  }

  .hero-image-bg {
    position: absolute;
    inset: auto 0 0 0;
    height: 88%;
    border-radius: 36px 36px 0 0;
    background: linear-gradient(180deg, #EAF8F0 0%, #DDF3E7 100%);
  }

  .hero-image-glow {
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(16,185,129,.12) 0%, rgba(16,185,129,0) 70%);
    top: 2%;
    right: 8%;
    filter: blur(10px);
  }

  .hero-person-img {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 560px;
    object-fit: contain;
    display: block;
  }

  .hero-soft-shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,.45);
    z-index: 1;
  }

  .hero-soft-shape.shape-1 {
    width: 84px;
    height: 84px;
    top: 10%;
    left: 8%;
  }

  .hero-soft-shape.shape-2 {
    width: 22px;
    height: 22px;
    top: 24%;
    left: 20%;
  }

  .hero-soft-shape.shape-3 {
    width: 60px;
    height: 60px;
    bottom: 20%;
    right: 10%;
  }

  .hero-soft-shape.shape-4 {
    width: 16px;
    height: 16px;
    bottom: 36%;
    right: 22%;
  }

  .stats-strip {
    margin-top: -1.5rem;
    position: relative;
    z-index: 3;
  }

  .stats-wrap {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
    padding: 1.25rem;
  }

  .stat-item {
    text-align: center;
    padding: .75rem 1rem;
  }

  .stat-item h3 {
    color: var(--primary);
    font-weight: 800;
    font-size: 1.6rem;
    margin-bottom: .2rem;
  }

  .stat-item p {
    margin: 0;
    color: var(--text-mid);
    font-size: .88rem;
  }

  .section-block {
    padding: 5rem 0;
  }

  .section-heading-wrap {
    max-width: 720px;
    margin: 0 auto 2.5rem;
    text-align: center;
  }

  .section-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 999px;
    padding: .38rem .85rem;
    font-size: .76rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: .9rem;
  }

  .section-heading-main {
    font-family: 'Fraunces', serif;
    color: var(--text-dark);
    font-size: clamp(1.8rem, 3vw, 2.7rem);
    line-height: 1.18;
    margin-bottom: .8rem;
  }

  .section-desc {
    color: var(--text-mid);
    line-height: 1.8;
    font-size: .95rem;
    margin: 0;
  }

  .feature-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.6rem;
    height: 100%;
    transition: all .2s ease;
  }

  .feature-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-sm);
  }

  .feature-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-soft);
    color: var(--primary);
    font-size: 1.3rem;
    margin-bottom: 1rem;
  }

  .feature-card h5 {
    font-weight: 700;
    font-size: 1.02rem;
    margin-bottom: .55rem;
    color: var(--text-dark);
  }

  .feature-card p {
    color: var(--text-mid);
    font-size: .9rem;
    line-height: 1.75;
    margin-bottom: 1rem;
  }

  .feature-card a {
    text-decoration: none;
    color: var(--primary);
    font-size: .86rem;
    font-weight: 700;
  }

  .dmhi-panel {
    background: linear-gradient(135deg, #F7FCF9 0%, #EFFCF5 100%);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 2rem;
  }

  .dmhi-list {
    display: grid;
    gap: 1rem;
  }

  .dmhi-item {
    background: rgba(255,255,255,.82);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 1rem 1rem 1rem 3.7rem;
    position: relative;
  }

  .dmhi-item-icon {
    position: absolute;
    left: 1rem;
    top: 1rem;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: var(--primary-soft);
    color: var(--primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
  }

  .dmhi-item h6 {
    margin: 0 0 .35rem;
    color: var(--text-dark);
    font-weight: 700;
  }

  .dmhi-item p {
    margin: 0;
    color: var(--text-mid);
    font-size: .9rem;
    line-height: 1.7;
  }

  .steps-row {
    row-gap: 1.5rem;
  }

  .step-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem;
    height: 100%;
    position: relative;
  }

  .step-number {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: var(--primary);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: .92rem;
    margin-bottom: 1rem;
  }

  .step-card h6 {
    font-weight: 700;
    margin-bottom: .5rem;
    color: var(--text-dark);
  }

  .step-card p {
    margin: 0;
    color: var(--text-mid);
    font-size: .9rem;
    line-height: 1.75;
  }

  .trust-box {
    background: var(--primary);
    color: rgba(255,255,255,.86);
    border-radius: 24px;
    padding: 2rem;
    height: 100%;
  }

  .trust-box h3 {
    font-family: 'Fraunces', serif;
    color: #fff;
    margin-bottom: .75rem;
  }

  .trust-box p {
    color: rgba(255,255,255,.8);
    line-height: 1.8;
    margin-bottom: 1.25rem;
  }

  .trust-list {
    display: grid;
    gap: .8rem;
  }

  .trust-list div {
    display: flex;
    align-items: flex-start;
    gap: .7rem;
    font-size: .92rem;
  }

  .trust-list i {
    color: #A7F3D0;
    margin-top: .15rem;
  }

  .testimonial-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem;
    height: 100%;
  }

  .testimonial-card p {
    color: var(--text-mid);
    line-height: 1.8;
    font-size: .92rem;
    margin-bottom: 1rem;
  }

  .testimonial-meta {
    display: flex;
    align-items: center;
    gap: .8rem;
    padding-top: 1rem;
    border-top: 1px solid #EEF5F1;
  }

  .testimonial-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: var(--primary);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
  }

  .final-cta {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 100%);
    border-radius: 28px;
    padding: 3rem 2rem;
    color: rgba(255,255,255,.85);
    text-align: center;
    overflow: hidden;
    position: relative;
  }

  .final-cta::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at top center, rgba(255,255,255,.08), transparent 35%);
  }

  .final-cta h2 {
    position: relative;
    z-index: 1;
    font-family: 'Fraunces', serif;
    color: #fff;
    font-size: clamp(1.9rem, 3.2vw, 2.8rem);
    margin-bottom: .8rem;
  }

  .final-cta p {
    position: relative;
    z-index: 1;
    max-width: 650px;
    margin: 0 auto 1.6rem;
    line-height: 1.8;
    color: rgba(255,255,255,.82);
  }

  .final-cta .btn-wrap {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: center;
    gap: .9rem;
    flex-wrap: wrap;
  }

  .btn-cta-light {
    background: #fff;
    color: var(--primary);
    border: 1px solid #fff;
    border-radius: 14px;
    padding: .9rem 1.25rem;
    font-weight: 700;
    text-decoration: none;
  }

  .btn-cta-light:hover {
    color: var(--primary);
    background: #F7FCF9;
  }

  .btn-cta-outline {
    background: transparent;
    color: #fff;
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 14px;
    padding: .9rem 1.25rem;
    font-weight: 700;
    text-decoration: none;
  }

  .btn-cta-outline:hover {
    color: #fff;
    background: rgba(255,255,255,.08);
  }

  @media (max-width: 991.98px) {
    .home-hero {
      padding: 4.5rem 0 3rem;
    }

    .hero-title {
      max-width: 100%;
    }

    .hero-visual-clean {
      min-height: auto;
      margin-top: 1.5rem;
    }

    .hero-image-wrap {
      max-width: 520px;
      margin: 0 auto;
    }

    .hero-image-bg {
      height: 85%;
      border-radius: 28px 28px 0 0;
    }
  }

  @media (max-width: 575.98px) {
    .hero-title {
      font-size: clamp(2.4rem, 12vw, 3.6rem);
    }

    .hero-actions a {
      width: 100%;
      justify-content: center;
    }

    .hero-image-bg {
      height: 82%;
    }

    .stats-wrap .row > div:not(:last-child) .stat-item {
      border-bottom: 1px solid #EEF5F1;
    }

    .final-cta .btn-wrap a {
      width: 100%;
      justify-content: center;
    }
  }
</style>
@endpush
@section('konten')

<section class="home-hero">
  <div class="container position-relative" style="z-index: 2;">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">


        <h1 class="hero-title">
          Ruang digital yang aman untuk memulai proses konseling di kampus
        </h1>

        <p class="hero-desc">
          Campus Care membantu mahasiswa Institut Teknologi Del mengakses layanan bimbingan dan konseling secara lebih mudah, terarah, dan tetap menjaga privasi. Platform ini dikembangkan sebagai bagian dari pendekatan <em>Digital Mental Health Intervention</em> agar dukungan psikologis terasa lebih dekat dengan kebutuhan mahasiswa.
        </p>

        <div class="hero-actions">
          <a href="/layanan" class="btn-home-primary">
            <i class="bi bi-calendar2-check me-2"></i>Jadwalkan Konseling
          </a>
          <a href="/about" class="btn-home-secondary">
            <i class="bi bi-info-circle me-2"></i>Tentang Platform
          </a>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="hero-visual-clean">
          <div class="hero-image-wrap">
            <div class="hero-image-glow"></div>
            <div class="hero-image-bg"></div>

            <span class="hero-soft-shape shape-1"></span>
            <span class="hero-soft-shape shape-2"></span>
            <span class="hero-soft-shape shape-3"></span>
            <span class="hero-soft-shape shape-4"></span>

            <img src="{{ asset('img/') }}" alt="Layanan konseling digital" class="hero-person-img">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="stats-strip">
  <div class="container">
    <div class="stats-wrap">
      <div class="row g-0">
        <div class="col-md-3">
          <div class="stat-item">
            <h3>Online</h3>
            <p>Layanan digital yang fleksibel</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-item">
            <h3>Anonim</h3>
            <p>Opsi menjaga kenyamanan awal</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-item">
            <h3>Terjadwal</h3>
            <p>Pengajuan sesi lebih terstruktur</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="stat-item">
            <h3>Terpantau</h3>
            <p>Riwayat konseling tersimpan rapi</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-block">
  <div class="container">
    <div class="section-heading-wrap">
      <div class="section-kicker">
        <i class="bi bi-grid"></i>
        Layanan Utama
      </div>
      <h2 class="section-heading-main">Fitur inti yang mendukung proses konseling digital</h2>
      <p class="section-desc">
        Halaman ini tidak hanya menampilkan layanan konseling, tetapi juga menjelaskan bagaimana platform mendukung intervensi kesehatan mental secara bertahap, praktis, dan sesuai konteks mahasiswa.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-person-plus"></i></div>
          <h5>Registrasi dan Login</h5>
          <p>
            Mahasiswa dapat mengakses layanan menggunakan akun yang terhubung dengan sistem, sehingga alur awal penggunaan menjadi lebih sederhana dan terkontrol.
          </p>
          <a href="{{ route('register') }}">Mulai dari akun pengguna <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-calendar-check"></i></div>
          <h5>Penjadwalan Konseling</h5>
          <p>
            Mahasiswa dapat memilih layanan online atau offline, menentukan topik, serta mengatur jadwal sesi sesuai ketersediaan konselor.
          </p>
          <a href="/layanan">Lihat alur layanan <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="feature-card">
          <div class="feature-icon"><i class="bi bi-incognito"></i></div>
          <h5>Mode Anonim</h5>
          <p>
            Fitur ini memberi ruang awal bagi mahasiswa yang belum siap menampilkan identitas penuh, sehingga proses mencari bantuan terasa lebih nyaman.
          </p>
          <a href="/profil">Kelola preferensi pengguna <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-block pt-0">
  <div class="container">
    <div class="dmhi-panel">
      <div class="row align-items-center g-4">
        <div class="col-lg-5">
          <div class="section-kicker">
            <i class="bi bi-heart-pulse"></i>
            Konsep DMHI
          </div>
          <h2 class="section-heading-main mb-3">Mengarah pada pendekatan Digital Mental Health Intervention</h2>
          <p class="section-desc">
            Platform ini dirancang bukan sekadar sebagai halaman informasi, tetapi sebagai media digital yang membantu mahasiswa mengakses bantuan, membangun kesiapan untuk berkonsultasi, dan menjaga kontinuitas layanan secara lebih praktis.
          </p>
        </div>

        <div class="col-lg-7">
          <div class="dmhi-list">
            <div class="dmhi-item">
              <span class="dmhi-item-icon"><i class="bi bi-phone"></i></span>
              <h6>Akses yang lebih mudah</h6>
              <p>Mahasiswa dapat memulai proses konseling tanpa harus datang lebih dulu, sehingga hambatan awal untuk mencari bantuan menjadi lebih rendah.</p>
            </div>

            <div class="dmhi-item">
              <span class="dmhi-item-icon"><i class="bi bi-shield-lock"></i></span>
              <h6>Privasi dan rasa aman</h6>
              <p>Dukungan digital perlu dibangun dengan rasa aman. Karena itu, fitur akun, mode anonim, dan pengelolaan data menjadi bagian penting dari pengalaman pengguna.</p>
            </div>

            <div class="dmhi-item">
              <span class="dmhi-item-icon"><i class="bi bi-diagram-3"></i></span>
              <h6>Alur yang terstruktur</h6>
              <p>Dari registrasi, pemilihan layanan, penjadwalan, hingga riwayat konseling, seluruh proses dibuat berurutan agar mahasiswa tidak bingung saat menggunakan platform.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-block pt-0">
  <div class="container">
    <div class="section-heading-wrap">
      <div class="section-kicker">
        <i class="bi bi-signpost-2"></i>
        Alur Penggunaan
      </div>
      <h2 class="section-heading-main">Langkah penggunaan yang sederhana dan mudah dipahami</h2>
      <p class="section-desc">
        Agar tidak terasa rumit, alur pada platform dibagi menjadi beberapa tahapan yang jelas sejak mahasiswa pertama kali masuk hingga sesi berhasil dijadwalkan.
      </p>
    </div>

    <div class="row steps-row">
      <div class="col-md-6 col-lg-3">
        <div class="step-card">
          <div class="step-number">01</div>
          <h6>Masuk ke sistem</h6>
          <p>Mahasiswa membuka platform dan login menggunakan akun yang tersedia untuk mengakses layanan secara personal.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="step-card">
          <div class="step-number">02</div>
          <h6>Pilih jenis layanan</h6>
          <p>Pengguna memilih konseling online atau offline sesuai kebutuhan, kondisi, dan kenyamanan masing-masing.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="step-card">
          <div class="step-number">03</div>
          <h6>Atur jadwal sesi</h6>
          <p>Mahasiswa mengisi topik konseling, memilih waktu yang tersedia, lalu meninjau ringkasan sebelum konfirmasi.</p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="step-card">
          <div class="step-number">04</div>
          <h6>Ikuti proses konseling</h6>
          <p>Setelah pengajuan diterima, mahasiswa dapat mengikuti sesi dan memantau riwayat konseling dari akun masing-masing.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-block pt-0">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-6">
        <div class="trust-box">
          <h3>Mengapa pendekatan ini relevan untuk mahasiswa?</h3>
          <p>
            Mahasiswa sering menghadapi tekanan akademik, adaptasi lingkungan, dan tantangan personal secara bersamaan. Karena itu, layanan digital yang mudah diakses dapat menjadi pintu masuk yang lebih realistis untuk mendapatkan bantuan.
          </p>

          <div class="trust-list">
            <div>
              <i class="bi bi-check-circle-fill"></i>
              <span>Mendukung akses bantuan sejak tahap awal, tanpa proses yang terlalu rumit.</span>
            </div>
            <div>
              <i class="bi bi-check-circle-fill"></i>
              <span>Mengurangi hambatan ketika mahasiswa belum nyaman melakukan konseling tatap muka.</span>
            </div>
            <div>
              <i class="bi bi-check-circle-fill"></i>
              <span>Memberikan pengalaman yang lebih tertata melalui penjadwalan dan riwayat layanan.</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="testimonial-card">
          <div class="section-kicker mb-3">
            <i class="bi bi-chat-quote"></i>
            Gambaran Pengalaman
          </div>
          <p>
            “Saya tidak langsung siap untuk bercerita secara terbuka, jadi fitur digital seperti penjadwalan dan mode anonim terasa membantu. Platform ini membuat proses awal mencari bantuan jadi lebih ringan.”
          </p>
          <div class="testimonial-meta">
            <div class="testimonial-avatar">M</div>
            <div>
              <div style="font-weight:700; color: var(--text-dark);">Mahasiswa IT Del</div>
              <div style="font-size:.82rem; color: var(--text-light);">Ilustrasi pengalaman pengguna</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection