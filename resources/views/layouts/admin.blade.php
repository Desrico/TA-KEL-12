<!DOCTYPE html>
<html lang="id">
<head>
  <title>@yield('page-title', 'Dashboard') - Campus Care</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

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
        --admin-primary-500: #D1FAE5;
        --admin-soft: #D1FAE5;
        --admin-soft-2: #EFFCF5;
        --admin-bg: #FFFAF4;
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
.pc-sidebar {
    width: 280px !important;
    min-width: 280px !important;
    max-width: 280px !important;
    height: 100vh !important;
    overflow: hidden !important;
    background: var(--admin-soft) !important;
    transition: width .15s ease !important;
}

.pc-sidebar.pc-sidebar-hide {
    width: 0 !important;
    min-width: 0 !important;
    max-width: 0 !important;
    overflow: hidden !important;
}

.pc-sidebar.pc-sidebar-hide .navbar-wrapper,
.pc-sidebar.pc-sidebar-hide .m-header,
.pc-sidebar.pc-sidebar-hide .navbar-content,
.pc-sidebar.pc-sidebar-hide .admin-sidebar-profile {
    width: 0 !important;
    min-width: 0 !important;
    max-width: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
}

.pc-sidebar.pc-sidebar-hide ~ .pc-header {
    left: 0 !important;
}

.pc-sidebar.pc-sidebar-hide ~ .pc-footer,
.pc-sidebar.pc-sidebar-hide ~ .pc-container {
    margin-left: 0 !important;
}

.pc-sidebar ~ .pc-header,
.pc-sidebar ~ .pc-footer,
.pc-sidebar ~ .pc-container {
    transition: left .15s ease, margin-left .15s ease !important;
}

.pc-sidebar .navbar-wrapper {
    width: 280px !important;
    height: 100vh !important;
    min-height: 100vh !important;
    background: var(--admin-soft) !important;
    border-right: 1px solid var(--admin-border);
    display: flex !important;
    flex-direction: column !important;
    overflow: hidden !important;
}

