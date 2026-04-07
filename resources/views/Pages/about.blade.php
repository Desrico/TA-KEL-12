@extends('layouts.master')

@push('styles')
<style>
/* HERO */
.about-hero{
  background:linear-gradient(140deg,#071825 0%,#0d2d4a 45%,#0f4a72 80%,#0b6b47 100%);
  padding:5rem 0 6rem;margin:0;position:relative;overflow:hidden;
}
.about-hero::after{
  content:'';position:absolute;inset:0;
  background:url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Ccircle cx='50' cy='50' r='1' fill='white' fill-opacity='0.04'/%3E%3C/svg%3E");
}
.about-hero-title{font-family:'Fraunces',serif;font-size:clamp(2.2rem,5vw,3.8rem);font-weight:700;color:white;line-height:1.1;}
.about-hero-title em{color:#52e8a6;font-style:italic;}
.about-blob{position:absolute;border-radius:50%;filter:blur(80px);}
.about-blob-a{width:500px;height:500px;background:rgba(15,184,122,.12);top:-100px;right:-80px;}
.about-blob-b{width:350px;height:350px;background:rgba(46,134,193,.1);bottom:-50px;left:0;}

/* STAT STRIP */
.stat-strip{background:white;border-radius:20px;padding:1.8rem 2rem;box-shadow:0 12px 48px rgba(13,27,42,.12);margin-top:-3rem;position:relative;z-index:10;}
.ss-item{text-align:center;}
.ss-num{font-family:'Fraunces',serif;font-size:2.4rem;font-weight:700;color:var(--primary);line-height:1;}
.ss-num span{color:var(--accent);}
.ss-lbl{font-size:.78rem;color:var(--text-light);margin-top:.25rem;text-transform:uppercase;letter-spacing:.04em;}

/* STORY SECTION - like Linear / Stripe style */
.story-img-card{
  border-radius:24px;overflow:hidden;
  background:linear-gradient(135deg,#1a3a5c,#0f4a72);
  aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;
  position:relative;box-shadow:var(--shadow-lg);
}
.story-img-card .img-illustration{font-size:6rem;}
.story-img-card .img-overlay{
  position:absolute;inset:0;
  background:linear-gradient(180deg,transparent 40%,rgba(7,24,37,.8) 100%);
}
.story-img-card .img-caption{
  position:absolute;bottom:1.5rem;left:1.5rem;right:1.5rem;color:white;
}
.story-img-card .img-caption h6{font-weight:700;margin-bottom:.2rem;}
.story-img-card .img-caption p{font-size:.8rem;opacity:.75;margin:0;}

/* SVG Stat Card */
.svg-stat-card{
  background:white;border-radius:16px;padding:1.5rem;
  box-shadow:0 4px 24px rgba(13,27,42,.08);
  border:1px solid rgba(26,58,92,.07);
}

/* METODOLOGI visual */
.method-step{
  display:flex;align-items:flex-start;gap:1.2rem;
  padding:1.5rem;border-radius:14px;
  background:white;border:1px solid rgba(26,58,92,.07);
  transition:all .25s;margin-bottom:.8rem;
}
.method-step:hover{box-shadow:var(--shadow-md);transform:translateX(4px);}
.method-letter{
  min-width:52px;height:52px;border-radius:14px;
  display:flex;align-items:center;justify-content:center;
  font-family:'Fraunces',serif;font-size:1.3rem;font-weight:700;
  color:white;flex-shrink:0;
}
.method-step h6{font-weight:700;margin-bottom:.2rem;}
.method-step p{font-size:.84rem;color:var(--text-mid);margin:0;line-height:1.65;}

/* TIMELINE modern */
.tl-container{position:relative;padding-left:1.5rem;}
.tl-container::before{
  content:'';position:absolute;left:8px;top:8px;bottom:8px;
  width:2px;background:linear-gradient(180deg,var(--primary-light),var(--accent));
  border-radius:2px;
}
.tl-node{position:relative;margin-bottom:2rem;}
.tl-dot{
  position:absolute;left:-1.5rem;top:4px;
  width:16px;height:16px;border-radius:50%;
  background:white;border:3px solid var(--primary-light);
  box-shadow:0 0 0 4px rgba(46,134,193,.15);
}
.tl-year{
  display:inline-block;background:var(--accent-soft);color:var(--accent);
  border-radius:50px;padding:.15rem .7rem;font-size:.72rem;font-weight:700;
  text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;
}
.tl-node h6{font-weight:700;margin-bottom:.2rem;}
.tl-node p{font-size:.85rem;color:var(--text-mid);margin:0;line-height:1.65;}

/* TEAM card - no visi misi, just team */
.team-card-v2{
  background:white;border-radius:var(--radius);overflow:hidden;
  box-shadow:var(--shadow-sm);border:1px solid rgba(26,58,92,.07);
  transition:all .3s;
}
.team-card-v2:hover{transform:translateY(-6px);box-shadow:var(--shadow-md);}
.tc-header{
  height:120px;display:flex;align-items:center;justify-content:center;
  position:relative;overflow:hidden;
}
.tc-avatar{
  width:72px;height:72px;border-radius:50%;
  border:4px solid white;display:flex;align-items:center;justify-content:center;
  font-size:1.8rem;background:linear-gradient(135deg,var(--primary),var(--primary-light));
  box-shadow:0 4px 16px rgba(13,27,42,.2);position:relative;z-index:1;
}
.tc-body{padding:1.2rem;text-align:center;}
.tc-name{font-weight:700;font-size:.95rem;margin-bottom:.1rem;}
.tc-role{font-size:.78rem;color:var(--text-light);margin-bottom:.8rem;}
.tc-tag{
  display:inline-block;background:var(--surface);border-radius:50px;
  padding:.2rem .7rem;font-size:.73rem;font-weight:600;color:var(--text-mid);
}

/* VALUES grid */
.value-card{
  background:white;border-radius:var(--radius);padding:1.8rem;
  border:1px solid rgba(26,58,92,.07);height:100%;
  transition:all .3s;position:relative;overflow:hidden;
}
.value-card::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:3px;
  background:var(--vc-color,var(--primary-light));
  transform:scaleX(0);transform-origin:left;transition:.3s;
}
.value-card:hover::after{transform:scaleX(1);}
.value-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-md);}
.vc-icon{font-size:2.5rem;margin-bottom:.8rem;}
.value-card h6{font-weight:700;margin-bottom:.4rem;}
.value-card p{font-size:.85rem;color:var(--text-mid);line-height:1.65;margin:0;}

