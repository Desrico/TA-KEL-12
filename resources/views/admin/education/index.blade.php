@extends('layouts.admin')

@section('page-title', 'Edukasi & Intervensi')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<style>
    :root {
        --edu-bg: #f9fafb;
        --edu-card: #ffffff;
        --edu-border: #e5e7eb;
        --edu-text-1: #111827;
        --edu-text-2: #6b7280;
        --edu-text-3: #9ca3af;
        --edu-green: #059669;
        --edu-green-light: #d1fae5;
        --edu-green-mid: #6ee7b7;
        --edu-red-light: #fee2e2;
        --edu-red: #ef4444;
    }

    .pc-container { background: var(--edu-bg) !important; }

    .edu-page-wrap {
        max-width: 960px;
        margin: 0 auto;
        padding: 0 24px 60px;
    }

    /* ---- Page Header ---- */
    .edu-page-header {
        padding: 32px 0 28px;
        border-bottom: 1px solid var(--edu-border);
        margin-bottom: 40px;
    }

    .edu-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        color: var(--edu-text-3);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .edu-breadcrumb a { color: var(--edu-text-3); text-decoration: none; }
    .edu-breadcrumb .active { color: var(--edu-text-1); }
    .edu-breadcrumb .sep { color: var(--edu-text-3); }

    .edu-page-header h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 2rem;
        font-weight: 800;
        color: var(--edu-text-1);
        margin: 0 0 10px;
        letter-spacing: -0.025em;
    }

    .edu-page-header p {
        color: var(--edu-text-2);
        font-size: 0.95rem;
        line-height: 1.65;
        max-width: 560px;
        margin: 0;
    }

    /* ---- Card Grid ---- */
    .edu-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
        gap: 24px;
    }

    .edu-card {
        background: var(--edu-card);
        border: 1.5px solid var(--edu-border);
        border-radius: 20px;
        padding: 32px 32px 24px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 0;
        position: relative;
        overflow: hidden;
        transition: box-shadow 0.22s ease, border-color 0.22s ease, transform 0.22s ease;
    }

    .edu-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.09);
        border-color: #d1d5db;
        transform: translateY(-3px);
        color: inherit;
        text-decoration: none;
    }

    /* Faded bg icon */
    .edu-card-bg-icon {
        position: absolute;
        bottom: -12px;
        right: -10px;
        font-size: 7rem;
        opacity: 0.07;
        line-height: 1;
        pointer-events: none;
        user-select: none;
    }

    /* Top row: icon + badge */
    .edu-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .edu-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .edu-card-icon.green { background: var(--edu-green-light); }
    .edu-card-icon.red   { background: var(--edu-red-light); }

    .edu-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 999px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .edu-badge.green { background: var(--edu-green-light); color: #065f46; }
    .edu-badge.red   { background: var(--edu-red-light); color: #991b1b; }

    .edu-badge::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
    }

    .edu-badge.green::before { background: var(--edu-green); }
    .edu-badge.red::before   { background: var(--edu-red); }

    /* Title */
    .edu-card-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--edu-text-1);
        margin: 0 0 10px;
        letter-spacing: -0.018em;
    }

    /* Desc */
    .edu-card-desc {
        font-size: 0.88rem;
        color: var(--edu-text-2);
        line-height: 1.65;
        flex: 1;
        margin-bottom: 28px;
    }

    /* Bottom row: avatars + CTA */
    .edu-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #f3f4f6;
        padding-top: 18px;
    }

    .edu-avatar-stack {
        display: flex;
        align-items: center;
    }

    .edu-avatar {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        margin-left: -8px;
        color: white;
        flex-shrink: 0;
    }

    .edu-avatar:first-child { margin-left: 0; }

    .edu-avatar-count {
        background: #e5e7eb;
        color: #374151;
        font-size: 0.68rem;
        font-weight: 700;
    }

    .edu-cta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--edu-text-1);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        text-decoration: none;
        transition: gap 0.2s;
    }

    .edu-card:hover .edu-cta { gap: 10px; }

    .edu-cta svg { transition: transform 0.2s; }
    .edu-card:hover .edu-cta svg { transform: translateX(3px); }

    @media (max-width: 640px) {
        .edu-card-grid { grid-template-columns: 1fr; }
        .edu-page-wrap { padding: 0 16px 40px; }
    }
</style>
@endpush

@section('konten')
<div class="edu-page-wrap">

    {{-- Page Header --}}
    <div class="edu-page-header">
        <p>Pusat manajemen konten edukatif dan program intervensi. Kelola materi pembelajaran serta tantangan psikologis yang dirancang khusus untuk mendukung kesejahteraan mahasiswa.</p>
    </div>

    {{-- Card Grid --}}
    <div class="edu-card-grid">

        {{-- Manajemen Modul --}}
        <div class="edu-card">
            <div class="edu-card-bg-icon">📖</div>
            <div class="edu-card-top">
                <div class="edu-card-icon green">📚</div>
                <span class="edu-badge green">{{ $moduleCount ?? 0 }} Terdaftar</span>
            </div>

            <h2 class="edu-card-title">Manajemen Modul</h2>
            <p class="edu-card-desc">Kelola konten pembelajaran interaktif, artikel kesehatan mental, dan modul bimbingan terstruktur untuk membantu mahasiswa memahami kondisi emosional mereka.</p>

            <div class="edu-card-footer">
                <a href="{{ route('counselor.education.modules.index') }}" class="edu-cta">
                    Kelola Modul
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>

        {{-- Manajemen Challenge --}}
        <div class="edu-card">
            <div class="edu-card-bg-icon">🏆</div>
            <div class="edu-card-top">
                <div class="edu-card-icon red">🏆</div>
                <span class="edu-badge red">{{ $challengeCount ?? 0 }} Terdaftar</span>
            </div>

            <h2 class="edu-card-title">Manajemen Challenge</h2>
            <p class="edu-card-desc">Rancang tantangan harian atau mingguan yang mendorong kebiasaan positif, meditasi, dan aktivitas sosial untuk meningkatkan resiliensi mahasiswa.</p>

            <div class="edu-card-footer">
                <a href="{{ route('counselor.education.challenges.index') }}" class="edu-cta">
                    Kelola Tantangan
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection