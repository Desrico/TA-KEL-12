@extends('layouts.admin')

@php
    $groupList = $groupList ?? collect();
    $activeRoom = $activeRoom ?? null;
    $chatPayload = $chatPayload ?? null;
@endphp

@section('page-title', 'Grup Chat')

@push('styles')
<style>
  .admin-chat-page {
    --admin-chat-shell-height: clamp(640px, calc(100vh - 142px), 900px);
    --admin-chat-composer-space: 132px;
    --admin-accent: #0b5d45;
    --admin-accent-soft: #e4f1eb;
    --admin-accent-soft-2: #f3f8f5;
    --admin-accent-border: #cfe2d8;
    display: grid;
    grid-template-columns: 340px minmax(0, 1fr);
    gap: 0;
    min-height: 0;
    height: var(--admin-chat-shell-height);
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
    background: #f7fbf8;
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

  .admin-chat-tab:hover {
    background: var(--admin-accent-soft-2);
    color: var(--admin-accent);
    border-color: rgba(16, 185, 129, .12);
  }

  .admin-chat-tab.active {
    background: #ffffff;
    color: var(--admin-accent);
    border-color: transparent;
    box-shadow: inset 0 -3px 0 var(--admin-accent);
  }

  .admin-chat-tab.active::after {
    content: "";
    position: absolute;
    left: 18%;
    right: 18%;
    bottom: 0;
    height: 3px;
    border-radius: 999px 999px 0 0;
    background: var(--admin-accent);
  }

  .admin-chat-tab-icon {
    font-size: .88rem;
    line-height: 1;
  }

  .admin-chat-search {
    position: relative;
    flex: 1;
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

  .admin-chat-toolbar {
    display: flex;
    align-items: stretch;
    gap: .65rem;
    margin-top: .85rem;
  }

  .admin-add-toggle {
    width: 46px;
    min-width: 46px;
    border: 1px solid #dbece3;
    border-radius: 16px;
    background: #ffffff;
    color: var(--admin-accent);
    font-size: 1.3rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
  }

  .admin-add-toggle:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 22px rgba(6, 78, 59, .08);
    background: #ffffff;
  }

  .admin-add-toggle.is-open {
    background: var(--admin-accent);
    color: #fff;
    border-color: transparent;
  }

  .admin-private-create {
    margin-top: .85rem;
    padding: .9rem;
    border-radius: 18px;
    border: 1px solid #dceee4;
    background: #f8fbf9;
  }

  .admin-private-create[hidden] {
    display: none !important;
  }

  .admin-private-create h3,
  .admin-invite-panel h3 {
    margin: 0 0 .3rem;
    color: #0f172a;
    font-size: .92rem;
    font-weight: 800;
  }

  .admin-private-create p,
  .admin-invite-panel p {
    margin: 0 0 .8rem;
    color: #64748b;
    font-size: .78rem;
    line-height: 1.6;
  }

  .admin-field {
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 14px;
    padding: .74rem .85rem;
    background: #fff;
    color: #0f172a;
    font-size: .84rem;
    outline: none;
  }

  .admin-field:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .admin-field + .admin-field,
  .admin-private-create .admin-inline-note,
  .admin-private-create .admin-create-actions,
  .admin-invite-panel .admin-inline-note,
  .admin-invite-panel .admin-create-actions {
    margin-top: .7rem;
  }

  .admin-inline-note {
    color: #64748b;
    font-size: .74rem;
    line-height: 1.6;
  }

  .admin-student-picker {
    position: relative;
    margin-top: .7rem;
  }

  .admin-student-picker-tags {
    display: flex;
    flex-wrap: wrap;
    gap: .45rem;
    min-height: 44px;
    padding: .55rem;
    border: 1px solid #dbece3;
    border-radius: 14px;
    background: #fff;
  }

  .admin-student-picker-tags.is-empty {
    display: none;
  }

  .admin-student-tag {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .42rem .55rem;
    border-radius: 999px;
    background: var(--admin-accent-soft);
    color: var(--admin-accent);
    font-size: .74rem;
    font-weight: 800;
    line-height: 1.2;
  }

  .admin-student-tag button {
    border: none;
    background: transparent;
    color: inherit;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .92rem;
  }

  .admin-student-picker-input {
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 14px;
    padding: .74rem .85rem;
    background: #fff;
    color: #0f172a;
    font-size: .84rem;
    outline: none;
    margin-top: .55rem;
  }

  .admin-student-picker-input:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .admin-student-results {
    position: absolute;
    left: 0;
    right: 0;
    top: calc(100% + .35rem);
    z-index: 15;
    border: 1px solid #dbece3;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 16px 32px rgba(15, 23, 42, .12);
    overflow: hidden;
    display: none;
  }

  .admin-student-results.is-open {
    display: block;
  }

  .admin-student-result {
    width: 100%;
    border: none;
    background: #fff;
    text-align: left;
    padding: .72rem .85rem;
    display: grid;
    gap: .12rem;
  }

  .admin-student-result:hover {
    background: #f8fffb;
  }

  .admin-student-result strong {
    color: #0f172a;
    font-size: .8rem;
  }

  .admin-student-result span {
    color: #64748b;
    font-size: .74rem;
  }

  .admin-student-empty {
    padding: .72rem .85rem;
    color: #64748b;
    font-size: .76rem;
  }

  .admin-create-actions {
    display: flex;
    gap: .65rem;
    flex-wrap: wrap;
  }

  .admin-create-btn,
  .admin-copy-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    border-radius: 14px;
    padding: .72rem .95rem;
    font-size: .78rem;
    font-weight: 800;
    text-decoration: none;
  }

  .admin-create-btn {
    border: none;
    color: #fff;
    background: var(--admin-accent);
    box-shadow: 0 12px 22px rgba(6, 95, 70, .18);
  }

  .admin-copy-link {
    border: 1px solid #dbece3;
    color: var(--admin-accent);
    background: #fff;
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
    background: #edf6f1;
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
    background: var(--admin-accent-soft);
    color: var(--admin-accent);
  }

  .admin-chat-card {
    overflow: hidden;
    min-height: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #f8fbf9;
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
    background: var(--admin-accent-soft);
    color: var(--admin-accent);
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

  /* Panel anggota dibuat dropdown agar ruang chat tidak menyusut ke samping. */
  .admin-chat-stage {
    position: relative;
    min-height: 0;
    height: 100%;
  }

  .admin-chat-main {
    min-width: 0;
    min-height: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
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
    position: relative;
    z-index: 6;
  }

  .admin-chat-person {
    display: flex;
    align-items: center;
    gap: .95rem;
    min-width: 0;
  }

  .admin-chat-title-row {
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .admin-chat-avatar {
    width: 56px;
    height: 56px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    background: var(--admin-accent);
    color: #fff;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 10px 25px rgba(6, 95, 70, .12);
  }

  .admin-chat-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: .14rem;
  }

  .admin-room-rename-toggle {
    border: 1px solid #d8eee2;
    border-radius: 999px;
    background: #fff;
    color: #065f46;
    padding: .36rem .68rem;
    font-size: .72rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    gap: .35rem;
  }

  .admin-room-rename-toggle:hover {
    background: #f8fffb;
  }

  .admin-room-rename-form {
    margin-top: .78rem;
    display: grid;
    gap: .65rem;
    padding: .82rem;
    border: 1px solid #dceee4;
    border-radius: 16px;
    background: #f8fbf9;
    max-width: 520px;
  }

  .admin-room-rename-form[hidden] {
    display: none !important;
  }

  .admin-room-rename-actions {
    display: flex;
    gap: .6rem;
    flex-wrap: wrap;
  }

  .admin-chat-subtitle {
    margin: 0;
    color: #64748b;
    font-size: .84rem;
    line-height: 1.6;
  }

  .admin-chat-head-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    justify-content: flex-end;
    position: relative;
    min-width: 0;
  }

  .admin-chat-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .22rem;
    width: 52px;
    height: 44px;
    border: 1px solid #d8eee2;
    border-radius: 14px;
    padding: 0;
    background: #fff;
    color: var(--admin-accent);
    font-size: 1.16rem;
    font-weight: 800;
    transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
  }

  .admin-chat-toggle:hover {
    transform: translateY(-1px);
    background: #f8fffb;
    box-shadow: 0 12px 22px rgba(6, 78, 59, .08);
  }

  .admin-chat-toggle-chevron {
    font-size: .78rem;
    transition: transform .18s ease;
  }

  .admin-chat-stage.is-profile-open .admin-chat-toggle-chevron {
    transform: rotate(180deg);
  }

  .admin-chat-toggle-text {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
  }
  .admin-chat-thread {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 1.15rem 1.3rem calc(var(--admin-chat-composer-space) + 1rem);
    overscroll-behavior: contain;
    scroll-behavior: smooth;
    background: #f8fbf9;
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
    background: var(--admin-accent);
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
    color: var(--admin-accent);
  }

  .admin-message-bubble {
    padding: .92rem 1.1rem .98rem;
    border-radius: 24px;
    font-size: .93rem;
    line-height: 1.7;
    word-break: break-word;
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
    background: var(--admin-accent);
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
    max-width: 920px;
    margin: 0 auto;
    padding: 0;
    border-top: none;
    background: transparent;
  }

  .admin-chat-form {
    display: flex;
    align-items: flex-end;
    gap: .72rem;
    padding: .65rem;
    border-radius: 22px;
    border: 1px solid #d8eee2;
    background: #ffffff;
    box-shadow: 0 14px 30px rgba(6, 78, 59, .08);
  }

  .admin-chat-input {
    flex: 1;
    border: none;
    resize: none;
    outline: none;
    background: transparent;
    min-height: 46px;
    max-height: 128px;
    font-size: .92rem;
    line-height: 1.55;
    color: #0f172a;
    padding: .45rem .25rem;
  }

  .admin-chat-send {
    width: 50px;
    height: 50px;
    border: none;
    border-radius: 16px;
    background: var(--admin-accent);
    color: #fff;
    font-size: 1.14rem;
    box-shadow: 0 14px 26px rgba(6,95,70,.2);
  }

  .admin-chat-send:disabled {
    opacity: .45;
    cursor: not-allowed;
    box-shadow: none;
  }

  .admin-chat-hint {
    max-width: 920px;
    margin: .55rem auto 0;
    color: #64748b;
    font-size: .78rem;
    padding: 0 .25rem;
  }

  /* Tray bawah dibuat sticky agar direct add tetap mudah dijangkau saat thread chat sudah panjang. */
  .admin-chat-bottom-stack {
    position: sticky;
    bottom: 0;
    z-index: 5;
    flex-shrink: 0;
    padding: .75rem 1.2rem 1rem;
    background:
      linear-gradient(180deg, rgba(248,251,249,0) 0%, rgba(255,255,255,.88) 18%, rgba(255,255,255,.98) 38%, rgba(255,255,255,.98) 100%);
    backdrop-filter: blur(12px);
  }

  /* Dropdown anggota berada di bawah ikon header dan tidak ikut mengambil kolom chat. */
  .admin-chat-profile {
    position: absolute;
    top: calc(100% + .65rem);
    right: 0;
    width: 320px;
    max-width: calc(100vw - 2rem);
    max-height: 0;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-6px);
    overflow: hidden;
    border: 1px solid #dceee4;
    border-radius: 18px;
    background: #fbfdfc;
    box-shadow: 0 18px 38px rgba(15, 23, 42, .12);
    transition: max-height .24s ease, opacity .18s ease, transform .18s ease;
    z-index: 30;
  }

  .admin-chat-stage.is-profile-open .admin-chat-profile {
    max-height: min(540px, 70vh);
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
  }

  .admin-chat-profile-head {
    padding: 1rem 1.1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background: #f6faf8;
  }

  .admin-chat-profile-head h3 {
    margin: 0 0 .28rem;
    font-size: .98rem;
    font-weight: 800;
    color: #0f172a;
  }

  .admin-chat-profile-head p {
    margin: 0;
    color: #4b7a68;
    font-size: .84rem;
    line-height: 1.6;
  }

  .admin-chat-profile-body {
    padding: .95rem 1.1rem 1.05rem;
    overflow-y: auto;
    max-height: min(430px, 58vh);
  }

  .admin-profile-section {
    margin-top: 1.35rem;
    padding-top: 1.2rem;
    padding-bottom: .35rem;
    border-top: 1px solid #edf7f1;
  }

  .admin-profile-section form {
    display: grid;
    gap: .9rem;
  }

  .admin-profile-section h4 {
    margin: 0 0 .28rem;
    color: #0f172a;
    font-size: .86rem;
    font-weight: 800;
  }

  .admin-profile-section p {
    margin: 0 0 .8rem;
    color: #64748b;
    font-size: .76rem;
    line-height: 1.6;
  }

  .admin-profile-section .admin-inline-note {
    margin-top: .15rem;
  }

  .admin-profile-section .admin-create-actions {
    margin-top: .35rem;
    padding-bottom: .25rem;
  }

  .admin-member-search {
    position: relative;
    margin-bottom: .9rem;
  }

  .admin-member-search i {
    position: absolute;
    left: .82rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: .95rem;
    pointer-events: none;
  }

  .admin-member-search input {
    width: 100%;
    border: 1px solid #dbece3;
    border-radius: 13px;
    padding: .68rem .85rem .68rem 2.35rem;
    background: #fff;
    color: #0f172a;
    font-size: .84rem;
    outline: none;
  }

  .admin-member-search input:focus {
    border-color: #8fd1b0;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .08);
  }

  .admin-member-list {
    display: grid;
    gap: .58rem;
  }

  /* Item anggota menampilkan foto kecil dan nama tanpa metadata tambahan. */
  .admin-member-item {
    display: flex;
    align-items: center;
    gap: .72rem;
    padding: .3rem 0;
  }

  .admin-member-avatar {
    width: 34px;
    height: 34px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    background: #e8fff1;
  }

  .admin-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .admin-member-name {
    font-size: .92rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
  }

  .admin-member-empty {
    display: none;
    padding: .25rem 0;
    color: #64748b;
    font-size: .84rem;
    line-height: 1.6;
  }

  @media (max-width: 1199.98px) {
    .admin-chat-page {
      grid-template-columns: 1fr;
      min-height: 0;
      height: auto;
    }

    .admin-chat-list {
      border-right: none;
      border-bottom: 1px solid #edf7f1;
    }
  }

  @media (max-width: 767.98px) {
    .admin-chat-page {
      --admin-chat-shell-height: calc(100vh - 112px);
      --admin-chat-composer-space: 124px;
    }

    .admin-chat-card {
      height: var(--admin-chat-shell-height);
    }

    .admin-chat-main {
      height: 100%;
    }

    .admin-chat-bottom-stack {
      padding: .65rem .85rem .85rem;
    }

    .admin-chat-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .admin-chat-head-actions {
      width: 100%;
      justify-content: flex-start;
    }

    .admin-chat-profile {
      left: 0;
      right: auto;
      width: 100%;
      max-width: 100%;
    }

    .admin-message-content {
      max-width: 100%;
    }

    .admin-chat-thread {
      padding-inline: 1rem;
    }
  }
