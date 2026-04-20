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
    --primary: #064E3B;
    --primary-700: #065F46;
    --primary-600: #047857;
    --primary-500: #10B981;
    --primary-soft: #D1FAE5;

    --navbar-bg: #EFFCF5;
    --surface: #FFFFFF;
    --surface-soft: #F7FCF9;
    --border: #DDEFE7;

    --text-dark: #0F172A;
    --text-mid: #475569;
    --text-light: #64748B;
    --white: #FFFFFF;
    --danger: #DC2626;

    --shadow-sm: 0 2px 12px rgba(6, 78, 59, 0.06);
    --shadow-md: 0 10px 30px rgba(6, 78, 59, 0.10);
    --radius: 16px;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }

  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: var(--surface);
    color: var(--text-dark);
    overflow-x: hidden;
  }

  /* NAVBAR */
  .navbar-main {
    background: rgba(239, 252, 245, 0.96);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    padding: .7rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: all .25s ease;
  }

  .navbar-main.scrolled {
    box-shadow: var(--shadow-sm);
  }

  .brand-top {
    font-family: 'Fraunces', serif;
    font-weight: 700;
    font-size: 1.05rem;
    color: var(--primary);
    line-height: 1.1;
  }

  .brand-sub {
    font-size: .68rem;
    color: var(--text-light);
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  .nav-link-custom {
    font-size: .92rem;
    font-weight: 600;
    color: var(--text-mid) !important;
    padding: .55rem .95rem !important;
    border-radius: 10px;
    transition: all .2s ease;
  }

  .nav-link-custom:hover,
  .nav-link-custom.active {
    color: var(--primary) !important;
    background: var(--primary-soft);
  }

  .navbar .dropdown-menu {
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: var(--shadow-md);
    padding: .45rem;
  }

  .navbar .dropdown-item {
    border-radius: 10px;
    font-size: .9rem;
    padding: .6rem .75rem;
    color: var(--text-mid);
  }

  .navbar .dropdown-item:hover {
    background: var(--primary-soft);
    color: var(--primary);
  }

  .notif-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 999px;
    color: var(--text-mid);
    text-decoration: none;
    transition: all .2s ease;
  }

  .notif-link:hover {
    background: var(--primary-soft);
    color: var(--primary);
  }

  .notif-badge {
    position: absolute;
    top: -2px;
    right: -3px;
    min-width: 17px;
    height: 17px;
    padding: 0 4px;
    border-radius: 999px;
    background: var(--danger);
    color: white;
    font-size: .62rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
  }

  .notif-dropdown {
    min-width: 320px;
    border: 1px solid var(--border) !important;
    border-radius: 14px;
    box-shadow: var(--shadow-md);
    padding: .4rem 0;
    overflow: hidden;
    background: var(--white);
  }

  .notif-header {
    padding: .6rem 1rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .notif-item {
    display: block;
    padding: .7rem 1rem;
    text-decoration: none;
    border-top: 1px solid #F1F5F9;
  }

  .notif-item:hover {
    background: #F8FFFB;
  }

  .notif-item p {
    margin: 0;
    font-size: .84rem;
    color: var(--text-dark);
    line-height: 1.45;
  }

  .notif-time {
    display: block;
    font-size: .72rem;
    color: var(--text-light);
    margin-top: .25rem;
  }

  .notif-empty {
    padding: .9rem 1rem;
    font-size: .82rem;
    color: var(--text-light);
  }

  .profile-wrap { position: relative; }

 .profile-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  overflow: hidden;
  background: #f1f5f9;
  border: 2px solid rgba(255,255,255,.95);
  box-shadow: var(--shadow-sm);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  position: relative;
  transition: transform .2s ease, box-shadow .2s ease;
}

.profile-btn:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.profile-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.online-dot {
  position: absolute;
  bottom: 1px;
  right: 1px;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: var(--primary-500);
  border: 2px solid white;
}

.profile-dropdown {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  width: 270px;
  background: white;
  border-radius: 14px;
  box-shadow: var(--shadow-md);
  border: 1px solid var(--border);
  opacity: 0;
  pointer-events: none;
  transform: translateY(-8px);
  transition: all .2s ease;
  z-index: 999;
  overflow: hidden;
}

.profile-dropdown.show {
  opacity: 1;
  pointer-events: all;
  transform: translateY(0);
}

.pd-header {
  padding: .9rem 1rem;
  border-bottom: 1px solid #F1F5F9;
  display: flex;
  align-items: center;
  gap: .75rem;
}

.pd-avatar {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  overflow: hidden;
  background: #f1f5f9;
  flex-shrink: 0;
}

.pd-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.pd-name {
  font-weight: 700;
  font-size: .92rem;
  color: var(--text-dark);
  line-height: 1.2;
  margin-bottom: .15rem;
}

.pd-nim {
  font-size: .76rem;
  color: var(--text-light);
  line-height: 1.45;
  word-break: break-word;
}

