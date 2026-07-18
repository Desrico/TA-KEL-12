@extends('layouts.master')

@section('title', 'Edukasi Mental')

@push('styles')
<style>
    .edu-page{
        background:linear-gradient(180deg,#f8fbf8 0%,#f3f7f3 100%);
        color:#202020;
        overflow:hidden;
    }

    .edu-hero{
        position:relative;
        padding:5.5rem 0 4.5rem;
        overflow:hidden;
    }

    .edu-hero::before{
        content:'';
        position:absolute;
        width:420px;
        height:420px;
        border-radius:50%;
        background:radial-gradient(circle, rgba(15,184,122,.13), rgba(15,184,122,0) 70%);
        left:-130px;
        top:-120px;
        pointer-events:none;
    }

    .edu-hero::after{
        content:'';
        position:absolute;
        width:420px;
        height:420px;
        border-radius:50%;
        background:radial-gradient(circle, rgba(46,134,193,.10), rgba(46,134,193,0) 70%);
        right:-130px;
        top:0;
        pointer-events:none;
    }

    .edu-hero .container{
        position:relative;
        z-index:2;
    }

    .edu-badge{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        background:#e8f8ef;
        color:#0a523a;
        border-radius:999px;
        padding:.45rem 1rem;
        font-size:.75rem;
        font-weight:800;
        letter-spacing:.08em;
        text-transform:uppercase;
        margin-bottom:1rem;
    }

    .edu-title{
        font-size:clamp(2.35rem,5vw,4.6rem);
        font-weight:800;
        line-height:1.05;
        color:#202020;
        margin-bottom:1.25rem;
    }

    .edu-title span{
        color:#0fb87a;
        font-style:italic;
    }

    .edu-desc{
        max-width:620px;
        color:#52616b;
        line-height:1.9;
        font-size:1rem;
        margin-bottom:1.8rem;
    }

    .edu-hero-actions{
        display:flex;
        flex-wrap:wrap;
        gap:.8rem;
    }

    .edu-btn-primary,
    .edu-btn-outline{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.55rem;
        border-radius:999px;
        padding:.9rem 1.35rem;
        font-weight:800;
        text-decoration:none;
        border:0;
        transition:.25s ease;
        cursor:pointer;
    }

    .edu-btn-primary{
        background:#0a523a;
        color:#fff;
        box-shadow:0 12px 25px rgba(10,82,58,.16);
    }

    .edu-btn-primary:hover{
        background:#0c6347;
        color:#fff;
        transform:translateY(-2px);
    }

    .edu-btn-outline{
        background:#fff;
        color:#0a523a;
        border:1px solid rgba(10,82,58,.14);
    }

    .edu-btn-outline:hover{
        background:#eff8f2;
        color:#0a523a;
        transform:translateY(-2px);
    }

    .edu-hero-card{
        background:#fff;
        border-radius:30px;
        padding:1.7rem;
        border:1px solid rgba(26,58,92,.08);
        box-shadow:0 24px 50px rgba(13,27,42,.10);
    }

    .edu-hero-visual{
        min-height:350px;
        border-radius:24px;
        background:
            radial-gradient(circle at 20% 20%, rgba(255,255,255,.95) 0 0, rgba(255,255,255,.95) 60px, transparent 62px),
            radial-gradient(circle at 80% 80%, rgba(15,184,122,.15) 0 0, rgba(15,184,122,0) 50%),
            linear-gradient(135deg,#d9f1e6 0%,#f5fbf7 50%,#cde5d7 100%);
        display:flex;
        align-items:center;
        justify-content:center;
        position:relative;
        overflow:hidden;
        box-shadow: inset 0 0 40px rgba(10,82,58,.04);
    }

    .edu-hero-icon{
        width:140px;
        height:140px;
        border-radius:42px;
        background:linear-gradient(135deg,#0a523a,#2e86c1);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:4rem;
        box-shadow:0 22px 40px rgba(10,82,58,.25);
        animation: floatIcon 6s ease-in-out infinite;
    }

    @keyframes floatIcon {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        50% {
            transform: translateY(-10px) rotate(3deg);
        }
    }

    .edu-floating-note{
        position:absolute;
        left:1.2rem;
        right:1.2rem;
        bottom:1.2rem;
        background:rgba(255,255,255,.8);
        border:1px solid rgba(255,255,255,.5);
        backdrop-filter:blur(16px);
        -webkit-backdrop-filter:blur(16px);
        border-radius:20px;
        padding:1.1rem 1.3rem;
        color:#0a523a;
        font-weight:700;
        line-height:1.6;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        font-size: 0.95rem;
    }

    .edu-section{
        padding:4.5rem 0;
    }

    .edu-section.pt-0{
        padding-top:0;
    }

    .edu-section-head{
        margin-bottom:2.4rem;
    }

    .edu-section-title{
        font-size:clamp(1.8rem,3vw,2.8rem);
        font-weight:800;
        line-height:1.2;
        color:#202020;
        margin-bottom:.8rem;
    }

    .edu-section-desc{
        color:#52616b;
        line-height:1.85;
        margin:0;
    }

    .edu-topic-card,
    .edu-content-card,
    .edu-guide-card{
        height:100%;
        background:#fff;
        border:1px solid rgba(26,58,92,.08);
        box-shadow:0 16px 34px rgba(13,27,42,.07);
        transition:.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .edu-topic-card:hover,
    .edu-content-card:hover,
    .edu-guide-card:hover{
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(10, 82, 58, 0.12);
    }

    .edu-topic-card{
        border-radius:22px;
        padding:1.45rem;
        cursor:pointer;
    }

    .edu-topic-card.active{
        border-color:rgba(10,82,58,.45);
        background:#f2fbf6;
        box-shadow:0 24px 46px rgba(10,82,58,.14);
    }

    .edu-topic-icon{
        width:54px;
        height:54px;
        border-radius:18px;
        background:#eff8f2;
        color:#0a523a;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.45rem;
        margin-bottom:1rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .edu-topic-card:hover .edu-topic-icon {
        background: #0a523a;
        color: #fff;
        transform: scale(1.1) rotate(5deg);
    }

    .edu-topic-card h3{
        font-size:1.15rem;
        font-weight:800;
        color:#202020;
        margin-bottom:.55rem;
    }

    .edu-topic-card p{
        color:#52616b;
        line-height:1.75;
        margin:0;
        font-size:.93rem;
    }

    .edu-click-note{
        display:inline-flex;
        align-items:center;
        gap:.4rem;
        margin-top:1rem;
        font-size:.78rem;
        font-weight:800;
        color:#0a523a;
    }

    .edu-mobile-swipe-hint{
        display:none;
    }

    .edu-content-link,
    .edu-content-button{
        width:100%;
        text-align:left;
        border:none;
        cursor:pointer;
        font-family:inherit;
        background:#ffffff;
        padding:0;
        border-radius:24px;
        overflow:hidden;
    }

    .edu-content-button:focus{
        outline:none;
    }

    .edu-content-button:focus-visible{
        outline:3px solid rgba(15,184,122,.35);
        outline-offset:4px;
    }

    .edu-content-card {
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
        background: #ffffff !important;
        border: 1px solid #d9e7df;
        border-radius: 24px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 10px 28px rgba(21, 71, 52, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        filter: none !important;
    }

    .edu-content-media{
        min-height:0;
        background: #edf7f1;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#0a523a;
        position:relative;
        width:100%;
        overflow: hidden;
        height:auto;
        aspect-ratio:16 / 9;
    }

    .edu-content-type{
        position:absolute;
        top:14px;
        left:14px;
        z-index:2;
        display:inline-flex;
        align-items:center;
        gap:.35rem;
        border-radius:999px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        background:#fff;
        color: #146c43;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
    }

    .edu-content-body {
        display:flex;
        flex:1;
        flex-direction:column;
        padding: 20px 20px 22px;
        background: #ffffff !important;
    }

    .edu-content-meta{
        display:flex;
        flex-wrap:wrap;
        gap:.8px;
        margin-bottom:14px;
    }

    .edu-chip{
        display:inline-flex;
        align-items:center;
        border-radius:999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
        background: #eef7f1;
        color: #146c43;
    }

    .edu-content-body h3 {
        margin-bottom: 10px;
        font-size: 20px;
        line-height: 1.35;
        color: #16211d;
        font-weight: 800;
    }
    .edu-content-body p {
        margin-bottom: 18px;
        color: #4e5d55;
        line-height: 1.75;
        font-size: 15px;
    }

    .edu-action-btn {
        display: inline-block;
        padding: 10px 16px;
        border-radius: 12px;
        background: #178754;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        transition: background .2s ease;
    }
    
    .edu-content-card:hover .edu-action-btn {
        background: #0f6b40;
    }

    .edu-action-row{
        display:flex;
        flex-wrap:wrap;
        gap:.65rem;
        align-items:center;
        margin-top:auto;
    }

    .edu-action-secondary{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.4rem;
        padding:10px 14px;
        border-radius:12px;
        border:1px solid rgba(20,108,67,.22);
        background:#eef7f1;
        color:#146c43;
        font-size:14px;
        font-weight:700;
        text-decoration:none;
        font-family:inherit;
        cursor:pointer;
        transition: all 0.2s ease;
    }

    .edu-action-secondary:hover{
        color:#0a523a;
        background:#dff3e8;
        text-decoration:none;
    }

    /* Tabs Filter (Artikel & Video) */
    .edu-tabs-wrapper {
        flex-shrink: 0;
    }

    .edu-tabs-container {
        display: inline-flex;
        background: #eff5f1;
        padding: 6px;
        border-radius: 99px;
        border: 1px solid rgba(10,82,58,.08);
        gap: 4px;
    }

    .edu-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        border: none;
        background: transparent;
        color: #52616b;
        padding: .65rem 1.25rem;
        font-weight: 800;
        font-size: .85rem;
        border-radius: 99px;
        cursor: pointer;
        transition: all .25s ease;
        line-height: 1;
    }

    .edu-tab-btn i {
        font-size: 1rem;
        color: #146c43;
        transition: color .25s ease;
    }

    .edu-tab-btn:hover {
        color: #0a523a;
    }

    .edu-tab-btn.active {
        background: #0a523a;
        color: #fff;
        box-shadow: 0 8px 20px rgba(10,82,58,.15);
    }

    .edu-tab-btn.active i {
        color: #fff;
    }

    /* Fallback Illustration Style */
    .edu-content-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #edf7f1;
        overflow: hidden;
    }

    /* Empty state */
    .edu-no-content {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: #fff;
        border-radius: 24px;
        border: 1px dashed rgba(10,82,58,.2);
        max-width: 500px;
        margin: 2rem auto;
    }

    .edu-no-content-icon {
        font-size: 3rem;
        color: #8caba0;
        margin-bottom: 1rem;
    }

    .edu-no-content h3 {
        font-size: 1.25rem;
        font-weight: 800;
        color: #202020;
        margin-bottom: .5rem;
    }

    .edu-no-content p {
        color: #52616b;
        font-size: .95rem;
        line-height: 1.6;
        margin: 0;
    }

    #contentGrid{
        display:grid;
        grid-template-columns:repeat(3,minmax(0,1fr));
        gap:2rem;
        position:relative;
        overflow:visible;
        width:100%;
        margin:0;
        padding:0;
        border:0;
        border-radius:0;
        background:transparent !important;
        box-shadow:none;
    }

    #contentGrid > [data-topic-item]{
        width:auto;
        max-width:none;
        min-width:0;
        margin:0;
        padding:0;
        border:0;
        background:transparent !important;
        box-shadow:none;
        opacity:0;
        visibility:hidden;
        pointer-events:none;
        transform:translateX(var(--edu-slide-shift, 64px));
        transition:opacity .34s ease,
                   transform .4s cubic-bezier(.22,.61,.36,1),
                   visibility 0s linear .4s;
    }

    #contentGrid > .edu-content-item-active{
        z-index:2;
        opacity:1;
        visibility:visible;
        pointer-events:auto;
        transform:translateX(0);
        transition-delay:0s;
    }

    #contentGrid > .edu-content-item-entering,
    #contentGrid > .edu-content-item-leaving{
        z-index:1;
        visibility:visible;
        transition-delay:0s;
    }

    #contentGrid > [data-content-slot="1"]{grid-column:1;grid-row:1;}
    #contentGrid > [data-content-slot="2"]{grid-column:2;grid-row:1;}
    #contentGrid > [data-content-slot="3"]{grid-column:3;grid-row:1;}
    #contentGrid > [data-content-slot="4"]{grid-column:1;grid-row:2;}
    #contentGrid > [data-content-slot="5"]{grid-column:2;grid-row:2;}
    #contentGrid > [data-content-slot="6"]{grid-column:3;grid-row:2;}

    .edu-content-pagination{
        display:flex;
        align-items:center;
        justify-content:center;
        gap:.85rem;
        margin:2.75rem 0 3.25rem;
    }

    .edu-content-pagination[hidden]{
        display:none !important;
    }

    .edu-pagination-btn{
        width:44px;
        height:44px;
        border-radius:999px;
        border:1px solid rgba(10,82,58,.14);
        background:#fff;
        color:#0a523a;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-weight:800;
        cursor:pointer;
        transition:.22s ease;
        box-shadow:0 7px 16px rgba(13,27,42,.08);
    }

    .edu-pagination-btn:hover:not(:disabled){
        background:#0a523a;
        color:#fff;
        border-color:#0a523a;
        transform:translateY(-2px);
    }

    .edu-pagination-btn:focus-visible{
        outline:3px solid rgba(10,82,58,.2);
        outline-offset:3px;
    }

    .edu-pagination-btn:disabled{
        opacity:.42;
        cursor:not-allowed;
        box-shadow:none;
    }

    .edu-pagination-status{
        min-width:52px;
        color:#52616b;
        font-size:.9rem;
        font-weight:800;
        text-align:center;
    }

    .edu-content-selected{
        border-color:rgba(10,82,58,.55) !important;
        box-shadow:0 24px 50px rgba(10,82,58,.18) !important;
        transform:translateY(-6px);
    }

    .edu-content-modal{
        position:fixed;
        inset:0;
        z-index:99999;
        display:block;
        padding:0;
        background:rgba(15,23,42,.58);
        opacity:0;
        visibility:hidden;
        pointer-events:none;
        overflow:hidden;
        overscroll-behavior:contain;
        transition:opacity .28s ease, visibility .28s ease;
    }

    .edu-content-modal.show{
        opacity:1;
        visibility:visible;
        pointer-events:auto;
    }

    .edu-content-modal-dialog{
        position:fixed;
        top:50%;
        left:var(--edu-modal-left, 50%);
        width:var(--edu-modal-width, min(1080px, calc(100vw - 2rem)));
        max-width:calc(100vw - 2rem);
        max-height:min(88vh, 920px);
        background:#fff;
        border-radius:18px;
        box-shadow:0 28px 70px rgba(13,27,42,.24);
        overflow:hidden;
        transform:translate(-50%, calc(-50% + 18px)) scale(.96);
        transition:transform .28s ease, opacity .28s ease;
        opacity:.96;
    }

    .edu-content-modal.show .edu-content-modal-dialog{
        transform:translate(-50%, -50%) scale(1);
        opacity:1;
    }

    .edu-content-modal-body{
        max-height:min(88vh, 920px);
        overflow-y:auto;
        padding:0;
    }

    .edu-content-modal[data-content-mode="video"] .edu-content-modal-body{
        overflow:hidden;
    }

    .edu-content-modal-close{
        position:absolute;
        top:14px;
        right:14px;
        z-index:2;
        width:42px;
        height:42px;
        border-radius:999px;
        border:1px solid rgba(10,82,58,.15);
        background:#fff;
        color:#0a523a;
        display:flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        transition:.25s ease;
        box-shadow:0 12px 24px rgba(13,27,42,.12);
    }

    .edu-content-modal-close:hover{
        background:#0a523a;
        color:#fff;
    }

    .edu-content-modal-embed{
        position:relative;
        width:100%;
        aspect-ratio:16 / 9;
        max-height:calc(100vh - 2rem);
        border-radius:18px;
        overflow:hidden;
        background:#0f172a;
    }

    .edu-content-modal-embed iframe,
    .edu-content-modal-embed video{
        width:100%;
        height:100%;
        border:0;
        display:block;
        overflow:hidden;
    }

    .edu-content-modal-pdf{
        width:100%;
        height:min(74vh, 760px);
        border-radius:18px;
        overflow:hidden;
        background:#fff;
        border:1px solid rgba(10,82,58,.10);
    }

    .edu-content-modal-pdf iframe{
        width:100%;
        height:100%;
        border:0;
        display:block;
        background:#fff;
    }

    .edu-content-modal-article{
        padding:1.2rem;
    }

    .edu-content-detail-material{
        padding:1.35rem;
    }

    .edu-content-detail-material h4{
        margin:0 0 .75rem;
        color:#202020;
        font-size:1rem;
        font-weight:800;
    }

    .edu-content-modal-article h3{
        margin:0 3rem 1rem 0;
        color:#202020;
        font-size:1.55rem;
        font-weight:800;
        line-height:1.35;
    }

    .edu-content-modal-article p{
        margin:0;
        color:#52616b;
        line-height:1.85;
        white-space:pre-line;
    }

    .edu-inline-detail-header{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
        padding:1.6rem 1.8rem;
        background:linear-gradient(135deg,#eff8f2,#ffffff);
        border-bottom:1px solid rgba(10,82,58,.08);
    }

    .edu-inline-detail-main{
        display:flex;
        gap:1rem;
        align-items:flex-start;
        flex:1;
        min-width:0;
    }

    .edu-inline-detail-icon{
        width:64px;
        height:64px;
        border-radius:22px;
        background:#0a523a;
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.7rem;
        flex-shrink:0;
    }

    .edu-inline-detail-title{
        font-size:1.65rem;
        font-weight:800;
        color:#202020;
        margin-bottom:.6rem;
    }

    .edu-inline-detail-desc{
        color:#52616b;
        line-height:1.8;
        margin:0;
    }

    .edu-inline-close{
        width:42px;
        height:42px;
        border-radius:999px;
        border:1px solid rgba(10,82,58,.15);
        background:#fff;
        color:#0a523a;
        display:flex;
        align-items:center;
        justify-content:center;
        cursor:pointer;
        transition:.25s ease;
        flex-shrink:0;
    }

    .edu-inline-close:hover{
        background:#0a523a;
        color:#fff;
    }

    .edu-inline-detail-body{
        padding:1.8rem;
    }

    .edu-inline-detail-grid{
        display:grid;
        grid-template-columns:1.15fr .85fr;
        gap:1rem;
    }

    .edu-inline-box{
        background:#f7fbf8;
        border:1px solid rgba(10,82,58,.08);
        border-radius:20px;
        padding:1.1rem;
        height:100%;
    }

    .edu-inline-box h5{
        font-size:1rem;
        font-weight:800;
        color:#202020;
        margin-bottom:.75rem;
    }

    .edu-inline-box p{
        color:#52616b;
        line-height:1.8;
        margin:0;
    }

    .edu-inline-list{
        margin:0;
        padding-left:1.1rem;
        color:#52616b;
        line-height:1.85;
    }

    .edu-inline-action{
        margin-top:1.2rem;
        display:flex;
        flex-wrap:wrap;
        gap:.75rem;
    }

    .edu-guide-card{
        border-radius:26px;
        padding:2rem;
    }

    .edu-guide-card.warning{
        background:linear-gradient(135deg,#fff 0%,#fff8f8 100%);
        border-color:rgba(200,29,37,.12);
    }

    .edu-guide-top{
        display:flex;
        gap:1rem;
        align-items:flex-start;
        margin-bottom:1rem;
    }

    .edu-guide-icon{
        width:58px;
        height:58px;
        border-radius:20px;
        background:#eff8f2;
        color:#0a523a;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.5rem;
        flex-shrink:0;
    }

    .edu-guide-card.warning .edu-guide-icon{
        background:#ffe8e8;
        color:#c81d25;
    }

    .edu-guide-card h3{
        font-size:1.35rem;
        font-weight:800;
        color:#202020;
        margin-bottom:.45rem;
    }

    .edu-guide-card p{
        color:#52616b;
        line-height:1.8;
        margin:0;
    }

    .edu-guide-list{
        margin:1.1rem 0 0;
        padding-left:1.1rem;
        color:#52616b;
        line-height:1.8;
    }

    .edu-emergency-action{
        margin-top:1.2rem;
        display:flex;
        flex-wrap:wrap;
        gap:.7rem;
    }

    .edu-note{
        margin-top:1.5rem;
        background:#f3f8f5;
        color:#52616b;
        border:1px solid rgba(10,82,58,.08);
        border-radius:18px;
        padding:1rem 1.1rem;
        line-height:1.75;
        font-size:.92rem;
    }

    .edu-cta{
        padding:0 0 5rem;
    }

    .edu-cta-box{
        background:#0a523a;
        color:#fff;
        border-radius:30px;
        padding:2.2rem;
        box-shadow:0 24px 48px rgba(10,82,58,.18);
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:1.5rem;
        flex-wrap:wrap;
    }

    .edu-cta-box h2{
        font-size:clamp(1.6rem,3vw,2.4rem);
        font-weight:800;
        margin-bottom:.5rem;
    }

    .edu-cta-box p{
        color:rgba(255,255,255,.82);
        margin:0;
        line-height:1.75;
        max-width:680px;
    }

    .edu-cta-box .edu-btn-primary{
        background:#fff;
        color:#0a523a;
        box-shadow:none;
    }

    .edu-cta-box .edu-btn-primary:hover{
        background:#f2fff8;
        color:#0a523a;
    }

    .edu-content-media.has-image {
        padding: 0;
        overflow: hidden;
        background: #f3f7f3;
    }

    .edu-content-img {
        width: 100%;
        height: 100%;
        min-height: 0;
        object-fit: cover;
        display: block;
    }

    @media(max-width:991.98px){
        #contentGrid{
            grid-template-columns:repeat(2,minmax(0,1fr));
        }

        #contentGrid > [data-content-slot="1"]{grid-column:1;grid-row:1;}
        #contentGrid > [data-content-slot="2"]{grid-column:2;grid-row:1;}
        #contentGrid > [data-content-slot="3"]{grid-column:1;grid-row:2;}
        #contentGrid > [data-content-slot="4"]{grid-column:2;grid-row:2;}
        #contentGrid > [data-content-slot="5"]{grid-column:1;grid-row:3;}
        #contentGrid > [data-content-slot="6"]{grid-column:2;grid-row:3;}

        .edu-hero{
            padding:4rem 0 3rem;
        }

        .edu-hero-card{
            margin-top:1.5rem;
        }

        .edu-cta-box{
            align-items:flex-start;
        }

        .edu-inline-detail-grid{
            grid-template-columns:1fr;
        }
        
        .edu-section-head {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1.5rem;
        }
    }

    @media(max-width:767.98px){
        .edu-section{
            padding:2.25rem 0;
        }

        .edu-section-head{
            gap:.7rem !important;
            margin-bottom:1rem;
        }

        .edu-section-title{
            margin-bottom:.35rem;
            font-size:1.3rem;
        }

        .edu-section-desc{
            font-size:.78rem;
            line-height:1.55;
        }

        .edu-mobile-swipe-hint{
            display:inline-flex;
            align-items:center;
            gap:.35rem;
            width:max-content;
            color:#0a523a;
            font-size:.7rem;
            font-weight:800;
            margin-bottom:.55rem;
        }

        .edu-topic-scroll{
            display:flex;
            flex-wrap:nowrap;
            gap:.75rem;
            margin-inline:calc(var(--bs-gutter-x, 1.5rem) * -.5);
            padding:.15rem calc(var(--bs-gutter-x, 1.5rem) * .5) .7rem;
            overflow-x:auto;
            overflow-y:hidden;
            scroll-snap-type:x mandatory;
            scroll-padding-inline:calc(var(--bs-gutter-x, 1.5rem) * .5);
            overscroll-behavior-inline:contain;
            -webkit-overflow-scrolling:touch;
            scrollbar-width:none;
        }

        .edu-topic-scroll::-webkit-scrollbar,
        #contentGrid::-webkit-scrollbar{
            display:none;
        }

        .edu-topic-scroll > [class*="col-"]{
            width:auto;
            max-width:none;
            flex:0 0 min(76vw, 270px);
            padding:0;
            scroll-snap-align:start;
        }

        .edu-topic-card{
            min-height:176px;
            padding:1rem;
            border-radius:17px;
        }

        .edu-topic-icon{
            width:42px;
            height:42px;
            margin-bottom:.65rem;
            border-radius:13px;
            font-size:1.05rem;
        }

        .edu-topic-card h3{
            margin-bottom:.35rem;
            font-size:.92rem;
        }

        .edu-topic-card p{
            display:-webkit-box;
            overflow:hidden;
            font-size:.72rem;
            line-height:1.5;
            -webkit-box-orient:vertical;
            -webkit-line-clamp:3;
        }

        .edu-click-note{
            margin-top:.55rem;
            font-size:.67rem;
        }

        #contentGrid{
            display:flex;
            grid-template-columns:none;
            align-items:stretch;
            gap:.8rem;
            margin-inline:calc(var(--bs-gutter-x, 1.5rem) * -.5);
            padding:.15rem calc(var(--bs-gutter-x, 1.5rem) * .5) .8rem;
            overflow-x:auto;
            overflow-y:hidden;
            scroll-snap-type:x mandatory;
            scroll-padding-inline:calc(var(--bs-gutter-x, 1.5rem) * .5);
            overscroll-behavior-inline:contain;
            -webkit-overflow-scrolling:touch;
        }

        #contentGrid > [data-topic-item]{
            display:none;
            width:auto;
            min-width:0;
            max-width:none;
            flex:0 0 min(82vw, 310px);
            transform:none;
            transition:opacity .22s ease;
            scroll-snap-align:start;
        }

        #contentGrid > .edu-content-item-active{
            display:block;
        }

        #noContentMessage{
            flex:0 0 calc(100% - 1rem);
            margin:.25rem auto;
            padding:1.5rem 1rem;
        }

        .edu-content-card{
            border-radius:17px;
        }

        .edu-content-body{
            padding:.9rem;
        }

        .edu-content-meta{
            margin-bottom:.55rem;
        }

        .edu-chip,
        .edu-content-type{
            padding:5px 9px;
            font-size:10px;
        }

        .edu-content-body h3{
            margin-bottom:.4rem;
            font-size:.95rem;
        }

        .edu-content-body p{
            display:-webkit-box;
            overflow:hidden;
            margin-bottom:.75rem;
            font-size:.73rem;
            line-height:1.55;
            -webkit-box-orient:vertical;
            -webkit-line-clamp:3;
        }

        .edu-action-btn,
        .edu-action-secondary{
            padding:7px 10px;
            border-radius:9px;
            font-size:.68rem;
        }

        .edu-tabs-container{
            padding:4px;
        }

        .edu-tab-btn{
            gap:.3rem;
            padding:.5rem .75rem;
            font-size:.7rem;
        }

        .edu-tab-btn i{
            font-size:.8rem;
        }

        .edu-content-modal{
            padding:0;
        }

        .edu-content-modal-embed{
            border-radius:14px;
        }

        .edu-hero-actions,
        .edu-emergency-action,
        .edu-inline-action{
            flex-direction:column;
        }

        .edu-btn-primary,
        .edu-btn-outline{
            width:100%;
        }

        .edu-hero-visual{
            min-height:300px;
        }

        .edu-title{
            font-size:clamp(2rem,10vw,2.4rem);
        }

        .edu-hero{
            padding:2.75rem 0 2.25rem;
        }

        .edu-hero-card,
        .edu-card,
        .edu-guide-card{
            border-radius:20px;
        }

        .edu-content-modal-dialog{
            width:calc(100vw - 1.5rem);
            max-height:calc(100dvh - 1.5rem);
        }

        .edu-guide-top,
        .edu-inline-detail-header,
        .edu-inline-detail-main{
            flex-direction:column;
        }

        .edu-inline-detail-header,
        .edu-inline-detail-body{
            padding:1.2rem;
        }

        .edu-inline-detail-title{
            font-size:1.3rem;
        }

        .edu-inline-close{
            align-self:flex-end;
        }

        .edu-content-pagination{
            display:none !important;
        }
    }