/* Logo */
.pc-sidebar .m-header {
    width: 100% !important;
    height: 175px !important;
    min-height: 175px !important;
    max-height: 175px !important;
    flex: 0 0 175px !important;
    padding: 20px 16px 8px !important;
    background: var(--admin-soft) !important;
    border-bottom: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.pc-sidebar .m-header .b-brand {
    width: 100% !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-decoration: none !important;
}

.admin-sidebar-logo-full {
    width: 145px !important;
    max-width: 145px !important;
    height: auto !important;
    object-fit: contain !important;
    display: block !important;
}

/* Menu */
.pc-sidebar .navbar-content {
    width: 100% !important;
    flex: 1 1 auto !important;
    min-height: 0 !important;
    padding: 0 18px !important;
    overflow: hidden !important;
    background: var(--admin-soft) !important;
    display: flex !important;
    align-items: center !important;
}

.pc-sidebar .pc-navbar {
    width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}

.pc-sidebar .pc-caption {
    display: none !important;
}

.pc-sidebar .pc-item {
    width: 100% !important;
    margin: 0 0 7px 0 !important;
    outline: none !important;
    border: none !important;
    box-shadow: none !important;
}

.pc-sidebar .pc-item:last-child {
    margin-bottom: 0 !important;
}

.pc-sidebar .pc-link {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 12px !important;
    width: 100% !important;
    min-height: 44px !important;
    height: auto !important;
    padding: 10px 1rem !important;
    border-radius: 13px !important;
    color: var(--admin-text-mid) !important;
    font-size: .88rem !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    outline: none !important;
    border: none !important;
    box-shadow: none !important;
    transition: all .18s ease !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    grid-template-columns: unset !important;
    column-gap: unset !important;
}

.pc-sidebar .pc-link:hover {
    background: rgba(255, 255, 255, 0.45) !important;
    color: var(--admin-primary) !important;
}

.pc-sidebar .pc-item.active > .pc-link {
    background: var(--admin-primary) !important;
    color: #ffffff !important;
    box-shadow: none !important;
}

.pc-sidebar .pc-navbar > .pc-item.active:not(.pc-hasmenu) > .pc-link::after,
.pc-sidebar .pc-navbar > .pc-item.active > .pc-link::before {
    display: none !important;
    content: none !important;
    opacity: 0 !important;
    background: none !important;
}

.pc-sidebar .pc-micon {
    width: 20px !important;
    min-width: 20px !important;
    max-width: 20px !important;
    height: 20px !important;
    flex: 0 0 20px !important;
    flex-shrink: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 0 !important;
    position: static !important;
    top: unset !important;
    left: unset !important;
}

.pc-sidebar .pc-micon i,
.pc-sidebar .pc-micon svg {
    font-size: 1rem !important;
    width: 1rem !important;
    height: 1rem !important;
    color: #9AA8B5 !important;
    transition: color .18s ease !important;
    display: block !important;
    line-height: 1 !important;
}

.pc-sidebar .pc-link:hover .pc-micon i,
.pc-sidebar .pc-link:hover .pc-micon svg {
    color: var(--admin-primary) !important;
}

.pc-sidebar .pc-item.active > .pc-link .pc-micon i,
.pc-sidebar .pc-item.active > .pc-link .pc-micon svg {
    color: #ffffff !important;
}

.pc-sidebar .pc-mtext {
    flex: 1 1 auto !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.2 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    display: inline-block !important;
    vertical-align: middle !important;
}

/* Custom nav - bypasses pcoded entirely */
.cc-nav {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 0;
    margin: 0;
    list-style: none;
}

.cc-nav-link {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 12px !important;
    width: 100%;
    min-height: 44px;
    padding: 10px 16px;
    border-radius: 13px;
    color: var(--admin-text-mid);
    font-size: .88rem;
    font-weight: 600;
    text-decoration: none !important;
    transition: all .18s ease;
    white-space: nowrap;
    overflow: hidden;
    border: none;
    box-shadow: none;
    background: transparent;
}

.cc-nav-link i {
    font-size: 1.1rem;
    width: 20px;
    min-width: 20px;
    text-align: center;
    flex-shrink: 0;
    color: #9AA8B5;
    line-height: 1;
    transition: color .18s ease;
    /* ensure icon doesnt shift */
    display: flex;
    align-items: center;
    justify-content: center;
}

.cc-nav-link span {
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.2;
}

.cc-nav-link:hover {
    background: rgba(255, 255, 255, 0.45);
    color: var(--admin-primary);
}

.cc-nav-link:hover i {
    color: var(--admin-primary);
}

.cc-nav-link.cc-active {
    background: var(--admin-primary);
    color: #ffffff;
}

.cc-nav-link.cc-active i {
    color: #ffffff;
}

/* Profil admin */
.admin-sidebar-profile {
    position: relative !important;
    flex: 0 0 auto !important;
    padding: 14px 18px 16px !important;
    margin-top: 8px !important;
    border-top: 1px solid rgba(15, 23, 42, 0.08) !important;
    background: var(--admin-soft) !important;
    z-index: 5 !important;
}

.admin-sidebar-profile-trigger {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 8px;
    text-decoration: none;
    background: transparent;
    border: 0;
    box-shadow: none;
}

.admin-sidebar-profile-trigger:hover {
    background: rgba(255, 255, 255, 0.35);
    border-radius: 14px;
}

.admin-sidebar-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    overflow: hidden;
    background: #d9e6df;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--admin-primary);
    font-weight: 800;
    font-size: .95rem;
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
    font-size: .95rem;
    font-weight: 800;
    color: var(--admin-text);
    line-height: 1.2;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.admin-sidebar-role {
    font-size: .78rem;
    color: var(--admin-text-mid);
    line-height: 1.2;
}

.admin-sidebar-arrow {
    font-size: .85rem;
    color: var(--admin-text-light);
    flex-shrink: 0;
}

.admin-sidebar-menu {
    width: calc(100% - 4px);
    min-width: unset;
    margin: 10px 2px 0;
    border-radius: 18px;
    border: 1px solid rgba(6, 95, 70, 0.16);
    box-shadow: 0 16px 34px rgba(15, 23, 42, 0.14);
    overflow: hidden;
    padding: 8px;
    background: #ffffff !important;
}

.admin-sidebar-menu .dropdown-item {
    transition: all .18s ease;
    font-size: .84rem;
    color: #334155;
    margin: 0;
    padding: .78rem .9rem;
    border-radius: 13px;
    font-weight: 700;
    background: transparent !important;
}

.admin-sidebar-menu .dropdown-item:hover {
    background: #ecfdf5 !important;
    color: #065f46 !important;
}

.admin-sidebar-menu .dropdown-divider {
    margin: .45rem .25rem;
    border-top: 1px solid #e5e7eb;
}

.admin-sidebar-menu .dropdown-logout {
    color: #dc2626 !important;
}

.admin-sidebar-menu .dropdown-logout:hover {
    background: #fef2f2 !important;
    color: #b91c1c !important;
}
    /* HEADER */
    .pc-header {
      background: rgba(255,255,255,.96);
      border-bottom: 2px solid var(--admin-border);
      box-shadow: 0 1px 8px rgba(0,0,0,.03);
    }

    .pc-header .pc-h-item.pc-sidebar-collapse {
      margin-left: 2rem !important;
    }

    .pc-header .pc-h-item.pc-sidebar-collapse .pc-head-link {
      padding: 0.75rem 1rem !important;
      border-radius: 12px;
      margin-left: 0.5rem !important;
    }

    .pc-header .pc-mob-drp {
      padding-left: 0.5rem !important;
    }

    @media (min-width: 1025px) {
      /* Show desktop header toggle */
      .pc-h-item.pc-sidebar-collapse { display: inline-flex !important; }

      .sidebar-edge-toggle {
        background: #fff;
        border-radius: 8px;
        width: 36px;
        height: 36px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
      }

      .pc-h-item.pc-sidebar-popup {
        display: none !important;
      }

      .pc-container { margin-left: 300px !important; }
      .pc-sidebar.pc-sidebar-hide ~ .pc-container { margin-left: 0 !important; }
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
      border: 2px solid var(--admin-border);
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
      border: 2px solid var(--admin-border);
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

   .pc-content {
      padding: 2rem 2rem 2.5rem !important;
    }

    .admin-page-inner {
      width: 100%;
      max-width: 1180px;
      margin-left: auto;
      margin-right: auto;
    }

    .admin-page-inner > .card,
    .admin-page-inner > .kons-card,
    .admin-page-inner > .admin-card,
    .admin-page-inner > .sesi-card,
    .admin-page-inner > .session-card,
    .admin-page-inner > .table-card {
      width: 100% !important;
      max-width: 100% !important;
      margin-left: 0 !important;
      margin-right: 0 !important;
    }

    .admin-page-inner > .container,
    .admin-page-inner > .container-fluid,
    .admin-page-inner .container,
    .admin-page-inner .container-fluid {
      width: 100% !important;
      max-width: 100% !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }

    .page-header {
      margin-bottom: 1.25rem;
      padding: 0 !important;
    }

    .admin-breadcrumb {
      display: flex;
      align-items: center;
      gap: .65rem;
      margin: 0 0 1.5rem 0;
      padding: 0;
      background: transparent;
      border: none;
      box-shadow: none;
      font-size: .9rem;
    }

    .admin-breadcrumb a {
      color: var(--admin-primary-600);
      font-weight: 700;
      text-decoration: none;
    }

    .admin-breadcrumb a:hover {
      color: var(--admin-primary);
    }

    .admin-breadcrumb .breadcrumb-separator {
      color: #94A3B8;
      font-weight: 600;
    }

    .admin-breadcrumb .breadcrumb-current {
      color: var(--admin-text-mid);
      font-weight: 600;
    }

    @media (max-width: 768px) {
      .admin-breadcrumb {
        margin: 0 0 1rem;
        font-size: .82rem;
        padding: .75rem .85rem;
      }
    }

    .page-header .page-header-title h5 {
      font-size: 2rem;
      font-weight: 800;
      color: var(--admin-primary);
      letter-spacing: -0.5px;
      margin-bottom: 0 !important;
    }

    .page-block {
      padding: 0;
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
      border-bottom: 2px solid var(--admin-border);
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
      border-top: 2px solid var(--admin-border);
      background: transparent;
    }

    .pc-footer p {
      font-size: .78rem;
      color: var(--admin-text-light);
    }

    .cc-sidebar-brand-link:hover {
        text-decoration: none;
    }

    /* Ensure main content area uses admin background color */
    .pc-container,
    .pc-content {
      background: var(--admin-bg) !important;
    }

    @media (max-width: 768px) {
      .pc-header .header-wrapper {
        padding-right: 8px;
      }

      .admin-notif-menu {
        min-width: 280px;
      }
    }

    /* Modal confirm override (use on modals with class .modal-confirm) */
    .modal-backdrop.show {
      background: rgba(0,0,0,0.55) !important;
    }

    .modal.modal-confirm .modal-dialog {
      max-width: 460px;
      margin: 0 auto;
    }

    .modal.modal-confirm .modal-content {
      background: var(--admin-primary);
      color: #fff;
      border-radius: 12px;
      padding: 1.6rem;
      box-shadow: 0 18px 40px rgba(6,78,59,0.25);
      border: none;
      text-align: center;
    }

    .modal.modal-confirm .modal-body { padding: 0; }

    .modal.modal-confirm .modal-icon {
      width: 76px;
      height: 76px;
      border-radius: 50%;
      border: 4px solid rgba(255,255,255,0.15);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 30px;
      background: rgba(255,255,255,0.04);
      margin: 0 auto;
      color: #fff;
    }

    .modal.modal-confirm .modal-title {
      font-size: 1.05rem;
      font-weight: 800;
      margin-top: .85rem;
      color: #fff;
    }

    .modal.modal-confirm .modal-text {
      color: rgba(255,255,255,0.9);
      margin-top: .45rem;
      font-size: .95rem;
    }

    .modal.modal-confirm .btn-confirm {
      background: #FDE68A;
      color: var(--admin-primary);
      border: 0;
      padding: .5rem 1.05rem;
      border-radius: 10px;
      font-weight: 700;
    }

    .modal.modal-confirm .btn-confirm:hover { background: #FCD34D; }

    .modal.modal-confirm .btn-cancel {
      background: transparent;
      color: #fff;
      border: 1px solid rgba(255,255,255,0.16);
      border-radius: 10px;
      padding: .45rem .9rem;
    }
  </style>

  @vite(['resources/js/app.js'])
  @stack('styles')
</head>

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">


@php
  $user = Auth::user();
@endphp


@if(!$user)
  <div style="display:flex;align-items:center;justify-content:center;height:100vh;background:#F7FCF9;">
    <div style="background:white;padding:2.5rem 2.2rem 2.2rem 2.2rem;border-radius:18px;box-shadow:0 4px 24px rgba(6,78,59,0.07);display:flex;flex-direction:column;align-items:center;min-width:320px;max-width:95vw;">
      <div style="background:#D1FAE5;border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;margin-bottom:1.2rem;border:1.5px solid #10B981;">
        <svg width="32" height="32" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="12" fill="none"/><path d="M8 11V9a4 4 0 1 1 8 0v2" stroke="#047857" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="6.5" y="11" width="11" height="7" rx="2.5" stroke="#047857" stroke-width="1.5"/><circle cx="12" cy="15" r="1.2" fill="#047857"/></svg>
      </div>
      <div style="font-size:1.35rem;font-weight:700;color:#047857;margin-bottom:0.7rem;text-align:center;">Sesi Anda Telah Berakhir</div>
      <div style="font-size:0.98rem;color:#475569;margin-bottom:2.1rem;text-align:center;max-width:320px;">
        Demi keamanan, sesi Anda telah habis karena tidak ada aktivitas dalam waktu lama. Silakan masuk kembali untuk melanjutkan pekerjaan Anda.
      </div>
      <a href="{{ route('login') }}" style="display:flex;align-items:center;justify-content:center;gap:0.7rem;padding:0.85rem 0;width:100%;background:#065F46;color:white;border-radius:8px;font-weight:600;text-decoration:none;font-size:1.05rem;transition:background .18s;box-shadow:0 2px 8px rgba(6,78,59,0.06);">
        Masuk Kembali
        <svg width="20" height="20" fill="none" viewBox="0 0 20 20"><path d="M8 15l4-5-4-5" stroke="white" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </a>
    </div>
  </div>
  @php exit; @endphp
@endif

@php
  $adminNotifUnread = $user->notifikasi()->where('status', 'belum')->count();
  $adminNotifItems = $user->notifikasi()->latest()->take(6)->get();
@endphp

<div class="loader-bg">
  <div class="loader-track"><div class="loader-fill"></div></div>
</div>
<nav class="pc-sidebar">
    <div class="navbar-wrapper">

        {{-- LOGO --}}
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand">
                <img src="{{ asset('img/logo.png') }}" alt="Campus Care Logo" class="admin-sidebar-logo-full">
            </a>
        </div>

        {{-- MENU: completely outside .navbar-content so pcoded.js cannot touch it --}}
        <div class="navbar-content" style="display:none!important;height:0!important;overflow:hidden!important;"></div>
        <div id="cc-sidebar-menu" style="flex:1 1 auto;min-height:0;padding:0 18px;overflow-y:auto;overflow-x:hidden;display:flex;align-items:center;">
            <nav style="width:100%;display:flex;flex-direction:column;gap:4px;">
                @php
                    $lnk = 'display:flex;align-items:center;gap:12px;width:100%;min-height:44px;padding:10px 16px;border-radius:13px;text-decoration:none;font-size:.88rem;font-weight:600;white-space:nowrap;overflow:hidden;transition:background .18s;';
                    $ico = 'display:inline-flex;align-items:center;justify-content:center;width:20px;min-width:20px;height:20px;flex-shrink:0;';
                    $txt = 'flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;line-height:1.2;';
                    $active   = fn($r) => request()->routeIs($r) ? 'background:var(--admin-primary);color:#fff;' : 'background:transparent;color:#475569;';
                    $activeIco = fn($r) => request()->routeIs($r) ? 'color:#fff;' : 'color:#9AA8B5;';
                @endphp

                <a href="{{ route('admin.dashboard') }}" style="{{ $active('admin.dashboard') }}{{ $lnk }}">
                    <span style="{{ $ico }}"><i class="ti ti-home" style="{{ $activeIco('admin.dashboard') }}font-size:1.1rem;"></i></span>
                    <span style="{{ $txt }}">Dashboard</span>
                </a>
                <a href="{{ route('admin.jadwal') }}" style="{{ $active('admin.jadwal*') }}{{ $lnk }}">
                    <span style="{{ $ico }}"><i class="ti ti-calendar-event" style="{{ $activeIco('admin.jadwal*') }}font-size:1.1rem;"></i></span>
                    <span style="{{ $txt }}">Penjadwalan</span>
                </a>
                <a href="{{ route('admin.chat') }}" style="{{ $active('admin.chat*') }}{{ $lnk }}">
                    <span style="{{ $ico }}"><i class="ti ti-message-circle" style="{{ $activeIco('admin.chat*') }}font-size:1.1rem;"></i></span>
                    <span style="{{ $txt }}">Chat</span>
                </a>
                <a href="{{ route('admin.riwayat') }}" style="{{ $active('admin.riwayat*') }}{{ $lnk }}">
                    <span style="{{ $ico }}"><i class="ti ti-stethoscope" style="{{ $activeIco('admin.riwayat*') }}font-size:1.1rem;"></i></span>
                    <span style="{{ $txt }}">Riwayat Konseling</span>
                </a>
                <a href="{{ route('admin.laporan') }}" style="{{ $active('admin.laporan*') }}{{ $lnk }}">
                    <span style="{{ $ico }}"><i class="ti ti-file-report" style="{{ $activeIco('admin.laporan*') }}font-size:1.1rem;"></i></span>
                    <span style="{{ $txt }}">Laporan Konseling</span>
                </a>
                <a href="{{ route('counselor.education.index') }}" style="{{ $active('counselor.education.*') }}{{ $lnk }}">
                    <span style="{{ $ico }}"><i class="ti ti-book" style="{{ $activeIco('counselor.education.*') }}font-size:1.1rem;"></i></span>
                    <span style="{{ $txt }}">Edukasi</span>
                </a>
            </nav>
        </div>
        {{-- PROFIL ADMIN --}}
        <div class="admin-sidebar-profile">
            <div class="dropdown">
                <a href="#" class="admin-sidebar-profile-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                    @php
                        $namaKonselorLogin = auth()->user()?->nama ?: env('CIS_KONSELOR_NAME', 'Konselor');
                        $inisialKonselorLogin = strtoupper(mb_substr($namaKonselorLogin, 0, 1));
                    @endphp

                    <div class="admin-sidebar-avatar">
                        {{ $inisialKonselorLogin }}
                    </div>

                    <div class="admin-sidebar-user">
                        <div class="admin-sidebar-name">{{ $namaKonselorLogin }}</div>
                    </div>

                    <i class="ti ti-chevron-up admin-sidebar-arrow"></i>
                </a>

                <div class="dropdown-menu admin-sidebar-menu">
                    <a href="{{ route('profil') }}" class="dropdown-item d-flex align-items-center gap-2">
                        <i class="ti ti-user" style="font-size:1rem;"></i> Profil Saya
                    </a>

                    <div class="dropdown-divider"></div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item dropdown-logout d-flex align-items-center gap-2">
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
    <div class="me-auto pc-mob-drp position-relative">
      <ul class="list-unstyled d-flex align-items-center mb-0">
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link sidebar-edge-toggle d-none d-lg-inline-flex" id="sidebar-hide">
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
      <ul class="list-unstyled d-flex align-items-center gap-2 mb-0">
        <li class="pc-h-item">
        </li>
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
                @php
                  $payload = null;
                  try {
                    $decoded = json_decode($notif->pesan, true);
                    $payload = is_array($decoded) ? $decoded : null;
                  } catch (\Exception $e) {
                    $payload = null;
                  }

                  $link = route('admin.jadwal');
                  $label = $notif->pesan;
                  if ($payload && ($payload['type'] ?? null) === 'penjadwalan') {
                    // student riwayat route with selected jadwal id
                    $link = route('riwayat', ['jadwal' => $payload['jadwal_id']]);
                    $label = $payload['text'] ?? $label;
                  }
                @endphp

                <a class="dropdown-item admin-notif-item" href="{{ $link }}">
                  <div class="d-flex gap-2 align-items-start">
                    <div style="width:8px;height:8px;border-radius:50%;background:{{ $notif->status === 'belum' ? 'var(--admin-primary-500)' : '#d0dce4' }};margin-top:5px;flex-shrink:0;"></div>
                    <div style="min-width:0;">
                      <p class="mb-0" style="font-size:.8rem;color:var(--admin-text);font-weight:600;line-height:1.4;">{{ $label }}</p>
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
    <div class="admin-page-inner">
     @php
        $currentTitle = trim($__env->yieldContent('page-title', 'Dashboard'));
        $breadcrumbParent = trim($__env->yieldContent('breadcrumb-parent', ''));
        $breadcrumbParentUrl = trim($__env->yieldContent('breadcrumb-parent-url', ''));

        if ($breadcrumbParent === '' && request()->is('admin/laporan/*/laporan')) {
            $breadcrumbParent = 'Laporan Konseling';
            $breadcrumbParentUrl = route('admin.laporan');
        }
    @endphp

@if(!request()->routeIs('admin.dashboard'))
    <nav class="admin-breadcrumb" aria-label="breadcrumb">
        <a href="{{ route('admin.dashboard') }}">
            Dashboard
        </a>

        <span class="breadcrumb-separator">/</span>

        @if($breadcrumbParent !== '')
            @if($breadcrumbParentUrl !== '')
                <a href="{{ $breadcrumbParentUrl }}">
                    {!! $breadcrumbParent !!}
                </a>
            @else
                <span class="breadcrumb-current">
                    {!! $breadcrumbParent !!}
                </span>
            @endif

            <span class="breadcrumb-separator">/</span>
        @endif

        <span class="breadcrumb-current">
            {!! $currentTitle !!}
        </span>
    </nav>
@endif

@hasSection('page-hero')
  @yield('page-hero')
@else
  <div class="page-header {{ request()->routeIs('admin.dashboard') ? 'page-header-dashboard' : '' }}">
    <div class="page-block">
      <div class="row align-items-center">
        <div class="col-md-12">
          <div class="page-header-title">
            <h5 class="m-b-10">@yield('page-title', 'Dashboard')</h5>
          </div>
        </div>
      </div>
    </div>
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
</div>

@include('components.modal_confirm')

<script src="{{ asset('template/dist') }}/assets/js/plugins/popper.min.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/plugins/simplebar.min.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/plugins/bootstrap.min.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/fonts/custom-font.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/pcoded.js"></script>
<script src="{{ asset('template/dist') }}/assets/js/plugins/feather.min.js"></script>

<script>
  window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
      // Halaman admin dari browser back-forward cache harus dicek ulang ke server setelah logout.
      window.location.reload();
    }
  });

  // Global confirm modal handler: elements can use data-confirm, data-confirm-title, data-confirm-text, data-confirm-ok, data-confirm-url
  document.addEventListener('click', function (e) {
    const trigger = e.target.closest('[data-confirm]');
    if (!trigger) return;
    e.preventDefault();

    const title = trigger.getAttribute('data-confirm-title') || 'Konfirmasi Penjadwalan';
    const text = trigger.getAttribute('data-confirm-text') || 'Apakah kamu yakin ingin melanjutkan aksi ini?';
    const okText = trigger.getAttribute('data-confirm-ok') || 'Jadwalkan';
    const url = trigger.getAttribute('data-confirm-url') || null;

    const modalEl = document.getElementById('globalConfirmModal');
    if (!modalEl) return;

    document.getElementById('globalConfirmTitle').textContent = title;
    document.getElementById('globalConfirmText').textContent = text;
    const okBtn = document.getElementById('globalConfirmYes');
    okBtn.textContent = okText;

    // clear previous handler
    okBtn.onclick = function () {
      if (url) {
        window.location.href = url;
        return;
      }
      // if inside a form, submit it
      const form = trigger.closest('form');
      if (form) {
        form.submit();
      }
      bootstrap.Modal.getInstance(modalEl)?.hide();
    };

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    try {
        if (typeof layout_change === 'function') {
            layout_change('light');
        }
    } catch (error) {
        console.warn('layout_change dilewati:', error.message);
    }

    try {
        if (typeof change_box_container === 'function') {
            change_box_container('false');
        }
    } catch (error) {
        console.warn('change_box_container dilewati:', error.message);
    }

    try {
        if (typeof layout_rtl_change === 'function') {
            layout_rtl_change('false');
        }
    } catch (error) {
        console.warn('layout_rtl_change dilewati:', error.message);
    }

    try {
        if (typeof preset_change === 'function') {
            preset_change('preset-1');
        }
    } catch (error) {
        console.warn('preset_change dilewati:', error.message);
    }

    try {
        if (typeof font_change === 'function') {
            font_change('Public-Sans');
        }
    } catch (error) {
        console.warn('font_change dilewati:', error.message);
    }
});
</script>

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

    async function fetchUrgentNotifications() {
      try {
        const res = await fetch('{{ route('counselor.notifications') }}', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) return [];

        const data = await res.json();
        return data.notifications || [];
      } catch (err) {
        console.error('Gagal memuat notifikasi urgent:', err);
        return [];
      }
    }

    async function fetchAllNotifications() {
      try {
        // 1. Ambil notifikasi sistem biasa
        const res = await fetch('{{ route('admin.notifikasi.list') }}', {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if (!res.ok) return;
        const data = await res.json();
        if (!data.success) return;

        let items = data.items || [];
        let unreadCount = Number(data.unread_count || 0);

        // 2. Ambil mahasiswa urgent
        const urgentStudents = await fetchUrgentNotifications();
        
        // 3. Gabungkan mahasiswa urgent ke daftar teratas jika ada
        const urgentItems = urgentStudents.map(student => ({
          id: 'urgent-' + student.nim,
          pesan: `⚠️ KRITIS: ${student.name} (${student.nim}) berada di Level ${student.mental_level}!`,
          created_at_human: 'Sekarang',
          status: 'urgent',
          link: `{{ url('/konselor/detail') }}/${student.nim}`
        }));

        const finalItems = [...urgentItems, ...items];
        const finalUnreadCount = unreadCount + urgentItems.length;

        renderBadge(finalUnreadCount);
        
        // Modifikasi fungsi renderNotifications untuk menangani item urgent
        notifList.innerHTML = finalItems.map((notif) => {
          let dotColor = notif.status === 'belum' ? 'var(--admin-primary-500)' : '#d0dce4';
          let bgColor = 'transparent';
          let textColor = 'var(--admin-text)';
          let link = notif.link || '{{ route('admin.jadwal') }}';
          let onclickAttr = '';

          if (notif.status === 'urgent') {
            dotColor = '#EF4444';
            bgColor = '#FEF2F2';
            textColor = '#B91C1C';
            const nim = notif.id.replace('urgent-', '');
            onclickAttr = `onclick="window.markUrgentRead('${nim}', event, '${link}')"`;
          }

          return `
            <a class="dropdown-item admin-notif-item" href="${link}" style="background-color: ${bgColor}" ${onclickAttr}>
              <div class="d-flex gap-2 align-items-start">
                <div style="width:8px;height:8px;border-radius:50%;background:${dotColor};margin-top:5px;flex-shrink:0;"></div>
                <div style="min-width:0;">
                  <p class="mb-0" style="font-size:.8rem;color:${textColor};font-weight:600;line-height:1.4;">${escapeHtml(notif.pesan)}</p>
                  <span style="font-size:.72rem;color:#aab5bc;">${escapeHtml(notif.created_at_human || 'Baru saja')}</span>
                </div>
              </div>
            </a>
          `;
        }).join('');

      } catch (err) {
        console.error('Gagal memuat semua notifikasi:', err);
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

    window.markUrgentRead = async function(nim, event, url) {
      event.preventDefault();
      try {
        await fetch(`{{ url('/konselor/notifications') }}/${nim}/read`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
          }
        });
      } catch (err) {
        console.error('Failed to mark urgent read:', err);
      }
      window.location.href = url;
    };

    // ── Browser Push Notification ──────────────────────────────────────────
    const VAPID_PUBLIC_KEY = 'BANc9RgVqlg0Oau0kRon4GfLRAU6shEkZVndWOiX_j-c0MsLAWKX3wpLWZZO_P6WTJjS720x8_WKaA2IBSh8DLg';
    let swRegistration = null;
    let urgentAlreadyNotified = new Set();

    function urlBase64ToUint8Array(base64String) {
      const padding = '='.repeat((4 - base64String.length % 4) % 4);
      const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
      const rawData = window.atob(base64);
      const output = new Uint8Array(rawData.length);
      for (let i = 0; i < rawData.length; ++i) output[i] = rawData.charCodeAt(i);
      return output;
    }

    async function registerSW() {
      if (!('serviceWorker' in navigator) || !('PushManager' in window)) return null;
      if (swRegistration) return swRegistration;
      try {
        swRegistration = await navigator.serviceWorker.register('/sw.js');
        return swRegistration;
      } catch (e) {
        console.error('SW registration failed', e);
        return null;
      }
    }

    async function requestNotifPermissionAndSubscribe() {
      if (!('Notification' in window)) return;
      if (Notification.permission === 'denied') return;
      if (Notification.permission !== 'granted') {
        const result = await Notification.requestPermission();
        if (result !== 'granted') return;
      }
      const reg = await registerSW();
      if (!reg) return;
      try {
        const existing = await reg.pushManager.getSubscription();
        if (existing) return;
        const sub = await reg.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/subscriptions', {
          method: 'POST',
          body: JSON.stringify(sub),
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }
        });
      } catch (e) {
        console.error('Push subscribe failed', e);
      }
    }

    function sendUrgentBrowserNotif(student) {
      if (Notification.permission !== 'granted') return;
      if (urgentAlreadyNotified.has(student.nim)) return;
      urgentAlreadyNotified.add(student.nim);
      const notif = new Notification('⚠️ Mahasiswa Perlu Perhatian!', {
        body: `${student.name} (${student.nim}) berada di Level Mental ${student.mental_level}. Segera tindak lanjuti!`,
        icon: '/img/logo.png',
        badge: '/img/logo.png',
        tag: 'urgent-' + student.nim,
        requireInteraction: true
      });
      
      let isReadMarked = false;
      const markAsRead = async () => {
        if (isReadMarked) return;
        isReadMarked = true;
        try {
          const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          await fetch(`/konselor/notifications/${student.nim}/read`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': token,
              'X-Requested-With': 'XMLHttpRequest'
            }
          });
        } catch (err) {
          console.error('Failed to mark urgent read:', err);
        }
      };

      notif.onclick = function() {
        window.focus();
        markAsRead();
        window.location.href = `/konselor/detail/${student.nim}`;
        notif.close();
      };

      notif.onclose = function() {
        markAsRead();
      };
    }

    // Auto-request izin saat halaman load (jika belum pernah diputuskan)
    (async function autoInitNotif() {
      if (Notification.permission === 'default') {
        // Daftarkan SW di background tanpa langsung minta izin
        await registerSW();
      } else if (Notification.permission === 'granted') {
        await registerSW();
        await requestNotifPermissionAndSubscribe();
      }
    })();

    notifTrigger.addEventListener('click', async function () {
      // Request izin notifikasi saat bell diklik (jika belum)
      await requestNotifPermissionAndSubscribe();

      setTimeout(async function () {
        await markAdminNotificationsAsRead();
        await fetchAllNotifications();
      }, 120);
    });

    // Override fetchUrgentNotifications agar juga kirim browser push
    const _origFetchAllNotifications = fetchAllNotifications;
    async function fetchAllNotificationsWithPush() {
      const urgentStudents = await fetchUrgentNotifications();
      if (urgentStudents.length > 0 && Notification.permission === 'granted') {
        urgentStudents.forEach(student => sendUrgentBrowserNotif(student));
      }
      await fetchAllNotifications();
    }

    fetchAllNotificationsWithPush();
    setInterval(fetchAllNotificationsWithPush, 10000);
  })();

</script>
@stack('scripts')

</body>
</html>
