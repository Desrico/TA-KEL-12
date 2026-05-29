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

    .edu-inline-detail{
        display:none;
        margin-top:2rem;
        background:#fff;
        border:1px solid rgba(10,82,58,.12);
        border-radius:28px;
        box-shadow:0 24px 50px rgba(13,27,42,.10);
        overflow:hidden;
    }

    .edu-inline-detail.show{
        display:block;
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

    {{-- 3. ARTIKEL & VIDEO EDUKASI --}}
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

        @if(!empty($content['source_url']))
            <a href="{{ $content['source_url'] }}"
               target="_blank"
               rel="noopener noreferrer"
               class="edu-content-card edu-content-link">
        @else
            <button
                type="button"
                class="edu-content-card edu-content-button"
                data-content-card
                data-content='@json($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'>
        @endif

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

                    <span class="edu-action-btn">
                        {{ $content['type'] === 'Video' ? 'Tonton Video' : 'Baca Artikel' }}
                    </span>
                </div>

        @if(!empty($content['source_url']))
            </a>
        @else
            </button>
        @endif

    </div>
@endforeach

            </div>

            {{-- DETAIL INLINE --}}
            <div class="edu-inline-detail" id="contentInlineDetail">
                <div class="edu-inline-detail-header">
                    <div class="edu-inline-detail-main">
                        <div class="edu-inline-detail-icon">
                            <i id="inlineContentIcon" class="bi bi-file-earmark-text"></i>
                        </div>

                        <div>
                            <div class="edu-content-meta mb-2">
                                <span class="edu-chip" id="inlineContentCategory"></span>
                                <span class="edu-chip" id="inlineContentType"></span>
                                <span class="edu-chip" id="inlineContentTime"></span>
                            </div>

                            <h3 class="edu-inline-detail-title" id="inlineContentTitle"></h3>
                            <p class="edu-inline-detail-desc" id="inlineContentDesc"></p>
                        </div>
                    </div>

                    <button type="button" class="edu-inline-close" id="closeInlineDetail" aria-label="Tutup detail">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="edu-inline-detail-body">
                    <div class="edu-inline-detail-grid">
                        <div class="edu-inline-box">
                            <h5>Penjelasan</h5>
                            <p id="inlineContentDetail"></p>
                        </div>

                        <div class="edu-inline-box">
                            <h5>Poin Penting</h5>
                            <ul class="edu-inline-list" id="inlineContentPoints"></ul>
                        </div>
                    </div>

                    <div class="edu-inline-action">
                        <a href="{{ route('konseling') }}" class="edu-btn-primary">
                            Butuh Konseling?
                            <i class="bi bi-calendar-check"></i>
                        </a>

                        <button type="button" class="edu-btn-outline" id="closeInlineDetailBottom">
                            Tutup Detail
                        </button>
                    </div>
                </div>
            </div>

            <div class="edu-note">
                <strong>Catatan:</strong> Konten edukasi ini bersifat informatif dan bukan pengganti
                diagnosis profesional. Jika kondisi terasa berat atau mengganggu aktivitas harian,
                mahasiswa disarankan untuk menghubungi konselor.
            </div>
        </div>
    </section>

    {{-- 4. KAPAN HARUS KONSELING & BANTUAN DARURAT --}}
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

        const inlineDetail = document.getElementById('contentInlineDetail');
        const closeInlineDetail = document.getElementById('closeInlineDetail');
        const closeInlineDetailBottom = document.getElementById('closeInlineDetailBottom');

        function renderList(target, items) {
            target.innerHTML = '';

            if (!items || items.length === 0) {
                const li = document.createElement('li');
                li.textContent = 'Belum ada informasi.';
                target.appendChild(li);
                return;
            }

            items.forEach(function (item) {
                const li = document.createElement('li');
                li.textContent = item;
                target.appendChild(li);
            });
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

        function hideInlineDetail() {
            if (inlineDetail) {
                inlineDetail.classList.remove('show');
            }
            clearSelectedContent();
        }

        function showInlineDetail(data, selectedCard) {
            document.getElementById('inlineContentIcon').className = 'bi ' + data.icon;
            document.getElementById('inlineContentCategory').textContent = data.category;
            document.getElementById('inlineContentType').textContent = data.type;
            document.getElementById('inlineContentTime').textContent = data.time;
            document.getElementById('inlineContentTitle').textContent = data.title;
            document.getElementById('inlineContentDesc').textContent = data.desc;
            document.getElementById('inlineContentDetail').textContent = data.detail;

            renderList(document.getElementById('inlineContentPoints'), data.points);

            clearSelectedContent();

            if (selectedCard) {
                selectedCard.classList.add('edu-content-selected');
            }

            if (inlineDetail) {
                inlineDetail.classList.add('show');

                setTimeout(function () {
                    inlineDetail.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 120);
            }
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
            card.addEventListener('click', function () {
                const data = JSON.parse(card.getAttribute('data-content'));

                if (data.source_url && data.source_url.trim() !== '') {
                    window.open(data.source_url, '_blank');
                    return;
                }

                showInlineDetail(data, card);
            });
        });
        
        if (closeInlineDetail) {
            closeInlineDetail.addEventListener('click', hideInlineDetail);
        }

        if (closeInlineDetailBottom) {
            closeInlineDetailBottom.addEventListener('click', hideInlineDetail);
        }
    });
</script>
@endpush