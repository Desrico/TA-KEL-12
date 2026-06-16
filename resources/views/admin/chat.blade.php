@extends('layouts.admin')

@php
    $jadwal = $activeJadwal;
    $mahasiswa = optional($jadwal)->mahasiswa;
    $studentUser = optional($mahasiswa)->user;
    $isActiveAnonim = filter_var($jadwal->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

    $studentNameTampil = $isActiveAnonim
        ? (
            $studentUser && method_exists($studentUser, 'getAnonimDisplayName')
                ? trim($studentUser->getAnonimDisplayName())
                : 'Anonim'
          )
        : ($studentUser->nama ?? 'Mahasiswa');
    $topik = null;
    $isBlockedBySchedule = $isBlockedBySchedule ?? false;
    $chatAccessGranted = $chatAccessGranted ?? false;

    if (!empty($jadwal?->catatan) && preg_match('/Topik:\s*([^|]+)/i', $jadwal->catatan, $match)) {
        $topik = trim($match[1]);
    }

    $statusJadwal = strtolower(str_replace(' ', '_', $jadwal?->status ?? ''));
    $statusSesi = strtolower(str_replace(' ', '_', optional($activeSession)->status ?? ''));

    $chatSelesai = $statusJadwal === 'selesai' || $statusSesi === 'selesai';
@endphp

@section('page-title', 'Chat Konseling')

@push('styles')
<style>
  .admin-chat-page {
    display: grid;
    grid-template-columns: 340px minmax(0, 1fr);
    gap: 0;
    min-height: 760px;
    background: #fff;
    border: 1px solid #dceee4;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 18px 44px rgba(6, 78, 59, .08);
  }

  .admin-chat-card,
  .admin-chat-list {
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
  }

  .admin-chat-list {
    overflow: hidden;
    align-self: stretch;
    border-right: 1px solid #edf7f1;
    background: linear-gradient(180deg, #f7fff9, #ffffff 18%);
  }

  .admin-chat-list-head {
    padding: 1rem 1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background: rgba(255, 255, 255, .92);
  }

  .admin-chat-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
    padding: .12rem;
    border-radius: 14px;
    background: #ffffff;
    border: 1px solid #dceee4;
    box-shadow: 0 10px 24px rgba(15, 23, 42, .04);
  }

  .admin-chat-tab {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .18rem;
    min-height: 52px;
    padding: .48rem .52rem .42rem;
    border-radius: 10px;
    text-decoration: none;
    font-size: .73rem;
    font-weight: 800;
    text-align: center;
    border: 1px solid transparent;
    background: transparent;
    color: #64748b;
    position: relative;
    transition: all .22s ease;
  }

  .admin-chat-avatar-fallback {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #b7ebc9;
    color: #065f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    font-weight: 800;
    border: 3px solid #ffffff;
    box-shadow: 0 10px 25px rgba(6, 95, 70, .12);
    flex-shrink: 0;
}

.admin-chat-readonly-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .7rem;
    width: 100%;
    padding: 1rem 1.2rem;
    border: 1px dashed #bbf7d0;
    border-radius: 18px;
    background: #f0fdf4;
    color: #065f46;
    font-size: .9rem;
    font-weight: 700;
    text-align: center;
}

.admin-chat-readonly-box i {
    font-size: 1.2rem;
}

.admin-message-avatar-fallback {
    width: 30px;
    height: 30px;
    min-width: 30px;
    border-radius: 50%;
    background: #b7ebc9;
    color: #065f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 800;
    flex-shrink: 0;
    box-shadow: 0 5px 12px rgba(15, 23, 42, 0.08);
}

