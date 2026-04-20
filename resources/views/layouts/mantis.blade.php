<!DOCTYPE html>
<html lang="id">
<head>
  <title>@yield('page-title', 'Dashboard') - Campus Care</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">
  <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/tabler-icons.min.css">
  <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/feather.css">
  <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/fontawesome.css">
  <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/fonts/material.css">
  <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/css/style.css" id="main-style-link">
  <link rel="stylesheet" href="{{ asset('template/dist') }}/assets/css/style-preset.css">

  <style>
    :root {
        --admin-primary: #064E3B;
        --admin-primary-700: #065F46;
        --admin-primary-600: #047857;
        --admin-primary-500: #10B981;
        --admin-soft: #D1FAE5;
        --admin-soft-2: #EFFCF5;
        --admin-bg: #F7FCF9;
        --admin-border: #DDEFE7;
        --admin-text: #0F172A;
        --admin-text-mid: #475569;
        --admin-text-light: #64748B;
        --admin-danger: #DC2626;
        --admin-white: #FFFFFF;
        --admin-shadow-sm: 0 2px 12px rgba(6, 78, 59, 0.06);
        --admin-shadow-md: 0 10px 30px rgba(6, 78, 59, 0.10);
    }

    body {
        background: var(--admin-bg);
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* SIDEBAR */
    .pc-sidebar .navbar-wrapper {
          background: var(--admin-white);
          border-right: 1px solid var(--admin-border);
          display: flex;
          flex-direction: column;
          min-height: 100vh;
          height: 100vh;
          position: relative;
      }

      .pc-sidebar .m-header {
          padding: 20px 22px 18px;
          border-bottom: 1px solid var(--admin-border);
          flex-shrink: 0;
          background: var(--admin-soft-2);
      }
    .pc-sidebar .navbar-content {
      padding: 16px 12px 120px;
      flex: 1;
      overflow-y: auto;
    }

    .pc-navbar .pc-caption label {
      font-size: .67rem;
      font-weight: 700;
      color: #9AA8B5;
      text-transform: uppercase;
      letter-spacing: .07em;
      padding: 10px 13px 6px;
      display: block;
    }

    .pc-navbar .pc-item .pc-link {
      border-radius: 12px;
      font-size: .88rem;
      font-weight: 600;
      color: var(--admin-text-mid);
      padding: 10px 14px;
      margin-bottom: 4px;
      transition: all .18s ease;
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
  }

    .pc-navbar .pc-item .pc-link:hover {
      background: var(--admin-soft-2);
      color: var(--admin-primary);
    }

    .pc-navbar .pc-item .pc-link:hover .pc-micon i {
      color: var(--admin-primary);
    }

    .pc-navbar .pc-item.active > .pc-link {
      background: var(--admin-primary);
      color: white !important;
      box-shadow: var(--admin-shadow-sm);
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
      font-size: 1.08rem;
      color: #9AA8B5;
      transition: color .18s ease;
    }

    /* SIDEBAR PROFILE BOTTOM */
   .admin-sidebar-profile {
      position: absolute;
      left: 0;
      right: 0;
      bottom: 0;
      padding: 16px 12px 18px;
      border-top: 1px solid var(--admin-border);
      background: var(--admin-white);
      z-index: 5;
    }

    .admin-sidebar-profile-trigger {
      display: flex;
      align-items: center;
      gap: 12px;
      width: 100%;
      padding: 14px 10px 8px;
      text-decoration: none;
      background: transparent;
      border: 0;
      box-shadow: none;
    }

    .admin-sidebar-profile-trigger:hover {
      background: transparent;
    }

    .admin-sidebar-avatar {
      width: 46px;
      height: 46px;
      border-radius: 50%;
      overflow: hidden;
      background: #d9e6df;
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--admin-primary);
      font-weight: 700;
      font-size: 1rem;
    }

    .admin-sidebar-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .admin-sidebar-user {
      min-width: 0;
      flex: 1;
    }

    .admin-sidebar-name {
      font-size: 1.02rem;
      font-weight: 700;
      color: var(--admin-text);
      line-height: 1.2;
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .admin-sidebar-role {
      font-size: .82rem;
      color: var(--admin-text-mid);
      line-height: 1.2;
    }

    .admin-sidebar-arrow {
      font-size: .9rem;
      color: var(--admin-text-light);
      flex-shrink: 0;
    }

    .admin-sidebar-menu {
      width: calc(100% - 4px);
      min-width: unset;
      margin: 8px 2px 0;
      border-radius: 14px;
      border: 1px solid var(--admin-border);
      box-shadow: var(--admin-shadow-md);
      overflow: hidden;
      padding: 6px 0;
    }

    .admin-sidebar-menu .dropdown-item {
      transition: all .18s ease;
      font-size: .84rem;
      color: var(--admin-text-mid);
      margin: 0;
      padding: .72rem 1rem;
    }

    .admin-sidebar-menu .dropdown-item:hover {
      background: var(--admin-soft-2);
      color: var(--admin-primary);
    }

    .admin-sidebar-menu .dropdown-divider {
      margin: .35rem 0;
    }

    /* HEADER */
    .pc-header {
      background: rgba(255,255,255,.96);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--admin-border);
      box-shadow: 0 1px 8px rgba(0,0,0,.03);
    }

    .pc-head-link {
      color: var(--admin-text-mid);
    }

    .pc-header .header-wrapper {
      padding-right: 14px;
      overflow: visible;
    }

    .admin-header-actions {
      display: flex;
      align-items: center;
      gap: .45rem;
      flex-wrap: nowrap;
    }

    .admin-notif-btn {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      border: 1px solid var(--admin-border);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #fff;
      color: var(--admin-text-mid);
      transition: all .2s ease;
    }

    .admin-notif-btn i {
      font-size: 1.15rem;
    }

    .admin-notif-btn:hover {
      background: var(--admin-soft-2);
      color: var(--admin-primary);
      border-color: #D7EBDD;
    }

    .notif-badge {
      position: absolute;
      top: -4px;
      right: -4px;
      min-width: 16px;
      height: 16px;
      padding: 0 4px;
      border-radius: 999px;
      background: #EF4444;
      color: white;
      font-size: .6rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      border: 2px solid white;
    }

    .admin-notif-menu {
      min-width: 300px;
      border-radius: 16px;
      border: 1px solid var(--admin-border);
      box-shadow: var(--admin-shadow-md);
      overflow: hidden;
      padding: 0;
    }

    .admin-notif-item {
      padding: .75rem .95rem;
      border-bottom: 1px solid rgba(15, 23, 42, .05);
    }

    .admin-notif-item:last-child {
      border-bottom: none;
    }

    /* MAIN */
    .pc-container {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .page-header .page-header-title h5 {
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--admin-primary);
    }

    .breadcrumb .breadcrumb-item,
    .breadcrumb .breadcrumb-item a {
      font-size: .8rem;
      color: var(--admin-text-light);
      text-decoration: none;
    }

    .alert {
      border-radius: 14px;
      font-size: .86rem;
      border: 1px solid transparent;
    }

    .alert-success {
      background: #ECFDF5;
      border-color: #D1FAE5;
      color: #065F46;
    }

    .alert-danger {
      background: #FEF2F2;
      border-color: #FECACA;
      color: #B91C1C;
    }

    .stat-kons {
      background: linear-gradient(180deg, #ffffff, #f8fffb);
      border-radius: 20px;
      padding: 1.4rem;
      border: 1px solid #e2f2ea;
      box-shadow: 0 8px 24px rgba(6, 78, 59, 0.05);
      transition: all .2s ease;
    }

    .stat-kons:hover {
      transform: translateY(-4px);
      box-shadow: 0 14px 30px rgba(6, 78, 59, 0.10);
    }

    .stat-kons .stat-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: #E8F8F2;
      color: var(--admin-primary);
      font-size: 1.2rem;
    }

    .stat-kons .stat-num {
      font-size: 2rem;
      font-weight: 800;
      color: var(--admin-primary);
    }

    .stat-kons .stat-lbl {
      font-size: .78rem;
      color: var(--admin-text-light);
      margin-top: .25rem;
    }

    .stat-kons .stat-change {
      font-size: .74rem;
      font-weight: 600;
      margin-top: .4rem;
    }

    .change-up { color: var(--admin-primary-600); }
    .change-down { color: var(--admin-danger); }

    .kons-card {
      background: #ffffff;
      border-radius: 20px;
      border: 1px solid #e3f1ea;
      box-shadow: 0 8px 25px rgba(6, 78, 59, 0.05);
    }

    .kons-card-header {
      padding: 1.1rem 1.4rem;
      border-bottom: 1px solid var(--admin-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .kons-card-header h6 {
      font-weight: 700;
      color: var(--admin-text);
      margin: 0;
    }

    .kons-card-body {
      padding: 1.4rem;
    }

    .badge-menunggu,
    .badge-disetujui,
    .badge-ditolak,
    .badge-selesai {
      border-radius: 999px;
      padding: .25rem .78rem;
      font-size: .72rem;
      font-weight: 600;
    }

    .badge-menunggu {
      background: #FEF3C7;
      color: #B45309;
    }

    .badge-disetujui {
      background: #D1FAE5;
      color: #065F46;
    }

    .badge-ditolak {
      background: #FEE2E2;
      color: #B91C1C;
    }

    .badge-selesai {
      background: #DBEAFE;
      color: #1D4ED8;
    }

    .pc-footer {
      border-top: 1px solid var(--admin-border);
      background: transparent;
    }

    .pc-footer p {
      font-size: .78rem;
      color: var(--admin-text-light);
    }

    @media (max-width: 768px) {
      .pc-header .header-wrapper {
        padding-right: 8px;
      }

      .admin-notif-menu {
        min-width: 280px;
      }
    }
  </style>

  @stack('styles')
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">

@php
  $adminNotifUnread = Auth::user()->notifikasi()->where('status', 'belum')->count();
  $adminNotifItems = Auth::user()->notifikasi()->latest()->take(6)->get();
@endphp

<div class="loader-bg">
  <div class="loader-track"><div class="loader-fill"></div></div>
</div>
<nav class="pc-sidebar">
  <div class="navbar-wrapper">

    <div class="m-header">
      <a href="{{ route('admin.dashboard') }}" class="b-brand d-flex align-items-center gap-2 text-decoration-none">
        <img src="{{ asset('img/logo.png') }}" alt="logo"
             style="width:36px;height:36px;object-fit:contain;border-radius:8px;">
        <div>
          <div style="font-weight:800;font-size:1rem;color:var(--admin-primary);line-height:1.1;">Campus Care</div>
          <div style="font-size:.7rem;color:var(--admin-text-light);margin-top:2px;">Admin Panel</div>
        </div>
      </a>
    </div>

    <div class="navbar-content">
      <ul class="pc-navbar">
        <li class="pc-item pc-caption"><label>Menu</label></li>

        <li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <a href="{{ route('admin.dashboard') }}" class="pc-link">
            <span class="pc-micon"><i class="ti ti-home"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li><nav class="pc-sidebar">
    <div class="navbar-wrapper">

        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('img/logo.png') }}" alt="logo"
                    style="width:36px;height:36px;object-fit:contain;border-radius:8px;">
                <div>
                    <div style="font-weight:800;font-size:1rem;color:var(--admin-primary);line-height:1.1;">Campus Care</div>
                    <div style="font-size:.7rem;color:var(--admin-text-light);margin-top:2px;">Admin Panel</div>
                </div>
            </a>
        </div>

        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item pc-caption"><label>Menu</label></li>

                <li class="pc-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-home"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('admin.jadwal*') ? 'active' : '' }}">
                    <a href="{{ route('admin.jadwal') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-calendar-event"></i></span>
                        <span class="pc-mtext">Penjadwalan</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('admin.chat*') ? 'active' : '' }}">
                    <a href="{{ route('admin.chat') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-message-circle"></i></span>
                        <span class="pc-mtext">Chat</span>
                    </a>
                </li>

                <li class="pc-item {{ request()->routeIs('admin.sesi*') ? 'active' : '' }}">
                    <a href="{{ route('admin.sesi') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-stethoscope"></i></span>
                        <span class="pc-mtext">Sesi Konseling</span>
                    </a>
                </li>

                <!-- Update: Laporan instead of Riwayat Konseling -->
                <li class="pc-item {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                    <a href="{{ route('admin.laporan') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-file-earmark"></i></span>
                        <span class="pc-mtext">Laporan Konseling</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="admin-sidebar-profile">
            <div class="dropdown">
                <a href="#" class="admin-sidebar-profile-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="admin-sidebar-avatar">
                        @if(optional(Auth::user()->profil)->foto)
                            <img src="{{ Storage::url(Auth::user()->profil->foto) }}" alt="Profil">
                        @else
                            {{ strtoupper(substr(Auth::user()->nama, 0, 1)) }}
                        @endif
                    </div>

                    <div class="admin-sidebar-user">
                        <div class="admin-sidebar-name">{{ Auth::user()->nama }}</div>
                        <div class="admin-sidebar-role">Konselor Utama</div>
                    </div>

                    <i class="ti ti-chevron-up admin-sidebar-arrow"></i>
                </a>

                <div class="dropdown-menu admin-sidebar-menu">
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item d-flex align-items-center gap-2">
                        <i class="ti ti-user" style="font-size:1rem;"></i> Profil Saya
                    </a>

                    <div class="dropdown-divider"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2" style="color: var(--admin-danger);">
                            <i class="ti ti-logout" style="font-size:1rem;"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</nav>

<header class="pc-header">
  <div class="header-wrapper">
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

    <div class="ms-auto admin-header-actions">
      <ul class="list-unstyled d-flex align-items-center gap-1 mb-0">
        <li class="dropdown pc-h-item">
          <a href="#" class="admin-notif-btn position-relative"
             id="adminNotifTrigger"
             data-bs-toggle="dropdown"
             aria-expanded="false">

            <i class="ti ti-bell"></i>

            @if($adminNotifUnread > 0)
              <span class="notif-badge" id="adminNotifBadge">
                {{ $adminNotifUnread > 9 ? '9+' : $adminNotifUnread }}
              </span>
            @else
              <span class="notif-badge" id="adminNotifBadge" style="display:none;">0</span>
            @endif
          </a>

          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown admin-notif-menu">
            <div class="dropdown-header d-flex align-items-center justify-content-between px-3 py-2">
              <h6 class="m-0 fw-700" style="font-size:.88rem;">Notifikasi</h6>
            </div>
            <div class="dropdown-divider m-0"></div>

            <div style="max-height:260px;overflow-y:auto;" id="adminNotifList">
              @forelse($adminNotifItems as $notif)
                <a class="dropdown-item admin-notif-item" href="{{ route('admin.jadwal') }}">
                  <div class="d-flex gap-2 align-items-start">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $notif->status === 'belum' ? 'var(--admin-primary-500)' : '#d0dce4' }};margin-top:5px;flex-shrink:0;"></div>
                    <div style="min-width:0;">
                      <p class="mb-0" style="font-size:.8rem;color:var(--admin-text);font-weight:600;line-height:1.4;">{{ $notif->pesan }}</p>
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
      </ul>
    </div>
  </div>