/* FAQ */
.faq-card{background:white;border-radius:12px;margin-bottom:.65rem;border:1px solid rgba(26,58,92,.07);overflow:hidden;}
.faq-q{padding:.9rem 1.1rem;font-weight:600;font-size:.88rem;cursor:pointer;display:flex;justify-content:space-between;align-items:center;color:var(--text-dark);transition:all .2s;}
.faq-q:hover,.faq-q.open{background:rgba(26,58,92,.03);color:var(--primary);}
.faq-a{padding:0 1.1rem;font-size:.84rem;color:var(--text-mid);line-height:1.7;max-height:0;overflow:hidden;transition:max-height .35s ease,padding .25s;}
.faq-a.open{max-height:200px;padding:.5rem 1.1rem .9rem;}
.faq-icon{transition:transform .25s;color:var(--text-light);font-size:.8rem;}
.faq-q.open .faq-icon{transform:rotate(180deg);}
</style>
@endpush

@section('konten')

<!-- HERO -->
<section class="about-hero">
  <div class="about-blob about-blob-a"></div>
  <div class="about-blob about-blob-b"></div>
  <div class="container position-relative" style="z-index:2">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <div class="section-eyebrow mb-3" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.15)">
          <i class="bi bi-heart-pulse-fill" style="color:var(--accent)"></i> Tentang BK Connect
        </div>
        <h1 class="about-hero-title">
          Platform Kesehatan Mental<br>untuk <em>Mahasiswa IT Del</em>
        </h1>
        <p style="color:rgba(255,255,255,.65);font-size:.97rem;line-height:1.8;max-width:520px;margin-top:1.2rem">
          BK Connect lahir dari kepedulian nyata — memastikan setiap mahasiswa IT Del memiliki akses mudah ke layanan bimbingan konseling profesional, kapanpun dan di manapun mereka membutuhkan.
        </p>
      </div>
      <div class="col-lg-5 d-none d-lg-flex justify-content-end gap-3 flex-wrap">
        <!-- Mini info cards -->
        <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:16px;padding:1.2rem;backdrop-filter:blur(12px);width:160px">
          <div style="font-size:2rem;margin-bottom:.4rem">🏛️</div>
          <div style="color:white;font-weight:700;font-size:.88rem">Institut Teknologi Del</div>
          <div style="color:rgba(255,255,255,.5);font-size:.73rem;margin-top:.2rem">Sitoluama, Toba</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STAT STRIP -->
