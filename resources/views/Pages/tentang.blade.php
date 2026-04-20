@extends('layouts.master')

@push('styles')
<style>
  .about-page-hero {
    background: linear-gradient(180deg, var(--navbar-bg) 0%, #ffffff 82%);
    padding: 4.5rem 0 3.5rem;
  }

  .about-hero-card {
    position: relative;
    border-radius: 28px;
    overflow: hidden;
    min-height: 380px;
    background: #e8f3ee;
  }

  .about-hero-card::after {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(6, 78, 59, 0.24);
  }

  .about-hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    inset: 0;
  }

  .about-hero-content {
    position: relative;
    z-index: 2;
    max-width: 560px;
    padding: 3rem;
    color: white;
  }

  .about-kicker {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .45rem .9rem;
    border-radius: 999px;
    background: rgba(255,255,255,.16);
    border: 1px solid rgba(255,255,255,.18);
    color: #fff;
    font-size: .76rem;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: 1rem;
  }

  .about-kicker .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #A7F3D0;
  }

  .about-hero-title {
    font-family: 'Fraunces', serif;
    font-size: clamp(2rem, 4vw, 3.4rem);
    line-height: 1.1;
    margin-bottom: .9rem;
    color: white;
  }

  .about-hero-desc {
    font-size: .98rem;
    line-height: 1.85;
    color: rgba(255,255,255,.9);
    margin: 0;
    max-width: 500px;
  }

  .about-section {
    padding: 4.5rem 0;
  }

  .about-heading-wrap {
    max-width: 760px;
    margin: 0 auto 2.5rem;
    text-align: center;
  }

  .about-section-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    background: var(--primary-soft);
    color: var(--primary);
    border-radius: 999px;
    padding: .4rem .85rem;
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: .9rem;
  }

  .about-section-title {
    font-family: 'Fraunces', serif;
    color: var(--text-dark);
    font-size: clamp(1.7rem, 3vw, 2.5rem);
    line-height: 1.18;
    margin-bottom: .8rem;
  }

  .about-section-desc {
    color: var(--text-mid);
    font-size: .96rem;
    line-height: 1.8;
    margin: 0;
  }

  .about-info-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.6rem;
    height: 100%;
  }

  .about-info-card h5 {
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: .6rem;
    font-size: 1.02rem;
  }

  .about-info-card p {
    color: var(--text-mid);
    font-size: .92rem;
    line-height: 1.75;
    margin: 0;
  }

  .about-two-col {
    align-items: center;
  }

  .about-image-panel {
    border-radius: 24px;
    overflow: hidden;
    background: #eef7f2;
    border: 1px solid var(--border);
    min-height: 320px;
  }

  .about-image-panel img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .about-text-block h3 {
    font-family: 'Fraunces', serif;
    color: var(--text-dark);
    font-size: clamp(1.6rem, 2.6vw, 2.2rem);
    line-height: 1.2;
    margin-bottom: 1rem;
  }

  .about-text-block p {
    color: var(--text-mid);
    line-height: 1.85;
    font-size: .95rem;
    margin-bottom: 1rem;
  }

  .about-list {
    display: grid;
    gap: .85rem;
    margin-top: 1rem;
  }

  .about-list-item {
    display: flex;
    align-items: flex-start;
    gap: .7rem;
    color: var(--text-mid);
    font-size: .92rem;
    line-height: 1.7;
  }

  .about-list-item i {
    color: var(--primary);
    margin-top: .12rem;
    flex-shrink: 0;
  }

  .service-mini-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 1.5rem;
    height: 100%;
    transition: all .2s ease;
  }

  .service-mini-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-sm);
  }

  .service-mini-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: var(--primary-soft);
    color: var(--primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 1rem;
  }

  .service-mini-card h5 {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-dark);
    margin-bottom: .55rem;
  }

  .service-mini-card p {
    color: var(--text-mid);
    font-size: .9rem;
    line-height: 1.75;
    margin: 0;
  }

  .about-cta-box {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 100%);
    border-radius: 28px;
    padding: 3rem 2rem;
    text-align: center;
    color: rgba(255,255,255,.85);
  }

  .about-cta-box h2 {
    font-family: 'Fraunces', serif;
    color: white;
    font-size: clamp(1.8rem, 3vw, 2.6rem);
    margin-bottom: .8rem;
  }

  .about-cta-box p {
    max-width: 700px;
    margin: 0 auto 1.4rem;
    line-height: 1.8;
    color: rgba(255,255,255,.85);
    font-size: .95rem;
  }

  .about-cta-actions {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: .9rem;
  }

  .btn-about-light {
    background: white;
    color: var(--primary);
    border: 1px solid white;
    border-radius: 14px;
    padding: .88rem 1.2rem;
    font-weight: 700;
    text-decoration: none;
  }

  .btn-about-light:hover {
    color: var(--primary);
    background: #f7fcf9;
  }

  .btn-about-outline {
    background: transparent;
    color: white;
    border: 1px solid rgba(255,255,255,.35);
    border-radius: 14px;
    padding: .88rem 1.2rem;
    font-weight: 700;
    text-decoration: none;
  }

  .btn-about-outline:hover {
    color: white;
    background: rgba(255,255,255,.08);
  }

  @media (max-width: 991.98px) {
    .about-hero-content {
      padding: 2rem;
    }
  }

  @media (max-width: 575.98px) {
    .about-page-hero {
      padding: 3.5rem 0 2.8rem;
    }

    .about-hero-card {
      min-height: 320px;
    }

    .about-hero-content {
      padding: 1.5rem;
    }

    .about-cta-actions a {
      width: 100%;
      justify-content: center;
    }
  }
</style>
@endpush

@section('konten')

<section class="about-page-hero">
  <div class="container">
    <div class="about-hero-card">
      <img src="{{ asset('img/about-hero.jpg') }}" alt="Tentang Campus Care" class="about-hero-image">

      <div class="about-hero-content">
        <h1 class="about-hero-title">
          Layanan konseling digital yang lebih dekat dengan kebutuhan mahasiswa
        </h1>

        <p class="about-hero-desc">
          Campus Care dikembangkan untuk membantu mahasiswa Institut Teknologi Del mengakses layanan bimbingan dan konseling secara lebih mudah, terarah, dan nyaman. Platform ini menjadi bagian dari pendekatan <em>Digital Mental Health Intervention</em> dalam konteks lingkungan kampus.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="about-section">
  <div class="container">
    <div class="about-heading-wrap">
      <div class="about-section-kicker">
        <i class="bi bi-info-circle"></i>
        Tentang Kami
      </div>
      <h2 class="about-section-title">Platform ini hadir untuk membuat akses bantuan menjadi lebih sederhana</h2>
      <p class="about-section-desc">
        Tidak semua mahasiswa langsung nyaman datang ke layanan konseling secara tatap muka. Karena itu, Campus Care dirancang sebagai ruang digital yang membantu proses awal mencari bantuan menjadi lebih jelas, lebih praktis, dan tetap menjaga privasi pengguna.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="about-info-card">
          <h5>Mudah diakses</h5>
          <p>
            Mahasiswa dapat memulai proses konseling melalui platform digital tanpa harus kebingungan dengan prosedur awal.
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="about-info-card">
          <h5>Lebih terstruktur</h5>
          <p>
            Alur layanan dibuat berurutan, mulai dari login, pemilihan layanan, penjadwalan, hingga pemantauan riwayat konseling.
          </p>
        </div>
      </div>

      <div class="col-md-4">
        <div class="about-info-card">
          <h5>Menjaga kenyamanan</h5>
          <p>
            Fitur seperti mode anonim membantu mahasiswa yang belum siap menampilkan identitas penuh pada tahap awal.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="about-section pt-0">
  <div class="container">
    <div class="row g-5 about-two-col">
      <div class="col-lg-6">
        <div class="about-image-panel">
          <img src="{{ asset('img/about-section.jpg') }}" alt="Mahasiswa dan layanan konseling">
        </div>
      </div>

      <div class="col-lg-6">
        <div class="about-text-block">
          <h3>Mengapa Campus Care dikembangkan?</h3>
          <p>
            Dalam keseharian akademik, mahasiswa dapat menghadapi tekanan belajar, penyesuaian diri, masalah personal, dan berbagai tantangan lain yang memengaruhi kesehatan mental. Namun pada praktiknya, tidak semua mahasiswa merasa mudah untuk mengakses bantuan secara langsung.
          </p>
          <p>
            Campus Care dikembangkan untuk menjembatani kebutuhan tersebut melalui pendekatan digital yang lebih dekat dengan pola penggunaan mahasiswa sehari-hari, tanpa menghilangkan peran konselor sebagai pendamping utama dalam proses layanan.
          </p>

          <div class="about-list">
            <div class="about-list-item">
              <i class="bi bi-check-circle-fill"></i>
              <span>Mendukung akses awal yang lebih nyaman bagi mahasiswa.</span>
            </div>
            <div class="about-list-item">
              <i class="bi bi-check-circle-fill"></i>
              <span>Mengurangi hambatan karena rasa ragu, malu, atau bingung terhadap prosedur.</span>
            </div>
            <div class="about-list-item">
              <i class="bi bi-check-circle-fill"></i>
              <span>Membantu proses layanan menjadi lebih tertata melalui fitur digital yang relevan.</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="about-section pt-0">
  <div class="container">
    <div class="about-heading-wrap">
      <div class="about-section-kicker">
        <i class="bi bi-grid"></i>
        Layanan yang Didukung
      </div>
      <h2 class="about-section-title">Fitur yang menjadi bagian dari layanan konseling digital</h2>
      <p class="about-section-desc">
        Halaman ini tidak hanya menjelaskan platform, tetapi juga menunjukkan bagaimana fitur-fitur utama mendukung pendekatan layanan yang lebih mudah diakses dan relevan bagi mahasiswa.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-md-6 col-lg-3">
        <div class="service-mini-card">
          <div class="service-mini-icon"><i class="bi bi-person-plus"></i></div>
          <h5>Registrasi dan Login</h5>
          <p>
            Akses awal ke sistem dibuat sederhana agar mahasiswa dapat segera menggunakan layanan sesuai kebutuhannya.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="service-mini-card">
          <div class="service-mini-icon"><i class="bi bi-calendar-check"></i></div>
          <h5>Penjadwalan Konseling</h5>
          <p>
            Pengguna dapat memilih jenis layanan dan mengatur sesi konseling sesuai waktu yang tersedia.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="service-mini-card">
          <div class="service-mini-icon"><i class="bi bi-incognito"></i></div>
          <h5>Mode Anonim</h5>
          <p>
            Memberi ruang awal yang lebih nyaman bagi mahasiswa yang belum siap membuka identitas penuh.
          </p>
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <div class="service-mini-card">
          <div class="service-mini-icon"><i class="bi bi-chat"></i></div>
          <h5>Chat</h5>
          <p>
            Fitur chat memungkinkan mahasiswa untuk berinteraksi secara langsung dengan konselor dan menjaga komunikasi yang terjaga.
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="container" style="padding-bottom: 2rem;">
  <div class="about-cta-box">
    <h2>Campus Care dikembangkan untuk menjadi pintu masuk yang lebih ramah ke layanan konseling</h2>
    <p>
      Melalui pendekatan Digital Mental Health Intervention, platform ini diharapkan dapat membantu mahasiswa memulai proses mencari bantuan dengan lebih nyaman, terarah, dan sesuai dengan konteks kehidupan kampus.
    </p>

    <div class="about-cta-actions">
      <a href="/layanan" class="btn-about-light">
        <i class="bi bi-calendar2-check me-2"></i>Lihat Layanan
      </a>
      <a href="/" class="btn-about-outline">
        <i class="bi bi-house-door me-2"></i>Kembali ke Beranda
      </a>
    </div>
  </div>
</section>

@endsection