@extends('layouts.admin')

@php
    $groupList = $groupList ?? collect();
    $activeRoom = $activeRoom ?? null;
    $chatPayload = $chatPayload ?? null;
@endphp

@section('page-title', 'Grup Chat')
@section('page-hero')
<div style="display:none !important;"></div>
@endsection

@push('styles')
<style>
  body {
    overflow: hidden;
  }

  .pc-container {
    height: calc(100vh - 74px);
    overflow: hidden;
  }

  .pc-content {
    height: 100%;
    padding: 1.5rem 2rem 1rem !important;
    overflow: hidden;
  }

  .admin-page-inner {
    height: 100%;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .admin-breadcrumb {
    margin: 0 0 1.5rem 0 !important;
  }

  .admin-chat-page {
    --admin-chat-shell-height: 100%;
    --admin-chat-composer-space: 86px;
    --admin-accent: #0b5d45;
    --admin-accent-soft: #e4f1eb;
    --admin-accent-soft-2: #f3f8f5;
    --admin-accent-border: #cfe2d8;
    display: grid;
    grid-template-columns: 340px minmax(0, 1fr);
    gap: 0;
    flex: 1 1 auto;
    min-height: 0;
    height: calc(100vh - 170px);
    max-height: calc(100vh - 170px);
    background: #fff;
    border: 1px solid #dceee4;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: none;
  }

  .admin-chat-card,
  .admin-chat-list {
    background: #ffffff;
    border: none;
    border-radius: 0;
    box-shadow: none;
  }

  .admin-chat-list {
    display: flex;
    flex-direction: column;
    min-height: 0;
    overflow-y: auto;
    overflow-x: visible;
    align-self: stretch;
    border-right: 1px solid #edf7f1;
    background: #ffffff;
    overscroll-behavior: contain;
  }

  .admin-chat-list-head {
    padding: 1rem 1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background: #ffffff;
    position: sticky;
    top: 0;
    z-index: 12;
    overflow: visible;
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

  .admin-chat-filter-tabs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .55rem;
    margin-top: .8rem;
  }

  .admin-chat-filter-btn {
    min-height: 42px;
    border: 1px solid #dbece3;
    background: #ffffff;
    color: #0f172a;
    border-radius: 999px;
    padding: .5rem .9rem;
    font-size: .8rem;
    font-weight: 800;
    text-align: center;
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

  .admin-create-shell {
    margin-top: .85rem;
  }

  .admin-chat-toolbar {
    display: flex;
    align-items: stretch;
    gap: .65rem;
  }

  .admin-toolbar-create {
    display: flex;
    flex: 0 0 auto;
  }

  .admin-add-toggle {
    min-width: 54px;
    min-height: 100%;
    padding: 0 1rem;
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

  .admin-add-toggle i {
    line-height: 1;
    transition: transform .18s ease;
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

  .admin-add-toggle.is-open i {
    transform: rotate(45deg);
  }

  #adminPrivateGroupCreatePanel {
    width: 100%;
    margin-top: .75rem;
  }

  #adminPrivateGroupCreatePanel[hidden] {
    display: none !important;
  }

  .admin-private-create {
    margin-top: 0;
    padding: .9rem;
    border-radius: 18px;
    border: 1px solid #dceee4;
    background: #ffffff;
    position: relative;
    z-index: 2;
    box-shadow: none;
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

  .admin-create-mode-panel[hidden] {
    display: none !important;
  }

  .admin-field-error {
    margin-top: .45rem;
    color: #b91c1c;
    font-size: .76rem;
    font-weight: 700;
    line-height: 1.5;
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
    z-index: 20;
  }

  .admin-student-picker-create {
    z-index: 28;
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
    z-index: 35;
    max-height: min(260px, 34vh);
    border: 1px solid #dbece3;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 16px 32px rgba(15, 23, 42, .12);
    overflow-y: auto;
    overscroll-behavior: contain;
    display: none;
  }

  .admin-student-results.is-open {
    display: block;
  }

  .admin-student-picker-create .admin-student-results {
    top: calc(100% + .45rem);
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

  .admin-student-result.is-disabled {
    background: #f8fafc;
    cursor: not-allowed;
  }

  .admin-student-result.is-disabled:hover {
    background: #f8fafc;
  }

  .admin-student-result strong {
    color: #0f172a;
    font-size: .8rem;
  }

  .admin-student-result span {
    color: #64748b;
    font-size: .74rem;
  }

  .admin-student-result.is-disabled strong,
  .admin-student-result.is-disabled span {
    color: #64748b;
  }

  .admin-student-empty {
    padding: .72rem .85rem;
    color: #64748b;
    font-size: .76rem;
  }

  .admin-student-loading {
    display: flex;
    align-items: center;
    gap: .65rem;
    padding: .78rem .85rem;
    color: #64748b;
    font-size: .76rem;
  }

  .admin-student-loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid #dbece3;
    border-top-color: var(--admin-accent);
    border-radius: 999px;
    flex-shrink: 0;
    animation: admin-student-loading-spin .7s linear infinite;
  }

  @keyframes admin-student-loading-spin {
    to {
      transform: rotate(360deg);
    }
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

  .admin-chat-session-item {
    position: relative;
  }

  .admin-chat-session-item.is-hidden {
    display: none;
  }

  .admin-chat-session-item.has-room-menu .admin-chat-session {
    padding-right: 3.9rem;
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

  .admin-chat-session-status-wrap {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    flex-shrink: 0;
  }

  .admin-room-menu-shell {
    position: absolute;
    top: .92rem;
    right: .82rem;
    z-index: 5;
  }

  .admin-room-menu-toggle {
    width: 30px;
    height: 30px;
    border: 1px solid #d8eee2;
    border-radius: 999px;
    background: rgba(255, 255, 255, .96);
    color: var(--admin-accent);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    box-shadow: 0 10px 22px rgba(15, 23, 42, .08);
  }

  .admin-room-menu-toggle:hover {
    background: #f8fffb;
  }

  .admin-room-menu {
    position: absolute;
    top: calc(100% + .35rem);
    right: 0;
    min-width: 152px;
    padding: .4rem;
    border-radius: 14px;
    background: #fff;
    border: 1px solid rgba(221, 239, 231, 0.96);
    box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
    display: none;
  }

  .admin-room-menu-shell.is-open .admin-room-menu {
    display: block;
  }

  .admin-room-menu-item {
    width: 100%;
    border: none;
    background: transparent;
    border-radius: 10px;
    padding: .58rem .72rem;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    color: #b91c1c;
    font-size: .8rem;
    font-weight: 700;
    text-align: left;
  }

  .admin-room-menu-item:hover {
    background: #fff5f5;
  }

  .admin-chat-card {
    overflow: hidden;
    min-height: 0;
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #ffffff;
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
    background: var(--admin-accent);
    color: #fff;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 10px 25px rgba(6, 95, 70, .12);
    overflow: hidden;
    display: grid;
    place-items: center;
  }

  .admin-chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .admin-chat-avatar-fallback {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    font-size: 1.32rem;
    font-weight: 800;
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

  .admin-message-row.system {
    justify-content: center;
    margin: 1.15rem 0;
  }

  .admin-message-row.system .admin-message-content {
    max-width: min(100%, 560px);
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

  .admin-message-row.system .admin-message-bubble {
    padding: .62rem 1rem;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid #dceee4;
    color: #5f7285;
    text-align: center;
    font-size: .77rem;
    font-weight: 700;
    line-height: 1.45;
    box-shadow: none;
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

  /* Tray bawah dibuat sticky agar direct add tetap mudah dijangkau saat thread chat sudah panjang. */
  .admin-chat-bottom-stack {
    position: sticky;
    bottom: 0;
    z-index: 5;
    flex-shrink: 0;
    padding: .75rem 1.2rem .85rem;
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
    display: flex;
    flex-direction: column;
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
    max-height: min(620px, 78vh);
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
  }

  .admin-chat-profile-head {
    padding: 1rem 1.1rem .85rem;
    border-bottom: 1px solid #edf7f1;
    background: #f6faf8;
    position: sticky;
    top: 0;
    z-index: 3;
    flex-shrink: 0;
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
    padding: .95rem 1.1rem 1.35rem;
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    max-height: min(510px, 66vh);
  }

  .admin-profile-section {
    margin-top: 1.35rem;
    padding-top: 1.2rem;
    padding-bottom: .9rem;
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
    margin-top: .7rem;
    padding-bottom: .75rem;
  }

  .admin-member-search {
    position: relative;
    margin-bottom: .9rem;
    position: sticky;
    top: 0;
    z-index: 2;
    padding-bottom: .65rem;
    background: #fbfdfc;
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
    padding-right: .15rem;
    padding-bottom: .35rem;
  }

  .admin-member-avatar-fallback {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #b7ebc9;
    color: #065f46;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .9rem;
    font-weight: 800;
    flex-shrink: 0;
}

  /* Item anggota menampilkan foto kecil dan nama tanpa metadata tambahan. */
  .admin-member-item {
    display: flex;
    align-items: center;
    gap: .72rem;
    padding: .3rem 0;
  }

  .admin-member-item-content {
    flex: 1;
    min-width: 0;
  }

  .admin-member-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    background: #e8fff1;
    display: flex;
    align-items: center;
    justify-content: center;
}

  .admin-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .admin-member-name {
    font-size: .92rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.35;
    word-break: break-word;
  }

  .admin-member-remove-form {
    margin-left: auto;
    flex-shrink: 0;
  }

  .admin-member-remove-btn {
    border: 1px solid rgba(185, 28, 28, 0.16);
    border-radius: 999px;
    background: #fff5f5;
    color: #b91c1c;
    padding: .44rem .72rem;
    display: inline-flex;
    align-items: center;
    gap: .36rem;
    font-size: .72rem;
    font-weight: 800;
  }

  .admin-member-remove-modal-overlay {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    background: rgba(255, 248, 240, 0.72);
    backdrop-filter: none;
    z-index: 2000;
    padding: 24px;
  }

  .admin-member-remove-modal-overlay.is-open {
    display: flex;
  }

  .admin-member-remove-modal {
    width: min(310px, 100%);
    background: #0f6b53;
    border-radius: 16px;
    padding: 26px 22px 24px;
    text-align: center;
    box-shadow: 0 24px 70px rgba(15, 23, 42, 0.22);
    color: #fff;
  }

  .admin-member-remove-modal-icon {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    border: 4px solid #fde68a;
    color: #fde68a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.95rem;
    line-height: 1;
    margin-bottom: 14px;
  }

  .admin-member-remove-modal-icon.is-success {
    border-color: #bbf7d0;
    color: #bbf7d0;
  }

  .admin-member-remove-modal h3 {
    margin: 0 0 10px;
    font-size: 1.15rem;
    font-weight: 800;
    color: #fff;
  }

  .admin-member-remove-modal p {
    margin: 0;
    font-size: .78rem;
    line-height: 1.35;
    color: rgba(255, 255, 255, 0.92);
  }

  .admin-member-remove-modal-actions {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 18px;
    flex-wrap: wrap;
  }

  .admin-member-remove-modal-btn {
    min-width: 86px;
    padding: 8px 14px;
    border-radius: 8px;
    border: 1px solid transparent;
    font-weight: 700;
    font-size: .8rem;
    cursor: pointer;
    transition: transform .15s ease, background .2s ease, color .2s ease, border-color .2s ease;
  }

  .admin-member-remove-modal-btn.primary {
    background: #fde68a;
    color: #14532d;
  }

  .admin-member-remove-modal-btn.primary:hover {
    transform: translateY(-1px);
    background: #fcd34d;
  }

  .admin-member-remove-modal-btn.secondary {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.65);
    color: #fff;
  }

  .admin-member-remove-modal-btn.secondary:hover {
    transform: translateY(-1px);
    background: rgba(255, 255, 255, 0.08);
  }

  .admin-member-empty {
    display: none;
    padding: .15rem 0 .35rem;
    color: #64748b;
    font-size: .84rem;
    line-height: 1.6;
  }

  @media (max-width: 1199.98px) {
    .pc-container {
      height: calc(100vh - 74px);
    }

    .admin-chat-page {
      grid-template-columns: 1fr;
      min-height: 0;
      height: 100%;
    }

    .admin-chat-list {
      border-right: none;
      border-bottom: 1px solid #edf7f1;
      overflow-x: hidden;
    }
  }

  @media (max-width: 767.98px) {
    body {
      overflow: auto;
    }

    .pc-container,
    .pc-content,
    .admin-page-inner {
      height: auto;
      overflow: visible;
    }

    .admin-chat-page {
      --admin-chat-shell-height: calc(100vh - 92px);
      --admin-chat-composer-space: 86px;
    }

    .admin-toolbar-create {
      flex: 0 0 auto;
    }

    .admin-chat-toolbar {
      gap: .5rem;
    }

    .admin-add-toggle {
      min-width: 50px;
      padding-inline: .85rem;
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

    .admin-student-results {
      max-height: min(220px, 32vh);
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
      @php
        $createRoomMode = old('visibility', 'public');
        $showCreatePanel = old('group_form_context') === 'create_room'
            || $errors->has('topic')
            || $errors->has('title')
            || $errors->has('invite_nims');
        $activeGroupFilter = old('visibility', optional($activeRoom)?->isPrivate() ? 'private' : 'public');
      @endphp
      <div class="admin-create-shell {{ $showCreatePanel ? 'is-open' : '' }}" data-create-dropdown>
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
          <div class="admin-toolbar-create">
            <button
              type="button"
              class="admin-add-toggle {{ $showCreatePanel ? 'is-open' : '' }}"
              id="adminGroupCreateToggle"
              aria-expanded="{{ $showCreatePanel ? 'true' : 'false' }}"
              aria-controls="adminPrivateGroupCreatePanel"
              aria-label="Tambah grup"
            >
              <i class="ti ti-plus"></i>
            </button>
          </div>
        </div>

        <div class="admin-chat-filter-tabs" role="tablist" aria-label="Filter jenis grup">
          <button
            type="button"
            class="admin-chat-filter-btn {{ $activeGroupFilter === 'public' ? 'active' : '' }}"
            data-group-filter="public"
            aria-selected="{{ $activeGroupFilter === 'public' ? 'true' : 'false' }}"
          >
            Publik
          </button>
          <button
            type="button"
            class="admin-chat-filter-btn {{ $activeGroupFilter === 'private' ? 'active' : '' }}"
            data-group-filter="private"
            aria-selected="{{ $activeGroupFilter === 'private' ? 'true' : 'false' }}"
          >
            Privat
          </button>
        </div>

        <!-- Panel create dibuat inline agar konselor tetap berada di halaman group chat yang sama. -->
        <div id="adminPrivateGroupCreatePanel" {{ $showCreatePanel ? '' : 'hidden' }}>
          <form action="{{ route('admin.group-chat.rooms.store') }}" method="POST" class="admin-private-create">
            @csrf
            <input type="hidden" name="group_form_context" value="create_room">
            <input type="hidden" name="visibility" value="{{ $createRoomMode }}" id="adminCreateGroupVisibilityInput">

            <div data-create-mode-panel="public" {{ $createRoomMode === 'public' ? '' : 'hidden' }}>
              <h3>Buat Grup Publik</h3>
              <p>Grup publik ada materinya diberikan oleh konselor sesuai dengan topik masalah yang dibahas. Mahasiswa dapat bergabung untuk mengikuti diskusi, materi, dan arahan konseling yang relevan.</p>
              <input
                type="text"
                name="topic"
                class="admin-field"
                placeholder="Masukkan topik grup publik"
                value="{{ old('topic') }}"
                data-create-mode-field="public"
                {{ $createRoomMode === 'public' ? 'required' : 'disabled' }}
              >
              @error('topic')
                <div class="admin-field-error">{{ $message }}</div>
              @enderror
              <div class="admin-inline-note">Isi topik publik secara spesifik agar mahasiswa mudah memahami fokus materi dan diskusinya.</div>
            </div>

            <div data-create-mode-panel="private" {{ $createRoomMode === 'private' ? '' : 'hidden' }}>
              <h3>Buat Grup Privat</h3>
              <p>Grup privat dipakai untuk undangan terbatas. Mahasiswa yang diundang akan menerima notifikasi, lalu melihat aturan dan consent sebelum resmi bergabung.</p>
              <input
                type="text"
                name="title"
                class="admin-field"
                placeholder="Nama grup privat"
                value="{{ old('title') }}"
                data-create-mode-field="private"
                {{ $createRoomMode === 'private' ? 'required' : 'disabled' }}
              >
              @error('title')
                <div class="admin-field-error">{{ $message }}</div>
              @enderror

              <div class="admin-student-picker admin-student-picker-create" data-student-picker data-hidden-input-name="invite_nims" data-create-mode-field="private" {{ $createRoomMode === 'private' ? '' : 'data-disabled=true' }}>
                <div class="admin-student-picker-tags is-empty" data-picker-tags></div>
                <input
                  type="search"
                  class="admin-student-picker-input"
                  data-picker-input
                  placeholder="Cari mahasiswa aktif berdasarkan NIM, misalnya 11S278"
                  autocomplete="off"
                  {{ $createRoomMode === 'private' ? '' : 'disabled' }}
                >
                <div class="admin-student-results" data-picker-results></div>
                <input type="hidden" name="invite_nims" value="{{ old('invite_nims') }}" data-picker-hidden {{ $createRoomMode === 'private' ? '' : 'disabled' }}>
              </div>
              <div class="admin-inline-note">Ketik minimal 3 karakter NIM untuk melihat mahasiswa aktif yang benar-benar bisa diundang.</div>
            </div>

            <div class="admin-create-actions">
              <button type="button" class="admin-copy-link" id="adminCreateGroupCancelBtn">
                <i class="ti ti-x"></i>
                <span>Batal</span>
              </button>
              <button type="submit" class="admin-create-btn">
                <i class="ti ti-plus"></i>
                <span id="adminCreateGroupSubmitLabel">{{ $createRoomMode === 'private' ? 'Buat Grup Privat' : 'Buat Grup Publik' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    @forelse($groupList as $room)
      @php
        $isSelected = optional($activeRoom)->id === $room->id;
        $roomMemberCount = (int) ($room->active_members_count ?? $room->members_count ?? $room->members->count());
        $latestMessagePreview = optional($room->latestMessage)->pesan
            ? \Illuminate\Support\Str::limit($room->latestMessage->pesan, 56)
            : 'Belum ada pesan pada grup ini.';
        $sessionSearchText = strtolower(trim(implode(' ', [
            $room->title,
            $room->topicLabel(),
            $latestMessagePreview,
        ])));
      @endphp
      <div
        class="admin-chat-session-item {{ $room->isPrivate() ? 'has-room-menu' : '' }}"
        data-session-search="{{ $sessionSearchText }}"
        data-room-visibility="{{ $room->isPrivate() ? 'private' : 'public' }}"
      >
        <a href="{{ route('admin.group-chat', ['group' => $room->id]) }}" class="admin-chat-session {{ $isSelected ? 'active' : '' }}">
          <div class="admin-chat-session-top">
            <div class="admin-chat-session-name">{{ $room->title }}</div>
            <div class="admin-chat-session-status-wrap">
              <span class="admin-chat-session-status">{{ $room->visibilityLabel() }}</span>
            </div>
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

        @if($room->isPrivate())
          <div class="admin-room-menu-shell" data-room-menu>
            <button
              type="button"
              class="admin-room-menu-toggle"
              data-room-menu-toggle
              aria-haspopup="true"
              aria-expanded="false"
              aria-label="Opsi grup {{ $room->title }}"
            >
              <i class="ti ti-dots"></i>
            </button>
            <div class="admin-room-menu" role="menu">
              <button
                type="button"
                class="admin-room-menu-item"
                data-room-delete-trigger
                data-room-title="{{ $room->title }}"
                data-room-delete-form-id="adminDeletePrivateGroupForm{{ $room->id }}"
              >
                <i class="ti ti-trash"></i>
                <span>Hapus grup</span>
              </button>
            </div>
            <form
              id="adminDeletePrivateGroupForm{{ $room->id }}"
              action="{{ route('admin.group-chat.rooms.destroy', ['group' => $room->id]) }}"
              method="POST"
              hidden
            >
              @csrf
              @method('DELETE')
            </form>
          </div>
        @endif
      </div>
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
                @if(!empty($chatPayload['roomAvatarUrl']))
                  <img src="{{ $chatPayload['roomAvatarUrl'] }}" alt="{{ $chatPayload['roomTitle'] }}">
                @else
                  <div class="admin-chat-avatar-fallback">{{ $chatPayload['roomAvatarInitial'] }}</div>
                @endif
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
                  <h3>{{ $activeRoom->isPrivate() ? 'Anggota Grup: Mahasiswa' : 'Anggota Grup' }}</h3>
                  <p>{{ $activeRoom->isPrivate() ? 'Mahasiswa tampil dengan nama asli di grup privat.' : 'Mahasiswa tampil sebagai alias anonim bernama binatang di grup publik.' }}</p>
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
                    <div class="admin-member-item" data-member-name="konselor">
                      <div class="admin-member-avatar-fallback">K</div>
                      <div class="admin-member-name">Konselor</div>
                    </div>
                  </div>
                  <div class="admin-member-empty" id="adminGroupMemberEmpty">Anggota tidak ditemukan.</div>

                  @if(!empty($chatPayload['canUpdateAvatar']))
                    <div class="admin-profile-section">
                      <h4>Foto Grup</h4>
                      <p>Semua anggota grup dapat mengganti foto profil grup. Gunakan gambar yang mudah dikenali anggota.</p>
                      <form action="{{ $chatPayload['updateAvatarUrl'] }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="avatar" class="admin-field" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required>
                        <div class="admin-create-actions">
                          <button type="submit" class="admin-create-btn">
                            <i class="ti ti-camera"></i>
                            <span>Perbarui Foto Grup</span>
                          </button>
                        </div>
                      </form>
                    </div>
                  @endif

                  @if(!empty($chatPayload['canInviteMembers']))
                    <!-- Direct add ditempatkan di panel profil grup agar ruang chat tetap fokus untuk membaca dan mengirim pesan. -->
                    <div class="admin-profile-section">
                      <h4>Undang Mahasiswa</h4>
                      <p>Pilih mahasiswa aktif berdasarkan NIM. Undangan hanya ditampilkan untuk akun mahasiswa aktif yang memang bisa menerima invite.</p>

                      <form action="{{ route('admin.group-chat.rooms.invite', ['group' => $activeRoom->id]) }}" method="POST">
                        @csrf
                        <div class="admin-student-picker" data-student-picker data-hidden-input-name="invite_nims" data-room-id="{{ $activeRoom->id }}">
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
            </div>
          </div>
        </div>
      </div>
    @endif
  </section>

  <div class="admin-member-remove-modal-overlay" id="adminMemberRemoveModal" aria-hidden="true">
    <div class="admin-member-remove-modal" role="dialog" aria-modal="true" aria-labelledby="adminMemberRemoveModalTitle">
      <div class="admin-member-remove-modal-icon">?</div>
      <h3 id="adminMemberRemoveModalTitle">Keluarkan Anggota</h3>
      <p id="adminMemberRemoveModalText">Anggota ini akan langsung kehilangan akses ke grup chat setelah Anda melanjutkan.</p>
      <div class="admin-member-remove-modal-actions">
        <button type="button" class="admin-member-remove-modal-btn primary" id="adminMemberRemoveConfirm">
          Ya, Keluarkan
        </button>
        <button type="button" class="admin-member-remove-modal-btn secondary" id="adminMemberRemoveCancel">
          Batalkan
        </button>
      </div>
    </div>
  </div>

  <div class="admin-member-remove-modal-overlay" id="adminPrivateGroupDeleteModal" aria-hidden="true">
    <div class="admin-member-remove-modal" role="dialog" aria-modal="true" aria-labelledby="adminPrivateGroupDeleteModalTitle">
      <div class="admin-member-remove-modal-icon">?</div>
      <h3 id="adminPrivateGroupDeleteModalTitle">Hapus Grup Privat</h3>
      <p id="adminPrivateGroupDeleteModalText">Grup privat ini beserta pesan, anggota, dan undangannya akan dihapus permanen.</p>
      <div class="admin-member-remove-modal-actions">
        <button type="button" class="admin-member-remove-modal-btn primary" id="adminPrivateGroupDeleteConfirm">
          Ya, Hapus
        </button>
        <button type="button" class="admin-member-remove-modal-btn secondary" id="adminPrivateGroupDeleteCancel">
          Batalkan
        </button>
      </div>
    </div>
  </div>

  @if(session('admin_private_group_deleted_modal'))
    @php($deletedGroupModal = session('admin_private_group_deleted_modal'))
    <div class="admin-member-remove-modal-overlay is-open" id="adminPrivateGroupDeletedSuccessModal" aria-hidden="false">
      <div class="admin-member-remove-modal" role="dialog" aria-modal="true" aria-labelledby="adminPrivateGroupDeletedSuccessModalTitle">
        <div class="admin-member-remove-modal-icon is-success">
          <i class="ti ti-check"></i>
        </div>
        <h3 id="adminPrivateGroupDeletedSuccessModalTitle">{{ $deletedGroupModal['title'] ?? 'Berhasil' }}</h3>
        <p id="adminPrivateGroupDeletedSuccessModalText">{{ $deletedGroupModal['message'] ?? 'Grup privat berhasil dihapus.' }}</p>
        <div class="admin-member-remove-modal-actions">
          <button type="button" class="admin-member-remove-modal-btn primary" id="adminPrivateGroupDeletedSuccessClose">
            Tutup
          </button>
        </div>
      </div>
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
(() => {
  const searchInput = document.getElementById('adminGroupChatSearchInput');
  const searchEmpty = document.getElementById('adminGroupChatSearchEmpty');
  const sessionItems = Array.from(document.querySelectorAll('[data-session-search]'));
  const roomMenuShells = Array.from(document.querySelectorAll('[data-room-menu]'));
  const privateGroupDeleteModal = document.getElementById('adminPrivateGroupDeleteModal');
  const privateGroupDeleteText = document.getElementById('adminPrivateGroupDeleteModalText');
  const privateGroupDeleteConfirm = document.getElementById('adminPrivateGroupDeleteConfirm');
  const privateGroupDeleteCancel = document.getElementById('adminPrivateGroupDeleteCancel');
  const privateGroupDeletedSuccessModal = document.getElementById('adminPrivateGroupDeletedSuccessModal');
  const privateGroupDeletedSuccessClose = document.getElementById('adminPrivateGroupDeletedSuccessClose');
  const createDropdownShell = document.querySelector('[data-create-dropdown]');
  const createDropdownToggle = document.getElementById('adminGroupCreateToggle');
  const createDropdownPanel = document.getElementById('adminPrivateGroupCreatePanel');
  const groupFilterButtons = Array.from(document.querySelectorAll('[data-group-filter]'));
  const createModePanels = Array.from(document.querySelectorAll('[data-create-mode-panel]'));
  const createVisibilityInput = document.getElementById('adminCreateGroupVisibilityInput');
  const createSubmitLabel = document.getElementById('adminCreateGroupSubmitLabel');
  const renameToggleBtn = document.getElementById('adminRoomRenameToggle');
  const renameForm = document.getElementById('adminRoomRenameForm');
  const cancelRenameBtn = document.getElementById('adminRoomRenameCancelBtn');
  const createCancelBtn = document.getElementById('adminCreateGroupCancelBtn');
  const pickerNodes = Array.from(document.querySelectorAll('[data-student-picker]'));
  const searchStudentsUrl = @json(route('admin.group-chat.students.search'));
  let activeRoomDeleteForm = null;
  let activeGroupFilter = @json($activeGroupFilter);

  const syncCreateDropdownState = (isOpen) => {
    if (!createDropdownToggle || !createDropdownPanel || !createDropdownShell) {
      return;
    }

    createDropdownShell.classList.toggle('is-open', isOpen);
    createDropdownToggle.classList.toggle('is-open', isOpen);
    createDropdownToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    createDropdownPanel.hidden = !isOpen;
  };

  const syncCreateModeState = (mode) => {
    if (!createVisibilityInput) {
      return;
    }

    createVisibilityInput.value = mode;

    createModePanels.forEach((panel) => {
      const isActive = panel.dataset.createModePanel === mode;
      panel.hidden = !isActive;

      panel.querySelectorAll('input, textarea, select').forEach((field) => {
        const isTopicField = field.name === 'topic';
        const isPrivateTitleField = field.name === 'title';

        if (isTopicField) {
          field.disabled = !isActive;
          field.required = isActive && mode === 'public';
          return;
        }

        if (isPrivateTitleField) {
          field.disabled = !isActive;
          field.required = isActive && mode === 'private';
          return;
        }

        field.disabled = !isActive;
      });
    });

    if (createSubmitLabel) {
      createSubmitLabel.textContent = mode === 'private' ? 'Buat Grup Privat' : 'Buat Grup Publik';
    }
  };

  const syncFilterButtons = () => {
    groupFilterButtons.forEach((button) => {
      const isActive = button.dataset.groupFilter === activeGroupFilter;
      button.classList.toggle('active', isActive);
      button.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
  };

  syncFilterButtons();
  syncCreateModeState(activeGroupFilter);

  if (createDropdownPanel) {
    syncCreateDropdownState(!createDropdownPanel.hidden);
  }

  createDropdownToggle?.addEventListener('click', (event) => {
    event.preventDefault();
    event.stopPropagation();
    syncCreateModeState(activeGroupFilter);
    syncCreateDropdownState(createDropdownPanel?.hidden ?? true);
  });

  createCancelBtn?.addEventListener('click', () => {
    syncCreateDropdownState(false);
  });

  groupFilterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activeGroupFilter = button.dataset.groupFilter || 'public';
      syncFilterButtons();
      syncCreateModeState(activeGroupFilter);
      syncSearch();
    });
  });

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
    const roomId = String(root.dataset.roomId || '').trim();

    if (!tagsEl || !inputEl || !resultsEl || !hiddenEl) {
      return;
    }

    let items = [];
    let debounceTimer = null;
    let emptyResultMessage = 'Mahasiswa aktif tidak ditemukan.';
    let latestResults = [];
    let activeRequestController = null;
    let activeRequestSequence = 0;

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

    const renderLoadingState = () => {
      // Loading ringan ditampilkan di dropdown agar admin tahu pencarian NIM masih diproses.
      resultsEl.innerHTML = '';

      const loadingState = document.createElement('div');
      const spinner = document.createElement('span');
      const text = document.createElement('span');

      loadingState.className = 'admin-student-loading';
      spinner.className = 'admin-student-loading-spinner';
      spinner.setAttribute('aria-hidden', 'true');
      text.textContent = 'Sedang mencari...';

      loadingState.appendChild(spinner);
      loadingState.appendChild(text);
      resultsEl.appendChild(loadingState);
      resultsEl.classList.add('is-open');
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
        const isSelectable = item.selectable !== false;

        if (items.some((selected) => selected.nim === item.nim)) {
          return;
        }

        const resultBtn = document.createElement('button');
        const title = document.createElement('strong');
        const meta = document.createElement('span');
        const detail = [item.name || 'Mahasiswa'];

        if (!isSelectable && item.note) {
          detail.push(item.note);
        }

        resultBtn.type = 'button';
        resultBtn.className = 'admin-student-result';
        resultBtn.disabled = !isSelectable;
        resultBtn.classList.toggle('is-disabled', !isSelectable);
        title.textContent = item.nim;
        meta.textContent = detail.join(' - ');
        resultBtn.appendChild(title);
        resultBtn.appendChild(meta);

        if (isSelectable) {
          resultBtn.addEventListener('click', () => {
            items.push(item);
            renderTags();
            renderResults(latestResults);
            inputEl.focus();
          });
        }

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
      // Request lama dibatalkan agar hasil pencarian tidak tertimpa response yang sudah usang.
      const requestSequence = ++activeRequestSequence;
      activeRequestController?.abort();
      activeRequestController = new AbortController();
      renderLoadingState();

      try {
        const searchParams = new URLSearchParams({
          nim,
        });

        if (roomId !== '') {
          searchParams.set('room_id', roomId);
        }

        const response = await fetch(`${searchStudentsUrl}?${searchParams.toString()}`, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
          signal: activeRequestController.signal,
        });

        if (requestSequence !== activeRequestSequence) {
          return;
        }

        if (!response.ok) {
          emptyResultMessage = 'Pencarian mahasiswa aktif sedang tidak tersedia.';
          renderResults([]);
          return;
        }

        const data = await response.json();
        if (requestSequence !== activeRequestSequence) {
          return;
        }
        // Pesan kosong dari backend dipakai agar admin tahu apakah hasil kosong karena mapping lokal atau CIS belum siap.
        emptyResultMessage = String(data.message || 'Mahasiswa aktif tidak ditemukan.');
        renderResults(Array.isArray(data.items) ? data.items : []);
      } catch (error) {
        if (error?.name === 'AbortError') {
          return;
        }
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
        activeRequestController?.abort();
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

  const syncSearch = () => {
    const keyword = searchInput ? searchInput.value.trim().toLowerCase() : '';
    let visibleCount = 0;

    sessionItems.forEach((item) => {
      const haystack = item.dataset.sessionSearch || '';
      const roomVisibility = item.dataset.roomVisibility || 'public';
      const matchFilter = roomVisibility === activeGroupFilter;
      const matchSearch = !keyword || haystack.includes(keyword);
      const isMatch = matchFilter && matchSearch;

      item.style.display = isMatch ? '' : 'none';

      if (isMatch) {
        visibleCount += 1;
      }
    });

    if (searchEmpty) {
      searchEmpty.style.display = visibleCount === 0 ? 'block' : 'none';
    }
  };

  if (searchInput && sessionItems.length > 0) {
    searchInput.addEventListener('input', syncSearch);
    syncSearch();
  }

  const closeRoomMenus = () => {
    roomMenuShells.forEach((shell) => {
      shell.classList.remove('is-open');
      shell.querySelector('[data-room-menu-toggle]')?.setAttribute('aria-expanded', 'false');
    });
  };

  const closePrivateGroupDeleteModal = () => {
    privateGroupDeleteModal?.classList.remove('is-open');
    privateGroupDeleteModal?.setAttribute('aria-hidden', 'true');
    activeRoomDeleteForm = null;
  };

  const closePrivateGroupDeletedSuccessModal = () => {
    privateGroupDeletedSuccessModal?.classList.remove('is-open');
    privateGroupDeletedSuccessModal?.setAttribute('aria-hidden', 'true');
  };

  roomMenuShells.forEach((shell) => {
    const toggle = shell.querySelector('[data-room-menu-toggle]');
    const deleteTrigger = shell.querySelector('[data-room-delete-trigger]');

    toggle?.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();

      const willOpen = !shell.classList.contains('is-open');
      closeRoomMenus();
      shell.classList.toggle('is-open', willOpen);
      toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    });

    deleteTrigger?.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();

      const formId = deleteTrigger.dataset.roomDeleteFormId;
      activeRoomDeleteForm = formId ? document.getElementById(formId) : null;

      if (privateGroupDeleteText) {
        const roomTitle = deleteTrigger.dataset.roomTitle || 'Grup privat ini';
        privateGroupDeleteText.textContent = `${roomTitle} beserta pesan, anggota, dan undangannya akan dihapus permanen.`;
      }

      closeRoomMenus();
      privateGroupDeleteModal?.classList.add('is-open');
      privateGroupDeleteModal?.setAttribute('aria-hidden', 'false');
    });
  });

  privateGroupDeleteCancel?.addEventListener('click', closePrivateGroupDeleteModal);
  privateGroupDeleteModal?.addEventListener('click', (event) => {
    if (event.target === privateGroupDeleteModal) {
      closePrivateGroupDeleteModal();
    }
  });
  privateGroupDeleteConfirm?.addEventListener('click', () => {
    if (activeRoomDeleteForm) {
      activeRoomDeleteForm.submit();
    }
  });
  privateGroupDeletedSuccessClose?.addEventListener('click', closePrivateGroupDeletedSuccessModal);
  privateGroupDeletedSuccessModal?.addEventListener('click', (event) => {
    if (event.target === privateGroupDeletedSuccessModal) {
      closePrivateGroupDeletedSuccessModal();
    }
  });

  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-room-menu]')) {
      closeRoomMenus();
    }

    if (createDropdownShell && !event.target.closest('[data-create-dropdown]')) {
      syncCreateDropdownState(false);
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeRoomMenus();
      closePrivateGroupDeleteModal();
      closePrivateGroupDeletedSuccessModal();
      syncCreateDropdownState(false);
    }
  });

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
  const memberRemoveModal = document.getElementById('adminMemberRemoveModal');
  const memberRemoveCancel = document.getElementById('adminMemberRemoveCancel');
  const memberRemoveConfirm = document.getElementById('adminMemberRemoveConfirm');
  const memberRemoveModalText = document.getElementById('adminMemberRemoveModalText');
  const membersUrl = @json($chatPayload['membersUrl']);
  const activeRoomId = @json($chatPayload['roomId']);

  if (!stage || !toggle || !profile || !memberList) {
    return;
  }

  let activeRemoveForm = null;
  let memberProfiles = [
    {
      name: 'Konselor',
      avatar_initial: 'K',
      is_counselor: true,
    },
  ];
  let membersLoaded = false;
  let membersLoading = false;

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
    // Daftar anggota diambil saat dibutuhkan agar payload Blade awal tetap ringan.
    memberList.innerHTML = '';

   memberProfiles.forEach((member) => {
    const item = document.createElement('div');
    const avatar = document.createElement('div');
    const content = document.createElement('div');
    const label = document.createElement('div');
    const name = member?.name || 'Pengguna';

    item.className = 'admin-member-item';
    item.dataset.memberName = String(name).toLowerCase();

    if (member?.is_counselor) {
      avatar.className = 'admin-member-avatar-fallback';
      avatar.textContent = 'K';
    } else {
      avatar.className = 'admin-member-avatar';

      const image = document.createElement('img');
      image.src = member?.avatar_url || '{{ asset('img/default-avatar.png') }}';
      image.alt = name;

      avatar.appendChild(image);
    }

    content.className = 'admin-member-item-content';
    label.className = 'admin-member-name';
    label.textContent = name;

    item.appendChild(avatar);
    content.appendChild(label);
    item.appendChild(content);

    if (member?.can_remove && member?.remove_url) {
      const form = document.createElement('form');
      const csrf = document.createElement('input');
      const button = document.createElement('button');

      form.className = 'admin-member-remove-form';
      form.method = 'POST';
      form.action = member.remove_url;
      form.dataset.memberName = name;

      csrf.type = 'hidden';
      csrf.name = '_token';
      csrf.value = @json(csrf_token());

      button.type = 'button';
      button.className = 'admin-member-remove-btn';
      button.innerHTML = '<i class="ti ti-user-x"></i><span>Keluarkan</span>';
      button.addEventListener('click', () => {
        activeRemoveForm = form;
        if (memberRemoveModalText) {
          memberRemoveModalText.textContent = `${name} akan langsung kehilangan akses ke grup chat setelah Anda melanjutkan.`;
        }
        memberRemoveModal?.classList.add('is-open');
        memberRemoveModal?.setAttribute('aria-hidden', 'false');
      });

      form.appendChild(csrf);
      form.appendChild(button);
      item.appendChild(form);
    }

    memberList.appendChild(item);
  });
  };

  const fetchMembers = async (force = false) => {
    if (membersLoading || (membersLoaded && !force)) {
      return;
    }

    // Anggota room aktif diambil saat panel dibuka supaya HTML awal tidak memuat daftar penuh.
    membersLoading = true;

    try {
      const response = await fetch(`${membersUrl}?group_id=${activeRoomId}`, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      });

      if (!response.ok) {
        return;
      }

      const data = await response.json();

      if (!data.success || !Array.isArray(data.members)) {
        return;
      }

      memberProfiles = [
        {
          name: 'Konselor',
          avatar_initial: 'K',
          is_counselor: true,
        },
        ...data.members,
      ];
      membersLoaded = true;
      renderMembers();
      syncMemberSearch();
    } catch (error) {
      console.error(error);
    } finally {
      membersLoading = false;
    }
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

  const closeMemberRemoveModal = () => {
    memberRemoveModal?.classList.remove('is-open');
    memberRemoveModal?.setAttribute('aria-hidden', 'true');
    activeRemoveForm = null;
  };

  memberRemoveCancel?.addEventListener('click', closeMemberRemoveModal);
  memberRemoveModal?.addEventListener('click', (event) => {
    if (event.target === memberRemoveModal) {
      closeMemberRemoveModal();
    }
  });
  memberRemoveConfirm?.addEventListener('click', () => {
    if (activeRemoveForm) {
      activeRemoveForm.submit();
    }
  });
  memberList.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-member-remove-trigger]');
    const form = event.target.closest('[data-member-remove-form]');

    if (!trigger || !form) {
      return;
    }

    activeRemoveForm = form;

    if (memberRemoveModalText) {
      memberRemoveModalText.textContent = `${form.dataset.memberName || 'Anggota ini'} akan langsung kehilangan akses ke grup chat setelah Anda melanjutkan.`;
    }

    memberRemoveModal?.classList.add('is-open');
    memberRemoveModal?.setAttribute('aria-hidden', 'false');
  });

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
    if (!membersLoaded) {
      fetchMembers();
    }
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
  const hint = document.getElementById('adminGroupChatHint') || { textContent: '' };

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

  const renderThreadState = (text) => {
    thread.innerHTML = `<div class="admin-chat-date">${escapeHtml(text)}</div>`;
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
    ${isMine && !message.is_system ? `
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
    const isSystemMessage = Boolean(message.is_system);
    const isMine = Boolean(message.is_mine ?? (message.sender_id === currentUserId));
    const dateParts = resolveDateParts(message.sent_at);

    ensureDateSeparator(dateParts.key, dateParts.label);

    if (isSystemMessage) {
      row.className = 'admin-message-row system';
      row.dataset.messageId = String(message.id);
      row.dataset.messageText = message.text ?? '';
      row.dataset.messageEdited = '0';
      row.innerHTML = `
        <div class="admin-message-content">
          <div class="admin-message-bubble-shell">${buildMessageBubbleMarkup(message, false)}</div>
        </div>
      `;

      thread.appendChild(row);
      return;
    }

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
      // Pesan diambil terpisah dari SSR agar pembukaan room tidak membawa seluruh riwayat ke Blade.
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
      if (!thread.children.length) {
        renderThreadState('Gagal memuat pesan');
      }
    }
  };

  renderThreadState('Memuat pesan...');
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

  syncMessages(true);
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