<div class="container">
  <div class="stat-strip">
    <div class="row align-items-center g-3">
      <div class="col-6 col-md-3 ss-item">
        <div class="ss-num">2026</div>
        <div class="ss-lbl">Tahun Berdiri</div>
      </div>
      <div class="col-6 col-md-3 ss-item">
        <div class="ss-num">500<span>+</span></div>
        <div class="ss-lbl">Mahasiswa Terbantu</div>
      </div>
      <div class="col-6 col-md-3 ss-item">
        <div class="ss-num">1</div>
        <div class="ss-lbl">Konselor Aktif</div>
      </div>
      <div class="col-6 col-md-3 ss-item">
        <div class="ss-num">98<span>%</span></div>
        <div class="ss-lbl">Tingkat Kepuasan</div>
      </div>
    </div>
  </div>
</div>

<!-- STORY SECTION 1 – Mengapa hadir -->
<section class="container" style="margin-top:5rem">
  <div class="row align-items-center g-5">

    <!-- Image visual left -->
    <div class="col-lg-5">
      <div class="story-img-card">
        <div class="img-illustration">📊</div>
        <div class="img-overlay"></div>
        <div class="img-caption">
          <h6>Data Kesehatan Mental Mahasiswa IT Del</h6>
          <p>Studi internal 2022 · N = 234 Mahasiswa</p>
        </div>
      </div>
      <!-- Mini stat cards below image -->
      <div class="row g-2 mt-2">
        <div class="col-4">
          <div class="svg-stat-card text-center">
            <div style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--primary);line-height:1">67%</div>
            <div style="font-size:.72rem;color:var(--text-light);margin-top:.2rem">Stres Tinggi</div>
          </div>
        </div>
        <div class="col-4">
          <div class="svg-stat-card text-center">
            <div style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:#e74c3c;line-height:1">< 20%</div>
            <div style="font-size:.72rem;color:var(--text-light);margin-top:.2rem">Akses BK</div>
          </div>
        </div>
        <div class="col-4">
          <div class="svg-stat-card text-center">
            <div style="font-family:'Fraunces',serif;font-size:1.6rem;font-weight:700;color:var(--accent);line-height:1">3x</div>
            <div style="font-size:.72rem;color:var(--text-light);margin-top:.2rem">Lebih Cepat</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Text right -->
    <div class="col-lg-7">
      <h2 class="section-heading mb-3">Mengapa BK Connect<br>Harus Hadir?</h2>
      <p class="section-sub mb-3">
        Berdasarkan data internal IT Del, lebih dari <strong>67% mahasiswa</strong> pernah mengalami stres akademik tinggi — namun kurang dari 20% yang mengakses layanan BK konvensional.
      </p>
      <p class="section-sub mb-4">
        Hambatan utamanya jelas: rasa malu, tidak tahu prosedur, dan keterbatasan waktu. BK Connect hadir untuk menghapus semua hambatan itu — melalui antarmuka digital yang intuitif, proses booking yang mudah, dan layanan yang fleksibel tanpa mengorbankan profesionalisme.
      </p>

      <!-- Feature highlight -->
      <div class="d-flex flex-column gap-2">
        <div class="d-flex align-items-center gap-3 p-3" style="background:var(--surface);border-radius:12px">
          <div style="min-width:40px;height:40px;background:rgba(46,134,193,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem">🎯</div>
          <div>
            <div style="font-weight:600;font-size:.9rem">Dirancang Bersama Mahasiswa</div>
            <div style="font-size:.82rem;color:var(--text-light)">Setiap fitur didesain berdasarkan riset dan masukan langsung dari mahasiswa IT Del</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-3 p-3" style="background:var(--surface);border-radius:12px">
          <div style="min-width:40px;height:40px;background:rgba(15,184,122,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem">🔐</div>
          <div>
            <div style="font-weight:600;font-size:.9rem">Kerahasiaan adalah Hak</div>
            <div style="font-size:.82rem;color:var(--text-light)">Tidak ada data yang dibagikan tanpa persetujuan eksplisit dari mahasiswa</div>
          </div>
        </div>
        <div class="d-flex align-items-center gap-3 p-3" style="background:var(--surface);border-radius:12px">
          <div style="min-width:40px;height:40px;background:rgba(245,166,35,.1);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem">📱</div>
          <div>
            <div style="font-weight:600;font-size:.9rem">Aksesibel Kapan Saja</div>
            <div style="font-size:.82rem;color:var(--text-light)">Bisa diakses dari asrama, rumah, bahkan saat KKN di daerah terpencil sekalipun</div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- STORY SECTION 3 – TIMELINE -->
