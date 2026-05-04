@extends('layouts.admin')

@php
    $jadwal = $activeJadwal;
    $mahasiswa = optional($jadwal)->mahasiswa;
    $studentUser = optional($mahasiswa)->user;
    $topik = null;
    $isBlockedBySchedule = $isBlockedBySchedule ?? false;
    $chatAccessGranted = $chatAccessGranted ?? false;

    if (!empty($jadwal?->catatan) && preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
        $topik = trim($match[1]);
    }

    $statusLabel = match (true) {
        $isBlockedBySchedule => 'Terjadwal',
        $chatAccessGranted => 'Sedang Berlangsung',
        $isReadyToStart => 'Siap Dimulai',
        default => match ($jadwal->status ?? null) {
            'berlangsung' => 'Sedang Berlangsung',
            'disetujui' => 'Siap Dimulai',
            default => 'Menunggu Persetujuan',
        },
    };
@endphp

@section('page-title', 'Chat Konseling')

@push('styles')
<style>
  .admin-chat-page {
    display: grid;
    grid-template-columns: 340px minmax(0, 1fr);
    gap: 1.35rem;
  }

  .admin-chat-card,
  .admin-chat-list {
    background: #fff;
    border: 1px solid #dceee4;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(6, 78, 59, .06);
  }

  .admin-chat-list {
    overflow: hidden;
    align-self: start;
  }

  .admin-chat-list-head {
    padding: 1rem 1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background: linear-gradient(180deg, #f3fff8, #ffffff);
  }

  .admin-chat-list-head h4 {
    margin: 0 0 .25rem;
    font-size: 1rem;
    font-weight: 800;
    color: #0f172a;
  }

  .admin-chat-list-head p {
    margin: 0;
    color: #64748b;
    font-size: .82rem;
  }

  .admin-chat-search {
    margin-top: .85rem;
    position: relative;
  }

  .admin-chat-search i {
    position: absolute;
    left: .95rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1rem;
    pointer-events: none;
  }

  .admin-chat-search input {
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 16px;
    padding: .78rem 1rem .78rem 2.65rem;
    font-size: .9rem;
    color: #0f172a;
    background: #fff;
    outline: none;
  }

  .admin-chat-search input:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .admin-chat-search-empty {
    display: none;
    padding: 1rem;
    color: #64748b;
    font-size: .84rem;
    border-top: 1px solid #f4f8f6;
  }

  .admin-chat-session {
    display: block;
    padding: .95rem 1rem;
    border-top: 1px solid #f4f8f6;
    text-decoration: none;
    transition: background .18s ease, transform .18s ease;
  }

  .admin-chat-session:hover {
    background: #f8fffb;
  }

  .admin-chat-session.active {
    background: linear-gradient(135deg, rgba(209, 250, 229, .76), rgba(239, 252, 245, .92));
  }

  .admin-chat-session-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .45rem;
  }

  .admin-chat-session-name {
    font-weight: 800;
    color: #0f172a;
    font-size: .92rem;
  }

  .admin-chat-session-meta {
    color: #64748b;
    font-size: .78rem;
    line-height: 1.55;
  }

  .admin-chat-session-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: .28rem .62rem;
    border-radius: 999px;
    font-size: .68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    white-space: nowrap;
  }

  .admin-chat-session-status.disetujui {
    background: #e8fff1;
    color: #047857;
  }

  .admin-chat-session-status.berlangsung {
    background: #def7ec;
    color: #065f46;
  }

  .admin-chat-session-status.terjadwal {
    background: #eff6ff;
    color: #1d4ed8;
  }

  .admin-chat-card {
    overflow: hidden;
    min-height: 760px;
    display: flex;
    flex-direction: column;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 22%),
      linear-gradient(180deg, #f6fff9 0%, #ffffff 16%, #ffffff 100%);
  }

  .admin-chat-empty {
    padding: 2.6rem 2rem;
    display: grid;
    place-items: center;
    text-align: center;
    min-height: 540px;
  }

  .admin-chat-empty-icon {
    width: 84px;
    height: 84px;
    border-radius: 28px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #047857;
    font-size: 2rem;
    box-shadow: 0 20px 40px rgba(16, 185, 129, .16);
    margin-bottom: 1rem;
  }

  .admin-chat-empty h3 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .55rem;
  }

  .admin-chat-empty p {
    max-width: 520px;
    margin: 0 auto;
    color: #64748b;
    line-height: 1.8;
  }

  .admin-chat-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.15rem 1.3rem;
    border-bottom: 1px solid #edf7f1;
    background: rgba(255,255,255,.88);
    backdrop-filter: blur(10px);
  }

  .admin-chat-person {
    display: flex;
    align-items: center;
    gap: .95rem;
    min-width: 0;
  }

  .admin-chat-avatar {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    overflow: hidden;
    background: #dff3e8;
    flex-shrink: 0;
    box-shadow: 0 10px 25px rgba(6, 95, 70, .12);
  }

  .admin-chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .admin-chat-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: .14rem;
  }

  .admin-chat-subtitle {
    margin: 0;
    color: #64748b;
    font-size: .84rem;
    line-height: 1.5;
  }

  .admin-chat-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .65rem .95rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .78rem;
    font-weight: 800;
  }

  .admin-chat-badge::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    box-shadow: 0 0 0 6px rgba(16, 185, 129, 0.13);
  }

  .admin-chat-head-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .admin-chat-video-btn {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .72rem 1rem;
    border-radius: 999px;
    border: 1px solid #d8eee2;
    background: #fff;
    color: #065f46;
    text-decoration: none;
    font-size: .84rem;
    font-weight: 800;
    box-shadow: 0 10px 20px rgba(6, 95, 70, .06);
  }

  .admin-chat-video-btn:hover {
    color: #047857;
    background: #f8fffb;
  }

  .admin-chat-thread {
    flex: 1;
    overflow-y: auto;
    padding: 1.35rem 1.35rem 0;
    background:
      radial-gradient(circle at center, rgba(209, 250, 229, 0.28), transparent 42%),
      linear-gradient(180deg, rgba(246,255,249,.7), rgba(255,255,255,.98));
  }

  .admin-chat-date {
    width: fit-content;
    margin: 0 auto 1.35rem;
    padding: .52rem .95rem;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid #e4f3eb;
    color: #64748b;
    font-size: .74rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .admin-message-row {
    display: flex;
    gap: .8rem;
    margin-bottom: .95rem;
    align-items: flex-end;
  }

  .admin-message-row.mine {
    justify-content: flex-end;
  }

  .admin-message-row.mine .admin-message-meta {
    justify-content: flex-end;
  }

  .admin-message-row.mine .admin-message-bubble {
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    border-bottom-right-radius: 10px;
    box-shadow: 0 16px 32px rgba(6, 95, 70, .2);
  }

  .admin-message-row.other .admin-message-bubble {
    background: #fff;
    color: #1f2937;
    border: 1px solid #e6eef3;
    border-bottom-left-radius: 10px;
  }

  .admin-message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 10px 20px rgba(15, 23, 42, .08);
  }

  .admin-message-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .admin-message-content {
    max-width: min(76%, 620px);
  }

  .admin-message-meta {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin: 0 .3rem .3rem;
    color: #64748b;
    font-size: .75rem;
  }

  .admin-message-name {
    font-weight: 800;
    color: #064e3b;
  }

  .admin-message-bubble {
    padding: .92rem 1.1rem .98rem;
    border-radius: 24px;
    font-size: .93rem;
    line-height: 1.7;
    word-break: break-word;
  }

  .admin-chat-compose {
    padding: 1rem 1.2rem 1.2rem;
    border-top: 1px solid #edf7f1;
    background: rgba(255,255,255,.95);
  }

  .admin-chat-form {
    display: flex;
    align-items: flex-end;
    gap: .8rem;
    padding: .75rem;
    border-radius: 24px;
    border: 1px solid #d8eee2;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
  }

  .admin-chat-input {
    flex: 1;
    border: none;
    resize: none;
    outline: none;
    background: transparent;
    min-height: 56px;
    max-height: 160px;
    font-size: .94rem;
    color: #0f172a;
    padding: .6rem .3rem;
  }

  .admin-chat-input:disabled {
    color: #94a3b8;
    cursor: not-allowed;
  }

  .admin-chat-send {
    width: 54px;
    height: 54px;
    border: none;
    border-radius: 18px;
    background: linear-gradient(135deg, #065f46, #10b981);
    color: #fff;
    font-size: 1.28rem;
    box-shadow: 0 16px 28px rgba(6,95,70,.22);
  }

  .admin-chat-send:disabled {
    opacity: .45;
    cursor: not-allowed;
    box-shadow: none;
  }

  .admin-chat-hint {
    margin-top: .65rem;
    color: #64748b;
    font-size: .78rem;
    padding: 0 .25rem;
  }

  .admin-chat-gate {
    min-height: 540px;
    display: grid;
    place-items: center;
    padding: 2.2rem;
  }

  .admin-chat-gate-card {
    width: min(100%, 620px);
    border-radius: 28px;
    padding: 2rem 1.7rem;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.2), transparent 30%),
      linear-gradient(180deg, #f7fff9, #ffffff);
    border: 1px solid #dceee4;
    box-shadow: 0 22px 60px rgba(6, 78, 59, .08);
    text-align: center;
  }

  .admin-chat-gate-icon {
    width: 84px;
    height: 84px;
    margin: 0 auto 1rem;
    border-radius: 28px;
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #047857;
    font-size: 2rem;
    box-shadow: 0 18px 36px rgba(16,185,129,.16);
  }

  .admin-chat-gate-card h3 {
    font-size: 1.3rem;
    font-weight: 800;
    color: #064e3b;
    margin-bottom: .55rem;
  }

  .admin-chat-gate-card p {
    margin: 0 auto 1.15rem;
    max-width: 460px;
    color: #64748b;
    line-height: 1.8;
  }

  .admin-chat-start {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    border: none;
    border-radius: 16px;
    padding: .92rem 1.25rem;
    color: #fff;
    font-weight: 800;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 32px rgba(6,95,70,.2);
  }

  .admin-chat-start:disabled {
    opacity: .55;
    cursor: not-allowed;
    box-shadow: none;
  }

  @media (max-width: 1199.98px) {
    .admin-chat-page {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 767.98px) {
    .admin-chat-card {
      min-height: 680px;
    }

    .admin-chat-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .admin-chat-head-actions {
      width: 100%;
      justify-content: flex-start;
    }

    .admin-message-content {
      max-width: 100%;
    }
=======
@section('page-title', 'Chat')

@push('styles')
<style>
  .chat-container {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 0;
    height: calc(100vh - 200px);
    min-height: 0;
    background: #fff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(15, 23, 42, .08);
  }

  .chat-sidebar {
    border-right: 1px solid #e5e7eb;
    display: flex;
    flex-direction: column;
    background: #fafbfc;
  }

  .chat-search {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
  }

  .chat-search input {
    width: 100%;
    border: 1px solid #dfe6e1;
    border-radius: 10px;
    padding: .65rem .9rem;
    font-size: .9rem;
    background: #fff;
    transition: border-color .15s ease;
  }

  .chat-search input:focus {
    outline: none;
    border-color: #0f766e;
  }

  .chat-list {
    flex: 1;
    overflow-y: auto;
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .chat-item {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background .15s ease;
    display: flex;
    gap: .8rem;
    align-items: center;
  }

  .chat-item:hover {
    background: #f0f8f5;
  }

  .chat-item.active {
    background: #e0f2f1;
    border-left: 4px solid #0f766e;
    padding-left: calc(1rem - 4px);
  }

  .chat-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #d1fae5;
    color: #064e3b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .9rem;
    flex-shrink: 0;
    overflow: hidden;
  }

  .chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .chat-info {
    flex: 1;
    min-width: 0;
  }

  .chat-name {
    font-weight: 700;
    font-size: .9rem;
    color: #111827;
    margin: 0;
  }

  .chat-status {
    font-size: .8rem;
    color: #6b7280;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .chat-main {
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow: hidden;
    background: #fff;
  }

  .chat-header {
    padding: 1.2rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .chat-header-info h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #111827;
  }

  .chat-header-info p {
    margin: 0.2rem 0 0;
    font-size: .8rem;
    color: #6b7280;
  }

  .chat-header-actions {
    display: flex;
    gap: .6rem;
  }

  .chat-header-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: #f3f4f6;
    border-radius: 8px;
    cursor: pointer;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s ease;
  }

  .chat-header-btn:hover {
    background: #e5e7eb;
  }

  .messages-container {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .message {
    width: 100%;
    display: flex;
    gap: .6rem;
    align-items: flex-end;
  }

  .message.sent {
    justify-content: flex-end;
  }

  .message.sent > div {
    margin-left: auto;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
  }

  .message.received > div {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .message > div {
    min-width: 0;
  }

  .message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #d1fae5;
    color: #064e3b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: .75rem;
    flex-shrink: 0;
  }

  .message-bubble {
    display: inline-block;
    width: fit-content;
    max-width: min(70%, 520px);
    padding: .8rem 1rem;
    border-radius: 14px;
    word-wrap: break-word;
    overflow-wrap: anywhere;
    word-break: break-word;
    white-space: pre-wrap;
    line-height: 1.45;
  }

  .message.sent .message-bubble {
    margin-left: auto;
  }

  .message.received .message-bubble {
    background: #f0f0f0;
    color: #111827;
  }

  .message.sent .message-bubble {
    background: #0f766e;
    color: #fff;
  }

  .message-time {
    font-size: .75rem;
    color: #9ca3af;
    margin-top: .2rem;
  }

  .chat-input-area {
    padding: 1.2rem 1.5rem;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: .7rem;
    align-items: flex-end;
    flex-shrink: 0;
  }

  .chat-input-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    border: 1px solid #dfe6e1;
    border-radius: 12px;
    background: #fff;
    padding: 0 1rem;
  }

  .chat-input-wrap input {
    flex: 1;
    border: none;
    padding: .75rem 0;
    font-size: .9rem;
    background: transparent;
    color: #111827;
  }

  .chat-input-wrap input:focus {
    outline: none;
  }

  .chat-input-wrap input::placeholder {
    color: #9ca3af;
  }

  .chat-send-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: #0f766e;
    color: #fff;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s ease;
  }

  .chat-send-btn:hover {
    background: #065f46;
  }

  .sidebar-counselor {
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
    background: #fafbfc;
  }

  .counselor-info {
    display: flex;
    gap: .8rem;
    align-items: center;
  }

  .counselor-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #d1fae5;
    color: #064e3b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .counselor-details h4 {
    margin: 0;
    font-size: .95rem;
    font-weight: 800;
    color: #111827;
  }

  .counselor-details p {
    margin: 0.1rem 0 0;
    font-size: .8rem;
    color: #6b7280;
  }

  @media (max-width: 1024px) {
    .chat-container {
      grid-template-columns: 1fr;
      height: auto;
    }

    .chat-sidebar {
      max-height: 400px;
    }
  }

  /* Scrollbar styling */
  .chat-list::-webkit-scrollbar,
  .messages-container::-webkit-scrollbar {
    width: 6px;
  }

  .chat-list::-webkit-scrollbar-track,
  .messages-container::-webkit-scrollbar-track {
    background: transparent;
  }

  .chat-list::-webkit-scrollbar-thumb,
  .messages-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
  }

  .chat-list::-webkit-scrollbar-thumb:hover,
  .messages-container::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
  }
