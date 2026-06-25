@extends('layouts.admin')

@section('page-title', 'Edukasi & Intervensi')
@section('page-hero')
<div style="display:none !important;"></div>
@endsection

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

        --edu-red: #ef4444;
        --edu-red-light: #fee2e2;

        --edu-blue: #0284c7;
        --edu-blue-light: #e0f2fe;
    }

    .pc-container {
        background: var(--admin-bg) !important;
    }

    .pc-content {
        padding: 1.5rem 2rem 2.5rem !important;
    }

    .admin-breadcrumb {
        margin: 0 0 1.5rem 0 !important;
    }

    .admin-page-inner {
        padding-top: 0 !important;
    }

    .edu-page-wrap {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 24px 60px;
    }

    .edu-card-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
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
        position: relative;
        overflow: hidden;
        min-height: 345px;
        transition: box-shadow 0.22s ease, border-color 0.22s ease, transform 0.22s ease;
    }

    .edu-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.09);
        border-color: #d1d5db;
        transform: translateY(-3px);
        color: inherit;
        text-decoration: none;
    }

    .edu-card-bg-icon {
        position: absolute;
        bottom: -14px;
        right: -10px;
        font-size: 7rem;
        opacity: 0.07;
        line-height: 1;
        pointer-events: none;
        user-select: none;
    }

    .edu-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
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

    .edu-card-icon.green {
        background: var(--edu-green-light);
    }

    .edu-card-icon.red {
        background: var(--edu-red-light);
    }

    .edu-card-icon.blue {
        background: var(--edu-blue-light);
    }

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
        white-space: nowrap;
    }

    .edu-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .edu-badge.green {
        background: var(--edu-green-light);
        color: #065f46;
    }

    .edu-badge.green::before {
        background: var(--edu-green);
    }

    .edu-badge.red {
        background: var(--edu-red-light);
        color: #991b1b;
    }

    .edu-badge.red::before {
        background: var(--edu-red);
    }

    .edu-badge.blue {
        background: var(--edu-blue-light);
        color: #075985;
    }

    .edu-badge.blue::before {
        background: var(--edu-blue);
    }

    .edu-card-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--edu-text-1);
        margin: 0 0 10px;
        letter-spacing: -0.018em;
    }

    .edu-card-desc {
        font-size: 0.88rem;
        color: var(--edu-text-2);
        line-height: 1.65;
        flex: 1;
        margin-bottom: 28px;
    }

    .edu-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #f3f4f6;
        padding-top: 18px;
        margin-top: auto;
        position: relative;
        z-index: 2;
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
        transition: gap 0.2s, color 0.2s;
    }

    .edu-card:hover .edu-cta {
        gap: 10px;
        color: var(--edu-green);
    }

    .edu-cta svg {
        transition: transform 0.2s;
    }

    .edu-card:hover .edu-cta svg {
        transform: translateX(3px);
    }

    @media (max-width: 1180px) {
        .edu-card-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .edu-card-grid {
            grid-template-columns: 1fr;
        }

        .edu-page-wrap {
            padding: 0 16px 40px;
        }

        .edu-card {
            min-height: auto;
        }
    }
</style>
@endpush

@section('konten')
<div class="edu-page-wrap">

    <div class="edu-card-grid">

        {{-- Manajemen Modul --}}
        <div class="edu-card">
            <div class="edu-card-bg-icon">📖</div>

            <div class="edu-card-top">
                <div class="edu-card-icon green">📚</div>
                <span class="edu-badge green">{{ $moduleCount ?? 0 }} Terdaftar</span>
            </div>

            <h2 class="edu-card-title">Manajemen Modul</h2>

            <p class="edu-card-desc">
                Kelola konten pembelajaran interaktif, artikel kesehatan mental,
                dan modul bimbingan terstruktur untuk membantu mahasiswa memahami
                kondisi emosional mereka.
            </p>

            <div class="edu-card-footer">
                <a href="{{ route('counselor.education.modules.index') }}" class="edu-cta">
                    Kelola Modul
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        </div>

        {{-- Trend Topik Edukasi Web --}}
        <div class="edu-card">
            <div class="edu-card-bg-icon">📰</div>

            <div class="edu-card-top">
                <div class="edu-card-icon blue">📰</div>
                <span class="edu-badge blue">{{ $webContentCount ?? 0 }} Terdaftar</span>
            </div>

            <h2 class="edu-card-title">Konten Edukasi Web</h2>

            <p class="edu-card-desc">
                Kelola video, artikel, atau berita edukatif yang akan ditampilkan
                pada halaman Edukasi Mental di website, seperti akademik, intrapersonal,
                keluarga, asrama, dan relasi.
            </p>

            <div class="edu-card-footer">
                <a href="{{ route('counselor.education.web-contents.index') }}" class="edu-cta">
                    Kelola Konten Web
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
