@extends('layouts.master')

@push('styles')
<style>
  body.group-chat-page-active {
    overflow-x: hidden;
    overflow-y: auto;
  }

  body.group-chat-page-active footer {
    display: none !important;
  }

  .group-lobby-page {
    height: auto;
    min-height: calc(100dvh - 96px);
    background:
      radial-gradient(circle at top left, rgba(16, 185, 129, 0.16), transparent 24%),
      radial-gradient(circle at top right, rgba(253, 230, 138, 0.16), transparent 22%),
      linear-gradient(180deg, #effcf5 0%, #f8fffb 24%, #ffffff 58%);
    padding: .75rem 0;
    overflow: visible;
  }

  .group-lobby-page > .container {
    width: 100%;
    max-width: none;
    height: auto;
    padding-inline: .75rem;
    display: flex;
    flex-direction: column;
  }

  .group-type-guide {
    width: 100%;
    margin: 0 0 .85rem;
    padding: 1rem 1.15rem;
    border: 1px solid #D9EEE4;
    border-radius: 22px;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, .12), transparent 30%),
      linear-gradient(135deg, #FFFFFF 0%, #F5FFF9 100%);
    box-shadow: 0 12px 32px rgba(6, 78, 59, .07);
  }

  .group-type-guide-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .8rem;
  }

  .group-type-guide-kicker {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    margin-bottom: .25rem;
    color: #07835F;
    font-size: .65rem;
    font-weight: 900;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  .group-type-guide-title {
    margin: 0;
    color: #064E3B;
    font-family: inherit;
    font-size: clamp(1.05rem, 1.4vw, 1.25rem);
    font-weight: 800;
    line-height: 1.25;
    letter-spacing: -.02em;
  }

  .group-type-guide-description {
    max-width: 920px;
    margin: .3rem 0 0;
    color: #64748B;
    font-family: inherit;
    font-size: .78rem;
    font-weight: 400;
    line-height: 1.55;
  }

  .group-type-guide-intro {
    max-width: 580px;
    margin: .2rem 0 0;
    color: #64748B;
    font-size: .76rem;
    line-height: 1.5;
    text-align: right;
  }

  .group-type-guide-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .7rem;
  }

  .group-type-guide-item {
    display: block;
    min-width: 0;
    padding: .75rem .85rem;
    border: 1px solid #E3EFE9;
    border-radius: 16px;
    background: rgba(255, 255, 255, .88);
  }

  .group-type-guide-item.is-private {
    border-color: #E4DDFC;
    background: #FCFAFF;
  }

  .group-type-guide-name {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: .4rem;
    margin: 0 0 .18rem;
    color: #0F172A;
    font-size: .84rem;
    font-weight: 900;
  }

  .group-type-guide-badge {
    padding: .2rem .45rem;
    border-radius: 999px;
    background: #DCFCE7;
    color: #047857;
    font-size: .58rem;
    font-weight: 900;
  }

  .group-type-guide-item.is-private .group-type-guide-badge {
    background: #EEE9FF;
    color: #6034C7;
  }

  .group-type-guide-copy {
    margin: 0;
    color: #64748B;
    font-size: .7rem;
    line-height: 1.45;
  }

  .group-lobby-hero {
    max-width: 1480px;
    margin: 0 auto 1.25rem;
    padding: 1.5rem 1.6rem;
    border: 1px solid rgba(209, 250, 229, 0.92);
    border-radius: 30px;
    background:
      radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 26%),
      linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 255, 251, 0.96));
    box-shadow: 0 18px 50px rgba(6, 78, 59, 0.08);
  }

  .group-lobby-kicker {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    margin-bottom: .9rem;
    padding: .42rem .78rem;
    border-radius: 999px;
    background: #e8fff1;
    color: #047857;
    font-size: .72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .group-lobby-hero h1 {
    margin: 0 0 .45rem;
    font-size: clamp(1.9rem, 3vw, 2.6rem);
    font-weight: 800;
    color: #064e3b;
  }

  .group-lobby-hero p {
    max-width: 760px;
    margin: 0;
    color: #475569;
    line-height: 1.8;
  }

  .anonim-toggle-card {
    max-width: 420px;
    margin-top: 1rem;
    border: 1px solid #D8E7E0;
    border-radius: 20px;
    background: linear-gradient(180deg, #FCFFFD, #F6FBF8);
    padding: 1rem 1.05rem;
  }

  .anonim-toggle-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
  }

  .anonim-toggle-copy {
    flex: 1;
    min-width: 0;
  }

  .anonim-toggle-title {
    margin: 0;
    color: #173428;
    font-size: .95rem;
    font-weight: 800;
  }

  .anonim-toggle-status {
    margin-top: .2rem;
    color: #047857;
    font-size: .77rem;
    font-weight: 700;
  }

  .anonim-toggle-status.is-error {
    color: #B42318;
  }

  .anonim-toggle-switch {
    position: relative;
    width: 52px;
    height: 30px;
    flex-shrink: 0;
  }

  .anonim-toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .anonim-toggle-slider {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #D4DAD7;
    cursor: pointer;
    transition: background .2s ease;
  }

  .anonim-toggle-slider::before {
    content: '';
    position: absolute;
    left: 3px;
    top: 3px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 4px 14px rgba(15, 23, 42, .14);
    transition: transform .2s ease;
  }

  .anonim-toggle-switch input:checked + .anonim-toggle-slider {
    background: #0A523A;
  }

  .anonim-toggle-switch input:checked + .anonim-toggle-slider::before {
    transform: translateX(22px);
  }

  .anonim-toggle-switch input:disabled + .anonim-toggle-slider {
    cursor: wait;
    opacity: .72;
  }

  .group-lobby-grid {
    max-width: none;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr;
    align-items: start;
    gap: 1.25rem;
  }

  .group-lobby-workspace {
    width: 100%;
    max-width: none;
    margin: 0;
    display: grid;
    grid-template-columns: clamp(340px, 23vw, 400px) minmax(0, 1fr);
    gap: 0;
    align-items: start;
    height: calc(100dvh - 120px);
    min-height: 620px;
    flex: 0 0 auto;
  }

  .group-lobby-sidebar {
    width: 100%;
    min-width: 0;
    height: 100%;
    overflow: hidden;
    border: 1px solid #dcece4;
    border-right: 0;
    border-radius: 26px 0 0 26px;
    background: #fff;
    display: flex;
    flex-direction: column;
  }

  .group-lobby-workspace .group-lobby-toolbar {
    margin-bottom: 0;
    flex: 0 0 auto;
  }

  .group-lobby-workspace .group-lobby-grid {
    width: 100%;
    min-height: 0;
    max-height: none;
    margin-inline: 0;
    flex: 1 1 auto;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #9ab8aa transparent;
  }

  .group-lobby-workspace .group-card {
    width: 100%;
    min-width: 0;
    border: 0;
    border-top: 1px solid #edf3f0;
    border-radius: 0;
    box-shadow: none;
  }

  .group-lobby-workspace .group-card-head,
  .group-lobby-workspace .group-my-footer {
    flex-direction: column;
    align-items: flex-start;
  }

  .group-lobby-workspace .group-card-head-actions,
  .group-lobby-workspace .group-card-cta-secondary {
    width: 100%;
  }

  .group-lobby-workspace .group-card-cta-secondary {
    justify-content: center;
  }

  .group-lobby-workspace .group-card-head {
    padding: .9rem 1rem .45rem;
  }

  .group-lobby-workspace .group-card-body {
    width: 100%;
    padding: .45rem 0 0;
  }

  .group-lobby-workspace .group-card-copy {
    display: none;
  }

  .group-lobby-workspace .group-card-badge {
    padding: .3rem .6rem;
  }

  .group-lobby-workspace .group-my-list {
    width: 100%;
    gap: 0;
  }

  .group-lobby-workspace .group-my-item {
    border: 0;
    border-top: 1px solid #edf3f0;
    border-radius: 0;
    padding: 1rem;
    background: #fff;
  }

  .group-lobby-workspace .group-my-item:hover,
  .group-lobby-workspace .group-my-item.is-active-room {
    transform: none;
    border-color: #edf3f0;
    background: #eaf7f1;
    box-shadow: none;
  }

  .group-lobby-workspace .group-my-item-top {
    margin-bottom: .35rem;
  }

  .group-lobby-workspace .group-my-meta {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
  }

  .group-lobby-workspace .group-my-footer {
    margin-top: .65rem;
  }

  .group-lobby-workspace .group-card-cta-secondary {
    display: none;
  }

  .group-lobby-chat-panel {
    min-width: 0;
    height: 100%;
    overflow: hidden;
    border: 1px solid rgba(209, 250, 229, .92);
    border-radius: 0 26px 26px 0;
    background: #fff;
    box-shadow: 0 18px 50px rgba(6, 78, 59, .08);
  }

  .group-mobile-chat-bar {
    display: none;
  }

  .group-lobby-chat-frame {
    display: block;
    width: 100%;
    height: 100%;
    border: 0;
    background: #fff;
  }

  .group-lobby-chat-empty {
    height: 100%;
    display: grid;
    place-items: center;
    padding: 2rem;
    text-align: center;
    color: #64748b;
  }

  .group-lobby-chat-empty i {
    display: block;
    margin-bottom: .8rem;
    color: #059669;
    font-size: 2rem;
  }

  .group-my-item.is-active-room {
    border-color: rgba(16, 185, 129, .5);
    background: #effaf4;
    box-shadow: 0 12px 24px rgba(6, 78, 59, .08);
  }

  .group-lobby-toolbar {
    max-width: none;
    margin: 0;
    padding: 1rem;
    border: 0;
    border-radius: 0;
    background: #fff;
    box-shadow: none;
  }

  .group-lobby-toolbar-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: .85rem;
  }

  .group-lobby-toolbar-title {
    margin: 0;
    color: #0f172a;
    font-size: 1.3rem;
    font-weight: 850;
  }

  .group-lobby-search-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: .7rem;
  }

  .group-lobby-search {
    position: relative;
  }

  .group-lobby-search i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
  }

  .group-lobby-search input {
    width: 100%;
    height: 52px;
    border: 1px solid #d7e9df;
    border-radius: 17px;
    padding: 0 1rem 0 2.75rem;
    color: #0f172a;
    background: #fff;
    outline: none;
  }

  .group-lobby-search input:focus {
    border-color: #34d399;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .10);
  }

  .group-join-quick {
    width: auto;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    border-radius: 14px;
    padding: 0 .9rem;
    border: 1px solid #bbf7d0;
    background: #ecfdf5;
    color: #047857;
    font-size: .82rem;
    font-weight: 800;
    text-decoration: none;
    transition: .18s ease;
  }

  .group-join-quick:hover {
    color: #fff;
    background: #047857;
  }

  .group-join-quick.is-hidden {
    display: none;
  }

  .group-public-join-panel {
    margin: 0 1rem 1rem;
    padding: 1rem;
    border: 1px solid #cfe9db;
    border-radius: 20px;
    background: #f8fcfa;
  }

  .group-public-join-panel[hidden] {
    display: none;
  }

  .group-public-join-panel h2 {
    margin: 0 0 .3rem;
    color: #0f172a;
    font-size: 1rem;
    font-weight: 850;
  }

  .group-public-join-panel p {
    margin: 0 0 .9rem;
    color: #64748b;
    font-size: .8rem;
    line-height: 1.6;
  }

  .group-public-topic-select {
    width: 100%;
    min-height: 50px;
    border: 1px solid #d5e8de;
    border-radius: 15px;
    padding: 0 .85rem;
    color: #0f172a;
    background: #fff;
    outline: none;
  }

  .group-public-topic-select:focus {
    border-color: #34d399;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, .1);
  }

  .group-public-join-actions {
    display: flex;
    gap: .65rem;
    margin-top: .85rem;
  }

  .group-public-join-submit,
  .group-public-join-cancel {
    min-height: 44px;
    border-radius: 14px;
    padding: 0 .9rem;
    font-size: .8rem;
    font-weight: 800;
  }

  .group-public-join-submit {
    border: 0;
    color: #fff;
    background: #076b4f;
  }

  .group-public-join-cancel {
    border: 1px solid #d5e8de;
    color: #065f46;
    background: #fff;
  }

  .group-public-join-error {
    margin-top: .55rem;
    color: #b42318;
    font-size: .75rem;
    font-weight: 700;
  }

  .group-lobby-tabs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: .6rem;
    margin-top: .75rem;
  }

  .group-lobby-tab {
    min-height: 46px;
    border: 1px solid #d7e9df;
    border-radius: 16px;
    background: #fff;
    color: #0f172a;
    font-weight: 800;
  }

  .group-lobby-tab.active {
    border-color: #076b4f;
    color: #fff;
    background: #076b4f;
    box-shadow: 0 10px 22px rgba(6, 95, 70, .16);
  }

  .group-card.is-section-hidden {
    display: none;
  }

  .group-card.is-empty-section {
    display: none;
  }

  .group-search-empty {
    border: 1px dashed #cfe9db;
    border-radius: 20px;
    padding: 1.15rem;
    color: #64748b;
    background: #f8fffb;
  }

  .group-card {
    border-radius: 28px;
    border: 1px solid rgba(209, 250, 229, 0.92);
    background: rgba(255, 255, 255, 0.94);
    box-shadow: 0 18px 50px rgba(6, 78, 59, 0.08);
    overflow: hidden;
  }

  .group-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.25rem 1.3rem 0;
  }

  .group-card-title {
    margin: 0;
    font-size: 1.08rem;
    font-weight: 800;
    color: #064e3b;
  }

  .group-card-copy {
    margin: .35rem 0 0;
    color: #64748b;
    font-size: .88rem;
    line-height: 1.7;
  }

  .group-card-head-actions {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .group-card-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
    padding: .45rem .8rem;
    border-radius: 999px;
    background: #ecfdf5;
    color: #047857;
    font-size: .72rem;
    font-weight: 800;
  }


  .group-card-action-link {
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    text-decoration: none;
    border-radius: 16px;
    padding: .82rem 1rem;
    font-size: .84rem;
    font-weight: 800;
    color: #fff;
    background: linear-gradient(135deg, #065f46, #10b981);
    box-shadow: 0 16px 30px rgba(6, 95, 70, 0.18);
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .group-card-action-link:hover {
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 18px 32px rgba(6, 95, 70, 0.22);
  }

  .group-card-body {
    padding: 1.1rem 1.3rem 1.3rem;
  }

  .group-empty {
    border: 1px dashed #cfe9db;
    border-radius: 20px;
    padding: 1.15rem;
    background: linear-gradient(180deg, #fbfffd, #f7fcf9);
    color: #64748b;
    font-size: .88rem;
    line-height: 1.75;
  }


  .group-empty-actions {
    margin-top: .95rem;
  }

  .group-my-list {
    display: grid;
    gap: .95rem;
  }

  .group-my-item {
    display: block;
    width: 100%;
    text-align: left;
    text-decoration: none;
    border: 1px solid rgba(221, 239, 231, 0.95);
    border-radius: 24px;
    padding: 1rem 1.05rem;
    background: linear-gradient(180deg, #ffffff, #f8fffb);
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .group-my-item:hover {
    transform: translateY(-2px);
    border-color: rgba(16, 185, 129, 0.28);
    box-shadow: 0 16px 28px rgba(6, 78, 59, 0.08);
  }


  .group-my-item-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .9rem;
    margin-bottom: .65rem;
  }

  .group-my-name {
    margin: 0 0 .2rem;
    font-size: .96rem;
    font-weight: 800;
    color: #0f172a;
  }

  .group-my-title-row {
    display: flex;
    align-items: center;
    gap: .55rem;
    flex-wrap: wrap;
  }

  .group-my-meta {
    color: #64748b;
    font-size: .8rem;
    line-height: 1.7;
  }

  .group-my-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .9rem;
    margin-top: .85rem;
  }

  .group-member-preview {
    display: flex;
    align-items: center;
    gap: .8rem;
    min-width: 0;
  }

  .group-member-avatars {
    display: flex;
    align-items: center;
  }

  .group-member-avatar {
    width: 38px;
    height: 38px;
    margin-left: -10px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid #fff;
    background: #d1fae5;
    box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
  }

  .group-member-avatar:first-child {
    margin-left: 0;
  }

  .group-member-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .group-member-fallback {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    color: #047857;
    font-size: .95rem;
  }

  .group-member-animal {
    width: 100%;
    height: 100%;
    display: grid;
    place-items: center;
    font-size: 1.15rem;
    background: #ecfdf5;
  }

  .group-member-text {
    min-width: 0;
  }

  .group-member-text strong {
    display: block;
    font-size: .78rem;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .group-member-text span {
    display: block;
    margin-top: .08rem;
    color: #64748b;
    font-size: .74rem;
    line-height: 1.5;
  }

  .group-card-cta-secondary {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    border: none;
    border-radius: 16px;
    padding: .82rem 1rem;
    font-size: .84rem;
    font-weight: 800;
    white-space: nowrap;
    color: #065f46;
    background: #ecfdf5;
  }


  .group-lobby-modal {
    position: fixed;
    inset: 0;
    z-index: 2200;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: rgba(15, 23, 42, .45);
  }

  .group-lobby-modal.show {
    display: flex;
  }

  .group-lobby-modal-dialog {
    width: min(390px, 100%);
    border-radius: 20px;
    background: #ffffff;
    border: 1px solid rgba(187, 247, 208, .95);
    box-shadow: 0 24px 60px rgba(15, 23, 42, .2);
    padding: 1.25rem;
    text-align: center;
  }

  .group-lobby-modal-icon {
    width: 54px;
    height: 54px;
    margin: 0 auto .85rem;
    border-radius: 50%;
    display: grid;
    place-items: center;
    background: #dcfce7;
    color: #047857;
    font-size: 1.55rem;
  }

  .group-lobby-modal-dialog h3 {
    margin: 0 0 .5rem;
    color: #0f172a;
    font-size: 1.08rem;
    font-weight: 900;
  }

  .group-lobby-modal-dialog p {
    margin: 0;
    color: #475569;
    font-size: .9rem;
    line-height: 1.65;
  }

  .group-lobby-modal-close {
    margin-top: 1rem;
    border: none;
    border-radius: 999px;
    background: #047857;
    color: #ffffff;
    padding: .68rem 1.2rem;
    font-size: .84rem;
    font-weight: 800;
  }

  @media (max-width: 991.98px) {
    body.group-chat-page-active {
      overflow: auto;
    }

    .group-lobby-page {
      height: auto;
      min-height: calc(100dvh - 96px);
      overflow: visible;
    }

    .group-lobby-page > .container {
      height: auto;
    }

    .group-lobby-workspace {
      grid-template-columns: 1fr;
      height: auto;
      min-height: 0;
    }

    .group-lobby-chat-panel {
      height: 680px;
      border-radius: 24px;
    }

    .group-lobby-sidebar {
      height: auto;
      border-right: 1px solid #dcece4;
      border-radius: 24px;
    }

    .group-lobby-workspace .group-lobby-grid {
      max-height: 520px;
    }

    .group-lobby-grid {
      grid-template-columns: 1fr;
    }

    .group-card-head {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-card-head-actions {
      justify-content: flex-start;
    }
  }

  @media (max-width: 767.98px) {
    body.group-chat-page-active {
      overflow-x: hidden;
      overflow-y: auto;
    }

    .group-lobby-page {
      height: auto;
      min-height: calc(100dvh - 96px);
      padding: .55rem 0;
      overflow: visible;
    }

    .group-lobby-page > .container {
      height: auto;
      min-height: 0;
      padding-inline: .55rem;
    }

    .group-type-guide {
      margin-bottom: .65rem;
      padding: .75rem;
      border-radius: 18px;
    }

    .group-type-guide-head {
      display: block;
      margin-bottom: .55rem;
    }

    .group-type-guide-title {
      font-size: 1rem;
      line-height: 1.3;
    }

    .group-type-guide-description {
      margin-top: .2rem;
      font-size: .7rem;
      line-height: 1.45;
    }

    .group-type-guide-intro {
      margin-top: .25rem;
      text-align: left;
    }

    .group-type-guide-list {
      display: flex;
      gap: .5rem;
      overflow-x: auto;
      padding: 0 0 .25rem;
      scroll-snap-type: x mandatory;
      overscroll-behavior-inline: contain;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: thin;
    }

    .group-type-guide-item {
      flex: 0 0 min(88%, 320px);
      padding: .65rem .7rem;
      border-radius: 14px;
      scroll-snap-align: start;
    }

    .group-type-guide-name {
      margin-bottom: .12rem;
      font-size: .78rem;
    }

    .group-type-guide-copy {
      font-size: .66rem;
      line-height: 1.4;
    }

    .group-lobby-workspace {
      display: block;
      height: calc(100svh - 96px);
      min-height: 520px;
    }

    .group-lobby-sidebar {
      width: 100%;
      height: 100%;
      min-height: 0;
      border: 1px solid #dcece4;
      border-radius: 20px;
    }

    .group-lobby-workspace .group-lobby-grid {
      max-height: none;
      min-height: 0;
      overflow-y: auto;
    }

    .group-lobby-chat-panel {
      display: none;
      height: 100%;
      min-height: 0;
      border-radius: 20px;
      flex-direction: column;
    }

    .group-lobby-workspace.is-mobile-chat-open .group-lobby-sidebar {
      display: none;
    }

    .group-lobby-workspace.is-mobile-chat-open .group-lobby-chat-panel {
      display: flex;
    }

    .group-mobile-chat-bar {
      display: flex;
      align-items: center;
      flex: 0 0 48px;
      padding: 0 .75rem;
      border-bottom: 1px solid #e3f0ea;
      background: #ffffff;
    }

    .group-mobile-chat-back {
      display: inline-flex;
      align-items: center;
      gap: .55rem;
      border: 0;
      background: transparent;
      color: #065f46;
      padding: .45rem .35rem;
      font-size: .84rem;
      font-weight: 800;
      cursor: pointer;
    }

    .group-mobile-chat-back i {
      font-size: 1.15rem;
    }

    .group-lobby-chat-frame,
    .group-lobby-chat-empty {
      flex: 1 1 auto;
      min-height: 0;
    }

    .group-lobby-hero,
    .group-lobby-toolbar,
    .group-card {
      border-radius: 20px;
    }

    .group-card-head {
      padding: 1rem 1rem 0;
    }

    .group-card-head,
    .group-my-item-top,
    .group-my-footer {
      flex-direction: column;
      align-items: flex-start;
    }

    .group-card-head-actions {
      width: 100%;
      justify-content: flex-start;
    }

    .group-card-body {
      padding: 1rem;
    }

    .group-my-item {
      min-width: 0;
      padding: .9rem;
      border-radius: 18px;
      overflow: hidden;
    }

    .group-my-item-top,
    .group-my-item-top > div,
    .group-my-title-row,
    .group-member-preview,
    .group-member-text {
      width: 100%;
      min-width: 0;
    }

    .group-my-title-row {
      display: grid;
      grid-template-columns: minmax(0, 1fr);
      align-items: start;
      gap: .45rem;
    }

    .group-my-name {
      width: 100%;
      overflow-wrap: anywhere;
      line-height: 1.4;
    }

    .group-my-meta {
      overflow-wrap: anywhere;
    }

    .group-my-footer {
      width: 100%;
    }

    .group-member-preview {
      align-items: flex-start;
    }

    .group-member-avatars {
      flex: 0 0 auto;
    }

    .group-member-text strong {
      white-space: normal;
      overflow: visible;
      text-overflow: clip;
      overflow-wrap: anywhere;
      line-height: 1.45;
    }

    .group-card-action-link,
    .group-card-cta-secondary {
      width: 100%;
      justify-content: center;
    }

    .anonim-toggle-row {
      flex-direction: column;
    }

    .anonim-toggle-switch {
      align-self: flex-end;
    }
  }

  @media (max-width: 420px) {
    .group-lobby-page .container {
      padding-inline: .5rem;
    }

    .group-type-guide-item {
      flex-basis: calc(100% - 1.5rem);
    }

    .group-card-head,
    .group-card-body {
      padding-inline: .85rem;
    }

    .group-member-preview {
      flex-direction: column;
      gap: .55rem;
    }

    .group-member-text span {
      margin-top: .2rem;
    }
  }
</style>
@endpush

@section('konten')
@php
  $user = Auth::user();
  $isAnonim = $user ? $user->isAnonim() : false;
  [$privateRooms, $publicRooms] = $joinedRooms->partition(fn ($room) => method_exists($room, 'isPrivate')
      ? $room->isPrivate()
      : (($room->visibility ?? 'public') === 'private'));
  $groupSections = [
      [
          'title' => 'Grup Privat',
          'copy' => 'Grup undangan dari konselor yang menampilkan identitas asli anggota.',
          'rooms' => $privateRooms,
          'empty' => 'Kamu belum bergabung ke grup privat mana pun.',
          'show_join_action' => false,
      ],
      [
          'title' => 'Grup Publik',
          'copy' => 'Grup berbagi pengalaman berdasarkan topik yang bisa kamu ikuti dengan identitas anonim.',
          'rooms' => $publicRooms,
          'empty' => 'Kamu belum bergabung ke grup publik mana pun.',
          'show_join_action' => true,
      ],
  ];
  $initialRoom = $selectedGroupId
      ? $joinedRooms->first(fn ($room) => (int) $room->id === (int) $selectedGroupId)
      : null;
  $initialRoom ??= $publicRooms->first() ?? $privateRooms->first();
  $initialSectionType = $initialRoom && method_exists($initialRoom, 'isPrivate') && $initialRoom->isPrivate()
      ? 'private'
      : 'public';
@endphp
<section class="group-lobby-page">
  <div class="container">
    @if(session('success'))
      <div style="max-width:1180px;margin:0 auto 1rem;background:#e8fff1;border:1px solid #bbf7d0;color:#166534;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div style="max-width:1180px;margin:0 auto 1rem;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;padding:1rem 1.15rem;border-radius:18px;font-weight:600;">
        {{ session('error') }}
      </div>
    @endif

    <section class="group-type-guide" aria-labelledby="groupTypeGuideTitle">
      <div class="group-type-guide-head">
        <div>
          <h1 class="group-type-guide-title" id="groupTypeGuideTitle">Pilih gurp yang ingin kamu ikuti<h1>
          <p class="group-type-guide-description">Buka grup yang sudah kamu ikuti atau gabung ke topik publik baru.</p>
        </div>
      </div>

      <div class="group-type-guide-list">
        <article class="group-type-guide-item">
          <div>
            <h2 class="group-type-guide-name">
              Grup Publik
            </h2>
            <p class="group-type-guide-copy">Kamu otomatis menggunakan identitas anonim saat bergabung.</p>
          </div>
        </article>

        <article class="group-type-guide-item is-private">
          <div>
            <h2 class="group-type-guide-name">
              Grup Privat
            </h2>
            <p class="group-type-guide-copy">Dibuat oleh konselor dan hanya dapat diikuti melalui undangan dengan identitas asli.</p>
          </div>
        </article>
      </div>
    </section>

    <div class="group-lobby-workspace">
      <aside class="group-lobby-sidebar">
    <div class="group-lobby-toolbar">
      <div class="group-lobby-toolbar-head">
        <h1 class="group-lobby-toolbar-title">Grup Chat</h1>
        <button
          type="button"
          class="group-join-quick {{ $initialSectionType === 'private' ? 'is-hidden' : '' }}"
          id="groupJoinQuick"
          aria-label="Gabung grup publik"
          title="Gabung grup publik"
          aria-expanded="{{ $errors->has('topic') ? 'true' : 'false' }}"
        >
          <i class="bi bi-plus-lg"></i>
          <span>Gabung Grup</span>
        </button>
      </div>
      <div class="group-lobby-search-row">
        <label class="group-lobby-search">
          <i class="bi bi-search" aria-hidden="true"></i>
          <input type="search" id="groupLobbySearch" placeholder="Cari topik atau nama grup" autocomplete="off">
        </label>
      </div>
      <div class="group-lobby-tabs" role="tablist" aria-label="Jenis grup">
        <button type="button" class="group-lobby-tab {{ $initialSectionType === 'public' ? 'active' : '' }}" data-group-lobby-tab="public" role="tab" aria-selected="{{ $initialSectionType === 'public' ? 'true' : 'false' }}">
          Publik ({{ $publicRooms->count() }})
        </button>
        <button type="button" class="group-lobby-tab {{ $initialSectionType === 'private' ? 'active' : '' }}" data-group-lobby-tab="private" role="tab" aria-selected="{{ $initialSectionType === 'private' ? 'true' : 'false' }}">
          Privat ({{ $privateRooms->count() }})
        </button>
      </div>
    </div>

    <div class="group-public-join-panel" id="groupPublicJoinPanel" {{ $errors->has('topic') ? '' : 'hidden' }}>
      <h2>Gabung Grup Publik</h2>
      <p>Pilih topik konseling yang ingin kamu ikuti.</p>
      <form action="{{ route('mahasiswa.group-chat.join') }}" method="POST">
        @csrf
        <input type="hidden" name="consent_acknowledged" value="1">
        <input type="hidden" name="consent_version" value="{{ $consentVersion ?? '1.0' }}">
        <select name="topic" class="group-public-topic-select" required>
          <option value="">Pilih topik konseling</option>
          @foreach($publicTopics as $topic)
            <option value="{{ $topic['topic_key'] }}" {{ old('topic') === $topic['topic_key'] ? 'selected' : '' }}>
              {{ $topic['topic_label'] }}
            </option>
          @endforeach
        </select>
        @error('topic')
          <div class="group-public-join-error">{{ $message }}</div>
        @enderror
        <div class="group-public-join-actions">
          <button type="button" class="group-public-join-cancel" id="groupPublicJoinCancel">Batal</button>
          <button type="submit" class="group-public-join-submit">Gabung Grup</button>
        </div>
      </form>
    </div>

    <div class="group-lobby-grid">
      @foreach($groupSections as $section)
      @php
        $sectionType = $section['title'] === 'Grup Privat' ? 'private' : 'public';
      @endphp
      <div class="group-card {{ $sectionType !== $initialSectionType ? 'is-section-hidden' : '' }} {{ $section['rooms']->isEmpty() ? 'is-empty-section' : '' }}" data-group-section="{{ $sectionType }}">

        <div class="group-card-body">
          {{-- Daftar ini sengaja hanya menampilkan grup yang sudah pernah diikuti mahasiswa. --}}
          @if($section['rooms']->isEmpty())
            {{-- Sidebar dibiarkan kosong jika pengguna belum memiliki grup pada tab ini. --}}
          @else
            <div class="group-my-list">
              @foreach($section['rooms'] as $room)
                @php
                  $latestPreview = optional($room->latestMessage)->pesan
                    ? \Illuminate\Support\Str::limit($room->latestMessage->pesan, 92)
                    : 'Belum ada pesan di grup ini.';
                @endphp
                <a href="{{ route('mahasiswa.group-chat.room', ['group' => $room->id, 'embedded' => 1]) }}" target="groupChatFrame" class="group-my-item {{ $initialRoom && (int) $initialRoom->id === (int) $room->id ? 'is-active-room' : '' }}" data-group-lobby-item data-group-search="{{ Illuminate\Support\Str::lower($room->title . ' ' . $latestPreview) }}">
                  <div class="group-my-item-top">
                    <div>
                      <div class="group-my-title-row">
                        <h3 class="group-my-name">{{ $room->title }}</h3>
                      </div>
                    </div>
                  </div>

                  <div class="group-my-meta">
                    {{ $latestPreview }}
                  </div>
                </a>
              @endforeach
            </div>
          @endif
          <div class="group-search-empty" data-group-search-empty hidden>Grup yang kamu cari tidak ditemukan.</div>
        </div>
      </div>
      @endforeach
    </div>
      </aside>

      <div class="group-lobby-chat-panel">
        <div class="group-mobile-chat-bar">
          <button type="button" class="group-mobile-chat-back" id="groupMobileChatBack" aria-label="Kembali ke daftar grup">
            <i class="bi bi-arrow-left"></i>
            <span>Daftar Grup</span>
          </button>
        </div>
        @if($initialRoom)
          <iframe
            class="group-lobby-chat-frame"
            id="groupChatFrame"
            name="groupChatFrame"
            src="{{ route('mahasiswa.group-chat.room', ['group' => $initialRoom->id, 'embedded' => 1]) }}"
            title="Ruang chat grup konseling"
          ></iframe>
        @else
          <div class="group-lobby-chat-empty">
            <div>
              <i class="bi bi-chat-dots"></i>
              <strong>Belum ada grup yang dapat dibuka.</strong>
              <p>Gabung ke grup publik atau tunggu undangan grup privat dari konselor.</p>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@if(session('group_left_success_modal'))
  @php($groupLeftModal = session('group_left_success_modal'))
  <div class="group-lobby-modal show" id="groupLeftSuccessModal" aria-hidden="false">
    <div class="group-lobby-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="groupLeftSuccessTitle">
      <div class="group-lobby-modal-icon">
        <i class="bi bi-check2"></i>
      </div>
      <h3 id="groupLeftSuccessTitle">{{ $groupLeftModal['title'] ?? 'Berhasil Keluar dari Grup' }}</h3>
      <p>{{ $groupLeftModal['message'] ?? 'Anda berhasil keluar dari grup.' }}</p>
      <button type="button" class="group-lobby-modal-close" id="groupLeftSuccessClose">Tutup</button>
    </div>
  </div>
@endif
@endsection

@push('scripts')
<script>
(() => {
  document.body.classList.add('group-chat-page-active');

  const searchInput = document.getElementById('groupLobbySearch');
  const joinQuick = document.getElementById('groupJoinQuick');
  const joinPanel = document.getElementById('groupPublicJoinPanel');
  const joinCancel = document.getElementById('groupPublicJoinCancel');
  const tabs = Array.from(document.querySelectorAll('[data-group-lobby-tab]'));
  const sections = Array.from(document.querySelectorAll('[data-group-section]'));
  const workspace = document.querySelector('.group-lobby-workspace');
  const mobileBack = document.getElementById('groupMobileChatBack');
  const mobileMedia = window.matchMedia('(max-width: 767.98px)');
  let activeType = @json($initialSectionType);
  let lastOpenedItem = null;

  const setJoinPanelOpen = (isOpen) => {
    if (!joinPanel || !joinQuick) {
      return;
    }

    joinPanel.hidden = !isOpen;
    joinQuick.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  };

  const filterActiveSection = () => {
    const keyword = String(searchInput?.value || '').trim().toLowerCase();

    sections.forEach((section) => {
      const isActive = section.dataset.groupSection === activeType;
      section.classList.toggle('is-section-hidden', !isActive);

      if (!isActive) {
        return;
      }

      const items = Array.from(section.querySelectorAll('[data-group-lobby-item]'));
      let visibleCount = 0;

      items.forEach((item) => {
        const matches = !keyword || String(item.dataset.groupSearch || '').includes(keyword);
        item.hidden = !matches;
        visibleCount += matches ? 1 : 0;
      });

      const searchEmpty = section.querySelector('[data-group-search-empty]');
      if (searchEmpty) {
        searchEmpty.hidden = items.length === 0 || visibleCount > 0;
      }
    });

    joinQuick?.classList.toggle('is-hidden', activeType !== 'public');

    if (activeType !== 'public') {
      setJoinPanelOpen(false);
    }
  };

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      activeType = tab.dataset.groupLobbyTab || 'public';
      tabs.forEach((item) => {
        const selected = item === tab;
        item.classList.toggle('active', selected);
        item.setAttribute('aria-selected', selected ? 'true' : 'false');
      });
      filterActiveSection();
    });
  });

  searchInput?.addEventListener('input', filterActiveSection);
  joinQuick?.addEventListener('click', () => {
    setJoinPanelOpen(joinPanel?.hidden ?? true);
  });
  joinCancel?.addEventListener('click', () => setJoinPanelOpen(false));
  document.querySelectorAll('[data-group-lobby-item]').forEach((item) => {
    item.addEventListener('click', () => {
      lastOpenedItem = item;
      document.querySelectorAll('[data-group-lobby-item]').forEach((current) => {
        current.classList.toggle('is-active-room', current === item);
      });

      if (mobileMedia.matches) {
        workspace?.classList.add('is-mobile-chat-open');
      }
    });
  });

  mobileBack?.addEventListener('click', () => {
    workspace?.classList.remove('is-mobile-chat-open');
    lastOpenedItem?.focus();
  });

  const resetMobileView = (event) => {
    if (!event.matches) {
      workspace?.classList.remove('is-mobile-chat-open');
    }
  };

  if (typeof mobileMedia.addEventListener === 'function') {
    mobileMedia.addEventListener('change', resetMobileView);
  } else {
    mobileMedia.addListener(resetMobileView);
  }
  filterActiveSection();
})();
</script>
@endpush

@push('scripts')
<script>
(() => {
  const modal = document.getElementById('groupLeftSuccessModal');
  const closeBtn = document.getElementById('groupLeftSuccessClose');

  if (!modal || !closeBtn) {
    return;
  }

  const closeModal = () => {
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
  };

  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeModal();
    }
  });
})();
</script>
@endpush

@push('scripts')

@endpush
