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

    .relax-player{
        max-width:920px;
        margin:0 auto;
    }

    .relax-player-shell{
        position:relative;
        min-height:460px;
        border-radius:34px;
        overflow:hidden;
        background:linear-gradient(180deg,#dfeef2 0%, #9ab8c8 34%, #19374d 76%, #102739 100%);
        box-shadow:0 30px 60px rgba(12,31,43,.18);
        isolation:isolate;
    }

    .relax-player-shell::before{
        content:'';
        position:absolute;
        inset:0;
        background:
            radial-gradient(circle at 18% 16%, rgba(255,255,255,.72) 0, rgba(255,255,255,0) 26%),
            radial-gradient(circle at 70% 10%, rgba(255,255,255,.34) 0, rgba(255,255,255,0) 24%);
        opacity:.8;
        z-index:0;
    }

    .relax-scene{
        position:absolute;
        inset:0;
        overflow:hidden;
    }

    .relax-scene-frame{
        position:absolute;
        inset:0;
        opacity:0;
        transition:opacity 1.2s ease;
    }

    .relax-scene-frame.is-active{
        opacity:1;
    }

    .relax-scene-frame::before{
        content:'';
        position:absolute;
        inset:0;
    }

    .relax-scene-frame.frame-dawn::before{
        background:
            linear-gradient(180deg, rgba(247,224,203,.92) 0%, rgba(235,205,181,.52) 18%, rgba(87,129,159,.18) 54%, rgba(14,39,57,0) 100%);
    }

    .relax-scene-frame.frame-noon::before{
        background:
            linear-gradient(180deg, rgba(218,243,249,.9) 0%, rgba(149,198,223,.34) 26%, rgba(58,99,127,.12) 60%, rgba(14,39,57,0) 100%);
    }

    .relax-scene-frame.frame-night::before{
        background:
            linear-gradient(180deg, rgba(15,32,55,.92) 0%, rgba(26,59,88,.38) 28%, rgba(16,39,57,.16) 58%, rgba(14,39,57,0) 100%);
    }

    .relax-stars{
        position:absolute;
        inset:0;
        opacity:0;
        transition:opacity 1s ease;
        pointer-events:none;
        background-image:
            radial-gradient(circle at 18% 22%, rgba(255,255,255,.7) 0 1px, transparent 2px),
            radial-gradient(circle at 62% 18%, rgba(255,255,255,.62) 0 1px, transparent 2px),
            radial-gradient(circle at 78% 28%, rgba(255,255,255,.75) 0 1.2px, transparent 2px),
            radial-gradient(circle at 38% 16%, rgba(255,255,255,.58) 0 1px, transparent 2px),
            radial-gradient(circle at 86% 15%, rgba(255,255,255,.72) 0 1px, transparent 2px);
        animation:twinkleStars 6s ease-in-out infinite;
    }

    .relax-player[data-scene="night"] .relax-stars{
        opacity:1;
    }

    .relax-orb,
    .relax-ripple,
    .relax-ripple::before,
    .relax-ripple::after,
    .relax-cloud,
    .relax-bird,
    .relax-firefly{
        animation-play-state:paused;
    }

    .relax-player.is-playing .relax-orb,
    .relax-player.is-playing .relax-ripple,
    .relax-player.is-playing .relax-ripple::before,
    .relax-player.is-playing .relax-ripple::after,
    .relax-player.is-playing .relax-cloud,
    .relax-player.is-playing .relax-bird,
    .relax-player.is-playing .relax-firefly{
        animation-play-state:running;
    }

    .relax-orb{
        position:absolute;
        top:14%;
        left:50%;
        width:170px;
        height:170px;
        transform:translateX(-50%);
        border-radius:50%;
        background:radial-gradient(circle, rgba(255,255,255,.92) 0%, rgba(215,241,245,.92) 52%, rgba(215,241,245,0) 74%);
        filter:blur(.2px);
        animation:breatheOrb 8s ease-in-out infinite;
        transition:background 1.2s ease, box-shadow 1.2s ease, opacity 1.2s ease;
    }

    .relax-player[data-scene="dawn"] .relax-orb{
        background:radial-gradient(circle, rgba(255,242,226,.96) 0%, rgba(255,212,173,.88) 52%, rgba(255,213,179,0) 76%);
    }

    .relax-player[data-scene="noon"] .relax-orb{
        background:radial-gradient(circle, rgba(255,255,255,.94) 0%, rgba(215,241,245,.9) 52%, rgba(215,241,245,0) 74%);
    }

    .relax-player[data-scene="night"] .relax-orb{
        background:radial-gradient(circle, rgba(248,248,255,.92) 0%, rgba(200,215,255,.8) 48%, rgba(200,215,255,0) 72%);
    }

    .relax-cloud{
        position:absolute;
        height:26px;
        border-radius:999px;
        background:rgba(255,255,255,.22);
        filter:blur(1px);
        animation:driftCloud 14s linear infinite;
    }
    .relax-cloud.c1{top:19%;left:16%;width:140px;}
    .relax-cloud.c2{top:24%;right:14%;width:110px;animation-duration:18s;}

    .relax-bird{
        position:absolute;
        width:26px;
        height:12px;
        border-top:2px solid rgba(255,255,255,.52);
        border-radius:50% 50% 0 0;
        animation:birdDrift 16s linear infinite;
        opacity:.75;
    }

    .relax-bird::after{
        content:'';
        position:absolute;
        right:-16px;
        top:-2px;
        width:26px;
        height:12px;
        border-top:2px solid rgba(255,255,255,.52);
        border-radius:50% 50% 0 0;
    }

    .relax-bird.b1{top:28%;left:18%;}
    .relax-bird.b2{top:21%;left:64%;transform:scale(.8);animation-duration:19s;}

    .relax-mountain{
        position:absolute;
        bottom:36%;
        width:58%;
        height:40%;
        background:linear-gradient(180deg, rgba(17,41,55,.78) 0%, rgba(10,24,33,.98) 100%);
        clip-path:polygon(0 100%, 17% 60%, 28% 66%, 49% 20%, 63% 45%, 79% 31%, 100% 100%);
    }
    .relax-mountain.left{left:-3%;}
    .relax-mountain.right{
        right:-6%;
        height:37%;
        width:56%;
        opacity:.92;
        clip-path:polygon(0 100%, 16% 54%, 33% 69%, 51% 33%, 69% 48%, 82% 25%, 100% 100%);
    }

    .relax-waterline{
        position:absolute;
        left:0;
        right:0;
        bottom:33%;
        height:2px;
        background:linear-gradient(90deg, transparent 0%, rgba(255,255,255,.58) 18%, rgba(255,255,255,.78) 50%, rgba(255,255,255,.58) 82%, transparent 100%);
        box-shadow:0 0 24px rgba(255,255,255,.24);
    }

    .relax-reflection{
        position:absolute;
        left:0;
        right:0;
        bottom:0;
        height:41%;
        background:
            linear-gradient(180deg, rgba(169,202,217,.28) 0%, rgba(20,47,66,.72) 24%, rgba(10,28,40,.96) 100%);
        transform:scaleY(-1);
        opacity:.9;
    }

    .relax-firefly{
        position:absolute;
        width:8px;
        height:8px;
        border-radius:50%;
        background:rgba(189,255,229,.85);
        box-shadow:0 0 14px rgba(189,255,229,.72);
        opacity:0;
        animation:fireflyFloat 8s ease-in-out infinite;
    }

    .relax-firefly.f1{left:18%;bottom:20%;}
    .relax-firefly.f2{left:72%;bottom:23%;animation-duration:10s;}
    .relax-firefly.f3{left:58%;bottom:16%;animation-duration:7s;}

    .relax-player[data-scene="night"] .relax-firefly{
        opacity:1;
    }

    .relax-ripple{
        position:absolute;
        left:50%;
        bottom:16%;
        width:220px;
        height:220px;
        transform:translateX(-50%);
        border-radius:50%;
        border:1px solid rgba(255,255,255,.18);
        animation:ripplePulse 7s ease-out infinite;
    }

    .relax-ripple::before,
    .relax-ripple::after{
        content:'';
        position:absolute;
        inset:22px;
        border-radius:50%;
        border:1px solid rgba(255,255,255,.14);
        animation:ripplePulse 7s ease-out infinite;
    }

    .relax-ripple::after{
        inset:50px;
        animation-duration:9s;
    }

    .relax-overlay{
        position:relative;
        z-index:2;
        height:100%;
        min-height:460px;
        padding:1.4rem 1.5rem 2.2rem;
        display:grid;
        grid-template-rows:auto 1fr auto;
    }

    .relax-topbar{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:1rem;
    }

    .relax-pill{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        padding:.55rem 1rem;
        border-radius:999px;
        background:rgba(255,255,255,.18);
        color:#fff;
        font-size:.78rem;
        font-weight:700;
        letter-spacing:.08em;
        text-transform:uppercase;
        backdrop-filter:blur(12px);
    }

    .relax-status{
        display:inline-flex;
        align-items:center;
        gap:.45rem;
        padding:.55rem .9rem;
        border-radius:999px;
        background:rgba(7,28,40,.24);
        color:rgba(255,255,255,.9);
        font-size:.78rem;
        font-weight:700;
        backdrop-filter:blur(12px);
    }

    .relax-status i{
        color:#b4f4df;
    }

    .relax-center{
        position:absolute;
        inset:0;
        display:flex;
        align-items:center;
        justify-content:center;
        z-index:3;
    }

    .relax-toggle{
        width:108px;
        height:108px;
        border:0;
        border-radius:50%;
        background:rgba(255,255,255,.18);
        color:#fff;
        backdrop-filter:blur(16px);
        box-shadow:0 20px 34px rgba(4,19,30,.24);
        transition:transform .25s ease, background .25s ease, box-shadow .25s ease;
        position:relative;
        display:flex;
        align-items:center;
        justify-content:center;
    }

    .relax-toggle i{
        font-size:2.45rem;
        line-height:1;
        transform:translateX(2px);
    }

    .relax-toggle::before{
        content:'';
        position:absolute;
        inset:10px;
        border-radius:50%;
        border:1px solid rgba(255,255,255,.22);
    }

    .relax-toggle:hover{
        transform:scale(1.05);
        background:rgba(255,255,255,.28);
        box-shadow:0 26px 42px rgba(4,19,30,.28);
    }

    .relax-player.is-playing .relax-toggle{
        background:rgba(14,64,54,.54);
    }

    .relax-footer{
        position:relative;
        z-index:2;
        display:flex;
        flex-direction:column;
        gap:.95rem;
        margin-top:auto;
    }

    .relax-meta{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:1rem;
        color:#fff;
    }

    .relax-kicker{
        font-size:.78rem;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:rgba(255,255,255,.74);
        margin-bottom:.45rem;
    }

    .relax-meta h3{
        margin:0;
        font-size:1.9rem;
        font-weight:700;
        max-width:500px;
        text-wrap:balance;
    }

    .relax-time{
        padding:.4rem .75rem;
        border-radius:999px;
        background:rgba(255,255,255,.14);
        color:#fff;
        font-size:.85rem;
        font-weight:700;
        white-space:nowrap;
        backdrop-filter:blur(10px);
    }

    .relax-progress{
        position:absolute;
        left:1.5rem;
        right:1.5rem;
        bottom:1.3rem;
        height:6px;
        border-radius:999px;
        background:rgba(255,255,255,.18);
        overflow:hidden;
        z-index:3;
    }

    .relax-progress span{
        display:block;
        height:100%;
        width:0;
        border-radius:inherit;
        background:linear-gradient(90deg,#a8f0d6 0%, #ffffff 100%);
        transition:width .35s ease;
    }

    .relax-sound-note{
        font-size:.82rem;
        color:rgba(255,255,255,.78);
        line-height:1.6;
        max-width:460px;
    }

    .article-section-head{
        max-width:720px;
        margin-bottom:2.4rem;
    }

    .article-title,
    .trend-title{
        font-size:clamp(1.9rem,3vw,2.9rem);
        font-weight:800;
        line-height:1.12;
        color:var(--text-dark);
        margin-bottom:.8rem;
    }

    .article-desc,
    .trend-desc{
        color:var(--text-mid);
        line-height:1.85;
        margin:0;
    }

    .article-card{
        height:100%;
        background:#fff;
        border:1px solid rgba(26,58,92,.08);
        border-radius:22px;
        overflow:hidden;
        box-shadow:0 18px 38px rgba(13,27,42,.07);
        transition:transform .25s ease, box-shadow .25s ease;
    }

    .article-card:hover{
        transform:translateY(-6px);
        box-shadow:0 24px 46px rgba(13,27,42,.11);
    }

    .article-media{
        aspect-ratio:16 / 10;
        overflow:hidden;
        background:#dcebe0;
    }

    .article-media img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }

    .article-body{
        padding:1.4rem 1.35rem 1.5rem;
    }

    .article-meta{
        display:flex;
        flex-wrap:wrap;
        gap:.55rem;
        margin-bottom:.9rem;
        font-size:.72rem;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:#0a523a;
    }

    .article-body h3{
        font-size:1.35rem;
        font-weight:700;
        line-height:1.35;
        color:var(--text-dark);
        margin-bottom:.75rem;
    }

    .article-body p{
        color:var(--text-mid);
        line-height:1.8;
        margin:0;
    }

    .trend-header{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:1rem;
        margin-bottom:2rem;
    }

    .trend-live{
        display:inline-flex;
        align-items:center;
        gap:.55rem;
        color:#315743;
        font-size:.78rem;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.08em;
        white-space:nowrap;
    }

    .trend-live::before{
        content:'';
        width:8px;
        height:8px;
        border-radius:50%;
        background:#0fb87a;
        box-shadow:0 0 0 6px rgba(15,184,122,.14);
    }

    .trend-feature-card,
    .trend-hashtag-card{
        background:#fff;
        border:1px solid rgba(26,58,92,.08);
        border-radius:26px;
        box-shadow:0 20px 38px rgba(13,27,42,.08);
        height:100%;
    }

    .trend-feature-card{
        padding:1.8rem;
        background:
            radial-gradient(circle at top right, rgba(15,184,122,.08), transparent 28%),
            linear-gradient(180deg,#ffffff 0%, #f7fbf8 100%);
    }

    .trend-badge{
        display:inline-flex;
        align-items:center;
        padding:.45rem .9rem;
        border-radius:999px;
        background:#edf7f1;
        color:#0a523a;
        font-size:.72rem;
        font-weight:800;
        text-transform:uppercase;
        letter-spacing:.08em;
        margin-bottom:1rem;
    }

    .trend-feature-card h3{
        font-size:1.8rem;
        font-weight:800;
        line-height:1.2;
        color:var(--text-dark);
        margin-bottom:.85rem;
        max-width:560px;
    }

    .trend-summary{
        color:var(--text-mid);
        line-height:1.85;
        margin-bottom:1.5rem;
        max-width:600px;
    }

    .trend-rank-list{
        display:grid;
        gap:1rem;
    }

    .trend-rank-item{
        display:grid;
        grid-template-columns:58px 1fr;
        gap:1rem;
        align-items:flex-start;
        padding:1rem 1.1rem;
        border-radius:18px;
        background:#fff;
        border:1px solid rgba(10,82,58,.08);
    }

    .trend-rank-number{
        width:58px;
        height:58px;
        border-radius:18px;
        background:#eff8f2;
        color:#0a523a;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.15rem;
        font-weight:800;
    }

    .trend-rank-item h4{
        margin:0 0 .35rem;
        font-size:1.15rem;
        font-weight:800;
        color:var(--text-dark);
    }

    .trend-rank-item p{
        margin:0;
        color:var(--text-mid);
        line-height:1.75;
        font-size:.95rem;
    }

    .trend-hashtag-card{
        padding:1.6rem;
        background:linear-gradient(180deg,#f4f9f5 0%, #edf6ef 100%);
    }

    .trend-hashtag-card h3{
        font-size:1.1rem;
        font-weight:800;
        color:var(--text-dark);
        margin-bottom:.85rem;
    }

    .trend-hashtag-card p{
        color:var(--text-mid);
        line-height:1.75;
        margin-bottom:1.2rem;
    }

    .trend-chip-wrap{
        display:flex;
        flex-wrap:wrap;
        gap:.7rem;
    }

    .trend-chip{
        display:inline-flex;
        align-items:center;
        padding:.65rem .95rem;
        border-radius:999px;
        background:#fff;
        border:1px solid rgba(10,82,58,.08);
        color:#315743;
        font-size:.88rem;
        font-weight:700;
    }

    @keyframes breatheOrb{
        0%, 100%{transform:translateX(-50%) scale(1);}
        50%{transform:translateX(-50%) scale(1.08);}
    }

    @keyframes driftCloud{
        0%{transform:translateX(0);}
        50%{transform:translateX(16px);}
        100%{transform:translateX(0);}
    }

    @keyframes ripplePulse{
        0%{transform:translateX(-50%) scale(.92); opacity:.48;}
        70%{transform:translateX(-50%) scale(1.08); opacity:.14;}
        100%{transform:translateX(-50%) scale(1.14); opacity:0;}
    }

    @keyframes twinkleStars{
        0%, 100%{opacity:.35;}
        50%{opacity:.8;}
    }

    @keyframes birdDrift{
        0%{transform:translateX(0) translateY(0);}
        50%{transform:translateX(24px) translateY(-6px);}
        100%{transform:translateX(48px) translateY(0);}
    }

    @keyframes fireflyFloat{
        0%, 100%{transform:translate3d(0, 0, 0); opacity:.25;}
        40%{transform:translate3d(10px, -16px, 0); opacity:.9;}
        70%{transform:translate3d(-6px, -24px, 0); opacity:.55;}
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
        .relax-player-shell,
        .relax-overlay{
            min-height:420px;
        }
        .trend-header,
        .relax-meta{
            align-items:flex-start;
            flex-direction:column;
        }
        .relax-topbar{
            align-items:flex-start;
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
        .relax-player-shell,
        .relax-overlay{
            min-height:360px;
        }
        .relax-overlay{
            padding:1rem 1rem 1.6rem;
        }
        .relax-toggle{
            width:82px;
            height:82px;
        }
        .relax-toggle i{
            font-size:1.95rem;
        }
        .relax-meta h3{
            font-size:1.35rem;
        }
        .relax-progress{
            left:1rem;
            right:1rem;
            bottom:1rem;
        }
        .relax-topbar{
            flex-direction:column;
        }
        .relax-status{
            align-self:flex-start;
        }
        .trend-feature-card,
        .trend-hashtag-card{
            border-radius:22px;
        }
        .trend-rank-item{
            grid-template-columns:46px 1fr;
            padding:.95rem;
        }
        .trend-rank-number{
            width:46px;
            height:46px;
            border-radius:14px;
            font-size:1rem;
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

    @php
        $pageContent = $pageContent ?? [];
        $articleCards = collect(data_get($pageContent, 'articles', []))->take(3);
        $trendingTopics = collect(data_get($pageContent, 'trending_topics', []))->take(3);
        $weeklyHashtags = collect(data_get($pageContent, 'weekly_hashtags', []))->take(8);
        $resolveMediaUrl = function ($path) {
            if (blank($path)) {
                return null;
            }

            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '//'])) {
                return $path;
            }

            if (\Illuminate\Support\Str::startsWith($path, 'about/')) {
                return \Illuminate\Support\Facades\Storage::url($path);
            }

            return asset(ltrim($path, '/'));
        };
    @endphp

    <section class="section-block pt-0">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">{{ data_get($pageContent, 'video_title') }}</h2>
                <p class="section-desc">
                    {{ data_get($pageContent, 'video_description') }}
                </p>
            </div>

            <div class="relax-player" data-relax-player data-duration="300" data-speed="1" data-scene="dawn">
                <div class="relax-player-shell">
                    <div class="relax-scene" aria-hidden="true">
                        <div class="relax-scene-frame frame-dawn is-active" data-scene-frame="dawn"></div>
                        <div class="relax-scene-frame frame-noon" data-scene-frame="noon"></div>
                        <div class="relax-scene-frame frame-night" data-scene-frame="night"></div>
                        <div class="relax-stars"></div>
                        <div class="relax-orb"></div>
                        <div class="relax-cloud c1"></div>
                        <div class="relax-cloud c2"></div>
                        <div class="relax-bird b1"></div>
                        <div class="relax-bird b2"></div>
                        <div class="relax-mountain left"></div>
                        <div class="relax-mountain right"></div>
                        <div class="relax-waterline"></div>
                        <div class="relax-reflection"></div>
                        <div class="relax-ripple"></div>
                        <div class="relax-firefly f1"></div>
                        <div class="relax-firefly f2"></div>
                        <div class="relax-firefly f3"></div>
                    </div>

                    <div class="relax-overlay">
                        <div class="relax-topbar">
                            <span class="relax-pill">{{ data_get($pageContent, 'video_badge') }}</span>
                            <span class="relax-status" data-relax-status>
                                <i class="bi bi-volume-mute-fill"></i>
                                <span>Ambient off</span>
                            </span>
                        </div>

                        <div class="relax-center">
                            <button type="button" class="relax-toggle" data-relax-toggle aria-label="Putar visual relaksasi">
                                <i class="bi bi-play-fill"></i>
                            </button>
                        </div>

                        <div class="relax-footer">
                            <div class="relax-meta">
                                <div>
                                    <div class="relax-kicker">Visual Relaksasi</div>
                                    <h3>{{ data_get($pageContent, 'video_caption') }}</h3>
                                </div>
                                <span class="relax-time" data-relax-time>00:00 / {{ data_get($pageContent, 'video_duration') }}</span>
                            </div>
                            <div class="relax-sound-note">
                                Putar untuk menikmati visual yang berganti suasana secara halus dengan ambient ringan yang dibuat langsung di browser.
                            </div>
                        </div>
                    </div>

                    <div class="relax-progress">
                        <span data-relax-progress></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-block pt-0">
        <div class="container">
            <div class="article-section-head">
                <h2 class="article-title">{{ data_get($pageContent, 'article_section_title') }}</h2>
                <p class="article-desc">
                    {{ data_get($pageContent, 'article_section_description') }}
                </p>
            </div>

            <div class="row g-4">
                @foreach($articleCards as $article)
                    @php $articleImage = $resolveMediaUrl(data_get($article, 'image')); @endphp
                    <div class="col-md-6 col-lg-4">
                        <article class="article-card">
                            <div class="article-media">
                                @if($articleImage)
                                    <img src="{{ $articleImage }}" alt="{{ data_get($article, 'title') }}">
                                @endif
                            </div>
                            <div class="article-body">
                                <div class="article-meta">
                                    <span>{{ data_get($article, 'category') }}</span>
                                    <span>{{ data_get($article, 'read_time') }}</span>
                                </div>
                                <h3>{{ data_get($article, 'title') }}</h3>
                                <p>{{ data_get($article, 'excerpt') }}</p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-block pt-0">
        <div class="container">
            <div class="trend-header">
                <div>
                    <h2 class="trend-title">{{ data_get($pageContent, 'trending_section_title') }}</h2>
                    <p class="trend-desc">
                        {{ data_get($pageContent, 'trending_section_description') }}
                    </p>
                </div>
                <div class="trend-live">Live Dashboard</div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="trend-feature-card">
                        <span class="trend-badge">Top 3 Pekan Ini</span>
                        <h3>Topik yang paling sering dibahas mahasiswa akhir-akhir ini</h3>
                        <p class="trend-summary">
                            {{ data_get($pageContent, 'trending_summary') }}
                        </p>

                        <div class="trend-rank-list">
                            @foreach($trendingTopics as $topic)
                                <div class="trend-rank-item">
                                    <div class="trend-rank-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div>
                                        <h4>{{ data_get($topic, 'title') }}</h4>
                                        <p>{{ data_get($topic, 'insight') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>

                <div class="col-lg-4">
                    <aside class="trend-hashtag-card">
                        <h3>Hashtag Populer Minggu Ini</h3>
                        <p>
                            Konselor dapat memperbarui daftar ini dari panel admin untuk mengikuti isu yang sedang ramai dibicarakan mahasiswa.
                        </p>

                        <div class="trend-chip-wrap">
                            @foreach($weeklyHashtags as $hashtag)
                                <span class="trend-chip">{{ $hashtag }}</span>
                            @endforeach
                        </div>
                    </aside>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const player = document.querySelector('[data-relax-player]');

        if (!player) {
            return;
        }

        const toggle = player.querySelector('[data-relax-toggle]');
        const icon = toggle?.querySelector('i');
        const timeLabel = player.querySelector('[data-relax-time]');
        const progressBar = player.querySelector('[data-relax-progress]');
        const status = player.querySelector('[data-relax-status]');
        const sceneFrames = Array.from(player.querySelectorAll('[data-scene-frame]'));
        const totalDuration = Number(player.dataset.duration || 300);
        const speed = Number(player.dataset.speed || 1);
        const scenes = ['dawn', 'noon', 'night'];
        let elapsed = 0;
        let timer = null;
        let sceneTimer = null;
        let currentSceneIndex = 0;
        let audioEngine = null;

        const updateStatus = (isPlaying) => {
            if (!status) {
                return;
            }

            const iconNode = status.querySelector('i');
            const textNode = status.querySelector('span');

            if (iconNode) {
                iconNode.className = isPlaying ? 'bi bi-volume-up-fill' : 'bi bi-volume-mute-fill';
            }

            if (textNode) {
                textNode.textContent = isPlaying ? 'Ambient on' : 'Ambient off';
            }
        };

        const setScene = (sceneName) => {
            player.dataset.scene = sceneName;
            sceneFrames.forEach((frame) => {
                frame.classList.toggle('is-active', frame.dataset.sceneFrame === sceneName);
            });
        };

        const advanceScene = () => {
            currentSceneIndex = (currentSceneIndex + 1) % scenes.length;
            setScene(scenes[currentSceneIndex]);
        };

        const formatTime = (value) => {
            const safeValue = Math.max(0, Math.floor(value));
            const minutes = String(Math.floor(safeValue / 60)).padStart(2, '0');
            const seconds = String(safeValue % 60).padStart(2, '0');
            return `${minutes}:${seconds}`;
        };

        const render = () => {
            const progress = Math.min((elapsed / totalDuration) * 100, 100);

            if (progressBar) {
                progressBar.style.width = `${progress}%`;
            }

            if (timeLabel) {
                timeLabel.textContent = `${formatTime(elapsed)} / ${formatTime(totalDuration)}`;
            }
        };

        const createAmbientAudio = () => {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;

            if (!AudioContextClass) {
                return null;
            }

            const context = new AudioContextClass();
            const masterGain = context.createGain();
            masterGain.gain.value = 0.045;
            masterGain.connect(context.destination);

            const padOscillator = context.createOscillator();
            const padGain = context.createGain();
            padOscillator.type = 'sine';
            padOscillator.frequency.value = 196;
            padGain.gain.value = 0.018;
            padOscillator.connect(padGain);
            padGain.connect(masterGain);

            const shimmerOscillator = context.createOscillator();
            const shimmerGain = context.createGain();
            shimmerOscillator.type = 'triangle';
            shimmerOscillator.frequency.value = 294;
            shimmerGain.gain.value = 0.008;
            shimmerOscillator.connect(shimmerGain);
            shimmerGain.connect(masterGain);

            const lfo = context.createOscillator();
            const lfoGain = context.createGain();
            lfo.type = 'sine';
            lfo.frequency.value = 0.08;
            lfoGain.gain.value = 0.01;
            lfo.connect(lfoGain);
            lfoGain.connect(padGain.gain);

            const noiseBuffer = context.createBuffer(1, context.sampleRate * 2, context.sampleRate);
            const noiseData = noiseBuffer.getChannelData(0);
            for (let i = 0; i < noiseData.length; i += 1) {
                noiseData[i] = (Math.random() * 2 - 1) * 0.22;
            }

            const noiseSource = context.createBufferSource();
            noiseSource.buffer = noiseBuffer;
            noiseSource.loop = true;

            const noiseFilter = context.createBiquadFilter();
            noiseFilter.type = 'lowpass';
            noiseFilter.frequency.value = 480;

            const noiseGain = context.createGain();
            noiseGain.gain.value = 0.012;

            noiseSource.connect(noiseFilter);
            noiseFilter.connect(noiseGain);
            noiseGain.connect(masterGain);

            padOscillator.start();
            shimmerOscillator.start();
            lfo.start();
            noiseSource.start();

            return { context };
        };

        const startAudio = async () => {
            try {
                if (!audioEngine) {
                    audioEngine = createAmbientAudio();
                }

                if (audioEngine?.context?.state === 'suspended') {
                    await audioEngine.context.resume();
                }
            } catch (error) {
                audioEngine = null;
            }
        };

        const stopAudio = async () => {
            try {
                if (audioEngine?.context?.state === 'running') {
                    await audioEngine.context.suspend();
                }
            } catch (error) {
                // Keep player functional even when audio state changes fail.
            }
        };

        const stopPlayback = async () => {
            window.clearInterval(timer);
            window.clearInterval(sceneTimer);
            timer = null;
            sceneTimer = null;
            player.classList.remove('is-playing');

            if (icon) {
                icon.className = 'bi bi-play-fill';
            }

            updateStatus(false);
            await stopAudio();
        };

        const startPlayback = async () => {
            if (elapsed >= totalDuration) {
                elapsed = 0;
            }

            player.classList.add('is-playing');

            if (icon) {
                icon.className = 'bi bi-pause-fill';
            }

            updateStatus(true);
            await startAudio();

            timer = window.setInterval(() => {
                elapsed = Math.min(elapsed + speed, totalDuration);
                render();

                if (elapsed >= totalDuration) {
                    stopPlayback();
                }
            }, 1000);

            sceneTimer = window.setInterval(() => {
                advanceScene();
            }, 8000);
        };

        toggle?.addEventListener('click', () => {
            if (timer) {
                stopPlayback();
                return;
            }

            startPlayback();
        });

        setScene(scenes[currentSceneIndex]);
        updateStatus(false);
        render();
    });
</script>
@endpush