<section class="container" style="margin-top:5rem">
  <div class="row g-5 align-items-start">

    <div class="col-lg-5">
      <div class="section-eyebrow"><i class="bi bi-clock-history"></i> Perjalanan</div>
      <h2 class="section-heading mb-3">Dari Ide ke Kenyataan</h2>
      <p class="section-sub">Setiap langkah dibangun di atas riset nyata dan kolaborasi bersama komunitas IT Del.</p>

      <!-- Image below -->
      <div class="story-img-card mt-4" style="aspect-ratio:16/9">
        <div class="img-illustration">🏫</div>
        <div class="img-overlay"></div>
        <div class="img-caption">
          <h6>Kampus Institut Teknologi Del</h6>
          <p>Sitoluama, Laguboti, Toba Samosir</p>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="tl-container mt-2">
        <div class="tl-node">
          <div class="tl-dot"></div>
          <span class="tl-year">2020</span>
          <h6>Inisiasi Unit BK IT Del</h6>
          <p>Unit BK resmi dibentuk di bawah Direktorat Kemahasiswaan IT Del. Layanan masih sepenuhnya bersifat offline dan tatap muka.</p>
        </div>
        <div class="tl-node">
          <div class="tl-dot" style="border-color:var(--accent)"></div>
          <span class="tl-year">2022</span>
          <h6>Studi Kebutuhan Digital</h6>
          <p>Riset mendalam bersama 234 mahasiswa mengungkap kesenjangan besar antara kebutuhan konseling dan akses nyata. Ide platform digital mulai dirancang.</p>
        </div>
        <div class="tl-node">
          <div class="tl-dot" style="border-color:var(--warm)"></div>
          <span class="tl-year">2023</span>
          <h6>Fase Desain & Prototipe</h6>
          <p>Wireframe pertama BK Connect dikerjakan. 50 mahasiswa pilot terlibat dalam usability testing. 3 iterasi desain dilakukan berdasarkan feedback.</p>
        </div>
        <div class="tl-node">
          <div class="tl-dot" style="border-color:var(--primary-light)"></div>
          <span class="tl-year">2024</span>
          <h6>Pengembangan Full Stack</h6>
          <p>Implementasi platform dengan Laravel + Bootstrap 5. Integrasi sistem booking, enkripsi data, dan dashboard mahasiswa diselesaikan.</p>
        </div>
        <div class="tl-node">
          <div class="tl-dot" style="border-color:#8e44ad;box-shadow:0 0 0 5px rgba(142,68,173,.15)"></div>
          <span class="tl-year" style="background:rgba(142,68,173,.1);color:#8e44ad">2024 — Sekarang</span>
          <h6>Peluncuran & Evaluasi</h6>
          <p>BK Connect resmi diluncurkan dengan fitur konseling online & offline, self-help module, dan hotline darurat. Evaluasi SUS berlangsung berkelanjutan.</p>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- NILAI-NILAI -->