</style>
@endpush

@section('konten')
@php
    $topics = [
        [
            'icon' => 'bi-journal-text',
            'title' => 'Akademik',
            'desc' => 'Membahas tekanan kuliah, tugas akhir, kerja praktik, MBKM, nilai, dan beban perkuliahan.'
        ],
        [
            'icon' => 'bi-person-heart',
            'title' => 'Intrapersonal',
            'desc' => 'Membahas kecemasan, kejenuhan, motivasi belajar, overthinking, dan pengelolaan emosi.'
        ],
        [
            'icon' => 'bi-building',
            'title' => 'Kehidupan di Kampus',
            'desc' => 'Membahas adaptasi lingkungan kampus, organisasi, aktivitas perkuliahan, dan tekanan sosial.'
        ],
        [
            'icon' => 'bi-house-heart',
            'title' => 'Keluarga',
            'desc' => 'Membahas komunikasi keluarga, tekanan dari rumah, harapan orang tua, dan konflik keluarga.'
        ],
        [
            'icon' => 'bi-door-open',
            'title' => 'Masalah di Asrama',
            'desc' => 'Membahas kenyamanan tinggal di asrama, konflik kamar, aturan, dan penyesuaian lingkungan.'
        ],
        [
            'icon' => 'bi-people',
            'title' => 'Relasi',
            'desc' => 'Membahas pertemanan, pacaran, kesalahpahaman, konflik, dan ketidaknyamanan dalam hubungan sosial.'
        ],
    ];

    $contents = $contents ?? collect();

    // Fungsi pembantu untuk merender SVG kustom yang edukatif berdasarkan kategori
    if (!function_exists('getIllustrativeSvg')) {
        function getIllustrativeSvg($category) {
            $category = strtolower($category);
            
            // 1. Akademik
            if (strpos($category, 'akademik') !== false) {
                return '
                <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; object-fit:cover; display:block;">
                  <defs>
                    <linearGradient id="grad-akademik" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" fill-opacity="1" stop-color="#0a523a" />
                      <stop offset="100%" fill-opacity="1" stop-color="#2e86c1" />
                    </linearGradient>
                  </defs>
                  <rect width="100%" height="100%" fill="url(#grad-akademik)" opacity="0.15"/>
                  <circle cx="160" cy="30" r="6" fill="#0fb87a" opacity="0.6"/>
                  <circle cx="40" cy="80" r="10" fill="#2e86c1" opacity="0.3"/>
                  <path d="M60 75h80v10H60z" fill="#0a523a" opacity="0.4" rx="2"/>
                  <path d="M55 80h90v8H55z" fill="#0a523a" opacity="0.6" rx="2"/>
                  <path d="M100 70c-15-5-35 0-35 0v-30s20-5 35 0c15-5 35 0 35 0v30s-20-5-35 0Z" fill="#fff" stroke="#0a523a" stroke-width="2" stroke-linejoin="round"/>
                  <path d="M100 40v30" stroke="#0a523a" stroke-width="1.5" stroke-dasharray="2 2"/>
                  <path d="M72 48h16M72 54h12M72 60h16M112 48h16M112 54h12M112 60h16" stroke="#cde5d7" stroke-width="2" stroke-linecap="round"/>
                  <path d="M90 28l10-4 10 4-10 4-10-4Z" fill="#0a523a"/>
                  <path d="M94 29.5v5c0 1.5 1.5 2.5 6 2.5s6-1 6-2.5v-5" fill="#0a523a"/>
                  <path d="M106 28v6l3 1.5" fill="none" stroke="#2e86c1" stroke-width="1"/>
                </svg>';
            } 
            // 2. Intrapersonal
            elseif (strpos($category, 'intrapersonal') !== false) {
                return '
                <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; object-fit:cover; display:block;">
                  <defs>
                    <linearGradient id="grad-intra" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#0fb87a" />
                      <stop offset="100%" stop-color="#2e86c1" />
                    </linearGradient>
                  </defs>
                  <rect width="100%" height="100%" fill="url(#grad-intra)" opacity="0.12"/>
                  <path d="M85 85c0-15-5-22 5-35 8-10 22-10 30-2 8 8 5 22 5 22s5 3 5 10c0 5-5 5-5 5H90" fill="#fff" opacity="0.5" stroke="#0a523a" stroke-width="2"/>
                  <path d="M105 45c0-10 5-15 15-20" stroke="#0fb87a" stroke-width="2" stroke-linecap="round"/>
                  <path d="M120 25c2-2 5 0 5 4s-3 5-5 4z" fill="#0fb87a"/>
                  <path d="M110 32c-5-5-5-12 0-16" stroke="#2e86c1" stroke-width="1.5" stroke-linecap="round"/>
                  <path d="M110 16c-2-2-4 0-4 3s2 3 4 2z" fill="#2e86c1"/>
                  <path d="M70 30l2 2 2-2-2-2zM140 50l1.5 1.5 1.5-1.5-1.5-1.5z" fill="#0fb87a"/>
                  <path d="M100 68c-2-2-5-2-7 0s-2 5 0 7l7 7 7-7c2-2 2-5 0-7s-5-2-7 0Z" fill="#0a523a"/>
                </svg>';
            } 
            // 3. Kehidupan di Kampus
            elseif (strpos($category, 'kampus') !== false) {
                return '
                <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; object-fit:cover; display:block;">
                  <defs>
                    <linearGradient id="grad-kampus" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#315743" />
                      <stop offset="100%" stop-color="#6e8d78" />
                    </linearGradient>
                  </defs>
                  <rect width="100%" height="100%" fill="url(#grad-kampus)" opacity="0.12"/>
                  <circle cx="150" cy="40" r="18" fill="#e8f8ef" stroke="#0fb87a" stroke-width="1" stroke-dasharray="3 3"/>
                  <circle cx="150" cy="40" r="12" fill="#0fb87a" opacity="0.2"/>
                  <rect x="50" y="55" width="45" height="35" rx="3" fill="#fff" stroke="#315743" stroke-width="2"/>
                  <rect x="95" y="45" width="35" height="45" rx="3" fill="#fff" stroke="#315743" stroke-width="2"/>
                  <polygon points="95,45 112.5,30 130,45" fill="#315743" opacity="0.8"/>
                  <rect x="60" y="65" width="8" height="8" rx="1" fill="#eff8f2"/>
                  <rect x="77" y="65" width="8" height="8" rx="1" fill="#eff8f2"/>
                  <rect x="108" y="55" width="10" height="12" rx="1" fill="#eff8f2"/>
                  <path d="M30 95c30-5 50-10 65-15" stroke="#0a523a" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 4"/>
                </svg>';
            } 
            // 4. Keluarga
            elseif (strpos($category, 'keluarga') !== false) {
                return '
                <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; object-fit:cover; display:block;">
                  <defs>
                    <linearGradient id="grad-keluarga" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#0a523a" />
                      <stop offset="100%" stop-color="#f5fbf7" />
                    </linearGradient>
                  </defs>
                  <rect width="100%" height="100%" fill="url(#grad-keluarga)" opacity="0.14"/>
                  <path d="M140 25c-3-3-8-3-11 0s-3 8 0 11l11 11 11-11c3-3 3-8 0-11s-8-3-11 0Z" fill="#0fb87a" opacity="0.7"/>
                  <path d="M60 40c-2-2-5-2-7 0s-2 5 0 7l7 7 7-7c2-2 2-5 0-7s-5-2-7 0Z" fill="#2e86c1" opacity="0.5"/>
                  <path d="M70 85V60l30-20 30 20v25H70Z" fill="#fff" stroke="#0a523a" stroke-width="2"/>
                  <path d="M90 85V70h20v15H90Z" fill="#0a523a"/>
                  <circle cx="100" cy="52" r="6" fill="#e8f8ef" stroke="#0a523a" stroke-width="1.5"/>
                </svg>';
            } 
            // 5. Masalah di Asrama
            elseif (strpos($category, 'asrama') !== false) {
                return '
                <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; object-fit:cover; display:block;">
                  <defs>
                    <linearGradient id="grad-asrama" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#2e86c1" />
                      <stop offset="100%" stop-color="#eff8f2" />
                    </linearGradient>
                  </defs>
                  <rect width="100%" height="100%" fill="url(#grad-asrama)" opacity="0.12"/>
                  <rect x="55" y="40" width="80" height="45" rx="2" fill="none" stroke="#2e86c1" stroke-width="2"/>
                  <line x1="55" y1="62" x2="135" y2="62" stroke="#2e86c1" stroke-width="1.5"/>
                  <rect x="60" y="52" width="14" height="8" rx="1" fill="#fff" stroke="#2e86c1" stroke-width="1"/>
                  <rect x="60" y="74" width="14" height="8" rx="1" fill="#fff" stroke="#2e86c1" stroke-width="1"/>
                  <path d="M80 56h50a5 5 0 0 1 5 5v1h-55v-6Z" fill="#0a523a" opacity="0.4"/>
                  <path d="M80 78h50a5 5 0 0 1 5 5v1h-55v-6Z" fill="#2e86c1" opacity="0.4"/>
                  <line x1="115" y1="40" x2="115" y2="85" stroke="#2e86c1" stroke-width="1.5"/>
                  <line x1="115" y1="50" x2="135" y2="50" stroke="#2e86c1" stroke-width="1.5"/>
                  <line x1="115" y1="62" x2="135" y2="62" stroke="#2e86c1" stroke-width="1.5"/>
                  <line x1="115" y1="73" x2="135" y2="73" stroke="#2e86c1" stroke-width="1.5"/>
                </svg>';
            } 
            // 6. Relasi (Default)
            else {
                return '
                <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; object-fit:cover; display:block;">
                  <defs>
                    <linearGradient id="grad-relasi" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#0fb87a" />
                      <stop offset="100%" stop-color="#315743" />
                    </linearGradient>
                  </defs>
                  <rect width="100%" height="100%" fill="url(#grad-relasi)" opacity="0.12"/>
                  <path d="M55 40h45c6 0 10 4 10 10v18c0 6-4 10-10 10H85l-12 12v-12H55c-6 0-10-4-10-10V50c0-6 4-10 10-10Z" fill="#fff" stroke="#0fb87a" stroke-width="2"/>
                  <path d="M100 50h45c6 0 10 4 10 10v18c0 6-4 10-10 10h-25l-12 12v-12h-8" fill="none" stroke="#0a523a" stroke-width="2" stroke-linecap="round"/>
                  <path d="M100 48c-2-2-5-2-7 0s-2 5 0 7l7 7 7-7c2-2 2-5 0-7s-5-2-7 0Z" fill="#0fb87a"/>
                  <circle cx="115" cy="62" r="2" fill="#0a523a"/>
                  <circle cx="127" cy="62" r="2" fill="#0a523a"/>
                </svg>';
            }
        }
    }