.pd-item {
  display: flex;
  align-items: center;
  gap: .7rem;
  width: 100%;
  padding: .78rem 1rem;
  font-size: .88rem;
  color: var(--text-mid);
  text-decoration: none;
  background: transparent;
  border: 0;
  transition: all .15s ease;
}

.pd-item:hover {
  background: #F8FFFB;
  color: var(--primary);
}

.pd-item i {
  font-size: .98rem;
  width: 18px;
  flex-shrink: 0;
}

.pd-item.danger {
  color: var(--danger);
}

.pd-item.danger:hover {
  background: #FEF2F2;
  color: #dc2626;
}

.pd-divider {
  height: 1px;
  background: #F1F5F9;
  margin: 0;
}

.btn-login-custom {
  border: 1px solid var(--primary);
  color: var(--primary);
  background: transparent;
  border-radius: 12px;
  padding: .58rem 1rem;
  font-weight: 600;
  font-size: .9rem;
  transition: all .2s ease;
}

.btn-login-custom:hover {
  background: var(--primary-soft);
  color: var(--primary);
}

.btn-register-custom {
  border: 1px solid var(--primary);
  background: var(--primary);
  color: white;
  border-radius: 12px;
  padding: .58rem 1rem;
  font-weight: 600;
  font-size: .9rem;
  transition: all .2s ease;
}

.btn-register-custom:hover {
  background: var(--primary-700);
  border-color: var(--primary-700);
  color: white;
}

.page-in {
  animation: pageIn .45s ease both;
}

@keyframes pageIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

/* FOOTER */
footer {
  background: var(--primary);
  color: rgba(255,255,255,.78);
  padding: 4rem 0 2rem;
  margin-top: 5rem;
}

.footer-brand-txt {
  font-family: 'Fraunces', serif;
  font-size: 1.45rem;
  font-weight: 700;
  color: white;
}

footer h6 {
  color: rgba(255,255,255,.55);
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  font-size: .72rem;
  margin-bottom: 1rem;
}

footer a {
  color: rgba(255,255,255,.78);
  text-decoration: none;
  font-size: .88rem;
  display: block;
  margin-bottom: .5rem;
  transition: color .2s ease;
}

footer a:hover {
  color: #D1FAE5;
}

.footer-copy {
  border-top: 1px solid rgba(255,255,255,.14);
  margin-top: 2.5rem;
  padding-top: 1.5rem;
  font-size: .78rem;
  color: rgba(255,255,255,.55);
  text-align: center;
}

.footer-social a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255,255,255,.10);
  color: rgba(255,255,255,.85) !important;
  transition: all .2s ease;
  margin-right: .4rem;
}

.footer-social a:hover {
  background: rgba(255,255,255,.18);
  color: white !important;
}

@media (max-width: 991.98px) {
  .navbar-nav {
    padding-top: 1rem;
    align-items: flex-start !important;
  }

  .navbar-collapse {
    background: var(--navbar-bg);
    border-radius: 16px;
    padding: .5rem .25rem 1rem;
    margin-top: .75rem;
  }

  .profile-dropdown {
    width: 250px;
  }
}
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
        <li class="nav-item">
          <a class="nav-link nav-link-custom {{ request()->is('tentang') ? 'active' : '' }}" href="/tentang">Tentang</a>
        </li>
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
              <img 
                  src="{{ optional(Auth::user()->profil)->foto 
                          ? Storage::url(Auth::user()->profil->foto) 
                          : asset('img/default-avatar.png') }}"
                  alt="Profile"
                  class="profile-img"
              >
              <div class="online-dot"></div>
          </div>

          <div class="profile-dropdown" id="profileDropdown">
              <div class="pd-header">
                  <div class="pd-avatar">
                      <img 
                          src="{{ optional(Auth::user()->profil)->foto 
                                  ? Storage::url(Auth::user()->profil->foto) 
                                  : asset('img/default-avatar.png') }}"
                          alt="Profile"
                          class="pd-avatar-img"
                      >
                  </div>

                  <div>
                      @if(Auth::user()->isAnonim())
                          <div class="pd-name">Mahasiswa Anonim</div>
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
                  <i class="bi bi-person-circle"></i>
                  <span>Profil Saya</span>
              </a>

              <a href="{{ route('riwayat') }}" class="pd-item">
                  <i class="bi bi-calendar2-check"></i>
                  <span>Riwayat Konseling</span>
              </a>

              <div class="pd-divider"></div>

              <form action="{{ route('logout') }}" method="POST" class="m-0">
                  @csrf
                  <button type="submit" class="pd-item danger w-100 text-start bg-transparent border-0">
                      <i class="bi bi-box-arrow-right"></i>
                      <span>Keluar</span>
                  </button>
              </form>
          </div>
      </div>
        @endauth


        {{-- Jika BELUM LOGIN --}}
        @guest
        <a href="{{ route('login') }}" class="btn btn-login-custom me-2">Login</a>
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