</header>

<div class="pc-container">
  <div class="pc-content">
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">
              <h5 class="m-b-10">@yield('page-title', 'Dashboard')</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}"></a>
              </li>
              <li class="breadcrumb-item" aria-current="page">
                @yield('page-title', 'Dashboard')
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

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

<script src="{{ asset('template/dist') }}/assets/js/plugins/popper.min.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/plugins/simplebar.min.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/plugins/bootstrap.min.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/fonts/custom-font.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/pcoded.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/plugins/feather.min.js"></script>

<script>layout_change('light');</script>
<script>change_box_container('false');</script>
<script>layout_rtl_change('false');</script>
<script>preset_change("preset-1");</script>
<script>font_change("Public-Sans");</script>

<script>
  (function () {
    const notifBadge = document.getElementById('adminNotifBadge');
    const notifList = document.getElementById('adminNotifList');
    const notifTrigger = document.getElementById('adminNotifTrigger');

    if (!notifBadge || !notifList || !notifTrigger) return;

    function escapeHtml(text) {
      return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function renderNotifications(items) {
      if (!items || items.length === 0) {
        notifList.innerHTML = '<div class="py-3 px-3" style="font-size:.8rem;color:#aab5bc;">Belum ada notifikasi.</div>';
        return;
      }

      notifList.innerHTML = items.map((notif) => {
        const dotColor = notif.status === 'belum' ? 'var(--admin-primary-500)' : '#d0dce4';
        return `
          <a class="dropdown-item admin-notif-item" href="{{ route('admin.jadwal') }}">
            <div class="d-flex gap-2 align-items-start">
              <div style="width:8px;height:8px;border-radius:50%;background:${dotColor};margin-top:5px;flex-shrink:0;"></div>
              <div style="min-width:0;">
                <p class="mb-0" style="font-size:.8rem;color:var(--admin-text);font-weight:600;line-height:1.4;">${escapeHtml(notif.pesan)}</p>
                <span style="font-size:.72rem;color:#aab5bc;">${escapeHtml(notif.created_at_human || 'Baru saja')}</span>
              </div>
            </div>
          </a>
        `;
      }).join('');
    }

    function renderBadge(unreadCount) {
      const count = Number(unreadCount || 0);

      if (count > 0) {
        notifBadge.style.display = 'inline-flex';
        notifBadge.textContent = count > 9 ? '9+' : String(count);
      } else {
        notifBadge.style.display = 'none';
      }
    }

    async function fetchAdminNotifications() {
      try {
        const res = await fetch('{{ route('admin.notifikasi.list') }}', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) return;

        const data = await res.json();
        if (!data.success) return;

        renderBadge(data.unread_count);
        renderNotifications(data.items || []);
      } catch (err) {
        console.error('Gagal memuat notifikasi admin:', err);
      }
    }

    async function markAdminNotificationsAsRead() {
      try {
        await fetch('{{ route('admin.notifikasi.baca') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
      } catch (err) {
        console.error('Gagal menandai notifikasi dibaca:', err);
      }
    }

    notifTrigger.addEventListener('click', function () {
      setTimeout(async function () {
        await markAdminNotificationsAsRead();
        await fetchAdminNotifications();
      }, 120);
    });

    fetchAdminNotifications();
    setInterval(fetchAdminNotifications, 8000);
  })();
</script>

@stack('scripts')
</body>
</html>