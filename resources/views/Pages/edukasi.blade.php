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
            radial-gradient(circle at 22% 20%, rgba(255,255,255,.9) 0 0, rgba(255,255,255,.9) 58px, transparent 60px),
            linear-gradient(135deg,#d9f1e6 0%,#f5fbf7 50%,#cde5d7 100%);
        display:flex;
        align-items:center;
        justify-content:center;
        position:relative;
        overflow:hidden;
    }

    .edu-hero-icon{
        width:140px;
        height:140px;
        border-radius:42px;
        background:linear-gradient(135deg,#315743,#6e8d78);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:4rem;
        box-shadow:0 22px 40px rgba(49,87,67,.25);
    }

    .edu-floating-note{
        position:absolute;
        left:1.2rem;
        right:1.2rem;
        bottom:1.2rem;
        background:rgba(255,255,255,.85);
        border:1px solid rgba(255,255,255,.85);
        backdrop-filter:blur(12px);
        border-radius:20px;
        padding:1rem;
        color:#315743;
        font-weight:700;
        line-height:1.6;
    }

    .edu-section{
        padding:4.5rem 0;
    }

    .edu-section.pt-0{
        padding-top:0;
    }

    .edu-section-head{
        max-width:760px;
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
        transition:.25s ease;
    }

    .edu-topic-card:hover,
    .edu-content-card:hover,
    .edu-guide-card:hover{
        transform: translateY(-4px);
    box-shadow: 0 16px 36px rgba(21, 71, 52, 0.12);
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
        min-height:165px;
        background: #edf7f1;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#0a523a;
        font-size:3.1rem;
        position:relative;
        width:100%;
        overflow: hidden;
        height: 220px;
    }

    .edu-content-type{
        position:absolute;
        top:14px;
        left:14px;
        z-index:2;
        display:inline-flex;
        align-items:center;
        gap:.4rem;
        border-radius:999px;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 700;
        background:#fff;
        color: #146c43;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.08);
    }

    .edu-content-body {
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
}

.edu-action-row{
    display:flex;
    flex-wrap:wrap;
    gap:.65rem;
    align-items:center;
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
}

.edu-action-secondary:hover{
    color:#0a523a;
    background:#dff3e8;
    text-decoration:none;
}

    .edu-content-selected{
        border-color:rgba(10,82,58,.55) !important;
        box-shadow:0 24px 50px rgba(10,82,58,.18) !important;
        transform:translateY(-6px);
    }

    .edu-content-highlight{
        border-color:rgba(10,82,58,.45) !important;
        box-shadow:0 22px 45px rgba(10,82,58,.14) !important;
    }

    .edu-content-highlight .edu-content-media{
        background:linear-gradient(135deg,#c8f3dc,#f7fbf8);
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
        padding:1.6rem;
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
        min-height: 190px;
        object-fit: cover;
        display: block;
    }

    .edu-content-fallback {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #edf7f1;
    }

    .edu-content-fallback i {
        font-size: 56px;
        color: #146c43;
    }


    @media(max-width:991.98px){
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
    }

    @media(max-width:767.98px){
        .edu-section{
            padding:3.5rem 0;
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
            font-size:2.4rem;
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

@endphp

<div class="edu-page">

    {{-- 1. BANNER PEMBUKA --}}
    <section class="edu-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="edu-badge">
                        <i class="bi bi-heart-pulse"></i>
                        Edukasi Mental
                    </div>

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
                    Artikel atau video yang berkaitan dengan topik tersebut akan diberi highlight.
                </p>
            </div>

            <div class="row g-4">
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
                                <!-- <i class="bi bi-arrow-down"></i> -->
                            </span>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

  
    <section class="edu-section pt-0" id="konten-edukasi">
        <div class="container">
            <div class="edu-section-head">
                <h2 class="edu-section-title" id="contentSectionTitle">Artikel & Video Edukasi</h2>
                <p class="edu-section-desc" id="contentSectionDesc">
                    Kumpulan konten singkat yang bisa membantu mahasiswa memahami kondisi mental
                    dan langkah sederhana untuk menjaga kesejahteraan diri.
                </p>
            </div>

            <div class="row g-4" id="contentGrid">
             @foreach($contents as $content)
    <div class="col-md-6 col-lg-4" data-topic-item="{{ $content['category'] }}">

        <article
            class="edu-content-card edu-content-button"
            role="button"
            tabindex="0"
            data-content-card
            data-content='@json($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'>

                <div class="edu-content-media">
                    <span class="edu-content-type">
                        {{ $content['type'] }}
                    </span>

                    @if(!empty($content['thumbnail']))
                        <img src="{{ $content['thumbnail'] }}"
                             alt="{{ $content['title'] }}"
                             class="edu-content-img">
                    @else
                        <div class="edu-content-fallback">
                            <i class="bi {{ $content['icon'] }}"></i>
                        </div>
                    @endif
                </div>

                <div class="edu-content-body">
                    <div class="edu-content-meta">
                        <span class="edu-chip">{{ $content['category'] }}</span>
                        <span class="edu-chip">{{ $content['time'] }}</span>
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
                                <h3>Kapan Harus Konseling?</h3>
                                <p>
                                    Kamu bisa mempertimbangkan konseling ketika kondisi yang dirasakan mulai
                                    mengganggu aktivitas, relasi, tidur, fokus belajar, atau membuatmu merasa
                                    kesulitan menghadapinya sendiri.
                                </p>
                            </div>
                        </div>

                        <ul class="edu-guide-list">
                            <li>Gejala terasa berat atau berlangsung cukup lama.</li>
                            <li>Sulit tidur, sulit fokus, atau kehilangan minat beraktivitas.</li>
                            <li>Sering merasa kewalahan, sedih, cemas, atau mudah panik.</li>
                            <li>Butuh ruang aman untuk bercerita tanpa takut dihakimi.</li>
                        </ul>
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="edu-guide-card warning">
                        <div class="edu-guide-top">
                            <div class="edu-guide-icon">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>

                            <div>
                                <h3>Bantuan Darurat / Butuh Bantuan Sekarang</h3>
                                <p>
                                    Jika kamu merasa berada dalam kondisi darurat, segera hubungi orang terdekat,
                                    pihak kampus, atau layanan bantuan profesional yang tersedia di daerahmu.
                                </p>
                            </div>
                        </div>

                        <ul class="edu-guide-list">
                            <li>Hubungi konselor kampus jika membutuhkan pendampingan.</li>
                            <li>Hubungi teman, keluarga, atau dosen wali yang kamu percaya.</li>
                            <li>Jika membahayakan diri sendiri atau orang lain, segera cari bantuan darurat terdekat.</li>
                        </ul>

                        <div class="edu-emergency-action">
                            <a href="{{ route('konseling') }}" class="edu-btn-primary">
                                Buat Jadwal Konseling
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
        const contentTitle = document.getElementById('contentSectionTitle');
        const contentDesc = document.getElementById('contentSectionDesc');
        const contentSection = document.getElementById('konten-edukasi');

        const contentModal = document.getElementById('contentModal');
        const contentModalBody = document.getElementById('contentModalBody');
        const closeContentModal = document.getElementById('closeContentModal');
        let modalCloseTimer = null;
        let activeContentCard = null;
        let activeModalMode = 'video';

        if (contentModal && contentModal.parentElement !== document.body) {
            document.body.appendChild(contentModal);
        }

        function clearSelectedContent() {
            document.querySelectorAll('.edu-content-card').forEach(function (card) {
                card.classList.remove('edu-content-selected');
            });
        }

        function resetTopicHighlight() {
            topicCards.forEach(function (card) {
                card.classList.remove('active');
            });

            topicItems.forEach(function (item) {
                const contentCard = item.querySelector('.edu-content-card');
                if (contentCard) {
                    contentCard.classList.remove('edu-content-highlight');
                }
            });

            if (contentTitle) {
                contentTitle.textContent = 'Artikel & Video Edukasi';
            }

            if (contentDesc) {
                contentDesc.textContent = 'Kumpulan konten singkat yang bisa membantu mahasiswa memahami kondisi mental dan langkah sederhana untuk menjaga kesejahteraan diri.';
            }
        }

        function closeModal() {
            if (contentModal) {
                contentModal.classList.remove('show');
                contentModal.setAttribute('aria-hidden', 'true');
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
            if (!contentModalBody) {
                return;
            }

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
                video.src = data.embed_url;
                embedBox.appendChild(video);
            } else {
                const iframe = document.createElement('iframe');
                iframe.src = data.embed_url;
                iframe.title = data.title || 'Video edukasi';
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                iframe.allowFullscreen = true;
                embedBox.appendChild(iframe);
            }

            contentModalBody.appendChild(embedBox);
        }

        function renderArticleContent(data) {
            if (!contentModalBody) {
                return;
            }

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
            if (!contentModalBody) {
                return;
            }

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
            if (!contentModal || !selectedCard) {
                return;
            }

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
            if (!contentModal || !contentModalBody) {
                return;
            }

            window.clearTimeout(modalCloseTimer);
            contentModalBody.innerHTML = '';
            clearSelectedContent();

            if (selectedCard) {
                activeContentCard = selectedCard;
                selectedCard.classList.add('edu-content-selected');
            }

            positionContentModal(selectedCard, data.type === 'Video' ? 'video' : 'article');

            if (data.type === 'Video') {
                renderVideoContent(data);
            } else {
                renderArticleContent(data);
            }

            activeModalMode = data.type === 'Video' ? 'video' : 'article';
            contentModal.classList.add('show');
            contentModal.setAttribute('aria-hidden', 'false');
            contentModal.scrollTop = 0;
            contentModalBody.scrollTop = 0;
        }

        function openPdfModal(url, title, selectedCard) {
            if (!contentModal || !contentModalBody || !url) {
                return;
            }

            window.clearTimeout(modalCloseTimer);
            contentModalBody.innerHTML = '';
            clearSelectedContent();

            if (selectedCard) {
                activeContentCard = selectedCard;
                selectedCard.classList.add('edu-content-selected');
            }

            positionContentModal(selectedCard, 'pdf');
            renderPdfContent(url, title);

            activeModalMode = 'pdf';
            contentModal.classList.add('show');
            contentModal.setAttribute('aria-hidden', 'false');
            contentModal.scrollTop = 0;
            contentModalBody.scrollTop = 0;
        }

        function highlightTopic(topic) {
            const isAlreadyActive = Array.from(topicCards).some(function(card){
                return card.classList.contains('active') && card.getAttribute('data-topic-card') === topic;
            });

            if (isAlreadyActive) {
                resetTopicHighlight();
                return;
            }

            topicCards.forEach(function (card) {
                card.classList.toggle('active', card.getAttribute('data-topic-card') === topic);
            });

            topicItems.forEach(function (item) {
                const itemTopic = item.getAttribute('data-topic-item');
                const contentCard = item.querySelector('.edu-content-card');

                if (!contentCard) return;

                contentCard.classList.remove('edu-content-highlight');

                if (itemTopic === topic) {
                    contentCard.classList.add('edu-content-highlight');
                }
            });

            if (contentTitle) {
                contentTitle.textContent = 'Artikel & Video Edukasi';
            }

            if (contentDesc) {
                contentDesc.textContent = 'Artikel atau video terkait topik "' + topic + '" diberi highlight. Semua konten tetap ditampilkan agar mahasiswa tetap bisa mengeksplorasi topik lain.';
            }

            if (contentSection) {
                contentSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        topicCards.forEach(function (card) {
            card.addEventListener('click', function () {
                const selectedTopic = card.getAttribute('data-topic-card');
                highlightTopic(selectedTopic);
            });
        });

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
                if (event.target.closest('[data-pdf-link]')) {
                    return;
                }

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
    });
</script>
@endpush