<section style="background:white;padding:5rem 0;margin-top:5rem">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-eyebrow"><i class="bi bi-gem"></i> Nilai Kami</div>
      <h2 class="section-heading">Prinsip yang Memandu<br>Setiap Keputusan Kami</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="value-card" style="--vc-color:var(--primary-light)">
          <div class="vc-icon">🤝</div>
          <h6>Empati Tanpa Syarat</h6>
          <p>Kami percaya setiap mahasiswa berhak didengar tanpa dihakimi. Setiap sesi dimulai dari posisi memahami, bukan menilai.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card" style="--vc-color:var(--accent)">
          <div class="vc-icon">🔬</div>
          <h6>Berbasis Bukti Ilmiah</h6>
          <p>Semua intervensi dan modul self-help dikembangkan berdasarkan penelitian psikologi terkini yang telah tervalidasi secara klinis.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card" style="--vc-color:var(--warm)">
          <div class="vc-icon">🛡️</div>
          <h6>Integritas & Kerahasiaan</h6>
          <p>Data kamu adalah milik kamu sepenuhnya. Kami tidak berbagi, menjual, atau menggunakan informasi konseling untuk tujuan apapun selain pelayanan.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card" style="--vc-color:#8e44ad">
          <div class="vc-icon">♿</div>
          <h6>Aksesibilitas Universal</h6>
          <p>Tidak ada mahasiswa yang boleh tertinggal karena hambatan fisik, jarak, atau waktu. Platform kami dirancang untuk semua kondisi.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card" style="--vc-color:#e74c3c">
          <div class="vc-icon">🌱</div>
          <h6>Pertumbuhan Berkelanjutan</h6>
          <p>Kami terus belajar dari pengguna. Setiap feedback dipertimbangkan untuk iterasi platform yang semakin lebih baik.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="value-card" style="--vc-color:var(--primary)">
          <div class="vc-icon">🤲</div>
          <h6>Kolaborasi Komunitas</h6>
          <p>BK Connect bukan hanya platform — ini ekosistem. Kami membangun komunitas suportif di antara mahasiswa IT Del.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- TEAM -->
<section class="container" style="margin-top:5rem">
  <div class="text-center mb-5">
    <div class="section-eyebrow"><i class="bi bi-people"></i> Di Balik Layar</div>
    <h2 class="section-heading">Tim yang Membangun<br>BK Connect</h2>
  </div>
  <div class="row g-4 justify-content-center">
    <div class="col-md-6 col-lg-3">
      <div class="team-card-v2">
        <div class="tc-header" style="background:linear-gradient(135deg,#071825,#1a3a5c)">
          <div class="tc-avatar">👩‍💻</div>
        </div>
        <div class="tc-body">
          <div class="tc-name">Nama Mahasiswa</div>
          <div class="tc-role">Pengembang Utama · IT Del</div>
          <span class="tc-tag">Tugas Akhir 2024</span>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="team-card-v2">
        <div class="tc-header" style="background:linear-gradient(135deg,#0d2d4a,#0f4a72)">
          <div class="tc-avatar" style="background:linear-gradient(135deg,#0fb87a,#0a7a50)">👨‍🏫</div>
        </div>
        <div class="tc-body">
          <div class="tc-name">Dosen Pembimbing I</div>
          <div class="tc-role">Teknik Informatika · IT Del</div>
          <span class="tc-tag">Pembimbing Teknis</span>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="team-card-v2">
        <div class="tc-header" style="background:linear-gradient(135deg,#2c1654,#6c3483)">
          <div class="tc-avatar" style="background:linear-gradient(135deg,#f5a623,#e67e22)">👩‍🏫</div>
        </div>
        <div class="tc-body">
          <div class="tc-name">Dosen Pembimbing II</div>
          <div class="tc-role">Psikologi Pendidikan · IT Del</div>
          <span class="tc-tag">Pembimbing Konten</span>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-lg-3">
      <div class="team-card-v2">
        <div class="tc-header" style="background:linear-gradient(135deg,#0b3d24,#1e8449)">
          <div class="tc-avatar" style="background:linear-gradient(135deg,var(--primary),var(--primary-light))">🏛️</div>
        </div>
        <div class="tc-body">
          <div class="tc-name">Unit BK IT Del</div>
          <div class="tc-role">Kemahasiswaan IT Del</div>
          <span class="tc-tag">Mitra Operasional</span>
        </div>
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
function toggleFaq(el){
  const a=el.nextElementSibling;
  const isOpen=a.classList.contains('open');
  document.querySelectorAll('.faq-q').forEach(q=>q.classList.remove('open'));
  document.querySelectorAll('.faq-a').forEach(x=>x.classList.remove('open'));
  if(!isOpen){el.classList.add('open');a.classList.add('open');}
}
</script>
@endpush

<style>
@keyframes rotateSlow{from{transform:rotate(0)}to{transform:rotate(360deg)}}
</style>

@endsection