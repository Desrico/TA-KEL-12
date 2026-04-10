<!DOCTYPE html>
<html lang="id">
<head>
  <title>@yield('page-title', 'Dashboard') - Campus Care</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Fonts & Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="{{ asset('template/dist')}}/assets/fonts/tabler-icons.min.css">
  <link rel="stylesheet" href="{{ asset('template/dist')}}/assets/fonts/feather.css">
  <link rel="stylesheet" href="{{ asset('template/dist')}}/assets/fonts/fontawesome.css">
  <link rel="stylesheet" href="{{ asset('template/dist')}}/assets/fonts/material.css">
  <link rel="stylesheet" href="{{ asset('template/dist')}}/assets/css/style.css" id="main-style-link">
  <link rel="stylesheet" href="{{ asset('template/dist')}}/assets/css/style-preset.css">

  <style>
    :root {
      --kons-primary: #1a5c3a;
      --kons-accent:  #0fb87a;
      --kons-soft:    #e8f5ee;
      --kons-dark:    #0d1b2a;
      --kons-border:  #eef0f4;
      --kons-muted:   #8898aa;
      --kons-text:    #5a6a72;
    }

    /* ══ SIDEBAR ══ */
    .pc-sidebar .navbar-wrapper {
      background: #ffffff;
      border-right: 1px solid var(--kons-border);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    .pc-sidebar .m-header {
      padding: 20px 22px 18px;
      border-bottom: 1px solid var(--kons-border);
      flex-shrink: 0;
    }
    .pc-sidebar .navbar-content {
      padding: 14px 12px;
      flex: 1;
      overflow-y: auto;
      padding-bottom: 110px;
    }

    /* Nav Items */
    .pc-navbar .pc-item .pc-link {
      border-radius: 10px;
      font-size: .855rem;
      font-weight: 500;
      color: var(--kons-text);
      padding: 9px 13px;
      margin-bottom: 2px;
      transition: all .18s ease;
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }
    .pc-navbar .pc-item .pc-link:hover {
      background: var(--kons-soft);
      color: var(--kons-primary);
    }
    .pc-navbar .pc-item .pc-link:hover .pc-micon i {
      color: var(--kons-primary);
    }
    .pc-navbar .pc-item.active > .pc-link {
      background: var(--kons-accent) !important;
      color: white !important;
    }
    .pc-navbar .pc-item.active > .pc-link .pc-micon i {
      color: white !important;
    }
    .pc-navbar .pc-micon {
      width: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .pc-navbar .pc-micon i {
      font-size: 1.1rem;
      color: #aab5bc;
      transition: color .18s;
    }
    .pc-navbar .pc-caption label {
      font-size: .67rem;
      font-weight: 700;
      color: #c0cad2;
      text-transform: uppercase;
      letter-spacing: .07em;
      padding: 10px 13px 4px;
      display: block;
    }

    /* ══ SIDEBAR BOTTOM — Konselor Card ══ */
    .sidebar-konselor {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      padding: 12px 14px;
      border-top: 1px solid var(--kons-border);
      background: white;
    }
    .konselor-card {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 9px 11px;
      border-radius: 12px;
      background: var(--kons-soft);
      cursor: pointer;
      transition: background .18s;
    }
    .konselor-card:hover { background: #d4f0e4; }
    .konselor-avatar {
      width: 36px; height: 36px;
      border-radius: 50%;
      background: var(--kons-accent);
      display: flex; align-items: center; justify-content: center;
      color: white; font-size: .88rem; font-weight: 800;
      flex-shrink: 0;
      overflow: hidden;
    }
    .konselor-avatar img {
      width: 100%; height: 100%; object-fit: cover;
    }
    .konselor-info { flex: 1; min-width: 0; }
    .konselor-name {
      font-size: .8rem; font-weight: 700;
      color: var(--kons-primary);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      line-height: 1.2;
    }
    .konselor-role {
      font-size: .69rem; color: var(--kons-accent);
      display: flex; align-items: center; gap: 4px;
      margin-top: 2px;
    }
    .online-dot {
      width: 6px; height: 6px;
      border-radius: 50%;
      background: var(--kons-accent);
      display: inline-block;
      animation: pulse-dot 2s infinite;
    }
    @keyframes pulse-dot {
      0%,100% { opacity: 1; }
      50% { opacity: .4; }
    }
    .bantuan-link {
      display: flex; align-items: center; gap: 8px;
      padding: 8px 11px;
      font-size: .82rem; font-weight: 500;
      color: var(--kons-text);
      text-decoration: none;
      border-radius: 10px;
      margin-bottom: 6px;
      transition: all .18s;
    }
    .bantuan-link:hover {
      background: var(--kons-soft);
      color: var(--kons-primary);
    }
    .bantuan-link i { font-size: 1.05rem; color: #aab5bc; }

    /* ══ HEADER ══ */
    .pc-header {
      background: white;
      border-bottom: 1px solid var(--kons-border);
      box-shadow: 0 1px 8px rgba(0,0,0,.04);
    }
    .pc-head-link { color: var(--kons-text); }
    .pc-header .header-wrapper {
      padding-right: 14px;
      overflow: visible;
    }

    .admin-header-actions {
      display: flex;
      align-items: center;
      gap: .35rem;
      flex-wrap: nowrap;
    }

    .admin-notif-btn {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      border: 1px solid var(--kons-border);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      transition: all .2s ease;
    }
    .admin-notif-btn:hover {
      background: var(--kons-soft);
      color: var(--kons-primary);
    }
    .admin-notif-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      min-width: 17px;
      height: 17px;
      padding: 0 4px;
      border-radius: 999px;
      background: #e74c3c;
      color: #fff;
      border: 2px solid #fff;
      font-size: .58rem;
      line-height: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
    }
    .admin-notif-menu {
      min-width: 330px;
      border-radius: 14px;
      border: 1px solid var(--kons-border);
      box-shadow: 0 14px 34px rgba(13,27,42,.12);
      padding: 0;
      overflow: hidden;
    }
    .admin-notif-item {
      padding: .7rem .9rem;
      border-bottom: 1px solid rgba(13,27,42,.05);
    }
    .admin-notif-item:last-child { border-bottom: none; }

    .admin-user-trigger {
      border: 1px solid var(--kons-border);
      border-radius: 12px;
      padding: 4px 8px 4px 4px;
      min-height: 40px;
      max-width: 220px;
      background: #fff;
      transition: all .2s ease;
    }
    .admin-user-trigger:hover {
      background: var(--kons-soft);
      border-color: #dce5ee;
    }
    .admin-user-meta {
      line-height: 1.15;
      min-width: 0;
      max-width: 140px;
    }
    .admin-user-name {
      font-size: .8rem;
      font-weight: 700;
      color: #0d1b2a;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .admin-user-role {
      font-size: .68rem;
      color: var(--kons-accent);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .admin-user-menu {
      min-width: 220px;
      border-radius: 14px;
      border: 1px solid var(--kons-border);
      box-shadow: 0 14px 34px rgba(13,27,42,.12);
      overflow: hidden;
    }

    @media (max-width: 768px) {
      .pc-header .header-wrapper { padding-right: 8px; }
      .admin-user-trigger {
        padding: 2px;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        min-height: 38px;
      }
      .admin-notif-menu { min-width: 280px; }
      .admin-user-meta,
      .admin-user-trigger .ti-chevron-down { display: none !important; }
    }

    /* Header user badge */
    .hdr-user-avatar {
      width: 34px; height: 34px;
      border-radius: 50%;
      background: var(--kons-accent);
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: 800; font-size: .85rem;
      overflow: hidden; flex-shrink: 0;
    }
    .hdr-user-avatar img { width: 100%; height: 100%; object-fit: cover; }

    /* ══ MAIN CONTENT ══ */
    .pc-container { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ══ PAGE HEADER ══ */
    .page-header .page-header-title h5 {
      font-size: 1rem;
      font-weight: 700;
      color: var(--kons-dark);
    }

    /* ══ ALERT ══ */
    .alert { border-radius: 12px; font-size: .85rem; }

    /* ══ STAT CARDS (shared style for child pages) ══ */
    .stat-kons {
      background: white; border-radius: 16px;
      padding: 1.3rem; border: 1px solid var(--kons-border);
      box-shadow: 0 2px 12px rgba(0,0,0,.04);
      transition: transform .2s, box-shadow .2s;
    }
    .stat-kons:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
    .stat-kons .stat-icon {
      width: 46px; height: 46px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1.25rem; margin-bottom: .75rem;
    }
    .stat-kons .stat-num { font-size: 1.8rem; font-weight: 800; line-height: 1; color: var(--kons-dark); }
    .stat-kons .stat-lbl { font-size: .78rem; color: var(--kons-muted); margin-top: .2rem; }
    .stat-kons .stat-change { font-size: .74rem; font-weight: 600; margin-top: .4rem; }
    .change-up   { color: var(--kons-accent); }
    .change-down { color: #e74c3c; }

    /* ══ CARD (shared) ══ */
    .kons-card {
      background: white; border-radius: 16px;
      border: 1px solid var(--kons-border);
      box-shadow: 0 2px 12px rgba(0,0,0,.04); overflow: hidden;
    }
    .kons-card-header {
      padding: 1.1rem 1.4rem;
      border-bottom: 1px solid var(--kons-border);
      display: flex; align-items: center; justify-content: space-between;
    }
    .kons-card-header h6 { font-weight: 700; color: var(--kons-dark); margin: 0; }
    .kons-card-body { padding: 1.4rem; }

    /* ══ BADGE STATUS (shared) ══ */
    .badge-menunggu  { background:#fff8e6; color:#f5a623; border-radius:50px; padding:.2rem .75rem; font-size:.72rem; font-weight:600; }
    .badge-disetujui { background:#d4f7ea; color:#0fb87a; border-radius:50px; padding:.2rem .75rem; font-size:.72rem; font-weight:600; }
    .badge-ditolak   { background:#fdf2f2; color:#e74c3c; border-radius:50px; padding:.2rem .75rem; font-size:.72rem; font-weight:600; }
    .badge-selesai   { background:#e8f4fd; color:#2e86c1; border-radius:50px; padding:.2rem .75rem; font-size:.72rem; font-weight:600; }

    /* ══ FOOTER ══ */
    .pc-footer { border-top: 1px solid var(--kons-border); }
    .pc-footer p { font-size: .78rem; color: var(--kons-muted); }
  </style>

  @stack('styles')
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">

@php
  $adminNotifUnread = Auth::user()->notifikasi()->where('status', 'belum')->count();
  $adminNotifItems = Auth::user()->notifikasi()->latest()->take(6)->get();
@endphp

<!-- Pre-loader -->
<div class="loader-bg">
  <div class="loader-track"><div class="loader-fill"></div></div>
</div>

<!-- ══════════════════════════════════
     SIDEBAR
══════════════════════════════════ -->
<nav class="pc-sidebar">
  <div class="navbar-wrapper" style="position:relative;">

    <!-- Logo -->
    <div class="m-header">
      <a href="{{ route('admin.dashboard') }}" class="b-brand d-flex align-items-center gap-2 text-decoration-none">
        <img src="{{ asset('img/logo.png') }}" alt="logo"
             style="width:36px;height:36px;object-fit:contain;border-radius:8px;">
        <div>
          <div style="font-weight:800;font-size:.98rem;color:var(--kons-primary);line-height:1.1;">Campus Care</div>
          <div style="font-size:.68rem;color:var(--kons-muted);margin-top:1px;">Admin Panel</div>
        </div>
      </a>
    </div>

    <!-- Nav Menu -->
    <div class="navbar-content">
      <ul class="pc-navbar">

        <li class="pc-item pc-caption"><label>Menu Utama</label></li>

        <li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-layout-dashboard"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        <li class="pc-item {{ request()->routeIs('admin.booking*') ? 'active' : '' }}">
          <a href="{{ route('admin.booking') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-calendar-event"></i></span>
            <span class="pc-mtext">Penjadwalan</span>
          </a>
        </li>


        <li class="pc-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
          <a href="{{ route('admin.laporan') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-file-description"></i></span>
            <span class="pc-mtext">Laporan</span>
          </a>
        </li>

        <li class="pc-item {{ request()->routeIs('admin.mahasiswa*') ? 'active' : '' }}">
          <a href="{{ route('admin.mahasiswa') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-users"></i></span>
            <span class="pc-mtext">Riwayat Mahasiswa</span>
          </a>
        </li>

        <li class="pc-item pc-caption"><label>Lainnya</label></li>

        <li class="pc-item {{ request()->routeIs('admin.pengaturan*') ? 'active' : '' }}">
          <a href="{{ route('admin.pengaturan') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-settings"></i></span>
            <span class="pc-mtext">Pengaturan</span>
          </a>
        </li>

      </ul>
    </div>
  </div>
</nav>
<!-- SIDEBAR END -->

<!-- ══════════════════════════════════
     HEADER
══════════════════════════════════ -->
<header class="pc-header">
  <div class="header-wrapper">

    <!-- Mobile toggle -->
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
      </ul>
    </div>

    <!-- Kanan: Notif + User -->
    <div class="ms-auto admin-header-actions">
      <ul class="list-unstyled d-flex align-items-center gap-1 mb-0">

        <!-- Notifikasi -->
        <li class="dropdown pc-h-item">
          <a href="#" class="pc-head-link position-relative admin-notif-btn"
             data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ti ti-bell" style="font-size:1.15rem;"></i>
            @if($adminNotifUnread > 0)
            <span class="admin-notif-badge">{{ $adminNotifUnread > 9 ? '9+' : $adminNotifUnread }}</span>
            @endif
          </a>
          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown admin-notif-menu">
            <div class="dropdown-header d-flex align-items-center justify-content-between px-3 py-2">
              <h6 class="m-0 fw-700" style="font-size:.88rem;">Notifikasi</h6>
            </div>
            <div class="dropdown-divider m-0"></div>
            <div style="max-height:260px;overflow-y:auto;">
              @forelse($adminNotifItems as $notif)
              <a class="dropdown-item admin-notif-item" href="{{ route('admin.booking') }}">
                <div class="d-flex gap-2 align-items-start">
                  <div style="width:8px;height:8px;border-radius:50%;background:{{ $notif->status === 'belum' ? 'var(--kons-accent)' : '#d0dce4' }};margin-top:5px;flex-shrink:0;"></div>
                  <div style="min-width:0;">
                    <p class="mb-0" style="font-size:.8rem;color:#0d1b2a;font-weight:600;line-height:1.4;">{{ $notif->pesan }}</p>
                    <span style="font-size:.72rem;color:#aab5bc;">{{ $notif->created_at?->diffForHumans() ?? 'Baru saja' }}</span>
                  </div>
                </div>
              </a>
              @empty
              <div class="py-3 px-3" style="font-size:.8rem;color:#aab5bc;">Belum ada notifikasi.</div>
              @endforelse
            </div>
          </div>
        </li>

        <!-- User Info -->
        <li class="dropdown pc-h-item">
          <a href="#" class="pc-head-link d-flex align-items-center gap-2 admin-user-trigger"
             data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration:none;">
            <div class="hdr-user-avatar">
              {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
            </div>
            <div class="d-none d-md-block admin-user-meta">
              <div class="admin-user-name">
                {{ Auth::user()->nama }}
              </div>
              <div class="admin-user-role">Konselor</div>
            </div>
            <i class="ti ti-chevron-down d-none d-md-block" style="font-size:.8rem;color:#aab5bc;"></i>
          </a>

          <div class="dropdown-menu dropdown-menu-end pc-h-dropdown admin-user-menu">
            <div class="px-3 py-2 border-bottom">
              <div style="font-size:.85rem;font-weight:700;color:#0d1b2a;">{{ Auth::user()->nama }}</div>
              <div style="font-size:.72rem;color:var(--kons-accent);">Konselor</div>
            </div>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.pengaturan') }}">
              <i class="ti ti-user" style="font-size:1rem;color:#aab5bc;"></i>
              <span style="font-size:.83rem;">Profil Saya</span>
            </a>
            <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.pengaturan') }}">
              <i class="ti ti-settings" style="font-size:1rem;color:#aab5bc;"></i>
              <span style="font-size:.83rem;">Pengaturan</span>
            </a>
            <div class="dropdown-divider"></div>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit"
                class="dropdown-item d-flex align-items-center gap-2 py-2"
                style="font-size:.83rem;color:#e74c3c;">
                <i class="ti ti-logout" style="font-size:1rem;"></i>
                Keluar
              </button>
            </form>
          </div>
        </li>

      </ul>
    </div>

  </div>
</header>
<!-- HEADER END -->

<!-- ══════════════════════════════════
     MAIN CONTENT
══════════════════════════════════ -->
<div class="pc-container">
  <div class="pc-content">

    <!-- Breadcrumb -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">
              <h5 class="m-b-10">@yield('page-title', 'Dashboard')</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" style="color:var(--kons-accent);"></a>
              </li>
              <li class="breadcrumb-item" aria-current="page">
                @yield('page-title', 'Dashboard')
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
      <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
        <i class="ti ti-circle-check" style="font-size:1.1rem;"></i>
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
        <i class="ti ti-alert-circle" style="font-size:1.1rem;"></i>
        {{ session('error') }}
      </div>
    @endif

    @yield('konten')

  </div>
</div>
<!-- MAIN CONTENT END -->

<!-- Footer -->
<footer class="pc-footer">
  <div class="footer-wrapper container-fluid">
    <div class="row">
      <div class="col-sm my-1">
        <p class="m-0">Campus Care &copy; {{ date('Y') }} &middot; IT Del Mental Health Platform</p>
      </div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="{{ asset('template/dist')}}/assets/js/plugins/popper.min.js"></script>
<script src="{{ asset('template/dist')}}/assets/js/plugins/simplebar.min.js"></script>
<script src="{{ asset('template/dist')}}/assets/js/plugins/bootstrap.min.js"></script>
<script src="{{ asset('template/dist')}}/assets/js/fonts/custom-font.js"></script>
<script src="{{ asset('template/dist')}}/assets/js/pcoded.js"></script>
<script src="{{ asset('template/dist')}}/assets/js/plugins/feather.min.js"></script>

<script>layout_change('light');</script>
<script>change_box_container('false');</script>
<script>layout_rtl_change('false');</script>
<script>preset_change("preset-1");</script>
<script>font_change("Public-Sans");</script>

@stack('scripts')
</body>
</html>