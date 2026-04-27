@extends('layouts.master')

@section('title', 'Tentang CampusCare')

@push('styles')
<style>
    .about-page{
        background: linear-gradient(180deg, #f8fbf8 0%, #f3f7f3 100%);
    }

    .about-hero{
        position: relative;
        padding: 5.5rem 0 4.5rem;
        overflow: hidden;
    }
    .about-hero::before{
        content:'';
        position:absolute;
        width:420px;height:420px;
        background: radial-gradient(circle, rgba(15,184,122,.12) 0%, rgba(15,184,122,0) 70%);
        top:-120px;left:-120px;border-radius:50%;
        pointer-events:none;
    }
    .about-hero::after{
        content:'';
        position:absolute;
        width:420px;height:420px;
        background: radial-gradient(circle, rgba(46,134,193,.10) 0%, rgba(46,134,193,0) 70%);
        right:-120px;top:0;border-radius:50%;
        pointer-events:none;
    }

    .hero-badge{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        background:#f8ddb3;
        color:#9a6a12;
        border-radius:999px;
        padding:.4rem .95rem;
        font-size:.72rem;
        font-weight:800;
        letter-spacing:.08em;
        text-transform:uppercase;
        margin-bottom:1rem;
    }

    .hero-title{
        font-size:clamp(2.4rem, 5vw, 4.7rem);
        font-weight:800;
        line-height:1.02;
        color:#202020;
        letter-spacing:0;
        margin-bottom:1.25rem;
    }
    .hero-title .accent{
        color:#0fb87a;
        font-style:italic;
    }

    .hero-desc{
        max-width:560px;
        font-size:1rem;
        line-height:1.9;
        color:var(--text-mid);
    }

    .hero-visual-wrap{
        position:relative;
        display:flex;
        justify-content:center;
        perspective:900px;
    }

    .hero-visual{
        position:relative;
        width:100%;
        max-width:460px;
        aspect-ratio:1 / 1;
        overflow:visible;
        isolation:isolate;
        background:
            radial-gradient(circle at 25% 18%, rgba(255,255,255,.84) 0 0, rgba(255,255,255,.84) 72px, transparent 73px),
            linear-gradient(135deg,#d8e7dc 0%,#eef6f0 52%,#c8d8cd 100%);
        border:1px solid rgba(96,122,105,.18);
        border-radius:28px;
        padding:2rem;
        box-shadow:0 22px 55px rgba(35,60,49,.14);
        transition:transform .28s ease, box-shadow .28s ease;
    }

    .hero-visual::before,
    .hero-visual::after{
        content:'';
        position:absolute;
        border-radius:50%;
        pointer-events:none;
    }

    .hero-visual::before{
        width:230px;
        height:230px;
        right:-82px;
        top:-70px;
        border:1px solid rgba(49,87,67,.14);
        z-index:-1;
    }

    .hero-visual::after{
        width:180px;
        height:180px;
        left:-58px;
        bottom:-54px;
        background:rgba(255,255,255,.28);
        z-index:-1;
    }

    .hero-visual:hover{
        transform:translateY(-4px) rotateX(1.2deg) rotateY(-1.2deg);
        box-shadow:0 28px 68px rgba(35,60,49,.18);
    }

    .hero-logo-stage{
        position:relative;
        z-index:1;
        height:100%;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .hero-logo-ring{
        position:relative;
        width:min(78%, 300px);
        aspect-ratio:1 / 1;
        border-radius:50%;
        border:1px solid rgba(49,87,67,.18);
        background:rgba(255,255,255,.34);
        display:flex;
        align-items:center;
        justify-content:center;
        box-shadow:inset 0 0 0 28px rgba(255,255,255,.14);
        transition:transform .3s ease;
    }

    .hero-visual:hover .hero-logo-ring{
        transform:scale(1.025);
    }

    .hero-logo-core{
        width:138px;
        height:138px;
        border-radius:42px;
        background:linear-gradient(135deg,#315743,#6e8d78);
        color:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:4.1rem;
        box-shadow:0 22px 38px rgba(49,87,67,.24);
    }

    .hero-orbit{
        position:absolute;
        width:54px;
        height:54px;
        border-radius:50%;
        background:rgba(255,255,255,.78);
        color:#315743;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.25rem;
        box-shadow:0 12px 24px rgba(35,60,49,.12);
        transition:transform .25s ease;
    }

    .hero-orbit-1{top:6%;right:18%;}
    .hero-orbit-2{left:10%;bottom:20%;}
    .hero-orbit-3{right:10%;bottom:13%;}

    .hero-visual:hover .hero-orbit-1{transform:translate(4px,-5px);}
    .hero-visual:hover .hero-orbit-2{transform:translate(-5px,4px);}
    .hero-visual:hover .hero-orbit-3{transform:translate(5px,5px);}

    .hero-note-card{
        position:absolute;
        left:-1.5rem;
        bottom:1.25rem;
        z-index:10;
        max-width:280px;
        padding:1.1rem 1.25rem;
        border-radius:22px;
        background:linear-gradient(135deg,#98f0d6 0%,#b7f7e4 100%);
        color:#124f3d;
        box-shadow:0 18px 34px rgba(31,84,65,.18);
        border:1px solid rgba(255,255,255,.45);
        font-weight:800;
        font-size:1rem;
        line-height:1.55;
        transition:transform .25s ease, box-shadow .25s ease;
    }

    .hero-note-card::before{
        content:'';
        position:absolute;
        top:.9rem;
        right:1rem;
        width:34px;
        height:34px;
        border-radius:50%;
        background:rgba(255,255,255,.28);
    }

    .hero-note-card::after{
        content:'';
        position:absolute;
        right:1.7rem;
        bottom:-10px;
        width:20px;
        height:20px;
        background:#a7f4dd;
        transform:rotate(45deg);
        border-radius:4px;
    }

    .hero-note-card i{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:30px;
        height:30px;
        margin-bottom:.65rem;
        border-radius:50%;
        background:rgba(255,255,255,.45);
        color:#0d6a4b;
        font-size:1rem;
    }

    .hero-visual:hover .hero-note-card{
        transform:translateY(-5px);
        box-shadow:0 24px 44px rgba(31,84,65,.22);
    }

    .section-block{
        padding:5rem 0;
    }

    .section-title{
        font-size:clamp(2rem,3.5vw,3.2rem);
        font-weight:800;
        line-height:1.15;
        color:var(--text-dark);
        margin-bottom:.85rem;
        letter-spacing:0;
    }

    .section-desc{
        max-width:720px;
        margin:0 auto;
        color:var(--text-mid);
        line-height:1.8;
        font-size:.98rem;
    }

    .feedback-carousel{
        position:relative;
        max-width:1120px;
        margin:0 auto;
        padding:0 5rem;
    }

    .feedback-carousel .carousel-inner{
        overflow:hidden;
        padding:.25rem .15rem 1.25rem;
    }

    .feedback-carousel .carousel-item{
        transition:transform .7s ease-in-out;
    }

    .feedback-carousel .carousel-indicators{
        gap:.45rem;
    }

    .feedback-carousel .carousel-indicators [data-bs-target]{
        width:9px;
        height:9px;
        border:0;
        border-radius:50%;
        background:#9fb7ae;
        opacity:.5;
        margin:0;
    }

    .feedback-carousel .carousel-indicators .active{
        width:24px;
        border-radius:999px;
        background:#0fb87a;
        opacity:1;
    }

    .feedback-control{
        top:42%;
        width:48px;
        height:48px;
        border:0;
        border-radius:50%;
        background:#eef5ef;
        color:#0A523A;
        box-shadow:0 10px 24px rgba(35,60,49,.12);
        opacity:1;
        transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .feedback-control:hover{
        transform:translateY(-2px) scale(1.04);
        background:#dfeee4;
        color:#0A523A;
        box-shadow:0 15px 30px rgba(35,60,49,.16);
    }

    .feedback-control i{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:100%;
        height:100%;
        border-radius:50%;
        color:#315743;
        font-size:1.25rem;
    }

    .feedback-control-prev{left:.75rem;}
    .feedback-control-next{right:.75rem;}

    .testi-card{
        min-width:0;
        background:#fff;
        border-radius:16px;
        padding:1.6rem;
        box-shadow:0 10px 28px rgba(13,27,42,.08);
        border:1px solid rgba(26,58,92,.06);
        transition:transform .25s ease, box-shadow .25s ease;
        height:100%;
    }
    .testi-card:hover{
        transform:translateY(-6px);
        box-shadow:0 18px 40px rgba(13,27,42,.12);
    }

    .testi-top{
        display:flex;
        align-items:center;
        justify-content:space-between;
        margin-bottom:1rem;
    }

    .quote-badge{
        width:48px;
        height:48px;
        border-radius:50%;
        background:#dff7ec;
        color:#0fb87a;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.4rem;
        font-weight:800;
    }

    .testi-accent{
        width:54px;
        height:10px;
        border-radius:999px;
        background:rgba(15,184,122,.18);
    }

    .testi-text{
        color:#334155;
        line-height:1.9;
        font-size:.92rem;
        min-height:145px;
    }

    .testi-meta{
        border-top:1px solid rgba(13,27,42,.06);
        margin-top:1.2rem;
        padding-top:1rem;
    }

    .testi-name{
        font-weight:800;
        color:var(--primary);
        margin-bottom:.2rem;
    }

    .testi-role{
        font-size:.9rem;
        color:var(--text-light);
    }

    .trust-section{
        position:relative;
        background:#06130f;
        color:#fff;
        overflow:hidden;
        padding:5.5rem 0;
    }

    .trust-radar{
        position:relative;
        width:320px;
        height:320px;
        margin:auto;
    }
    .trust-ring,
    .trust-center{
        position:absolute;
        top:50%;
        left:50%;
        transform:translate(-50%,-50%);
        border-radius:50%;
    }
    .trust-ring.r1{width:80px;height:80px;border:1px solid rgba(15,184,122,.55);}
    .trust-ring.r2{width:150px;height:150px;border:1px solid rgba(15,184,122,.35);}
    .trust-ring.r3{width:220px;height:220px;border:1px solid rgba(15,184,122,.18);}
    .trust-center{
        width:86px;height:86px;
        background:linear-gradient(135deg,#0b7a4f,#0fb87a);
        display:flex;align-items:center;justify-content:center;
        box-shadow:0 0 30px rgba(15,184,122,.25);
        font-size:2rem;
    }

    .trust-wave{
        position:absolute;
        left:0;right:0;
        border-top:2px dashed rgba(255,255,255,.18);
    }
    .trust-wave.wave1{top:58%;}
    .trust-wave.wave2{top:65%;}

    .trust-title{
        font-size:clamp(2rem,4vw,4rem);
        font-weight:800;
        line-height:1.06;
        letter-spacing:0;
        color:#fff;
    }
    .trust-title .accent{
        color:#66f0cc;
    }

    .trust-desc{
        color:rgba(255,255,255,.72);
        line-height:1.9;
        margin-top:1.4rem;
        max-width:650px;
    }

    .trust-card{
        background:rgba(255,255,255,.05);
        border:1px solid rgba(255,255,255,.08);
        border-radius:20px;
        padding:1.2rem;
        backdrop-filter:blur(8px);
        height:100%;
    }

    .trust-card h5{
        color:#fff;
        font-size:1rem;
        font-weight:700;
        margin-bottom:.65rem;
    }

    .trust-card p{
        margin:0;
        color:rgba(255,255,255,.68);
        line-height:1.8;
        font-size:.92rem;
    }

    .flow-eyebrow{
        color:#c9972c;
        text-transform:uppercase;
        letter-spacing:.18em;
        font-size:.7rem;
        font-weight:800;
        margin-bottom:1rem;
    }

    .flow-card{
        text-align:center;
        padding:1rem 1rem 0;
    }

    .flow-icon{
        width:82px;
        height:82px;
        border-radius:50%;
        background:#fff;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:0 auto;
        position:relative;
        box-shadow:var(--shadow-sm);
        font-size:1.7rem;
    }
    .flow-icon.green{border:2px solid #0fb87a;}
    .flow-icon.gold{border:2px solid #b8860b;}
    .flow-icon.teal{border:2px solid #0f8d7f;}

    .flow-step{
        position:absolute;
        top:-4px;
        right:-4px;
        width:28px;
        height:28px;
        border-radius:50%;
        color:#fff;
        font-size:.75rem;
        font-weight:800;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .flow-step.green{background:#0A523A;}
    .flow-step.gold{background:#b8860b;}
    .flow-step.teal{background:#0f8d7f;}

    .flow-body{
        border-top:1px solid rgba(26,58,92,.12);
        margin-top:1.2rem;
        padding-top:1.2rem;
    }

    .flow-body h4{
        font-size:1.2rem;
        font-weight:800;
        color:var(--text-dark);
        margin-bottom:.6rem;
    }

    .flow-body p{
        margin:0;
        color:var(--text-mid);
        line-height:1.8;
        font-size:.94rem;
    }

    .cta-title{
        font-size:clamp(2rem,4vw,3.7rem);
        font-weight:800;
        line-height:1.12;
        letter-spacing:0;
        color:#202020;
    }
    .cta-title .accent{
        color:#0A523A;
    }

    .btn-delcare{
        background:#0A523A;
        color:#fff;
        border:none;
        border-radius:999px;
        padding:.95rem 1.9rem;
        font-weight:700;
        box-shadow:0 12px 25px rgba(10,82,58,.16);
        transition:all .25s ease;
        text-decoration:none;
        display:inline-flex;
        align-items:center;
        justify-content:center;
    }
    .btn-delcare:hover{
        transform:translateY(-2px);
        background:#0c6347;
        color:#fff;
    }

    .emergency-wrap{
        background:#c81d25;
        color:#fff;
        border-radius:28px;
        padding:1.7rem 1.6rem;
        box-shadow:0 20px 38px rgba(200,29,37,.18);
    }

    .emergency-icon{
        width:58px;
        height:58px;
        border-radius:50%;
        background:rgba(255,255,255,.15);
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.35rem;
        flex-shrink:0;
    }

    .emergency-title{
        font-size:1.75rem;
        font-weight:800;
        line-height:1.1;
        margin-bottom:.5rem;
    }

    .emergency-text{
        margin:0;
        color:rgba(255,255,255,.86);
        line-height:1.8;
        font-size:.96rem;
    }

    .emergency-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:.7rem;
        background:#fff;
        color:#c81d25;
        border-radius:999px;
        padding:1rem 1.5rem;
        font-weight:800;
        text-decoration:none;
        transition:all .25s ease;
        min-width:220px;
    }
    .emergency-btn:hover{
        color:#b8171f;
        transform:translateY(-2px);
        background:#fff6f6;
    }

    @media (max-width: 991.98px){
        .about-hero{
            padding-top:4.25rem;
        }
        .hero-visual{
            margin-top:2rem;
        }
        .trust-radar{
            width:260px;
            height:260px;
            margin-bottom:2rem;
        }
    }

    @media (max-width: 767.98px){
        .section-block{
            padding:4rem 0;
        }
        .feedback-carousel{
            padding:0;
        }
        .feedback-carousel .carousel-inner{
            padding-bottom:1rem;
        }
        .feedback-control{
            display:none;
        }
        .hero-visual{
            padding:.75rem;
            border-radius:20px;
        }
        .hero-note-card{
            position:relative;
            left:auto;
            bottom:auto;
            max-width:none;
            margin-top:.85rem;
            border-radius:18px;
            font-size:.92rem;
        }
        .hero-note-card::after{
            display:none;
        }
        .hero-logo-core{
            width:112px;
            height:112px;
            border-radius:34px;
            font-size:3.2rem;
        }
        .hero-orbit{
            width:44px;
            height:44px;
            font-size:1.05rem;
        }
        .testi-text{
            min-height:auto;
        }
        .emergency-title{
            font-size:1.45rem;
        }
        .emergency-btn{
            width:100%;
        }
    }
</style>
@endpush

@section('konten')
<div class="about-page">

    {{-- HERO --}}
    <section class="about-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="hero-badge">Tentang Campus Care</div>
                    <h1 class="hero-title">
                        Ruang <span class="accent">Konseling</span><br>Mahasiswa IT Del.
                    </h1>
                    <p class="hero-desc">
                        Campus Care hadir untuk membantu mahasiswa menemukan ruang cerita yang aman,
                        terarah, dan mudah dijangkau. Setiap proses pendampingan dirancang agar mahasiswa
                        dapat memahami kondisi diri dan mengambil langkah berikutnya dengan lebih tenang.
                    </p>
                </div>

                <div class="col-lg-6">
                    <div class="hero-visual-wrap">
                        <div class="hero-visual" role="img" aria-label="Ilustrasi logo layanan psikologi Campus Care">
                            <div class="hero-logo-stage">
                                <div class="hero-logo-ring">
                                    <div class="hero-logo-core">
                                        <i class="bi bi-chat-heart"></i>
                                    </div>
                                    <span class="hero-orbit hero-orbit-1" aria-hidden="true">
                                        <i class="bi bi-shield-check"></i>
                                    </span>
                                    <span class="hero-orbit hero-orbit-2" aria-hidden="true">
                                        <i class="bi bi-heart-pulse"></i>
                                    </span>
                                    <span class="hero-orbit hero-orbit-3" aria-hidden="true">
                                        <i class="bi bi-person-check"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="hero-note-card">
                                <i class="bi bi-stars" aria-hidden="true"></i>
                                <div>Mendukung perjalanan mental Anda melalui kurasi kinetik.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TESTIMONIAL --}}
    <section class="section-block">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Cerita Pengguna Campus Care</h2>
                <p class="section-desc">
                    Cerita singkat dari mahasiswa yang merasakan ruang aman, pendampingan,
                    dan proses bertumbuh bersama layanan bimbingan dan konseling.
                </p>
            </div>

            @php
                $feedbackStories = collect($feedbacks ?? [
                    [
                        'id' => 1,
                        'sesi_id' => null,
                        'mahasiswa_id' => null,
                        'isi_feedback' => 'Awalnya saya ragu untuk bercerita. Setelah mencoba membuat jadwal, prosesnya terasa lebih ringan dan saya jadi lebih siap membuka diri.',
                        'created_at' => null,
                        'updated_at' => null,
                        'nama' => 'Mahasiswa',
                        'keterangan' => 'Semester 5',
                    ],
                    [
                        'id' => 2,
                        'sesi_id' => null,
                        'mahasiswa_id' => null,
                        'isi_feedback' => 'Saya terbantu karena konselor mendengarkan tanpa menghakimi. Saya bisa membahas tekanan kuliah dengan lebih tenang.',
                        'created_at' => null,
                        'updated_at' => null,
                        'nama' => 'Mahasiswa',
                        'keterangan' => 'Semester 7',
                    ],
                    [
                        'id' => 3,
                        'sesi_id' => null,
                        'mahasiswa_id' => null,
                        'isi_feedback' => 'Mood tracker membantu saya melihat pola emosi harian. Dari sana saya lebih mudah menjelaskan kondisi saat sesi.',
                        'created_at' => null,
                        'updated_at' => null,
                        'nama' => 'Mahasiswa',
                        'keterangan' => 'Semester 3',
                    ],
                    [
                        'id' => 4,
                        'sesi_id' => null,
                        'mahasiswa_id' => null,
                        'isi_feedback' => 'Informasi alur konseling yang jelas membuat saya lebih berani memilih jadwal dan datang ke sesi.',
                        'created_at' => null,
                        'updated_at' => null,
                        'nama' => 'Mahasiswa',
                        'keterangan' => 'Semester 2',
                    ],
                    [
                        'id' => 5,
                        'sesi_id' => null,
                        'mahasiswa_id' => null,
                        'isi_feedback' => 'Mode anonim memberi saya waktu untuk merasa aman dulu. Setelah itu, saya lebih percaya diri melanjutkan proses konseling.',
                        'created_at' => null,
                        'updated_at' => null,
                        'nama' => 'Mahasiswa',
                        'keterangan' => 'Semester 4',
                    ],
                    [
                        'id' => 6,
                        'sesi_id' => null,
                        'mahasiswa_id' => null,
                        'isi_feedback' => 'Saya merasa lebih tertata karena jadwal dan riwayat layanan bisa dipantau dari akun.',
                        'created_at' => null,
                        'updated_at' => null,
                        'nama' => 'Mahasiswa',
                        'keterangan' => 'Semester 6',
                    ],
                ])->filter(fn ($feedback) => filled(data_get($feedback, 'isi_feedback')));

                $feedbackChunks = $feedbackStories->chunk(3);
            @endphp

            @if($feedbackStories->isNotEmpty())
                <div id="feedbackCarousel" class="carousel slide feedback-carousel" data-bs-ride="carousel" data-bs-interval="4500" data-bs-touch="true" data-bs-pause="hover">
                    <div class="carousel-inner">
                        @foreach($feedbackChunks as $feedbackChunk)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <div class="row g-4 justify-content-center">
                                    @foreach($feedbackChunk as $feedback)
                                        @php
                                            $feedbackText = data_get($feedback, 'isi_feedback');
                                            $feedbackName = data_get($feedback, 'nama') ?? 'Mahasiswa';
                                            $feedbackRole = data_get($feedback, 'keterangan')
                                                ?? data_get($feedback, 'mahasiswa.jurusan')
                                                ?? 'Pengguna Campus Care';
                                        @endphp

                                        <div class="col-md-6 col-lg-4">
                                            <article
                                                class="testi-card"
                                                data-feedback-id="{{ data_get($feedback, 'id') }}"
                                                data-sesi-id="{{ data_get($feedback, 'sesi_id') }}"
                                                data-mahasiswa-id="{{ data_get($feedback, 'mahasiswa_id') }}"
                                                data-created-at="{{ data_get($feedback, 'created_at') }}"
                                                data-updated-at="{{ data_get($feedback, 'updated_at') }}">
                                                <div class="testi-top">
                                                    <div class="quote-badge"><i class="bi bi-quote"></i></div>
                                                    <div class="testi-accent"></div>
                                                </div>
                                                <p class="testi-text mb-0">
                                                    "{{ $feedbackText }}"
                                                </p>
                                                <div class="testi-meta">
                                                    <div class="testi-name">{{ $feedbackName }}</div>
                                                    <div class="testi-role">{{ $feedbackRole }}</div>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($feedbackChunks->count() > 1)
                        <button class="carousel-control-prev feedback-control feedback-control-prev" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="prev" aria-label="Cerita sebelumnya">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="carousel-control-next feedback-control feedback-control-next" type="button" data-bs-target="#feedbackCarousel" data-bs-slide="next" aria-label="Cerita berikutnya">
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <div class="carousel-indicators position-static mt-4 mb-0">
                            @foreach($feedbackChunks as $feedbackChunk)
                                <button
                                    type="button"
                                    data-bs-target="#feedbackCarousel"
                                    data-bs-slide-to="{{ $loop->index }}"
                                    class="{{ $loop->first ? 'active' : '' }}"
                                    aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                    aria-label="Cerita {{ $loop->iteration }}">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="alert alert-light border text-center mb-0" role="status">
                    Belum ada cerita pengguna yang ditampilkan.
                </div>
            @endif
        </div>
    </section>

    {{-- TRUST / SECURITY --}}
    <section class="trust-section">
        <div class="trust-wave wave1 d-none d-lg-block"></div>
        <div class="trust-wave wave2 d-none d-lg-block"></div>

        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-5 text-center">
                    <div class="trust-radar">
                        <div class="trust-ring r1"></div>
                        <div class="trust-ring r2"></div>
                        <div class="trust-ring r3"></div>
                        <div class="trust-center"><i class="bi bi-shield-check"></i></div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <h2 class="trust-title">
                        Tempat <span class="accent">Perlindungan</span><br>
                        <span class="accent">Digital</span> Anda.
                    </h2>

                    <p class="trust-desc">
                        Keamanan data bukan sekadar fitur, melainkan janji. Di DelCare,
                        setiap kata yang Anda bagikan disimpan dengan pendekatan privasi yang serius
                        agar Anda dapat bercerita dengan lebih tenang.
                    </p>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="trust-card">
                                <h5><i class="bi bi-lock me-2"></i>Perlindungan Data</h5>
                                <p>
                                    Data dan komunikasi dilindungi untuk menjaga privasi sesi konseling
                                    dan catatan personal pengguna.
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="trust-card">
                                <h5><i class="bi bi-folder-check me-2"></i>Kontrol Riwayat</h5>
                                <p>
                                    Riwayat pendampingan disusun secara terbatas dan terkontrol
                                    agar akses tetap aman dan relevan.
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="trust-card">
                                <h5><i class="bi bi-check-circle me-2"></i>Alur yang Aman</h5>
                                <p>
                                    Setiap alur dirancang untuk mendukung kenyamanan emosional pengguna,
                                    mulai dari akses layanan hingga pengelolaan data.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FLOW --}}
    <section class="section-block">
        <div class="container">
            <div class="text-center mb-5">
                <div class="flow-eyebrow">Alur Layanan</div>
                <h2 class="section-title">Alur Pendampingan Terintegrasi</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="flow-card">
                        <div class="flow-icon green">
                            <i class="bi bi-person"></i>
                            <div class="flow-step green">1</div>
                        </div>
                        <div class="flow-body">
                            <h4>Mahasiswa</h4>
                            <p>
                                Mulai perjalanan dengan mood tracker, refleksi mandiri,
                                dan pengajuan sesi sesuai kebutuhan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="flow-card">
                        <div class="flow-icon gold">
                            <i class="bi bi-briefcase"></i>
                            <div class="flow-step gold">2</div>
                        </div>
                        <div class="flow-body">
                            <h4>Sistem &amp; Konselor</h4>
                            <p>
                                Sistem membantu membaca tren emosional agar konselor
                                dapat memberikan respons yang lebih tepat sasaran.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="flow-card">
                        <div class="flow-icon teal">
                            <i class="bi bi-bank"></i>
                            <div class="flow-step teal">3</div>
                        </div>
                        <div class="flow-body">
                            <h4>Dukungan Kampus</h4>
                            <p>
                                Kolaborasi aktif dengan pihak kampus untuk menciptakan
                                lingkungan akademik yang lebih suportif.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section-block pt-0">
        <div class="container text-center">
            <h2 class="cta-title">
                Siap memulai<br>
                <span class="accent">konseling</span> dengan tenang?
            </h2>
            <br><br>
            <div class="mt-4">
                <a href="{{ route('konseling') }}" class="btn-delcare">
                    Mulai Konseling
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