</style>
@endpush

@section('konten')
<div class="admin-chat-page">
  <aside class="admin-chat-list">
    <div class="admin-chat-list-head">
      <div class="admin-chat-tabs">
        <a href="{{ route('admin.chat') }}" class="admin-chat-tab">
          <i class="ti ti-message-heart admin-chat-tab-icon"></i>
          <span>Chat Konseling</span>
        </a>
        <a href="{{ route('admin.group-chat') }}" class="admin-chat-tab active">
          <i class="ti ti-users-group admin-chat-tab-icon"></i>
          <span>Grup Chat</span>
        </a>
      </div>
      <div class="admin-chat-toolbar">
        <div class="admin-chat-search">
          <i class="ti ti-search"></i>
          <input
            type="search"
            id="adminGroupChatSearchInput"
            placeholder="Cari topik atau nama grup"
            autocomplete="off"
          >
        </div>
        <button type="button" class="admin-add-toggle" id="adminPrivateGroupToggle" aria-expanded="false" aria-controls="adminPrivateGroupCreatePanel" title="Tambah grup privat">
          <i class="ti ti-plus"></i>
        </button>
      </div>

      <!-- Panel create dibuat inline agar konselor tetap berada di halaman group chat yang sama. -->
      <form action="{{ route('admin.group-chat.rooms.store') }}" method="POST" class="admin-private-create" id="adminPrivateGroupCreatePanel" {{ old('create_private_group') ? '' : 'hidden' }}>
        @csrf
        <input type="hidden" name="create_private_group" value="1">
        <h3>Buat Grup Privat</h3>
        <p>Grup privat dipakai untuk undangan terbatas. Mahasiswa yang diundang akan menerima notifikasi, lalu melihat aturan dan consent sebelum resmi bergabung.</p>
        <input type="text" name="title" class="admin-field" placeholder="Nama grup privat" value="{{ old('title') }}" required>

        <div class="admin-student-picker" data-student-picker data-hidden-input-name="invite_nims">
          <div class="admin-student-picker-tags is-empty" data-picker-tags></div>
          <input
            type="search"
            class="admin-student-picker-input"
            data-picker-input
            placeholder="Cari mahasiswa aktif berdasarkan NIM, misalnya 11S278"
            autocomplete="off"
          >
          <div class="admin-student-results" data-picker-results></div>
          <input type="hidden" name="invite_nims" value="{{ old('invite_nims') }}" data-picker-hidden>
          <div class="admin-inline-note">Ketik minimal 3 karakter NIM untuk melihat mahasiswa aktif yang benar-benar bisa diundang.</div>
        </div>

        <div class="admin-create-actions">
          <button type="button" class="admin-copy-link" id="adminPrivateGroupCancelBtn">
            <i class="ti ti-x"></i>
            <span>Batal</span>
          </button>
          <button type="submit" class="admin-create-btn">
            <i class="ti ti-lock-plus"></i>
            <span>Buat Grup Privat</span>
          </button>
        </div>
      </form>
    </div>

    @forelse($groupList as $room)
      @php
        $isSelected = optional($activeRoom)->id === $room->id;
        $memberNames = $room->members->map(fn ($member) => $member->anonymous_name ?: 'Mahasiswa Anonim')->filter()->values();
        $roomMemberCount = (int) ($room->active_members_count ?? $room->members_count ?? $room->members->count());
        $latestMessagePreview = optional($room->latestMessage)->pesan
            ? \Illuminate\Support\Str::limit($room->latestMessage->pesan, 56)
            : 'Belum ada pesan pada grup ini.';
        $sessionSearchText = strtolower(trim(implode(' ', [
            $room->title,
            $room->topicLabel(),
            $memberNames->implode(' '),
            $latestMessagePreview,
        ])));
      @endphp
      <a href="{{ route('admin.group-chat', ['group' => $room->id]) }}" class="admin-chat-session {{ $isSelected ? 'active' : '' }}" data-session-search="{{ $sessionSearchText }}">
        <div class="admin-chat-session-top">
          <div class="admin-chat-session-name">{{ $room->title }}</div>
          <span class="admin-chat-session-status">{{ $room->visibilityLabel() }}</span>
        </div>
        <div class="admin-chat-session-meta">
          {{ $room->topicLabel() }} &bull; {{ $roomMemberCount }} anggota aktif
          @if(($room->invited_members_count ?? 0) > 0)
            &bull; {{ $room->invited_members_count }} undangan
          @endif
          <br>
          {{ $latestMessagePreview }}
        </div>
      </a>
    @empty
      <div style="padding:1.1rem 1rem;color:#64748b;font-size:.84rem;">
        Belum ada grup chat yang dibuat mahasiswa.
      </div>
    @endforelse

    @if($groupList->isNotEmpty())
      <div class="admin-chat-search-empty" id="adminGroupChatSearchEmpty">
        Tidak ada grup chat yang cocok dengan kata kunci pencarian.
      </div>
    @endif
  </aside>

  <section class="admin-chat-card">
    @if(!$activeRoom || !$chatPayload)
      <div class="admin-chat-empty">
        <div>
          <div class="admin-chat-empty-icon"><i class="ti ti-users-group"></i></div>
          <h3>Belum Ada Grup Chat Aktif</h3>
          <p>
            Saat mahasiswa membuat grup konseling berdasarkan topik, daftar grup tersebut akan muncul di panel kiri dan bisa langsung Anda buka dari sini.
          </p>
        </div>
      </div>
    @else
      <div class="admin-chat-stage" id="adminGroupChatStage">
        <div class="admin-chat-main">
          <div class="admin-chat-head">
            <div class="admin-chat-person">
              <div class="admin-chat-avatar">
                <i class="ti ti-users-group"></i>
              </div>
              <div>
                <div class="admin-chat-title-row">
                  <div class="admin-chat-title">{{ $chatPayload['roomTitle'] }}</div>
                  @if(!empty($chatPayload['canRenameRoom']))
                    <button
                      type="button"
                      class="admin-room-rename-toggle"
                      id="adminRoomRenameToggle"
                      aria-expanded="{{ old('rename_room_id') == $activeRoom->id ? 'true' : 'false' }}"
                      aria-controls="adminRoomRenameForm"
                    >
                      <i class="ti ti-pencil"></i>
                      <span>Ubah Nama</span>
                    </button>
                  @endif
                </div>
                <p class="admin-chat-subtitle">
                  {{ $chatPayload['visibilityLabel'] }} &bull; {{ $chatPayload['topicLabel'] }} &bull; {{ $chatPayload['memberCount'] }} anggota aktif
                  @if(($chatPayload['pendingInviteCount'] ?? 0) > 0)
                    &bull; {{ $chatPayload['pendingInviteCount'] }} undangan menunggu
                  @endif
                  <br>
                  {{ implode(', ', array_slice($chatPayload['memberNames'], 0, 5)) }}{{ count($chatPayload['memberNames']) > 5 ? ' dan lainnya' : '' }}
                </p>
                @if(!empty($chatPayload['canRenameRoom']))
                  <!-- Rename grup privat dibuat inline agar konselor tetap berada di room yang sama saat memperbarui judul grup. -->
                  <form
                    action="{{ route('admin.group-chat.rooms.update', ['group' => $activeRoom->id]) }}"
                    method="POST"
                    class="admin-room-rename-form"
                    id="adminRoomRenameForm"
                    {{ old('rename_room_id') == $activeRoom->id ? '' : 'hidden' }}
                  >
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="rename_room_id" value="{{ $activeRoom->id }}">
                    <input
                      type="text"
                      name="title"
                      class="admin-field"
                      value="{{ old('title', $chatPayload['roomTitle']) }}"
                      maxlength="120"
                      placeholder="Masukkan nama grup privat"
                      required
                    >
                    <div class="admin-inline-note">Gunakan nama grup yang singkat dan mudah dipahami anggota.</div>
                    <div class="admin-room-rename-actions">
                      <button type="button" class="admin-copy-link" id="adminRoomRenameCancelBtn">
                        <i class="ti ti-x"></i>
                        <span>Batal</span>
                      </button>
                      <button type="submit" class="admin-create-btn">
                        <i class="ti ti-check"></i>
                        <span>Simpan Nama</span>
                      </button>
                    </div>
                  </form>
                @endif
              </div>
            </div>
            <div class="admin-chat-head-actions">
              <button type="button" class="admin-chat-toggle" id="adminGroupMemberToggle" aria-expanded="false" aria-controls="adminGroupChatProfile" aria-label="Lihat anggota grup" title="Lihat anggota grup">
                <i class="ti ti-users"></i>
                <i class="ti ti-chevron-down admin-chat-toggle-chevron"></i>
                <span class="admin-chat-toggle-text">Lihat anggota grup</span>
              </button>
              {{-- Dropdown anggota dibuka dari ikon agar ruang chat tetap penuh. --}}
              <aside class="admin-chat-profile" id="adminGroupChatProfile">
                <div class="admin-chat-profile-head">
                  <h3>Anggota Grup</h3>
                  <p>Semua mahasiswa tampil sebagai alias anonim per grup.</p>
                </div>

                <div class="admin-chat-profile-body">
                  {{-- Search lokal membantu admin menemukan anggota tanpa menunggu request baru. --}}
                  <div class="admin-member-search">
                    <i class="ti ti-search"></i>
                    <input
                      type="search"
                      id="adminGroupMemberSearchInput"
                      placeholder="Cari anggota..."
                      autocomplete="off"
                    >
                  </div>
                  <div class="admin-member-list" id="adminGroupMemberList">
                    @foreach($chatPayload['memberProfiles'] as $memberProfile)
                      <div class="admin-member-item" data-member-name="{{ \Illuminate\Support\Str::lower($memberProfile['name']) }}">
                        <div class="admin-member-avatar">
                          <img src="{{ $memberProfile['avatar_url'] }}" alt="{{ $memberProfile['name'] }}">
                        </div>
                        <div class="admin-member-name">{{ $memberProfile['name'] }}</div>
                      </div>
                    @endforeach
                  </div>
                  <div class="admin-member-empty" id="adminGroupMemberEmpty">Anggota tidak ditemukan.</div>

                  @if(!empty($chatPayload['canInviteMembers']))
                    <!-- Direct add ditempatkan di panel profil grup agar ruang chat tetap fokus untuk membaca dan mengirim pesan. -->
                    <div class="admin-profile-section">
                      <h4>Undang Mahasiswa</h4>
                      <p>Pilih mahasiswa aktif berdasarkan NIM. Undangan hanya ditampilkan untuk akun mahasiswa aktif yang memang bisa menerima invite.</p>

                      <form action="{{ route('admin.group-chat.rooms.invite', ['group' => $activeRoom->id]) }}" method="POST">
                        @csrf
                        <div class="admin-student-picker" data-student-picker data-hidden-input-name="invite_nims">
                          <div class="admin-student-picker-tags is-empty" data-picker-tags></div>
                          <input
                            type="search"
                            class="admin-student-picker-input"
                            data-picker-input
                            placeholder="Cari mahasiswa aktif berdasarkan NIM, misalnya 11S278"
                            autocomplete="off"
                          >
                          <div class="admin-student-results" data-picker-results></div>
                          <input type="hidden" name="invite_nims" value="" data-picker-hidden>
                        </div>
                        <div class="admin-create-actions">
                          <button type="submit" class="admin-create-btn">
                            <i class="ti ti-user-plus"></i>
                            <span>Kirim Undangan</span>
                          </button>
                        </div>
                      </form>
                    </div>
                  @endif
                </div>
              </aside>
            </div>
          </div>

          <div class="admin-chat-thread" id="adminGroupChatThread"></div>

          <!-- Tray bawah dibuat tetap terlihat agar kolom pesan selalu mudah dijangkau saat thread chat sudah panjang. -->
          <div class="admin-chat-bottom-stack">
            <div class="admin-chat-compose">
              <form id="adminGroupChatForm" class="admin-chat-form">
                <textarea
                  id="adminGroupChatInput"
                  class="admin-chat-input"
                  rows="1"
                  maxlength="2000"
                  placeholder="Tulis pesan untuk grup konseling ini..."
                ></textarea>
                <button type="submit" class="admin-chat-send" id="adminGroupChatSendBtn">
                  <i class="ti ti-send"></i>
                </button>
              </form>
              <div class="admin-chat-hint" id="adminGroupChatHint">
                Pesan akan langsung tampil untuk seluruh anggota grup aktif dan konselor lain yang membuka ruang ini. Nama mahasiswa tetap tampil sebagai alias anonim.
              </div>
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
  const searchInput = document.getElementById('adminGroupChatSearchInput');
  const searchEmpty = document.getElementById('adminGroupChatSearchEmpty');
  const sessionItems = Array.from(document.querySelectorAll('[data-session-search]'));
  const addToggleBtn = document.getElementById('adminPrivateGroupToggle');
  const createPanel = document.getElementById('adminPrivateGroupCreatePanel');
  const cancelCreateBtn = document.getElementById('adminPrivateGroupCancelBtn');
  const renameToggleBtn = document.getElementById('adminRoomRenameToggle');
  const renameForm = document.getElementById('adminRoomRenameForm');
  const cancelRenameBtn = document.getElementById('adminRoomRenameCancelBtn');
  const pickerNodes = Array.from(document.querySelectorAll('[data-student-picker]'));
  const searchStudentsUrl = @json(route('admin.group-chat.students.search'));

  // Toggle panel create tetap dibuat inline agar admin tidak perlu pindah halaman.
  const syncCreatePanelState = (isOpen) => {
    if (!addToggleBtn || !createPanel) {
      return;
    }

    createPanel.hidden = !isOpen;
    addToggleBtn.classList.toggle('is-open', isOpen);
    addToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  };

  addToggleBtn?.addEventListener('click', () => {
    syncCreatePanelState(createPanel?.hidden ?? true);
  });

  cancelCreateBtn?.addEventListener('click', () => {
    syncCreatePanelState(false);
  });

  if (createPanel && !createPanel.hidden && addToggleBtn) {
    syncCreatePanelState(true);
  }

  // Rename grup privat dibuka inline agar konselor tidak keluar dari room aktif saat mengganti judul grup.
  const syncRenameFormState = (isOpen) => {
    if (!renameToggleBtn || !renameForm) {
      return;
    }

    renameForm.hidden = !isOpen;
    renameToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  };

  renameToggleBtn?.addEventListener('click', () => {
    syncRenameFormState(renameForm?.hidden ?? true);
  });

  cancelRenameBtn?.addEventListener('click', () => {
    syncRenameFormState(false);
  });

  if (renameForm && !renameForm.hidden && renameToggleBtn) {
    syncRenameFormState(true);
  }

  // Autocomplete direct add menerima NIM alfanumerik agar pola kampus seperti 11S278 tetap bisa dicari.
  const initStudentPicker = (root) => {
    const tagsEl = root.querySelector('[data-picker-tags]');
    const inputEl = root.querySelector('[data-picker-input]');
    const resultsEl = root.querySelector('[data-picker-results]');
    const hiddenEl = root.querySelector('[data-picker-hidden]');

    if (!tagsEl || !inputEl || !resultsEl || !hiddenEl) {
      return;
    }

    let items = [];
    let debounceTimer = null;
    let emptyResultMessage = 'Mahasiswa aktif tidak ditemukan.';
    let latestResults = [];

    const eventCameFrom = (event, element) => {
      if (!event || !element) {
        return false;
      }

      if (typeof event.composedPath === 'function') {
        return event.composedPath().includes(element);
      }

      return element.contains(event.target);
    };

    const syncHiddenValue = () => {
      hiddenEl.value = items.map((item) => item.nim).join(',');
    };

    const renderTags = () => {
      tagsEl.innerHTML = '';
      tagsEl.classList.toggle('is-empty', items.length === 0);

      if (items.length === 0) {
        syncHiddenValue();
        return;
      }

      items.forEach((item) => {
        const tag = document.createElement('span');
        const removeBtn = document.createElement('button');
        tag.className = 'admin-student-tag';
        tag.textContent = item.label || item.nim;
        removeBtn.type = 'button';
        removeBtn.setAttribute('aria-label', `Hapus ${item.nim}`);
        removeBtn.innerHTML = '&times;';
        removeBtn.addEventListener('click', () => {
          items = items.filter((current) => current.nim !== item.nim);
          renderTags();
          if (resultsEl.classList.contains('is-open')) {
            renderResults(latestResults);
          }
          inputEl.focus();
        });
        tag.appendChild(removeBtn);
        tagsEl.appendChild(tag);
      });

      syncHiddenValue();
    };

    const closeResults = () => {
      resultsEl.innerHTML = '';
      resultsEl.classList.remove('is-open');
    };

    const renderResults = (results) => {
      latestResults = Array.isArray(results) ? results : [];
      resultsEl.innerHTML = '';

      if (!latestResults.length) {
        const emptyState = document.createElement('div');
        emptyState.className = 'admin-student-empty';
        emptyState.textContent = emptyResultMessage;
        resultsEl.appendChild(emptyState);
        resultsEl.classList.add('is-open');
        return;
      }

      latestResults.forEach((item) => {
        if (items.some((selected) => selected.nim === item.nim)) {
          return;
        }

        const resultBtn = document.createElement('button');
        const title = document.createElement('strong');
        const meta = document.createElement('span');

        resultBtn.type = 'button';
        resultBtn.className = 'admin-student-result';
        title.textContent = item.nim;
        meta.textContent = item.name || 'Mahasiswa';
        resultBtn.appendChild(title);
        resultBtn.appendChild(meta);
        resultBtn.addEventListener('click', () => {
          items.push(item);
          renderTags();
          renderResults(latestResults);
          inputEl.focus();
        });

        resultsEl.appendChild(resultBtn);
      });

      if (resultsEl.children.length === 0) {
        const emptyState = document.createElement('div');
        emptyState.className = 'admin-student-empty';
        emptyState.textContent = 'Semua hasil sudah dipilih.';
        resultsEl.appendChild(emptyState);
      }

      resultsEl.classList.add('is-open');
    };

    const normalizeKeyword = (value) => String(value || '').replace(/\s+/g, '').trim();

    const fetchStudents = async (nim) => {
      try {
        const response = await fetch(`${searchStudentsUrl}?nim=${encodeURIComponent(nim)}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (!response.ok) {
          emptyResultMessage = 'Pencarian mahasiswa aktif sedang tidak tersedia.';
          closeResults();
          return;
        }

        const data = await response.json();
        // Pesan kosong dari backend dipakai agar admin tahu apakah hasil kosong karena mapping lokal atau CIS belum siap.
        emptyResultMessage = String(data.message || 'Mahasiswa aktif tidak ditemukan.');
        renderResults(Array.isArray(data.items) ? data.items : []);
      } catch (error) {
        console.error(error);
        emptyResultMessage = 'Pencarian mahasiswa aktif sedang tidak tersedia.';
        renderResults([]);
      }
    };

    const hydrateInitialTags = () => {
      const initialNims = String(hiddenEl.value || '')
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);

      if (!initialNims.length) {
        renderTags();
        return;
      }

      items = initialNims.map((nim) => ({
        nim,
        name: '',
        label: nim,
      }));

      renderTags();
    };

    inputEl.addEventListener('input', () => {
      const nim = normalizeKeyword(inputEl.value);

      if (inputEl.value !== nim) {
        inputEl.value = nim;
      }

      window.clearTimeout(debounceTimer);

      if (nim.length < 3) {
        closeResults();
        return;
      }

      debounceTimer = window.setTimeout(() => {
        fetchStudents(nim);
      }, 350);
    });

    document.addEventListener('click', (event) => {
      if (!eventCameFrom(event, root)) {
        closeResults();
      }
    });

    hydrateInitialTags();
  };

  if (!searchInput || sessionItems.length === 0) {
    pickerNodes.forEach(initStudentPicker);
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

  pickerNodes.forEach(initStudentPicker);
})();
</script>
@endpush

@if($activeRoom && $chatPayload)
@push('scripts')
<script>
(() => {
  const stage = document.getElementById('adminGroupChatStage');
  const toggle = document.getElementById('adminGroupMemberToggle');
  const profile = document.getElementById('adminGroupChatProfile');
  const memberSearch = document.getElementById('adminGroupMemberSearchInput');
  const memberList = document.getElementById('adminGroupMemberList');
  const memberEmpty = document.getElementById('adminGroupMemberEmpty');
  const memberProfiles = @json($chatPayload['memberProfiles']);

  if (!stage || !toggle || !profile || !memberList) {
    return;
  }

  const eventCameFrom = (event, element) => {
    if (!event || !element) {
      return false;
    }

    if (typeof event.composedPath === 'function') {
      return event.composedPath().includes(element);
    }

    return element.contains(event.target);
  };

  const renderMembers = () => {
    // Daftar anggota dirender dari payload yang sama dengan header agar tidak kosong saat relasi Blade belum stabil.
    memberList.innerHTML = '';

    memberProfiles.forEach((member) => {
      const item = document.createElement('div');
      const avatar = document.createElement('div');
      const image = document.createElement('img');
      const label = document.createElement('div');
      const name = member?.name || 'Pengguna';
      item.className = 'admin-member-item';
      item.dataset.memberName = String(name).toLowerCase();
      avatar.className = 'admin-member-avatar';
      image.src = member?.avatar_url || '{{ asset('img/default-avatar.png') }}';
      image.alt = name;
      label.className = 'admin-member-name';
      label.textContent = name;
      avatar.appendChild(image);
      item.appendChild(avatar);
      item.appendChild(label);
      memberList.appendChild(item);
    });
  };

  const syncMemberSearch = () => {
    const keyword = (memberSearch?.value || '').trim().toLowerCase();
    const items = Array.from(memberList.querySelectorAll('.admin-member-item'));
    let visibleCount = 0;

    items.forEach((item) => {
      const isMatch = !keyword || (item.dataset.memberName || '').includes(keyword);
      item.style.display = isMatch ? '' : 'none';

      if (isMatch) {
        visibleCount += 1;
      }
    });

    if (memberEmpty) {
      memberEmpty.textContent = keyword ? 'Anggota tidak ditemukan.' : 'Belum ada anggota dalam grup ini.';
      memberEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  const syncDropdownState = (isOpen) => {
    // Sinkronkan label aksesibilitas saat dropdown anggota dibuka atau ditutup.
    const label = isOpen ? 'Sembunyikan anggota grup' : 'Lihat anggota grup';
    stage.classList.toggle('is-profile-open', isOpen);
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    toggle.setAttribute('aria-label', label);
    toggle.setAttribute('title', label);
    toggle.querySelector('.admin-chat-toggle-text').textContent = label;
  };

  toggle.addEventListener('click', (event) => {
    event.stopPropagation();
    renderMembers();
    syncMemberSearch();
    syncDropdownState(!stage.classList.contains('is-profile-open'));
  });

  memberSearch?.addEventListener('input', syncMemberSearch);

  document.addEventListener('click', (event) => {
    if (!stage.classList.contains('is-profile-open')) {
      return;
    }

    if (eventCameFrom(event, toggle) || eventCameFrom(event, profile)) {
      return;
    }

    syncDropdownState(false);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      syncDropdownState(false);
    }
  });
})();
</script>
@endpush

@push('scripts')
<script>
(() => {
  const payload = @json($chatPayload);
  const currentUserId = {{ auth()->id() }};
  const thread = document.getElementById('adminGroupChatThread');
  const form = document.getElementById('adminGroupChatForm');
  const input = document.getElementById('adminGroupChatInput');
  const sendBtn = document.getElementById('adminGroupChatSendBtn');
  const hint = document.getElementById('adminGroupChatHint');

  if (!thread || !form || !input || !sendBtn) {
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
    input.style.height = `${Math.min(input.scrollHeight, 128)}px`;
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
  const messageUpdateUrl = (messageId) => payload.updateUrlTemplate.replace('__MESSAGE_ID__', String(messageId));
  const messageDeleteUrl = (messageId) => payload.deleteUrlTemplate.replace('__MESSAGE_ID__', String(messageId));

  const closeAllMenus = () => {
    thread.querySelectorAll('.admin-message-row.is-menu-open').forEach((element) => {
      element.classList.remove('is-menu-open');
    });
  };

  // Bubble dan editor inline dipisah agar edit tetap terasa menyatu di percakapan grup.
  const buildMessageBubbleMarkup = (message, isMine) => `
    <div class="admin-message-bubble">${escapeHtml(message.text).replace(/\n/g, '<br>')}</div>
    ${isMine ? `
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

  // Sinkronisasi grup admin ditahan saat ada state inline aktif agar isi tidak kembali sendiri.
  const hasActiveInlineState = () => Boolean(
    thread.querySelector('.admin-message-row.is-editing, [data-delete-message-id]')
  );

  // Bubble asli dikembalikan jika admin batal edit atau batal hapus pesan grup.
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

  const renderMessage = (message) => {
    const row = document.createElement('div');
    const isMine = Boolean(message.is_mine ?? (message.sender_id === currentUserId));
    const dateParts = resolveDateParts(message.sent_at);

    ensureDateSeparator(dateParts.key, dateParts.label);

    row.className = `admin-message-row ${isMine ? 'mine' : 'other'}`;
    row.dataset.messageId = message.id;
    row.dataset.messageText = message.text ?? '';
    row.dataset.messageEdited = message.is_edited ? '1' : '0';

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
          ${message.is_edited ? '<span class="admin-message-edited">telah diedit</span>' : ''}
        </div>
        <div class="admin-message-bubble-shell">${buildMessageBubbleMarkup(message, isMine)}</div>
      </div>
      ${isMine ? `
        <div class="admin-message-avatar">
          <img src="${escapeHtml(message.avatar_url)}" alt="${escapeHtml(message.sender_name)}">
        </div>
      ` : ''}
    `;

    thread.appendChild(row);
  };

  const renderMessages = (messages, force = false) => {
    // Render ulang penuh agar edit dan delete cepat sinkron di ruang grup admin.
    if (!force && hasActiveInlineState()) {
      return;
    }

    thread.innerHTML = '';
    messages.forEach((message) => renderMessage(message));
    closeAllMenus();
    scrollToBottom();
  };

  // Force dipakai setelah aksi sukses agar daftar pesan grup langsung diperbarui dari server.
  const syncMessages = async (force = false) => {
    try {
      const response = await fetch(`${payload.messagesUrl}?group_id=${payload.roomId}`, {
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

  renderMessages(payload.messages || []);
  autoResize();

  if (window.Echo) {
    window.Echo.private(payload.channel)
      .listen('.group.chat.message.sent', (event) => {
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

    sendBtn.disabled = true;
    hint.textContent = 'Mengirim pesan ke grup...';

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
          group_id: payload.roomId,
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
      hint.textContent = 'Pesan terkirim ke seluruh anggota grup.';
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
