@extends('layouts.admin')

@section('page-title', 'Profil Konselor')
@section('page-hero')
<div style="display:none !important;"></div>
@endsection

@push('styles')
<style>
    .pc-content {
        padding: 1.5rem 2rem 2.5rem !important;
    }

    .admin-breadcrumb {
        margin: 0 0 1.5rem 0 !important;
    }

    .admin-page-inner {
        padding-top: 0 !important;
    }

    .admin-profile-wrap {
        width: calc(100% - 48px);
        max-width: 1120px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 340px minmax(0, 1fr);
        gap: 18px;
    }

    .admin-profile-card {
        background: #ffffff;
        border: 1px solid #dceee4;
        border-radius: 22px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .04);
    }

    .admin-profile-identity {
        padding: 28px 24px;
        text-align: center;
    }

    .admin-profile-avatar {
        width: 112px;
        height: 112px;
        border-radius: 50%;
        margin: 0 auto 16px;
        background: #d1fae5;
        color: #065f46;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        font-weight: 900;
        overflow: hidden;
        border: 5px solid #ffffff;
        box-shadow: 0 14px 30px rgba(6, 95, 70, .12);
    }

    .admin-profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .admin-profile-name {
        margin: 0;
        color: #0f172a;
        font-size: 1.35rem;
        font-weight: 900;
        line-height: 1.3;
    }

    .admin-profile-role {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-top: 10px;
        padding: 7px 12px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #065f46;
        font-size: .78rem;
        font-weight: 900;
    }

    .admin-profile-email {
        margin-top: 12px;
        color: #64748b;
        font-size: .88rem;
        word-break: break-word;
    }

    .admin-profile-note {
        margin-top: 20px;
        padding: 13px 14px;
        border-radius: 14px;
        border: 1px dashed #bbf7d0;
        background: #f0fdf4;
        color: #065f46;
        font-size: .82rem;
        font-weight: 700;
        line-height: 1.6;
        text-align: left;
    }

    .admin-profile-main {
        padding: 24px;
    }

    .admin-profile-section-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 18px;
        border-bottom: 1px solid #edf2ef;
    }

    .admin-profile-section-head h2 {
        margin: 0;
        color: #0f172a;
        font-size: 1.2rem;
        font-weight: 900;
    }

    .admin-profile-section-head p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: .86rem;
        line-height: 1.55;
    }

    .admin-profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 20px;
    }

    .admin-profile-field {
        border: 1px solid #edf2ef;
        border-radius: 16px;
        padding: 15px 16px;
        background: #ffffff;
    }

    .admin-profile-field.full {
        grid-column: 1 / -1;
    }

    .admin-profile-label {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
        color: #64748b;
        font-size: .76rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .admin-profile-label i {
        color: #065f46;
        font-size: .96rem;
    }

    .admin-profile-value {
        color: #0f172a;
        font-size: .94rem;
        font-weight: 800;
        line-height: 1.5;
        word-break: break-word;
    }

    .admin-profile-muted {
        color: #94a3b8;
    }

    @media (max-width: 992px) {
        .admin-profile-wrap {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .pc-content {
            padding: 1rem 0 2rem !important;
        }

        .admin-profile-wrap {
            width: calc(100% - 24px);
        }

        .admin-profile-main,
        .admin-profile-identity {
            padding: 20px;
        }

        .admin-profile-grid {
            grid-template-columns: 1fr;
        }

        .admin-profile-section-head {
            display: block;
        }

    }
</style>
@endpush

@section('konten')
@php
    $namaKonselor = $counselorProfile['nama'] ?? ($user->nama ?: 'Konselor');
    $inisialKonselor = collect(explode(' ', trim($namaKonselor)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('') ?: 'K';
    $fotoProfil = !empty($counselorProfile['foto'])
        ? \Illuminate\Support\Facades\Storage::url($counselorProfile['foto'])
        : null;
    $usernameCis = trim((string) ($counselorProfile['username_cis'] ?? ''));
    $emailKonselor = trim((string) ($counselorProfile['email'] ?? ''));
    $jabatan = trim((string) ($counselorProfile['jabatan'] ?? ''));
    $nomorTelepon = trim((string) ($counselorProfile['nomor_telepon'] ?? ''));
@endphp

<div class="admin-profile-wrap">
    <aside class="admin-profile-card">
        <div class="admin-profile-identity">
            <div class="admin-profile-avatar" aria-hidden="true">
                @if($fotoProfil)
                    <img src="{{ $fotoProfil }}" alt="">
                @else
                    {{ $inisialKonselor }}
                @endif
            </div>

            <h1 class="admin-profile-name">{{ $namaKonselor }}</h1>
        </div>

    </aside>

    <section class="admin-profile-card admin-profile-main">
        <div class="admin-profile-section-head">
            <div>
                <h2>Informasi Konselor</h2>
                <p>Data profil ini mengikuti akun konselor yang sedang aktif di sistem Campus Care.</p>
            </div>
        </div>

        <div class="admin-profile-grid">
            <div class="admin-profile-field">
                <div class="admin-profile-label">
                    <i class="ti ti-user"></i>
                    Nama Lengkap
                </div>
                <div class="admin-profile-value">{{ $namaKonselor }}</div>
            </div>

            <div class="admin-profile-field">
                <div class="admin-profile-label">
                    <i class="ti ti-id"></i>
                    Username CIS
                </div>
                <div class="admin-profile-value {{ $usernameCis !== '' ? '' : 'admin-profile-muted' }}">
                    {{ $usernameCis !== '' ? $usernameCis : 'Belum tersedia' }}
                </div>
            </div>

            <div class="admin-profile-field">
                <div class="admin-profile-label">
                    <i class="ti ti-mail"></i>
                    Email
                </div>
                <div class="admin-profile-value">{{ $emailKonselor !== '' ? $emailKonselor : '-' }}</div>
            </div>

            <div class="admin-profile-field full">
                <div class="admin-profile-label">
                    <i class="ti ti-certificate"></i>
                    Jabatan
                </div>
                <div class="admin-profile-value {{ $jabatan !== '' ? '' : 'admin-profile-muted' }}">
                    {{ $jabatan !== '' ? $jabatan : 'Belum tersedia' }}
                </div>
            </div>

            <div class="admin-profile-field full">
                <div class="admin-profile-label">
                    <i class="ti ti-phone"></i>
                    Nomor Telepon
                </div>
                <div class="admin-profile-value {{ $nomorTelepon !== '' ? '' : 'admin-profile-muted' }}">
                    {{ $nomorTelepon !== '' ? $nomorTelepon : 'Belum tersedia' }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