</style>
@endpush

@section('konten')
<div class="admin-chat-page">
  <aside class="admin-chat-list">
    <div class="admin-chat-list-head">
      <h4>Daftar Sesi Online</h4>
      <p>Pilih mahasiswa untuk membuka ruang chat konseling yang sesuai.</p>
      <div class="admin-chat-search">
        <i class="ti ti-search"></i>
        <input
          type="search"
          id="adminChatSearchInput"
          placeholder="Cari mahasiswa, topik, atau tanggal sesi..."
          autocomplete="off"
        >
      </div>
    </div>

    @forelse($jadwalList as $item)
      @php
        $itemUser = optional(optional($item)->mahasiswa)->user;
        $isSelected = optional($activeJadwal)->id === $item->id;
        $itemScheduledAt = \Carbon\Carbon::parse(trim($item->tanggal . ' ' . ($item->waktu ?? '00:00:00')), 'Asia/Jakarta');
        $itemIsBlockedBySchedule = now('Asia/Jakarta')->lt($itemScheduledAt);
        $itemStatusKey = $itemIsBlockedBySchedule ? 'terjadwal' : strtolower($item->status ?? '');
        $itemStatusLabel = $itemIsBlockedBySchedule ? 'Terjadwal' : ucfirst($item->status ?? '-');
        $itemTopik = $item->catatan && preg_match('/Topik:\s*([^|]+)/i', $item->catatan, $match) ? trim($match[1]) : 'Topik belum tersedia';
        $sessionSearchText = strtolower(trim(implode(' ', [
            $itemUser?->getNamaDisplay() ?? 'Mahasiswa',
            \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j F Y'),
            substr($item->waktu, 0, 5),
            $itemTopik,
            $itemStatusLabel,
        ])));
      @endphp
      <a href="{{ route('admin.chat', ['jadwal' => $item->id]) }}" class="admin-chat-session {{ $isSelected ? 'active' : '' }}" data-session-search="{{ $sessionSearchText }}">
        <div class="admin-chat-session-top">
          <div class="admin-chat-session-name">{{ $itemUser?->getNamaDisplay() ?? 'Mahasiswa' }}</div>
          <span class="admin-chat-session-status {{ $itemStatusKey }}">{{ $itemStatusLabel }}</span>
        </div>
        <div class="admin-chat-session-meta">
          {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j M Y') }} • {{ substr($item->waktu, 0, 5) }} WIB<br>
          {{ $itemTopik }}
        </div>
      </a>
    @empty
      <div style="padding:1.1rem 1rem;color:#64748b;font-size:.84rem;">
        Belum ada sesi konseling online yang disetujui atau sedang berlangsung.
      </div>
    @endforelse
    @if($jadwalList->isNotEmpty())
      <div class="admin-chat-search-empty" id="adminChatSearchEmpty">
        Tidak ada percakapan yang cocok dengan kata kunci pencarian.
      </div>
    @endif
  </aside>

  <section class="admin-chat-card">
    @if(session('success'))
      <div style="margin:1rem 1rem 0;background:#e8fff1;border:1px solid #bbf7d0;color:#166534;padding:1rem 1.1rem;border-radius:18px;font-weight:700;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="margin:1rem 1rem 0;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:1rem 1.1rem;border-radius:18px;font-weight:700;">
        {{ session('error') }}
      </div>
    @endif

    @if(!$activeSession || !$activeJadwal)
      <div class="admin-chat-empty">
        <div>
          <div class="admin-chat-empty-icon"><i class="ti ti-message-heart"></i></div>
          <h3>Belum Ada Ruang Chat Aktif</h3>
          <p>
            Pilih salah satu sesi konseling online yang sudah disetujui dari daftar di samping untuk membuka ruang percakapan dengan mahasiswa.
          </p>
        </div>
      </div>
    @elseif($isBlockedBySchedule)
      <div class="admin-chat-gate">
        <div class="admin-chat-gate-card">
          <div class="admin-chat-gate-icon">
            <i class="ti ti-clock-hour-4"></i>
          </div>
          <h3>Sesi Akan Aktif Sesuai Jadwal</h3>
          <p>
            Jadwal konseling online dengan <strong>{{ $studentUser?->getNamaDisplay() ?? 'Mahasiswa' }}</strong>
            sudah tercatat, tetapi ruang chat dan video call baru bisa diakses pada
            <strong>{{ $scheduledStartLabel }}</strong>.
          </p>
          <button type="button" class="admin-chat-start" disabled>
            <i class="ti ti-lock"></i>
            <span>Menunggu Jadwal Sesi</span>
          </button>
        </div>
      </div>
    @elseif($isReadyToStart)
      <div class="admin-chat-gate">
        <div class="admin-chat-gate-card">
          <div class="admin-chat-gate-icon">
            <i class="ti {{ $canStartNow ? 'ti-message-chatbot' : 'ti-clock-hour-4' }}"></i>
          </div>
          <h3>{{ $canStartNow ? 'Sesi Siap Dimulai' : 'Sesi Belum Bisa Dimulai' }}</h3>
          <p>
            @if($canStartNow)
              Jadwal konseling online dengan <strong>{{ $studentUser?->getNamaDisplay() ?? 'Mahasiswa' }}</strong> sudah siap.
              Klik tombol di bawah untuk masuk ke ruang chat realtime.
            @else
              Jadwal sudah disetujui, tetapi sesi chat baru bisa dimulai pada
              <strong>{{ $scheduledStartLabel }}</strong>.
            @endif
          </p>
          <form action="{{ route('admin.chat.start') }}" method="POST">
            @csrf
            <input type="hidden" name="jadwal_id" value="{{ $activeJadwal->id }}">
            <button type="submit" class="admin-chat-start" {{ $canStartNow ? '' : 'disabled' }}>
              <i class="ti ti-player-play-filled"></i>
              <span>Mulai Sesi</span>
            </button>
          </form>
        </div>
      </div>
    @else
      <div class="admin-chat-head">
        <div class="admin-chat-person">
          <div class="admin-chat-avatar">
            <img src="{{ $chatPayload['studentAvatar'] }}" alt="{{ $chatPayload['studentName'] }}">
          </div>
          <div>
            <div class="admin-chat-title">{{ $chatPayload['studentName'] }}</div>
            <p class="admin-chat-subtitle">
              {{ $topik ?: 'Konseling online aktif' }}<br>
              {{ \Carbon\Carbon::parse($activeJadwal->tanggal)->translatedFormat('j F Y') }} • {{ substr($activeJadwal->waktu, 0, 5) }} WIB
            </p>
          </div>
        </div>
        <div class="admin-chat-head-actions">
          <a href="{{ $chatPayload['videoCallUrl'] }}" target="_blank" rel="noopener noreferrer" class="admin-chat-video-btn">
            <i class="ti ti-video"></i>
            <span>Video Call</span>
          </a>
          <div class="admin-chat-badge">{{ $statusLabel }}</div>
        </div>
      </div>

      <div class="admin-chat-thread" id="adminChatThread">
        <div class="admin-chat-date">
          {{ \Carbon\Carbon::parse($activeJadwal->tanggal)->translatedFormat('l, j F Y') }}
        </div>
      </div>

      <div class="admin-chat-compose">
        <form id="adminChatForm" class="admin-chat-form">
          <textarea
            id="adminChatInput"
            class="admin-chat-input"
            rows="1"
            maxlength="2000"
            placeholder="Tulis respons konseling Anda di sini..."
          ></textarea>
          <button type="submit" class="admin-chat-send" id="adminChatSendBtn">
            <i class="ti ti-send"></i>
          </button>
        </form>
        <div class="admin-chat-hint" id="adminChatHint">
          Pesan akan langsung terkirim ke ruang chat mahasiswa yang sesuai secara realtime.
        </div>
      </div>
    @endif
  </section>
</div>
@php
  $sessionActive = isset($sessionData);
  $studentName = $sessionActive ? $sessionData['nama'] : 'Aldo Darrel';
  $studentInitials = $sessionActive ? strtoupper(substr($sessionData['nama'], 0, 1)) : 'AD';
  $messages = $messages ?? collect();
  $participants = $participants ?? collect();
  $currentUserId = auth()->id();
@endphp

<div class="chat-container">
  <!-- Sidebar -->
  <div class="chat-sidebar">
    <div class="chat-search">
      <input type="text" placeholder="Cari percakapan..." id="searchChat">
    </div>

    <ul class="chat-list" id="chatList">
      @forelse($participants as $p)
        @php
          $isActive = $sessionActive && ($sessionData['id'] == $p['id']);
        @endphp
        <li class="chat-item {{ $isActive ? 'active' : '' }}" data-session-id="{{ $p['id'] }}" onclick="window.location='{{ route('admin.chat.session', $p['id']) }}'">
          <div class="chat-avatar">{{ $p['initial'] }}</div>
          <div class="chat-info">
            <p class="chat-name">{{ $p['nama'] }}</p>
            <p class="chat-status">{{ $p['last'] }}</p>
          </div>
        </li>
      @empty
        <li class="chat-item">
          <div class="chat-avatar">-</div>
          <div class="chat-info">
            <p class="chat-name">Belum ada percakapan</p>
            <p class="chat-status">Mulai sesi untuk membuat percakapan</p>
          </div>
        </li>
      @endforelse
    </ul>

    <!-- Counselor Info -->
    <div class="sidebar-counselor">
      <div class="counselor-info">
        <div class="counselor-avatar">L</div>
        <div class="counselor-details">
          <h4>Laura</h4>
          <p>Konselor Utama</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Chat Area -->
  <div class="chat-main">
    <!-- Header -->
    <div class="chat-header">
      <div class="chat-header-info">
        <h3>{{ $studentName }}</h3>
      </div>
      <div class="chat-header-actions">
        <button class="chat-header-btn" title="Video">
          <i class="bi bi-camera-video-fill"></i>
        </button>
      </div>
    </div>

    <!-- Messages -->
    <div class="messages-container" id="messagesContainer">
      @forelse($messages as $message)
        @php
          $isSent = $message->pengirim_id === $currentUserId;
          $initial = strtoupper(substr(optional($message->pengirim)->nama ?? 'A', 0, 1));
          $messageTime = $message->created_at
            ? $message->created_at->timezone(config('app.timezone'))->format('H:i')
            : now()->timezone(config('app.timezone'))->format('H:i');
        @endphp
        <div class="message {{ $isSent ? 'sent' : 'received' }}">
          @if(!$isSent)
            <div class="message-avatar">{{ $initial }}</div>
            <div>
              <div class="message-bubble">{{ $message->pesan }}</div>
              <div class="message-time">{{ $messageTime }}</div>
            </div>
          @else
            <div>
              <div class="message-bubble">{{ $message->pesan }}</div>
              <div class="message-time">{{ $messageTime }}</div>
            </div>
          @endif
        </div>
      @empty
        <div class="text-center text-muted py-5">Belum ada pesan. Mulai percakapan sekarang.</div>
      @endforelse
    </div>

    <!-- Input Area -->
    <form class="chat-input-area" method="POST" action="{{ $sessionActive ? route('admin.chat.store', $sessionData['id']) : '#' }}">
      @csrf
      <div class="chat-input-wrap">
        <input type="text" name="pesan" id="messageInput" placeholder="Tulis pesan..." autocomplete="off" {{ $sessionActive ? '' : 'disabled' }}>
      </div>
      <button class="chat-send-btn" id="sendBtn" title="Send" {{ $sessionActive ? '' : 'disabled' }}>
        <i class="bi bi-send-fill"></i>
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const searchInput = document.getElementById('adminChatSearchInput');
  const searchEmpty = document.getElementById('adminChatSearchEmpty');
  const sessionItems = Array.from(document.querySelectorAll('[data-session-search]'));

  if (!searchInput || sessionItems.length === 0) {
    return;
  }

  const syncSearch = () => {
    const keyword = searchInput.value.trim().toLowerCase();
    let visibleCount = 0;

    sessionItems.forEach((item) => {
      const haystack = item.dataset.sessionSearch || '';
      const isMatch = !keyword || haystack.includes(keyword);
      item.style.display = isMatch ? '' : 'none';
      if (isMatch) {
        visibleCount += 1;
      }
    });

    if (searchEmpty) {
      searchEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  searchInput.addEventListener('input', syncSearch);
})();
</script>
@endpush

@if($activeSession && $chatPayload && $chatAccessGranted)
@push('scripts')
<script>
(() => {
  const payload = @json($chatPayload);
  const currentUserId = {{ auth()->id() }};
  const thread = document.getElementById('adminChatThread');
  const form = document.getElementById('adminChatForm');
  const input = document.getElementById('adminChatInput');
  const sendBtn = document.getElementById('adminChatSendBtn');
  const hint = document.getElementById('adminChatHint');

  if (!thread || !form || !input || !sendBtn) {
    return;
  }

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  const scrollToBottom = () => {
    thread.scrollTop = thread.scrollHeight;
  };

  const autoResize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
  };

  const renderMessage = (message) => {
    const row = document.createElement('div');
    const isMine = Boolean(message.is_mine ?? (message.sender_id === currentUserId));

    row.className = `admin-message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;

    row.innerHTML = `
      ${isMine ? '' : `
        <div class="admin-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      `}
      <div class="admin-message-content">
        <div class="admin-message-meta">
          <span class="admin-message-name">${escapeHtml(message.sender_name)}</span>
          <span>${escapeHtml(message.time)}</span>
        </div>
        <div class="admin-message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
      </div>
      ${isMine ? `
        <div class="admin-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      ` : ''}
    `;

    thread.appendChild(row);
  };

  const syncMessages = async () => {
    try {
      const response = await fetch(`${payload.messagesUrl}?sesi_id=${payload.sessionId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (!response.ok) {
        return;
      }

      const data = await response.json();

      if (!data.success || !Array.isArray(data.messages)) {
        return;
      }

      const knownIds = new Set(Array.from(thread.querySelectorAll('[data-message-id]')).map((element) => Number(element.dataset.messageId)));

      data.messages.forEach((message) => {
        if (!knownIds.has(Number(message.id))) {
          renderMessage(message);
        }
      });

      scrollToBottom();
    } catch (error) {
      console.error(error);
    }
  };

  (payload.messages || []).forEach((message) => renderMessage(message));
  scrollToBottom();
  autoResize();

  if (window.Echo) {
    window.Echo.private(payload.channel)
      .listen('.chat.message.sent', (event) => {
        if (!event?.message) {
          return;
        }

        const exists = thread.querySelector(`[data-message-id="${event.message.id}"]`);
        if (exists) {
          return;
        }

        renderMessage({
          ...event.message,
          is_mine: Number(event.message.sender_id) === currentUserId,
        });

        scrollToBottom();
      });
  }

  syncMessages();
  window.setInterval(syncMessages, 10000);

  input.addEventListener('input', autoResize);
  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      form.requestSubmit();
    }
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const pesan = input.value.trim();
    if (!pesan) {
      return;
    }

    sendBtn.disabled = true;
    hint.textContent = 'Mengirim pesan ke mahasiswa...';

    try {
      const response = await fetch(payload.sendUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
          'X-Requested-With': 'XMLHttpRequest',
          'X-Socket-ID': window.Echo?.socketId?.() ?? '',
        },
        body: JSON.stringify({
          sesi_id: payload.sessionId,
          pesan,
        }),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        hint.textContent = data.message ?? 'Pesan gagal dikirim.';
        sendBtn.disabled = false;
        return;
      }

      renderMessage(data.message);
      scrollToBottom();
      input.value = '';
      autoResize();
      hint.textContent = 'Pesan terkirim dan langsung masuk ke ruang chat mahasiswa.';
    } catch (error) {
      console.error(error);
      hint.textContent = 'Terjadi kendala saat mengirim pesan.';
    } finally {
      sendBtn.disabled = false;
    }
  });
})();
</script>
@endpush
@endif
  // Chat item selection
  document.querySelectorAll('.chat-item').forEach(item => {
    item.addEventListener('click', function() {
      document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
      this.classList.add('active');
      
      const name = this.querySelector('.chat-name').textContent;
      document.querySelector('.chat-header-info h3').textContent = name;
    });
  });

  const messagesContainer = document.getElementById('messagesContainer');
  if (messagesContainer) {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
  }

  // Search functionality
  const searchInput = document.getElementById('searchChat');
  searchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.chat-item').forEach(item => {
      const name = item.querySelector('.chat-name').textContent.toLowerCase();
      item.style.display = name.includes(query) ? '' : 'none';
    });
  });
</script>
@endpush
