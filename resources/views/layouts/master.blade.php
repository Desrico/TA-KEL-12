<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>BK Connect - IT Del Mental Health</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,700;1,9..144,400&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    :root {
      --primary:       #1a3a5c;
      --primary-mid:   #1e5f8e;
      --primary-light: #2e86c1;
      --accent:        #0fb87a;
      --accent-soft:   #d4f7ea;
      --warm:          #f5a623;
      --surface:       #f0f5fa;
      --surface2:      #e4edf6;
      --text-dark:     #0d1b2a;
      --text-mid:      #4a5568;
      --text-light:    #8898aa;
      --white:         #ffffff;
      --shadow-sm:     0 2px 16px rgba(13,27,42,.08);
      --shadow-md:     0 8px 40px rgba(13,27,42,.12);
      --shadow-lg:     0 20px 60px rgba(13,27,42,.18);
      --radius:        16px;
    }
    *{box-sizing:border-box;margin:0;padding:0;}
    html{scroll-behavior:smooth;}
    body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--surface);color:var(--text-dark);overflow-x:hidden;}

    /* NAVBAR */
    .navbar-main{
      background:rgba(255,255,255,0.96);
      backdrop-filter:blur(20px);
      border-bottom:1px solid rgba(26,58,92,.07);
      padding:.5rem 0;position:sticky;top:0;z-index:1000;
      transition:box-shadow .3s;
    }
    .navbar-main.scrolled{box-shadow:0 4px 30px rgba(13,27,42,.12);}
    .brand-icon{
      width:38px;height:38px;border-radius:10px;flex-shrink:0;
      background:linear-gradient(135deg,var(--primary),var(--primary-light));
      display:flex;align-items:center;justify-content:center;
      color:white;font-size:1.1rem;box-shadow:0 4px 12px rgba(26,58,92,.3);
    }
    .brand-top{font-family:'Fraunces',serif;font-weight:700;font-size:1.1rem;color:var(--primary);line-height:1;}
    .brand-sub{font-size:.6rem;color:var(--text-light);letter-spacing:.05em;text-transform:uppercase;}
    .nav-link-custom{
      font-size:.875rem;font-weight:600;color:var(--text-mid)!important;
      padding:.4rem .9rem!important;border-radius:9px;transition:all .2s;
    }
    .nav-link-custom:hover,.nav-link-custom.active{
      color:var(--primary)!important;background:rgba(26,58,92,.07);
    }
    .notif-link{
      position:relative;display:inline-flex;align-items:center;justify-content:center;
      width:36px;height:36px;border-radius:50%;color:var(--text-mid);
      text-decoration:none;background:transparent;transition:all .2s;
    }
    .notif-link:hover{background:rgba(26,58,92,.07);color:var(--primary);}
    .notif-badge{
      position:absolute;top:-2px;right:-3px;min-width:17px;height:17px;padding:0 4px;
      border-radius:999px;background:#e74c3c;color:white;font-size:.62rem;
      font-weight:700;display:flex;align-items:center;justify-content:center;
      border:2px solid #fff;
    }
    .notif-dropdown{
      min-width:320px;border:none;border-radius:12px;box-shadow:var(--shadow-md);
      padding:.4rem 0;overflow:hidden;
    }
    .notif-header{padding:.55rem .9rem;font-size:.75rem;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:.05em;}
    .notif-item{display:block;padding:.62rem .9rem;text-decoration:none;border-top:1px solid rgba(26,58,92,.05);}
    .notif-item:hover{background:var(--surface);}
    .notif-item p{margin:0;font-size:.8rem;color:var(--text-dark);line-height:1.45;}
    .notif-time{display:block;font-size:.7rem;color:var(--text-light);margin-top:.25rem;}
    .notif-empty{padding:.8rem .9rem;font-size:.78rem;color:var(--text-light);}
    /* Profile btn */
    .profile-wrap{position:relative;}
    .profile-btn{
      width:36px;height:36px;border-radius:50%;
      background:linear-gradient(135deg,var(--primary),var(--primary-light));
      border:2.5px solid rgba(255,255,255,.9);
      box-shadow:0 2px 10px rgba(26,58,92,.3);
      display:flex;align-items:center;justify-content:center;
      color:white;cursor:pointer;font-size:.9rem;transition:all .2s;
      position:relative;
    }
    .profile-btn:hover{transform:scale(1.08);box-shadow:0 4px 18px rgba(26,58,92,.4);}
    .online-dot{
      position:absolute;bottom:1px;right:1px;
      width:9px;height:9px;border-radius:50%;
      background:var(--accent);border:2px solid white;
    }
    .profile-dropdown{
      position:absolute;top:calc(100% + 10px);right:0;
      background:white;border-radius:14px;
      box-shadow:var(--shadow-lg);min-width:220px;
      border:1px solid rgba(26,58,92,.08);
      opacity:0;pointer-events:none;
      transform:translateY(-8px);
      transition:all .2s;z-index:999;
    }
    .profile-dropdown.show{opacity:1;pointer-events:all;transform:translateY(0);}
    .pd-header{padding:1rem;border-bottom:1px solid rgba(26,58,92,.08);display:flex;align-items:center;gap:.7rem;}
    .pd-avatar{width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;flex-shrink:0;}
    .pd-name{font-weight:700;font-size:.85rem;color:var(--text-dark);}
    .pd-nim{font-size:.72rem;color:var(--text-light);}
    .pd-item{display:flex;align-items:center;gap:.6rem;padding:.62rem 1rem;font-size:.83rem;color:var(--text-mid);text-decoration:none;transition:background .15s;}
    .pd-item:hover{background:var(--surface);color:var(--primary);}
    .pd-item i{font-size:1rem;width:18px;}
    .pd-item.danger{color:#e74c3c;}
    .pd-item.danger:hover{background:#fdf2f2;}
    .pd-divider{height:1px;background:rgba(26,58,92,.07);margin:.25rem 0;}

    /* FOOTER */
    footer{
      background:var(--primary);
      background-image:radial-gradient(circle at 15% 85%,rgba(46,134,193,.2) 0%,transparent 45%),
                       radial-gradient(circle at 85% 15%,rgba(15,184,122,.1) 0%,transparent 45%);
      color:rgba(255,255,255,.7);padding:4rem 0 2rem;margin-top:5rem;
    }
    .footer-brand-txt{font-family:'Fraunces',serif;font-size:1.5rem;font-weight:700;color:white;}
    footer h6{color:rgba(255,255,255,.45);font-weight:700;letter-spacing:.08em;text-transform:uppercase;font-size:.7rem;margin-bottom:1rem;}
    footer a{color:rgba(255,255,255,.6);text-decoration:none;font-size:.85rem;display:block;margin-bottom:.4rem;transition:color .2s;}
    footer a:hover{color:white;}
    .footer-copy{border-top:1px solid rgba(255,255,255,.1);margin-top:2.5rem;padding-top:1.5rem;font-size:.77rem;color:rgba(255,255,255,.35);text-align:center;}
    .footer-social a{display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.1);color:rgba(255,255,255,.65)!important;transition:all .2s;margin-right:.35rem;}
    .footer-social a:hover{background:rgba(255,255,255,.2);color:white!important;}

    /* PAGE ANIM */
    .page-in{animation:pageIn .5s cubic-bezier(.22,.61,.36,1) both;}
    @keyframes pageIn{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}

    /* UTILITIES */
    .section-eyebrow{display:inline-flex;align-items:center;gap:6px;background:var(--accent-soft);color:var(--accent);border-radius:50px;padding:.28rem .85rem;font-size:.73rem;font-weight:700;letter-spacing:.05em;text-transform:uppercase;margin-bottom:.6rem;}
    .section-heading{font-family:'Fraunces',serif;font-size:clamp(1.8rem,3vw,2.6rem);font-weight:700;color:var(--text-dark);line-height:1.15;}
    .section-sub{font-size:.93rem;color:var(--text-mid);line-height:1.78;}
    .card-hover{transition:transform .28s,box-shadow .28s;}
    .card-hover:hover{transform:translateY(-5px);box-shadow:var(--shadow-md);}
  </style>
  @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-main" id="mainNav">
    <div class="container">
        @php
          $unreadNotif = 0;
          $notifItems = collect();
          if (Auth::check()) {
            $mahasiswaId = optional(Auth::user()->mahasiswa)->id;
            if ($mahasiswaId) {
              $approvedBookings = \App\Models\JadwalKonseling::where('mahasiswa_id', $mahasiswaId)
                ->where('status', 'disetujui')
                ->get(['id', 'tanggal', 'waktu']);

              foreach ($approvedBookings as $jadwal) {
                $pesan = 'Booking #' . $jadwal->id . ' pada ' . $jadwal->tanggal . ' pukul ' . $jadwal->waktu . ' telah disetujui oleh konselor.';
                \App\Models\Notifikasi::firstOrCreate(
                  ['user_id' => Auth::id(), 'pesan' => $pesan],
                  ['status' => 'belum']
                );
              }
            }

              $unreadNotif = Auth::user()->notifikasi()->where('status', 'belum')->count();
              $notifItems = Auth::user()->notifikasi()->latest()->take(6)->get();
          }
        @endphp

        <a class="d-flex align-items-center gap-2 text-decoration-none" href="/">

            <!-- LOGO GAMBAR -->
            <div class="">
                <img src="{{ asset('img/logo.png') }}" 
                  alt="Logo Campus Care"
                  style="width: 45px; height: 45px; object-fit: contain;">
            </div>

            <!-- TEXT -->
            <div>
                <div class="brand-top">Campus Care</div>
                <div class="brand-sub">IT Del - Mental Health</div>
            </div>
        </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link nav-link-custom {{ request()->is('/') ? 'active' : '' }}" href="/">Beranda</a></li>
        <li class="nav-item"><a class="nav-link nav-link-custom {{ request()->is('about') ? 'active' : '' }}" href="/about">About</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link nav-link-custom dropdown-toggle {{ request()->is('layanan*') ? 'active' : '' }}" href="#" id="layananDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Layanan
          </a>
          <ul class="dropdown-menu" aria-labelledby="layananDropdown" style="border:none;border-radius:12px;box-shadow:var(--shadow-md);padding:.4rem .35rem;min-width:190px;">
            <li><a class="dropdown-item rounded-3" href="/layanan#online" style="font-size:.84rem;padding:.5rem .65rem;">Konseling Online</a></li>
            <li><a class="dropdown-item rounded-3" href="/layanan#offline" style="font-size:.84rem;padding:.5rem .65rem;">Konseling Offline</a></li>
          </ul>
        </li>

        @auth
        <li class="nav-item dropdown ms-1">
          <a class="notif-link" href="#" id="notifDropdownBtn" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi">
            <i class="bi bi-bell" style="font-size:1rem;"></i>
            <span id="notifBadge" class="notif-badge {{ $unreadNotif > 0 ? '' : 'd-none' }}">{{ $unreadNotif > 9 ? '9+' : $unreadNotif }}</span>
          </a>
          <div class="dropdown-menu dropdown-menu-end notif-dropdown" aria-labelledby="notifDropdownBtn">
            <div class="notif-header">Notifikasi</div>
            @forelse($notifItems as $notif)
              <a href="{{ route('riwayat') }}" class="notif-item">
                <p>{{ $notif->pesan }}</p>
                <span class="notif-time">{{ $notif->created_at?->diffForHumans() ?? 'Baru saja' }}</span>
              </a>
            @empty
              <div class="notif-empty">Belum ada notifikasi.</div>
            @endforelse
          </div>
        </li>
        @endauth
      </ul>
      <div class="d-flex align-items-center ms-lg-3 mt-3 mt-lg-0">
        {{-- Jika SUDAH LOGIN --}}
       @auth
        <div class="profile-wrap">
            <div class="profile-btn" id="profileBtn" onclick="toggleProfile()">
                @if(optional(Auth::user()->profil)->foto)
                    <img src="{{ Storage::url(Auth::user()->profil->foto) }}"
                        style="width:100%;height:100%;border-radius:50%;object-fit:cover">
                @else
                    <i class="bi bi-person-fill"></i>
                @endif
                <div class="online-dot"></div>
            </div>

            <div class="profile-dropdown" id="profileDropdown">
                <div class="pd-header">
                  <div class="pd-avatar">
                      @if(optional(Auth::user()->profil)->foto)
                          <img src="{{ Storage::url(Auth::user()->profil->foto) }}"
                              style="width:40px;height:40px;border-radius:50%;object-fit:cover">
                      @else
                          <i class="bi bi-person-fill"></i>
                      @endif
                  </div>
                  <div>
                      @if(Auth::user()->isAnonim())
                          <div class="pd-name">🎭 Mahasiswa Anonim</div>
                          <div class="pd-nim">
                              {{ optional(Auth::user()->mahasiswa)->jurusan ?? '' }}
                              {{ optional(Auth::user()->mahasiswa)->angkatan ?? '' }}
                          </div>
                      @else
                          <div class="pd-name">{{ Auth::user()->nama }}</div>
                          <div class="pd-nim">
                              {{ optional(Auth::user()->mahasiswa)->nim ?? '' }}
                              · {{ optional(Auth::user()->mahasiswa)->jurusan ?? '' }}
                              {{ optional(Auth::user()->mahasiswa)->angkatan ?? '' }}
                          </div>
                      @endif
                  </div>
              </div>

                <a href="{{ route('profil') }}" class="pd-item">
                    <i class="bi bi-person-circle"></i> Profil Saya
                </a>

                <a href="{{ route('riwayat') }}" class="pd-item">
                    <i class="bi bi-calendar2-check"></i> Riwayat Konseling
                </a>

                <div class="pd-divider"></div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="pd-item danger w-100 text-start border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
        @endauth


        {{-- Jika BELUM LOGIN --}}
        @guest
        <a href="{{ route('login') }}" class="btn btn-outline-success me-2">Login</a>
        <a href="{{ route('register') }}" class="btn btn-success">Daftar</a>
        @endguest

      <!-- </div>
            </div>
            <a href="#" class="pd-item"><i class="bi bi-person-circle"></i> Profil Saya</a>
            <a href="#" class="pd-item"><i class="bi bi-calendar2-check"></i> Riwayat Konseling</a>
            <a href="#" class="pd-item"><i class="bi bi-bell"></i> Notifikasi <span class="badge bg-danger ms-auto" style="font-size:.62rem">3</span></a>
            <div class="pd-divider"></div>
            <a href="#" class="pd-item danger"><i class="bi bi-box-arrow-right"></i> Keluar</a>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</nav>

<div class="page-in">@yield('konten')</div>

<!-- FOOTER -->
<footer>
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <div class="footer-brand-txt mb-2"><i class="bi bi-heart-pulse-fill me-2" style="color:var(--accent)"></i>BK Connect</div>
        <p style="font-size:.86rem;line-height:1.75;margin-bottom:1.5rem">Platform Bimbingan dan Konseling digital IT Del — mendukung kesehatan mental mahasiswa dengan layanan profesional, aman, dan mudah diakses.</p>
        <div class="footer-social">
          <a href="#"><i class="bi bi-instagram"></i></a>
          <a href="#"><i class="bi bi-whatsapp"></i></a>
          <a href="#"><i class="bi bi-youtube"></i></a>
          <a href="#"><i class="bi bi-envelope-fill"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <h6>Navigasi</h6>
        <a href="/">Beranda</a><a href="/about">About</a><a href="/layanan">Layanan</a>
      </div>
      <div class="col-6 col-lg-3">
        <h6>Layanan</h6>
        <a href="/layanan#online">Konseling Online</a>
        <a href="/layanan#offline">Konseling Offline</a>
        <a href="#">Self-Help Resources</a>
        <a href="#">Hotline Darurat</a>
      </div>
      <div class="col-lg-3">
        <h6>Kontak</h6>
        <a href="mailto:bk@del.ac.id"><i class="bi bi-envelope me-2"></i>bk@del.ac.id</a>
        <a href="#"><i class="bi bi-telephone me-2"></i>(0623) 95102</a>
        <a href="#"><i class="bi bi-geo-alt me-2"></i>Sitoluama, Laguboti, Toba</a>
        <div class="mt-3 p-3" style="background:rgba(255,255,255,.07);border-radius:10px;">
          <div style="color:rgba(255,255,255,.4);font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.3rem">Jam Operasional</div>
          <div style="color:rgba(255,255,255,.8);font-size:.82rem;">Senin – Jumat: 08.00 – 17.00</div>
        </div>
      </div>
    </div>
    <div class="footer-copy">© 2024 BK Connect · Institut Teknologi Del — Pengembangan Digital Mental Health Intervention</div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
window.addEventListener('scroll',()=>{
  document.getElementById('mainNav').classList.toggle('scrolled',window.scrollY>20);
});
function toggleProfile(){
  document.getElementById('profileDropdown').classList.toggle('show');
}
document.addEventListener('click',(e)=>{
  if(!document.getElementById('profileBtn')?.contains(e.target)&&!document.getElementById('profileDropdown')?.contains(e.target)){
    document.getElementById('profileDropdown')?.classList.remove('show');
  }
});

const notifDropdownBtn = document.getElementById('notifDropdownBtn');
const notifBadge = document.getElementById('notifBadge');
let notifMarkedRead = false;

if (notifDropdownBtn) {
  notifDropdownBtn.addEventListener('shown.bs.dropdown', async () => {
    if (notifMarkedRead || !notifBadge || notifBadge.classList.contains('d-none')) {
      return;
    }

    notifMarkedRead = true;

    try {
      const response = await fetch("{{ route('notifikasi.baca') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
      });

      if (response.ok) {
        notifBadge.classList.add('d-none');
      } else {
        notifMarkedRead = false;
      }
    } catch (error) {
      notifMarkedRead = false;
    }
  });
}
</script>
@stack('scripts')
</body>
</html>