@endphp

<div class="edu-page">

    {{-- 1. BANNER PEMBUKA --}}
    <section class="edu-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h1 class="edu-title">
                        Kesehatan mental sama pentingnya dengan <span>kesehatan fisik.</span>
                    </h1>

                    <p class="edu-desc">
                        Ruang edukasi ini membantu mahasiswa memahami berbagai topik konseling,
                        mulai dari akademik, intrapersonal, kehidupan kampus, keluarga, asrama,
                        hingga relasi sosial.
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="edu-hero-card">
                        <div class="edu-hero-visual">
                            <div class="edu-hero-icon">
                                <i class="bi bi-chat-heart"></i>
                            </div>

                            <div class="edu-floating-note">
                                Kamu tidak harus menunggu semuanya terasa berat untuk mulai bercerita.
                                Mencari bantuan adalah langkah yang berani.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. TOPIK UTAMA KONSELING --}}
    <section class="edu-section pt-0">
        <div class="container">
            <div class="edu-section-head">
                <h2 class="edu-section-title">Topik Utama Konseling</h2>
                <p class="edu-section-desc">
                    Pilih topik yang sesuai dengan kondisi yang sedang kamu alami.
                    Artikel dan video yang berkaitan dengan topik tersebut akan langsung disaring dan ditampilkan di bawah.
                </p>
            </div>

            <span class="edu-mobile-swipe-hint">
                <i class="bi bi-arrow-left-right"></i>
                Geser untuk melihat topik lainnya
            </span>

            <div class="row g-4 edu-topic-scroll">
                @foreach($topics as $topic)
                    <div class="col-md-6 col-lg-4">
                        <article class="edu-topic-card" data-topic-card="{{ $topic['title'] }}">
                            <div class="edu-topic-icon">
                                <i class="bi {{ $topic['icon'] }}"></i>
                            </div>

                            <h3>{{ $topic['title'] }}</h3>
                            <p>{{ $topic['desc'] }}</p>

                            <span class="edu-click-note">
                                Lihat Konten
                            </span>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 3. KONTEN EDUKASI --}}
    <section class="edu-section pt-0" id="konten-edukasi">
        <div class="container">
            <div class="edu-section-head d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4">
                <div>
                    <h2 class="edu-section-title" id="contentSectionTitle">Artikel & Video Edukasi</h2>
                    <p class="edu-section-desc" id="contentSectionDesc">
                        Kumpulan konten singkat yang bisa membantu mahasiswa memahami kondisi mental
                        dan langkah sederhana untuk menjaga kesejahteraan diri.
                    </p>
                    <span class="edu-mobile-swipe-hint">
                        <i class="bi bi-arrow-left-right"></i>
                        Geser untuk melihat konten lainnya
                    </span>
                </div>
                
                {{-- TAB FILTER (Pemisah Artikel & Video) --}}
                <div class="edu-tabs-wrapper">
                    <div class="edu-tabs-container">
                        <button type="button" class="edu-tab-btn active" data-tab-filter="semua">
                            <i class="bi bi-grid-fill"></i>
                            <span>Semua</span>
                        </button>
                        <button type="button" class="edu-tab-btn" data-tab-filter="Artikel">
                            <i class="bi bi-file-text-fill"></i>
                            <span>Artikel</span>
                        </button>
                        <button type="button" class="edu-tab-btn" data-tab-filter="Video">
                            <i class="bi bi-play-btn-fill"></i>
                            <span>Video</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="edu-content-carousel" id="contentGrid">
                
                {{-- State Kosong / Empty State (Akan muncul jika pencarian/filter tidak menghasilkan konten) --}}
                <div class="edu-no-content" id="noContentMessage" style="display: none;">
                    <div class="edu-no-content-icon">
                        <i class="bi bi-folder-x"></i>
                    </div>
                    <h3>Belum Ada Konten</h3>
                    <p>Maaf, saat ini belum ada artikel atau video edukasi untuk topik ini. Silakan pilih kategori atau filter lain.</p>
                </div>

                @foreach($contents as $content)
                    <div class="edu-content-item {{ $loop->index < 6 ? 'edu-content-item-active' : '' }}"
                         data-topic-item="{{ $content['category'] }}"
                         data-content-type="{{ $content['type'] }}"
                         data-content-page="{{ intdiv($loop->index, 6) + 1 }}"
                         data-content-slot="{{ ($loop->index % 6) + 1 }}"
                         aria-hidden="{{ $loop->index < 6 ? 'false' : 'true' }}">

                        <article
                            class="edu-content-card edu-content-button"
                            role="button"
                            tabindex="0"
                            data-content-card
                            data-content='@json($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'>

                                <div class="edu-content-media">
                                    <span class="edu-content-type">
                                        @if($content['type'] === 'Video')
                                            <i class="bi bi-play-fill me-1"></i>
                                        @else
                                            <i class="bi bi-file-text-fill me-1"></i>
                                        @endif
                                        {{ $content['type'] }}
                                    </span>

                                    @if(!empty($content['thumbnail']))
                                        <img src="{{ $content['thumbnail'] }}"
                                             alt="{{ $content['title'] }}"
                                             class="edu-content-img">
                                    @else
                                        <div class="edu-content-fallback">
                                            {!! getIllustrativeSvg($content['category']) !!}
                                        </div>
                                    @endif
                                </div>

                                <div class="edu-content-body">
                                    <div class="edu-content-meta">
                                        <span class="edu-chip">{{ $content['category'] }}</span>
                                    </div>

                                    <h3>{{ $content['title'] }}</h3>
                                    <p>{{ $content['desc'] }}</p>

                                    <div class="edu-action-row">
                                        <span class="edu-action-btn">
                                            {{ $content['type'] === 'Video' ? 'Tonton Video' : 'Baca Artikel' }}
                                        </span>

                                        @if(!empty($content['material_url']))
                                            <button type="button"
                                                class="edu-action-secondary"
                                                data-pdf-url="{{ $content['material_url'] }}"
                                                data-pdf-title="{{ $content['title'] }}"
                                                data-pdf-link>
                                                <i class="bi bi-file-earmark-pdf"></i>
                                                Lihat PDF
                                            </button>
                                        @endif
                                    </div>
                                </div>

                        </article>

                    </div>
                @endforeach

            </div>

            <nav class="edu-content-pagination" id="contentPagination" aria-label="Pagination konten edukasi" hidden>
                <button type="button" class="edu-pagination-btn" id="contentPrevPage" aria-label="Halaman sebelumnya">
                    <i class="bi bi-chevron-left" aria-hidden="true"></i>
                </button>
                <span class="edu-pagination-status" id="contentPaginationStatus" aria-live="polite"></span>
                <button type="button" class="edu-pagination-btn" id="contentNextPage" aria-label="Halaman berikutnya">
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </button>
            </nav>

            {{-- POPUP KONTEN --}}
            <div class="edu-content-modal" id="contentModal" aria-hidden="true">
                <div class="edu-content-modal-dialog" role="dialog" aria-modal="true" aria-label="Konten edukasi">
                    <button type="button" class="edu-content-modal-close" id="closeContentModal" aria-label="Tutup konten">
                        <i class="bi bi-x-lg"></i>
                    </button>

                    <div class="edu-content-modal-body" id="contentModalBody"></div>
                </div>
            </div>

            <div class="edu-note">
                <strong>Catatan:</strong> Konten edukasi ini bersifat informatif dan bukan pengganti
                diagnosis profesional. Jika kondisi terasa berat atau mengganggu aktivitas harian,
                mahasiswa disarankan untuk menghubungi konselor.
            </div>
        </div>
    </section>

    {{-- 4. PANDUAN KONSELING --}}
    <section class="edu-section pt-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <article class="edu-guide-card">
                        <div class="edu-guide-top">
                            <div class="edu-guide-icon">
                                <i class="bi bi-chat-square-heart"></i>
                            </div>

                            <div>
                                <h3>Kapan Sebaiknya Konseling?</h3>
                                <p>
                                    Konseling dapat membantu ketika pikiran atau perasaanmu mulai mengganggu
                                    aktivitas, hubungan, tidur, atau proses belajar.
                                </p>
                            </div>
                        </div>

                        <ul class="edu-guide-list">
                            <li>Merasa sedih, cemas, atau kewalahan dalam waktu lama.</li>
                            <li>Sulit tidur, fokus, atau menjalankan aktivitas sehari-hari.</li>
                            <li>Menghadapi masalah yang terasa sulit diselesaikan sendiri.</li>
                            <li>Membutuhkan ruang aman untuk bercerita tanpa dihakimi.</li>
                        </ul>

                        <div class="edu-emergency-action">
                            <a href="{{ route('konseling') }}" class="edu-btn-primary">
                                Buat Jadwal Konseling
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="edu-guide-card warning">
                        <div class="edu-guide-top">
                            <div class="edu-guide-icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>

                            <div>
                                <h3>Butuh Bantuan Sekarang?</h3>
                                <p>
                                    Jika kamu merasa tidak aman atau berisiko menyakiti diri sendiri maupun orang
                                    lain, jangan menghadapinya sendirian.
                                </p>
                            </div>
                        </div>

                        <ul class="edu-guide-list">
                            <li>Hubungi orang terdekat yang kamu percaya.</li>
                            <li>Hubungi konselor atau pihak kampus.</li>
                            <li>Pergi ke tempat yang aman and jangan menyendiri.</li>
                            <li>Hubungi layanan darurat terdekat apabila diperlukan.</li>
                        </ul>

                        <div class="edu-emergency-action">
                            <a
                                href="https://wa.me/"
                                class="edu-btn-primary"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                <i class="bi bi-whatsapp"></i>
                                Hubungi Bantuan Sekarang
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const topicCards = document.querySelectorAll('[data-topic-card]');
        const topicItems = document.querySelectorAll('[data-topic-item]');
        const contentSection = document.getElementById('konten-edukasi');
        const contentGrid = document.getElementById('contentGrid');
        const contentPagination = document.getElementById('contentPagination');
        const contentPaginationStatus = document.getElementById('contentPaginationStatus');
        const contentPrevPage = document.getElementById('contentPrevPage');
        const contentNextPage = document.getElementById('contentNextPage');
        
        // Simpan semua item konten ke array utama
        const allContentItems = Array.from(topicItems);
        const desktopContentPerPage = 6;
        const mobileCarouselMedia = window.matchMedia('(max-width: 767.98px)');
        
        // State Filter dan Pagination Gabungan
        let currentContentPage = 1;
        let currentTypeFilter = 'semua'; // 'semua', 'Artikel', 'Video'
        let activeTopic = null; // Menyimpan topik terpilih saat ini

        const tabButtons = document.querySelectorAll('[data-tab-filter]');
        const contentModal = document.getElementById('contentModal');
        const contentModalBody = document.getElementById('contentModalBody');
        const closeContentModal = document.getElementById('closeContentModal');
        let modalCloseTimer = null;
        let activeContentCard = null;
        let activeModalMode = 'video';

        if (contentModal && contentModal.parentElement !== document.body) {
            document.body.appendChild(contentModal);
        }

        // --- COMBINED FILTER & PAGINATION ENGINE ---

        // Dapatkan item yang aktif berdasarkan gabungan filter Topik + Tab saat ini
        function getActiveFilteredItems() {
            return allContentItems.filter(function (item) {
                // 1. Filter Kategori Topik
                const matchesTopic = !activeTopic || item.getAttribute('data-topic-item') === activeTopic;
                
                // 2. Filter Jenis Konten (Tab)
                const matchesType = currentTypeFilter === 'semua' || item.getAttribute('data-content-type') === currentTypeFilter;
                
                return matchesTopic && matchesType;
            });
        }

        // Hitung total halaman berdasarkan jumlah item tersaring saat ini
        function getTotalContentPages() {
            const activeItems = getActiveFilteredItems();
            const contentPerPage = mobileCarouselMedia.matches
                ? Math.max(activeItems.length, 1)
                : desktopContentPerPage;

            return Math.max(1, Math.ceil(activeItems.length / contentPerPage));
        }

        // Atur ulang data-content-page dan data-content-slot secara dinamis untuk item tersaring
        function updateFilteredLayout() {
            const activeItems = getActiveFilteredItems();
            const contentPerPage = mobileCarouselMedia.matches
                ? Math.max(activeItems.length, 1)
                : desktopContentPerPage;

            allContentItems.forEach(function (item) {
                item.removeAttribute('data-content-page');
                item.removeAttribute('data-content-slot');
                
                // Sembunyikan item yang tidak lolos filter
                if (!activeItems.includes(item)) {
                    item.classList.remove('edu-content-item-active', 'edu-content-item-entering', 'edu-content-item-leaving');
                    item.setAttribute('aria-hidden', 'true');
                    const card = item.querySelector('[data-content-card]');
                    if (card) card.tabIndex = -1;
                }
            });

            // Susun posisi item yang lolos filter agar tidak bercelah di grid
            activeItems.forEach(function (item, index) {
                const page = Math.floor(index / contentPerPage) + 1;
                const slot = (index % contentPerPage) + 1;
                item.setAttribute('data-content-page', page);
                item.setAttribute('data-content-slot', slot);
            });
        }

        function clearSelectedContent() {
            document.querySelectorAll('.edu-content-card').forEach(function (card) {
                card.classList.remove('edu-content-selected');
            });
        }

        function renderContentPagination() {
            if (!contentPagination) return;

            const totalPages = getTotalContentPages();

            if (totalPages <= 1) {
                contentPagination.hidden = true;
                return;
            }

            contentPagination.hidden = false;
            updateContentPaginationState();
        }

        function updateContentPaginationState() {
            const activeItems = getActiveFilteredItems();
            const totalPages = getTotalContentPages();
            const noContentMessage = document.getElementById('noContentMessage');

            // Tampilkan Empty State jika tidak ada konten sama sekali
            if (activeItems.length === 0) {
                if (noContentMessage) noContentMessage.style.display = 'block';
                if (contentPagination) contentPagination.hidden = true;
                return;
            } else {
                if (noContentMessage) noContentMessage.style.display = 'none';
            }

            if (contentPaginationStatus) {
                contentPaginationStatus.textContent = currentContentPage + ' / ' + totalPages;
                contentPaginationStatus.setAttribute(
                    'aria-label',
                    'Halaman ' + currentContentPage + ' dari ' + totalPages
                );
            }

            if (contentPrevPage) {
                contentPrevPage.disabled = currentContentPage <= 1;
            }

            if (contentNextPage) {
                contentNextPage.disabled = currentContentPage >= totalPages;
            }
        }

        function applyContentPage() {
            const activeItems = getActiveFilteredItems();
            
            allContentItems.forEach(function (item) {
                const isVisible = activeItems.includes(item) && Number(item.getAttribute('data-content-page')) === currentContentPage;
                const card = item.querySelector('[data-content-card]');

                item.classList.toggle('edu-content-item-active', isVisible);
                item.classList.remove('edu-content-item-entering', 'edu-content-item-leaving');
                item.style.removeProperty('--edu-slide-shift');
                item.setAttribute('aria-hidden', isVisible ? 'false' : 'true');

                if (card) {
                    card.tabIndex = isVisible ? 0 : -1;
                }
            });

            updateContentPaginationState();
        }

        function setContentPage(page) {
            const totalPages = getTotalContentPages();
            const nextPage = Math.min(Math.max(page, 1), totalPages);

            if (nextPage === currentContentPage) {
                updateContentPaginationState();
                return;
            }

            const previousPage = currentContentPage;
            const direction = nextPage > previousPage ? 1 : -1;
            
            const activeItems = getActiveFilteredItems();
            const previousItems = activeItems.filter(function (item) {
                return Number(item.getAttribute('data-content-page')) === previousPage;
            });
            const nextItems = activeItems.filter(function (item) {
                return Number(item.getAttribute('data-content-page')) === nextPage;
            });

            currentContentPage = nextPage;

            previousItems.forEach(function (item) {
                const card = item.querySelector('[data-content-card]');
                item.style.setProperty('--edu-slide-shift', (-64 * direction) + 'px');
                item.classList.remove('edu-content-item-active');
                item.classList.add('edu-content-item-leaving');
                item.setAttribute('aria-hidden', 'true');
                if (card) card.tabIndex = -1;
            });

            nextItems.forEach(function (item) {
                const card = item.querySelector('[data-content-card]');
                item.style.setProperty('--edu-slide-shift', (64 * direction) + 'px');
                item.classList.remove('edu-content-item-leaving');
                item.classList.add('edu-content-item-entering');
                item.setAttribute('aria-hidden', 'false');
                if (card) card.tabIndex = 0;
            });

            if (contentGrid) {
                void contentGrid.offsetWidth;
            }

            requestAnimationFrame(function () {
                nextItems.forEach(function (item) {
                    item.classList.add('edu-content-item-active');
                    item.classList.remove('edu-content-item-entering');
                    item.style.removeProperty('--edu-slide-shift');
                });
            });

            updateContentPaginationState();

            window.setTimeout(function () {
                previousItems.forEach(function (item) {
                    item.classList.remove('edu-content-item-leaving');
                    item.style.removeProperty('--edu-slide-shift');
                });
            }, 420);
        }

        // --- TOPIC FILTER LOGIC (DIKLIK LANGSUNG MENYARING GRID) ---

        function highlightTopic(topic) {
            const isAlreadyActive = Array.from(topicCards).some(function(card){
                return card.classList.contains('active') && card.getAttribute('data-topic-card') === topic;
            });

            if (isAlreadyActive) {
                resetTopicHighlight();
                return;
            }

            activeTopic = topic;

            // Beri tanda aktif pada kartu topik yang dipilih
            topicCards.forEach(function (card) {
                card.classList.toggle('active', card.getAttribute('data-topic-card') === topic);
            });

            // Reset ke halaman 1 dan saring ulang grid konten
            currentContentPage = 1;
            updateFilteredLayout();
            applyContentPage();
            renderContentPagination();

            if (contentGrid) {
                contentGrid.scrollTo({ left: 0, behavior: 'smooth' });
            }

            // Scroll ke area konten agar pengguna langsung melihat hasil saringan
            if (contentSection) {
                contentSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        function resetTopicHighlight() {
            topicCards.forEach(function (card) {
                card.classList.remove('active');
            });

            activeTopic = null;

            currentContentPage = 1;
            updateFilteredLayout();
            applyContentPage();
            renderContentPagination();

            if (contentGrid) {
                contentGrid.scrollTo({ left: 0, behavior: 'smooth' });
            }
        }

        // --- TAB FILTERS EVENT LISTENERS ---

        tabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                const filterValue = button.getAttribute('data-tab-filter');
                
                if (filterValue === currentTypeFilter) return;

                // Update kelas aktif pada tombol tab
                tabButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                currentTypeFilter = filterValue;
                currentContentPage = 1;

                // Bangun ulang struktur grid dan pagination
                updateFilteredLayout();
                applyContentPage();
                renderContentPagination();

                if (contentGrid) {
                    contentGrid.scrollTo({ left: 0, behavior: 'smooth' });
                }
            });
        });

        // --- MODAL & POPUP LOGIC ---

        function closeModal() {
            if (contentModal) {
                contentModal.classList.remove('show');
                contentModal.setAttribute('aria-hidden', 'true');
                contentModal.removeAttribute('data-content-mode');
            }

            window.clearTimeout(modalCloseTimer);
            modalCloseTimer = window.setTimeout(function () {
                if (contentModalBody) {
                    contentModalBody.innerHTML = '';
                }

                activeContentCard = null;
                activeModalMode = 'video';
                clearSelectedContent();
            }, 280);
        }

        function renderVideoContent(data) {
            if (!contentModalBody) return;

            if (!data.embed_url || !data.embed_type) {
                renderArticleContent({
                    title: data.title || 'Video Edukasi',
                    detail: 'Video belum dapat ditampilkan di Campus Care.'
                });
                return;
            }

            const embedBox = document.createElement('div');
            embedBox.className = 'edu-content-modal-embed';

            if (data.embed_type === 'video') {
                const video = document.createElement('video');
                video.controls = true;
                video.playsInline = true;
                video.preload = 'metadata';
                video.src = data.embed_url;
                embedBox.appendChild(video);
            } else {
                const iframe = document.createElement('iframe');
                iframe.src = data.embed_url;
                iframe.title = data.title || 'Video edukasi';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                iframe.setAttribute('scrolling', 'no');
                iframe.referrerPolicy = 'strict-origin-when-cross-origin';
                embedBox.appendChild(iframe);
            }

            contentModalBody.appendChild(embedBox);

        }

        function renderArticleContent(data) {
            if (!contentModalBody) return;

            const article = document.createElement('article');
            article.className = 'edu-content-modal-article';

            const title = document.createElement('h3');
            title.id = 'contentModalTitle';
            title.textContent = data.title || 'Artikel Edukasi';

            const body = document.createElement('p');
            body.textContent = data.detail || data.desc || 'Artikel sedang dilengkapi.';

            article.appendChild(title);
            article.appendChild(body);
            contentModalBody.appendChild(article);
        }

        function renderPdfContent(url, title) {
            if (!contentModalBody) return;

            const material = document.createElement('section');
            material.className = 'edu-content-detail-material';

            const heading = document.createElement('h4');
            heading.textContent = 'Materi Pendukung';

            const pdfBox = document.createElement('div');
            pdfBox.className = 'edu-content-modal-pdf';

            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.title = title || 'Materi PDF';

            pdfBox.appendChild(iframe);
            material.appendChild(heading);
            material.appendChild(pdfBox);
            contentModalBody.appendChild(material);
        }

        function positionContentModal(selectedCard, mode) {
            if (!contentModal || !selectedCard) return;

            const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const margin = 16;
            const maxByWidth = viewportWidth - (margin * 2);
            const maxByHeight = mode === 'pdf'
                ? maxByWidth
                : (viewportHeight - (margin * 2)) * (16 / 9);
            const targetWidth = viewportWidth <= 767
                ? maxByWidth
                : (mode === 'pdf' ? Math.min(980, viewportWidth * 0.78) : Math.min(1120, viewportWidth * 0.74));
            const width = Math.min(targetWidth, maxByWidth, maxByHeight);

            contentModal.style.setProperty('--edu-modal-left', '50%');
            contentModal.style.setProperty('--edu-modal-width', width + 'px');
        }

        function openContentModal(data, selectedCard) {
            if (!contentModal || !contentModalBody) return;

            window.clearTimeout(modalCloseTimer);
            contentModalBody.innerHTML = '';
            clearSelectedContent();

            if (selectedCard) {
                activeContentCard = selectedCard;
                selectedCard.classList.add('edu-content-selected');
            }

            activeModalMode = data.type === 'Video' ? 'video' : 'article';
            contentModal.setAttribute('data-content-mode', activeModalMode);
            positionContentModal(selectedCard, activeModalMode);

            if (data.type === 'Video') {
                renderVideoContent(data);
            } else {
                renderArticleContent(data);
            }

            contentModal.classList.add('show');
            contentModal.setAttribute('aria-hidden', 'false');
            contentModal.scrollTop = 0;
            contentModalBody.scrollTop = 0;
        }

        function openPdfModal(url, title, selectedCard) {
            if (!contentModal || !contentModalBody || !url) return;

            window.clearTimeout(modalCloseTimer);
            contentModalBody.innerHTML = '';
            clearSelectedContent();

            if (selectedCard) {
                activeContentCard = selectedCard;
                selectedCard.classList.add('edu-content-selected');
            }

            contentModal.setAttribute('data-content-mode', 'pdf');
            positionContentModal(selectedCard, 'pdf');
            renderPdfContent(url, title);

            activeModalMode = 'pdf';
            contentModal.classList.add('show');
            contentModal.setAttribute('aria-hidden', 'false');
            contentModal.scrollTop = 0;
            contentModalBody.scrollTop = 0;
        }

        // --- ATTACH EVENTS ---

        topicCards.forEach(function (card) {
            card.addEventListener('click', function () {
                const selectedTopic = card.getAttribute('data-topic-card');
                highlightTopic(selectedTopic);
            });
        });

        if (contentPrevPage) {
            contentPrevPage.addEventListener('click', function () {
                setContentPage(currentContentPage - 1);
            });
        }

        if (contentNextPage) {
            contentNextPage.addEventListener('click', function () {
                setContentPage(currentContentPage + 1);
            });
        }

        // Inisialisasi awal struktur grid
        updateFilteredLayout();
        applyContentPage();
        renderContentPagination();

        document.querySelectorAll('[data-content-card]').forEach(function (card) {
            card.addEventListener('click', function (event) {
                const pdfButton = event.target.closest('[data-pdf-link]');

                if (pdfButton) {
                    openPdfModal(
                        pdfButton.getAttribute('data-pdf-url'),
                        pdfButton.getAttribute('data-pdf-title'),
                        card
                    );
                    return;
                }

                const data = JSON.parse(card.getAttribute('data-content'));

                if (data.type === 'Artikel' && data.source_url) {
                    window.open(data.source_url, '_blank', 'noopener,noreferrer');
                    return;
                }

                openContentModal(data, card);
            });

            card.addEventListener('keydown', function (event) {
                if (event.target.closest('[data-pdf-link]')) return;

                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    const data = JSON.parse(card.getAttribute('data-content'));

                    if (data.type === 'Artikel' && data.source_url) {
                        window.open(data.source_url, '_blank', 'noopener,noreferrer');
                        return;
                    }

                    openContentModal(data, card);
                }
            });
        });

        if (closeContentModal) {
            closeContentModal.addEventListener('click', closeModal);
        }

        if (contentModal) {
            contentModal.addEventListener('click', function (event) {
                if (event.target === contentModal) {
                    closeModal();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && contentModal && contentModal.classList.contains('show')) {
                closeModal();
            }
        });

        window.addEventListener('resize', function () {
            if (contentModal && contentModal.classList.contains('show') && activeContentCard) {
                positionContentModal(activeContentCard, activeModalMode);
            }
        });

        const refreshCarouselMode = function () {
            currentContentPage = 1;
            updateFilteredLayout();
            applyContentPage();
            renderContentPagination();

            if (contentGrid) {
                contentGrid.scrollLeft = 0;
            }
        };

        if (typeof mobileCarouselMedia.addEventListener === 'function') {
            mobileCarouselMedia.addEventListener('change', refreshCarouselMode);
        } else {
            mobileCarouselMedia.addListener(refreshCarouselMode);
        }
    });
</script>
@endpush