.admin-message-avatar-fallback.admin {
    background: #d1fae5;
    color: #064e3b;
}

  .admin-chat-tab:hover {
    background: linear-gradient(180deg, #f7fffb, #f1fcf6);
    color: #065f46;
    border-color: rgba(16, 185, 129, .12);
  }

  .admin-chat-tab.active {
    background: linear-gradient(180deg, #ffffff, #f4fcf7);
    color: #065f46;
    border-color: transparent;
    box-shadow: inset 0 -3px 0 #10b981;
  }

  .admin-chat-tab.active::after {
    content: "";
    position: absolute;
    left: 18%;
    right: 18%;
    bottom: 0;
    height: 3px;
    border-radius: 999px 999px 0 0;
    background: linear-gradient(135deg, #059669, #34d399);
  }

  .admin-chat-tab-icon {
    font-size: .88rem;
    line-height: 1;
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
  
  .admin-chat-filter-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .55rem;
    margin-top: .8rem;
  }

  .admin-chat-filter-btn {
    border: 1px solid #dceee4;
    background: #ffffff;
    color: #334155;
    border-radius: 999px;
    padding: .55rem .9rem;
    font-size: .8rem;
    font-weight: 800;
    cursor: pointer;
    transition: all .18s ease;
  }

  .admin-chat-filter-btn:hover {
    background: #f0fdf4;
    color: #065f46;
  }

  .admin-chat-filter-btn.active {
    background: #065f46;
    color: #ffffff;
    border-color: #065f46;
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
    overflow: visible;
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
    overflow: visible;
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
    gap: .35rem;
    margin-bottom: .85rem;
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
    max-width: min(72%, 420px);
    width: fit-content;
    position: relative;
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
    display: inline-block;
    width: fit-content;
    max-width: 100%;
    padding: .65rem .95rem;
    border-radius: 18px;
    font-size: .88rem;
    line-height: 1.45;
    word-break: break-word;
    white-space: pre-wrap;
}

  .admin-message-bubble-shell {
    position: relative;
  }

  .admin-message-edited {
    font-size: .68rem;
    color: #94a3b8;
    font-weight: 600;
  }

  .admin-message-actions {
    position: absolute;
    top: .55rem;
    right: .7rem;
    opacity: 0;
    pointer-events: none;
    transition: opacity .18s ease;
  }

  .admin-message-row.mine:hover .admin-message-actions,
  .admin-message-row.mine.is-menu-open .admin-message-actions {
    opacity: 1;
    pointer-events: auto;
  }

  .admin-message-action-toggle {
    width: 28px;
    height: 28px;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.18);
    color: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .admin-message-action-menu {
    position: absolute;
    top: calc(100% + .3rem);
    right: 0;
    min-width: 150px;
    padding: .4rem;
    border-radius: 14px;
    background: #fff;
    border: 1px solid rgba(221, 239, 231, 0.96);
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
    display: none;
    z-index: 4;
  }

  .admin-message-row.is-menu-open .admin-message-action-menu {
    display: block;
  }

  .admin-message-action-item {
    width: 100%;
    border: none;
    background: transparent;
    border-radius: 10px;
    padding: .55rem .7rem;
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    color: #0f172a;
    font-size: .8rem;
    font-weight: 700;
    text-align: left;
  }

  .admin-message-action-item:hover {
    background: #f8fffb;
  }

  .admin-message-action-item.delete {
    color: #b91c1c;
  }

  .admin-message-row.is-editing .admin-message-actions {
    display: none;
  }

  .admin-message-editor-shell {
    display: grid;
    gap: .7rem;
  }

  .admin-message-editor-input {
    width: 100%;
    min-height: 92px;
    border: 1px solid rgba(209, 250, 229, 0.96);
    border-radius: 18px;
    padding: .8rem .9rem;
    resize: vertical;
    outline: none;
    font-size: .92rem;
    line-height: 1.65;
    color: #0f172a;
    background: rgba(255, 255, 255, 0.98);
  }

  .admin-message-editor-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .admin-message-editor-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .admin-message-editor-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .admin-message-editor-btn.save {
    background: #065f46;
    color: #fff;
  }

  .admin-message-delete-confirm {
    display: grid;
    gap: .75rem;
  }

  .admin-message-delete-confirm-text {
    font-size: .83rem;
    line-height: 1.6;
    color: #334155;
  }

  .admin-message-delete-confirm-actions {
    display: flex;
    justify-content: flex-end;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .admin-message-delete-confirm-btn {
    border: none;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .76rem;
    font-weight: 700;
  }

  .admin-message-delete-confirm-btn.cancel {
    background: #e2e8f0;
    color: #334155;
  }

  .admin-message-delete-confirm-btn.delete {
    background: #b91c1c;
    color: #fff;
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

  .admin-session-menu {
    position: relative;
}

.admin-session-menu-toggle {
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 14px;
    background: #F0FDF4;
    color: #064E3B;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    cursor: pointer;
    transition: background .18s ease, transform .18s ease;
}

.admin-session-menu-toggle:hover {
    background: #D1FAE5;
    transform: translateY(-1px);
}

.admin-session-menu-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 220px;
    padding: .65rem;
    border-radius: 18px;
    background: #ffffff;
    border: 1px solid #DCEEE4;
    box-shadow: 0 18px 40px rgba(15, 23, 42, .14);
    display: none;
    z-index: 9999;
}

.admin-session-menu-dropdown {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: 260px;
    padding: .7rem;
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid #dceee4;
    box-shadow: 0 22px 55px rgba(15, 23, 42, .16);
    display: none;
    z-index: 9999;
}

.admin-session-menu.is-open .admin-session-menu-dropdown {
    display: grid;
    gap: .45rem;
}

.admin-session-menu-item {
    width: 100%;
    border: none;
    text-decoration: none;
    border-radius: 16px;
    padding: .78rem .85rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    background: #ffffff;
    color: #064E3B;
    font-size: .88rem;
    font-weight: 800;
    text-align: left;
    cursor: pointer;
    transition: all .18s ease;
}

.admin-session-menu-item:hover {
    background: #ecfdf5;
    color: #065F46;
    transform: translateY(-1px);
}

.admin-session-menu-item .menu-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: #d1fae5;
    color: #065F46;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.admin-session-menu-item .menu-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.admin-session-menu-item .menu-text small {
    color: #64748b;
    font-size: .72rem;
    font-weight: 600;
}

.admin-session-menu-item.finish {
    color: #065F46;
}

.admin-session-menu-item.detail .menu-icon {
    background: #eef6f2;
    color: #475569;
}

.admin-session-menu-divider {
    height: 1px;
    background: #edf2ef;
    margin: .25rem .2rem;
}

.finish-session-modal {
    position: fixed;
    inset: 0;
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgba(15, 23, 42, .52);
    backdrop-filter: blur(4px);
}

.finish-session-modal.is-open {
    display: flex;
}

.finish-session-box {
    width: 100%;
    max-width: 470px;
    background: #ffffff;
    border-radius: 28px;
    padding: 2rem;
    box-shadow: 0 28px 80px rgba(15, 23, 42, .24);
    text-align: center;
    animation: finishModalIn .18s ease-out;
}

@keyframes finishModalIn {
    from {
        opacity: 0;
        transform: translateY(10px) scale(.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.finish-session-icon {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: #d1fae5;
    color: #065F46;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 2.3rem;
    margin-bottom: 1.1rem;
}

.finish-session-box h3 {
    margin: 0 0 .75rem;
    font-size: 1.55rem;
    line-height: 1.25;
    font-weight: 900;
    color: #064E3B;
}

.finish-session-box p {
    margin: 0 auto;
    color: #64748B;
    font-size: .95rem;
    line-height: 1.65;
    max-width: 380px;
}

.finish-session-actions {
    display: flex;
    justify-content: center;
    gap: .8rem;
    margin-top: 1.6rem;
    flex-wrap: wrap;
}

.btn-finish-cancel,
.btn-finish-confirm {
    border: none;
    border-radius: 14px;
    padding: .85rem 1.25rem;
    font-weight: 900;
    font-size: .92rem;
    cursor: pointer;
    min-width: 120px;
    transition: all .18s ease;
}

.btn-finish-cancel {
    background: #f1f5f9;
    color: #334155;
}

.btn-finish-cancel:hover {
    background: #e2e8f0;
}

.btn-finish-confirm {
    background: #065F46;
    color: #ffffff;
    box-shadow: 0 12px 24px rgba(6, 95, 70, .22);
}

.btn-finish-confirm:hover {
    background: #064E3B;
    transform: translateY(-1px);
}

.btn-finish-cancel {
    background: #F1F5F9;
    color: #334155;
}

.btn-finish-confirm {
    background: #064E3B;
    color: #ffffff;
}

  @media (max-width: 1199.98px) {
    .admin-chat-page {
      grid-template-columns: 1fr;
      min-height: 0;
    }

    .admin-chat-list {
      border-right: none;
      border-bottom: 1px solid #edf7f1;
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
  }
</style>
@endpush

@section('konten')
<div class="admin-chat-page">
  <aside class="admin-chat-list">
    <div class="admin-chat-list-head">
      <div class="admin-chat-tabs">
        <a href="{{ route('admin.chat') }}" class="admin-chat-tab active">
          <i class="ti ti-message-heart admin-chat-tab-icon"></i>
          <span>Chat Konseling</span>
        </a>
        <a href="{{ route('admin.group-chat') }}" class="admin-chat-tab">
          <i class="ti ti-users-group admin-chat-tab-icon"></i>
          <span>Grup Chat</span>
        </a>
      </div>
      <div class="admin-chat-search">
        <i class="ti ti-search"></i>
        <input
          type="search"
          id="adminChatSearchInput"
          placeholder="Cari"
          autocomplete="off"
        >
      </div>
      @php
          $activeRoomType = filter_var(optional($activeJadwal)->anonim ?? false, FILTER_VALIDATE_BOOLEAN)
            ? 'anonim'
            : 'biasa';
      @endphp

      <div class="admin-chat-filter-tabs">
          <button type="button"
              class="admin-chat-filter-btn {{ $activeRoomType === 'biasa' ? 'active' : '' }}"
              data-room-filter="biasa">
              Akun Mahasiswa
          </button>

          <button type="button"
              class="admin-chat-filter-btn {{ $activeRoomType === 'anonim' ? 'active' : '' }}"
              data-room-filter="anonim">
              Anonim
          </button>
      </div>
    </div>

   @forelse($jadwalList as $item)
    @php
        $itemUser = optional(optional($item)->mahasiswa)->user;

      $isItemAnonim = filter_var($item->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

      $itemNamaTampil = $isItemAnonim
          ? (
              $itemUser && method_exists($itemUser, 'getAnonimDisplayName')
                  ? trim($itemUser->getAnonimDisplayName())
                  : 'Anonim'
            )
          : ($itemUser->nama ?? 'Mahasiswa');

      $isActiveAnonimItem = filter_var(optional($activeJadwal)->anonim ?? false, FILTER_VALIDATE_BOOLEAN);

      $isSelected = $isItemAnonim
          ? optional($activeJadwal)->id === $item->id
          : (
              !$isActiveAnonimItem
              && optional($activeJadwal)->mahasiswa_id === $item->mahasiswa_id
          );
      $itemScheduledAt = $item->scheduledAt('Asia/Jakarta');
      $itemRawStatus = strtolower(str_replace(' ', '_', $item->status ?? ''));
      $itemIsFinished = $itemRawStatus === 'selesai';

      $itemIsBlockedBySchedule = !$itemIsFinished && $itemScheduledAt
          ? now('Asia/Jakarta')->lt($itemScheduledAt)
          : false;

      $itemStatusKey = $itemIsFinished
          ? 'selesai'
          : ($itemIsBlockedBySchedule ? 'terjadwal' : $itemRawStatus);

      $itemStatusLabel = $itemIsFinished
          ? 'Selesai'
          : ($itemIsBlockedBySchedule ? 'Terjadwal' : ucfirst($item->status ?? '-'));

      $itemTopik = $item->catatan && preg_match('/Topik:\s*([^|]+)/i', $item->catatan, $match)
          ? trim($match[1])
          : 'Topik belum tersedia';

      $sessionSearchText = strtolower(trim(implode(' ', [
          $itemNamaTampil,
          \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j F Y'),
          substr($item->waktu, 0, 5),
          $itemTopik,
      ])));
  @endphp
     <a href="{{ route('admin.chat', ['jadwal' => $item->id]) }}"
        class="admin-chat-session {{ $isSelected ? 'active' : '' }}"
        data-room-type="{{ $isItemAnonim ? 'anonim' : 'biasa' }}"
        data-session-search="{{ $sessionSearchText }}">
        <div class="admin-chat-session-top">
          <div class="admin-chat-session-name">{{ $itemNamaTampil }}</div>
        </div>
        <div class="admin-chat-session-meta">
          {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('j M Y') }} &bull; {{ substr($item->waktu, 0, 5) }} WIB<br>
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
    @else
  @php
      $canSendChat = $chatAccessGranted && !$isBlockedBySchedule && !$chatSelesai;
  @endphp

  <div class="admin-chat-head">
    <div class="admin-chat-person">
      <div class="admin-chat-avatar-fallback">
        {{ strtoupper(substr($chatPayload['studentName'] ?? 'M', 0, 1)) }}
      </div>

      <div>
        <div class="admin-chat-title">{{ $chatPayload['studentName'] }}</div>
        <p class="admin-chat-subtitle">
          {{ $topik ?: 'Konseling online aktif' }}<br>
          {{ \Carbon\Carbon::parse($activeJadwal->tanggal)->translatedFormat('j F Y') }} &bull; {{ substr($activeJadwal->waktu, 0, 5) }} WIB
        </p>
      </div>
    </div>

    <div class="admin-chat-head-actions">
      <div class="admin-session-menu" id="adminSessionMenu">
          <button type="button" class="admin-session-menu-toggle" id="adminSessionMenuToggle">
              <i class="ti ti-dots-vertical"></i>
          </button>

         <div class="admin-session-menu-dropdown" id="adminSessionMenuDropdown">
            <button 
                type="button" 
                class="admin-session-menu-item"
                id="openFinishSessionModal"
            >
                Selesaikan Sesi
            </button>

            <a href="{{ route('admin.sesi.detail', $activeSession->id) }}" class="admin-session-menu-item">
                Lihat Detail Sesi
            </a>
        </div>
      </div>
    </div>
  </div>

  <div class="admin-chat-thread" id="adminChatThread"></div>

      <div class="admin-chat-compose">
        @if($canSendChat)
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
        @else
          <div class="admin-chat-readonly-box">
            <i class="ti ti-lock"></i>
            <span>
              Pesan baru dapat dikirim setelah penjadwalan konseling diterima.
            </span>
          </div>
        @endif
      </div>

     <div class="finish-session-modal" id="finishSessionModal">
      <div class="finish-session-box">
          <div class="finish-session-icon">
              <i class="ti ti-circle-check"></i>
          </div>

          <h3>Selesaikan Sesi Konseling?</h3>

          <p>
              Pastikan proses konseling sudah selesai sebelum mengubah status sesi.
              Setelah dikonfirmasi, sesi akan ditandai sebagai selesai.
          </p>

          <div class="finish-session-actions">
              <button type="button" class="btn-finish-cancel" id="closeFinishSessionModal">
                  Batal
              </button>

              <form action="{{ route('admin.sesi.selesai', $activeJadwal->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn-finish-confirm">
                      Ya, Selesaikan
                  </button>
              </form>
          </div>
      </div>
  </div>
  </div>
    @endif
  </section>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const searchInput = document.getElementById('adminChatSearchInput');
  const searchEmpty = document.getElementById('adminChatSearchEmpty');
  const sessionItems = Array.from(document.querySelectorAll('[data-session-search]'));
  const filterButtons = Array.from(document.querySelectorAll('[data-room-filter]'));

  if (sessionItems.length === 0) {
    return;
  }

  let activeFilter = document.querySelector('[data-room-filter].active')?.dataset.roomFilter || 'biasa';

  const syncList = () => {
    const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';
    let visibleCount = 0;

    sessionItems.forEach((item) => {
      const haystack = item.dataset.sessionSearch || '';
      const roomType = item.dataset.roomType || 'biasa';

      const matchFilter = roomType === activeFilter;
      const matchSearch = !keyword || haystack.includes(keyword);

      const isVisible = matchFilter && matchSearch;

      item.style.display = isVisible ? '' : 'none';

      if (isVisible) {
        visibleCount += 1;
      }
    });

    if (searchEmpty) {
      searchEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  filterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.roomFilter;

      filterButtons.forEach((btn) => btn.classList.remove('active'));
      button.classList.add('active');

      syncList();
    });
  });

  if (searchInput) {
    searchInput.addEventListener('input', syncList);
  }

  syncList();
})();
</script>
@endpush

@if($activeSession && $chatPayload)
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

  const canSendMessage = @json($canSendChat ?? false);

  if (!thread) {
    return;
  }

  const escapeHtml = (value) => String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
  const dateFormatter = new Intl.DateTimeFormat('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    timeZone: 'Asia/Jakarta',
  });
  const dateKeyFormatter = new Intl.DateTimeFormat('en-CA', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    timeZone: 'Asia/Jakarta',
  });

  const resolveDateParts = (value) => {
    const date = value ? new Date(value) : new Date();
    const keyParts = Object.fromEntries(dateKeyFormatter.formatToParts(date).map((part) => [part.type, part.value]));

    return {
      key: `${keyParts.year}-${keyParts.month}-${keyParts.day}`,
      label: dateFormatter.format(date).toUpperCase(),
    };
  };

  const scrollToBottom = () => {
    thread.scrollTop = thread.scrollHeight;
  };

  const autoResize = () => {
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 160)}px`;
  };
  const messageUpdateUrl = (messageId) => payload.updateUrlTemplate.replace('__CHAT_ID__', String(messageId));
  const messageDeleteUrl = (messageId) => payload.deleteUrlTemplate.replace('__CHAT_ID__', String(messageId));

  const closeAllMenus = () => {
    thread.querySelectorAll('.admin-message-row.is-menu-open').forEach((element) => {
      element.classList.remove('is-menu-open');
    });
  };

  // Renderer bubble dipisah dari editor inline supaya edit terasa natural di dalam chat.
  const buildMessageBubbleMarkup = (message, isMine) => `
    <div class="admin-message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
    ${isMine && canSendMessage ? `
      <div class="admin-message-actions">
        <button type="button" class="admin-message-action-toggle" data-action="toggle-menu" aria-label="Opsi pesan">
          <i class="ti ti-dots"></i>
        </button>
        <div class="admin-message-action-menu">
          <button type="button" class="admin-message-action-item" data-action="edit-message" data-message-id="${message.id}">
            <i class="ti ti-edit"></i>
            <span>Edit pesan</span>
          </button>
          <button type="button" class="admin-message-action-item delete" data-action="delete-message" data-message-id="${message.id}">
            <i class="ti ti-trash"></i>
            <span>Hapus pesan</span>
          </button>
        </div>
      </div>
    ` : ''}
  `;

  const buildInlineEditorMarkup = (text, messageId) => `
    <div class="admin-message-editor-shell" data-editing-message-id="${messageId}">
      <textarea class="admin-message-editor-input" maxlength="2000">${escapeHtml(text)}</textarea>
      <div class="admin-message-editor-actions">
        <button type="button" class="admin-message-editor-btn cancel" data-action="cancel-edit" data-message-id="${messageId}">Batal</button>
        <button type="button" class="admin-message-editor-btn save" data-action="save-edit" data-message-id="${messageId}">Simpan</button>
      </div>
    </div>
  `;

  const buildDeleteConfirmMarkup = (messageId) => `
    <div class="admin-message-delete-confirm" data-delete-message-id="${messageId}">
      <div class="admin-message-delete-confirm-text">Hapus pesan ini secara permanen?</div>
      <div class="admin-message-delete-confirm-actions">
        <button type="button" class="admin-message-delete-confirm-btn cancel" data-action="cancel-delete" data-message-id="${messageId}">Batal</button>
        <button type="button" class="admin-message-delete-confirm-btn delete" data-action="confirm-delete" data-message-id="${messageId}">Hapus</button>
      </div>
    </div>
  `;

  // Polling ditahan saat admin sedang edit atau konfirmasi hapus agar bubble tidak reset sendiri.
  const hasActiveInlineState = () => Boolean(
    thread.querySelector('.admin-message-row.is-editing, [data-delete-message-id]')
  );

  // Bubble asli dikembalikan jika admin membatalkan edit atau hapus.
  const restoreMessageBubble = (row) => {
    const bubbleShell = row.querySelector('.admin-message-bubble-shell');
    const isMine = row.classList.contains('mine');

    if (!bubbleShell) {
      return;
    }

    bubbleShell.innerHTML = buildMessageBubbleMarkup({
      id: Number(row.dataset.messageId),
      text: row.dataset.messageText ?? '',
    }, isMine);
    row.classList.remove('is-editing');
    row.classList.remove('is-menu-open');
  };

  const lastRenderedDateKey = () => Array.from(thread.querySelectorAll('[data-date-key]')).pop()?.dataset.dateKey || null;

  const renderDateSeparator = (label, key) => {
    const separator = document.createElement('div');
    separator.className = 'admin-chat-date';
    separator.dataset.dateKey = key;
    separator.textContent = label;
    thread.appendChild(separator);
  };

  const ensureDateSeparator = (key, label) => {
    if (!key || lastRenderedDateKey() === key) {
      return;
    }

    renderDateSeparator(label, key);
  };

  const renderMessage = (message) => {
    const row = document.createElement('div');
    const isMine = Boolean(message.is_mine ?? (message.sender_id === currentUserId));
    const dateParts = resolveDateParts(message.sent_at);

    const senderRole = String(message.sender_role || '').toLowerCase();

    const isCounselorMessage =
        isMine ||
        senderRole === 'konselor' ||
        senderRole === 'admin' ||
        String(message.sender_name || '').toLowerCase() === 'admin';

    const displaySenderName = isCounselorMessage
        ? 'Konselor'
        : (message.sender_name || 'Mahasiswa');

    const avatarInitial = isCounselorMessage
        ? 'K'
        : String(displaySenderName || 'M').charAt(0).toUpperCase();

    ensureDateSeparator(dateParts.key, dateParts.label);

    row.className = `admin-message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;
    row.dataset.messageText = message.text ?? '';
    row.dataset.messageEdited = message.is_edited ? '1' : '0';

    row.innerHTML = `
      ${isMine ? '' : `
        <div class="admin-message-avatar-fallback">
          ${escapeHtml(avatarInitial)}
        </div>
      `}
      <div class="admin-message-content">
        <div class="admin-message-meta">
          <span class="admin-message-name">${escapeHtml(displaySenderName)}</span>
          <span>${escapeHtml(message.time)}</span>
          ${message.is_edited ? '<span class="admin-message-edited">telah diedit</span>' : ''}
        </div>
        <div class="admin-message-bubble-shell">${buildMessageBubbleMarkup(message, isMine)}</div>
      </div>
      ${isMine ? `
        <div class="admin-message-avatar-fallback admin">
          ${escapeHtml(avatarInitial)}
        </div>
      ` : ''}
    `;

    thread.appendChild(row);
};

  const renderInitialMessages = () => {
    renderMessages(payload.messages || []);
  };

  const renderMessages = (messages, force = false) => {
    // Render ulang penuh agar edit dan delete ikut tersinkron ke sisi admin.
    if (!force && hasActiveInlineState()) {
      return;
    }

    thread.innerHTML = '';
    messages.forEach((message) => renderMessage(message));
    closeAllMenus();
    scrollToBottom();
  };

  // Force dipakai setelah aksi sukses supaya daftar pesan tetap sinkron dengan server.
  const syncMessages = async (force = false) => {
    try {
      const response = await fetch(`${payload.messagesUrl}?sesi_id=${payload.sessionId}&jadwal_id=${payload.jadwalId ?? ''}`, {
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

      renderMessages(data.messages, force);
    } catch (error) {
      console.error(error);
    }
  };

  renderInitialMessages();

  if (form && input && sendBtn && canSendMessage) {
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
  let isSending = false;

  input.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();

      if (!isSending) {
        form.requestSubmit();
      }
    }
  });

  document.addEventListener('click', (event) => {
    if (!thread.contains(event.target)) {
      closeAllMenus();
    }
  });

  thread.addEventListener('click', async (event) => {
    const toggleButton = event.target.closest('[data-action="toggle-menu"]');
    const editButton = event.target.closest('[data-action="edit-message"]');
    const deleteButton = event.target.closest('[data-action="delete-message"]');
    const saveButton = event.target.closest('[data-action="save-edit"]');
    const cancelButton = event.target.closest('[data-action="cancel-edit"]');
    const cancelDeleteButton = event.target.closest('[data-action="cancel-delete"]');
    const confirmDeleteButton = event.target.closest('[data-action="confirm-delete"]');

    if (toggleButton) {
      const row = toggleButton.closest('.admin-message-row');
      const willOpen = !row.classList.contains('is-menu-open');
      closeAllMenus();
      row.classList.toggle('is-menu-open', willOpen);
      return;
    }

    if (editButton) {
      const messageId = Number(editButton.dataset.messageId);
      const row = editButton.closest('.admin-message-row');
      const bubbleShell = row?.querySelector('.admin-message-bubble-shell');
      const currentText = row?.dataset.messageText ?? '';

      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      row.classList.add('is-editing');
      bubbleShell.innerHTML = buildInlineEditorMarkup(currentText, messageId);
      const textarea = bubbleShell.querySelector('.admin-message-editor-input');
      if (textarea) {
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
      }
      return;
    }

    if (cancelButton) {
      const row = cancelButton.closest('.admin-message-row');
      if (row) {
        restoreMessageBubble(row);
      }
      return;
    }

    if (cancelDeleteButton) {
      const row = cancelDeleteButton.closest('.admin-message-row');
      if (row) {
        restoreMessageBubble(row);
      }
      return;
    }

    if (saveButton) {
      const messageId = Number(saveButton.dataset.messageId);
      const row = saveButton.closest('.admin-message-row');
      const textarea = row?.querySelector('.admin-message-editor-input');
      const currentText = row?.dataset.messageText ?? '';
      const pesan = textarea?.value?.trim() ?? '';

      if (!row || !textarea) {
        return;
      }

      if (!pesan) {
        hint.textContent = 'Pesan tidak boleh kosong.';
        textarea.focus();
        return;
      }

      if (pesan === currentText.trim()) {
        restoreMessageBubble(row);
        return;
      }

      try {
        const response = await fetch(messageUpdateUrl(messageId), {
          method: 'PATCH',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify({ pesan }),
        });

        const data = await response.json();
        hint.textContent = response.ok && data.success
          ? 'Pesan berhasil diedit.'
          : (data.message ?? 'Pesan gagal diedit.');

        if (response.ok && data.success) {
          syncMessages(true);
        }
      } catch (error) {
        console.error(error);
        hint.textContent = 'Terjadi kendala saat mengedit pesan.';
      }

      return;
    }

    if (deleteButton) {
      const messageId = Number(deleteButton.dataset.messageId);
      const row = deleteButton.closest('.admin-message-row');
      const bubbleShell = row?.querySelector('.admin-message-bubble-shell');
      closeAllMenus();

      if (!row || !bubbleShell) {
        return;
      }

      bubbleShell.innerHTML = buildDeleteConfirmMarkup(messageId);
      return;
    }

    if (confirmDeleteButton) {
      const messageId = Number(confirmDeleteButton.dataset.messageId);

      try {
        const response = await fetch(messageDeleteUrl(messageId), {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        const data = await response.json();
        hint.textContent = response.ok && data.success
          ? 'Pesan berhasil dihapus.'
          : (data.message ?? 'Pesan gagal dihapus.');

        if (response.ok && data.success) {
          syncMessages(true);
        }
      } catch (error) {
        console.error(error);
        hint.textContent = 'Terjadi kendala saat menghapus pesan.';
      }

      return;
    }
  });

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const pesan = input.value.trim();
    if (!pesan) {
      return;
    }

    if (isSending) {
      return;
    }

    isSending = true;
    sendBtn.disabled = true;
    hint.textContent = 'Mengirim pesan...';

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
          jadwal_id: payload.jadwalId,
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
    } catch (error) {
      console.error(error);
      hint.textContent = 'Terjadi kendala saat mengirim pesan.';
    } finally {
      isSending = false;
      sendBtn.disabled = false;
    }
  });
}
})();

document.addEventListener('DOMContentLoaded', function () {
    const menu = document.getElementById('adminSessionMenu');
    const toggle = document.getElementById('adminSessionMenuToggle');

    const openFinishBtn = document.getElementById('openFinishSessionModal');
    const closeFinishBtn = document.getElementById('closeFinishSessionModal');
    const finishModal = document.getElementById('finishSessionModal');

    if (menu && toggle) {
        toggle.addEventListener('click', function (event) {
            event.stopPropagation();
            menu.classList.toggle('is-open');
        });

        document.addEventListener('click', function (event) {
            if (!menu.contains(event.target)) {
                menu.classList.remove('is-open');
            }
        });
    }

    if (openFinishBtn && finishModal) {
        openFinishBtn.addEventListener('click', function () {
            finishModal.classList.add('is-open');

            if (menu) {
                menu.classList.remove('is-open');
            }
        });
    }

    if (closeFinishBtn && finishModal) {
        closeFinishBtn.addEventListener('click', function () {
            finishModal.classList.remove('is-open');
        });
    }

    if (finishModal) {
        finishModal.addEventListener('click', function (event) {
            if (event.target === finishModal) {
                finishModal.classList.remove('is-open');
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && finishModal) {
            finishModal.classList.remove('is-open');
        }
    });
});
</script>
@endpush
@endif