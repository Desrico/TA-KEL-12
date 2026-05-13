@extends('layouts.admin')

@section('page-title', isset($challenge) ? 'Edit Challenge' : 'Tambah Challenge')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<style>
    :root {
        --f-bg: #f9fafb;
        --f-card: #ffffff;
        --f-border: #e5e7eb;
        --f-border-focus: #6ee7b7;
        --f-green: #059669;
        --f-green-light: #d1fae5;
        --f-text-1: #111827;
        --f-text-2: #6b7280;
        --f-text-3: #9ca3af;
        --f-radius: 14px;
        --f-shadow: 0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.04);
    }

    .pc-container { background: var(--f-bg) !important; }

    .f-wrap {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 24px 80px;
    }

    .f-page-title {
        padding: 32px 0 24px;
    }
    .f-page-title h1 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1.55rem;
        font-weight: 800;
        color: var(--f-text-1);
        margin: 0 0 6px;
        letter-spacing: -0.02em;
    }
    .f-page-title p {
        font-size: 0.875rem;
        color: var(--f-text-2);
        margin: 0;
    }

    .f-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--f-text-2);
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 4px;
        transition: color 0.18s;
    }
    .f-back:hover { color: var(--f-green); }

    .f-section {
        background: var(--f-card);
        border: 1.5px solid var(--f-border);
        border-radius: 18px;
        padding: 28px 28px 24px;
        margin-bottom: 20px;
        box-shadow: var(--f-shadow);
    }
    .f-section-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 1rem;
        font-weight: 800;
        color: var(--f-green);
        margin: 0 0 22px;
    }

    .f-group { margin-bottom: 18px; }
    .f-group:last-child { margin-bottom: 0; }

    .f-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--f-text-3);
        margin-bottom: 7px;
    }

    .f-input, .f-select, .f-textarea {
        width: 100%;
        background: #f3f4f6;
        border: 1.5px solid transparent;
        border-radius: 10px;
        padding: 12px 16px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.92rem;
        color: var(--f-text-1);
        outline: none;
        transition: border-color 0.18s, background 0.18s, box-shadow 0.18s;
        appearance: none;
    }
    .f-input::placeholder, .f-textarea::placeholder { color: var(--f-text-3); }
    .f-input:focus, .f-select:focus, .f-textarea:focus {
        border-color: var(--f-border-focus);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
    }
    .f-textarea { min-height: 130px; resize: vertical; line-height: 1.6; }

    .f-select-wrap { position: relative; }
    .f-select-wrap svg {
        position: absolute;
        right: 13px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--f-text-3);
    }

    .f-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .f-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
    }

    .f-error { font-size: 0.78rem; color: #ef4444; font-weight: 600; margin-top: 6px; display: flex; align-items: center; gap: 4px; }

    /* Challenge icon indicator */
    .f-challenge-hint {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: #fffbeb;
        border: 1.5px solid #fde68a;
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 18px;
    }
    .f-challenge-hint svg { flex-shrink: 0; color: #d97706; margin-top: 1px; }
    .f-challenge-hint p { font-size: 0.82rem; color: #92400e; line-height: 1.5; margin: 0; }

    .f-submit-wrap { padding-top: 4px; }
    .f-submit {
        width: 100%;
        background: var(--f-green);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 14px 28px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
        box-shadow: 0 4px 14px rgba(5,150,105,0.3);
    }
    .f-submit:hover { background: #047857; box-shadow: 0 6px 20px rgba(5,150,105,0.38); transform: translateY(-1px); }
    .f-submit:active { transform: translateY(0); }

    @media (max-width: 640px) {
        .f-row, .f-row-3 { grid-template-columns: 1fr; }
        .f-wrap { padding: 0 12px 60px; }
    }
</style>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        .pc-container {
            background: var(--admin-bg) !important;
        }

        .container { max-width: 900px; margin: 0 auto; width: 100%; padding: 32px; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .form-title { font-family: 'Outfit', sans-serif; font-size: 2.2rem; font-weight: 800; color: #1e293b; margin-bottom: 40px; text-align: center; }

        .form-group { margin-bottom: 24px; }
        .label { display: block; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 12px; }
        
        input[type="text"], input[type="number"], textarea, select {
            width: 100%; background: #fcfcfc; border: 1px solid #e2e8f0; border-radius: 10px;
            padding: 16px 20px; color: #1e293b; font-family: inherit; font-size: 1.05rem; outline: none; transition: all 0.2s;
        }
        input:focus, textarea:focus, select:focus { border-color: #059669; background: #fff; box-shadow: 0 0 0 4px #d1fae5; }

        textarea { min-height: 140px; resize: vertical; }

        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 14px 28px; border-radius: 10px;
            background: #059669; color: white;
            font-weight: 800; font-size: 1.15rem; text-decoration: none;
            transition: all 0.2s; border: none; cursor: pointer; width: 100%;
            margin-top: 20px;
        }
        .btn-primary:hover { background: #047857; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); color: white;}

        .error-msg { color: #dc2626; font-size: 0.9rem; margin-top: 8px; font-weight: 600; }
        
        .btn-back-link { display: inline-flex; align-items: center; gap: 6px; color: #475569; text-decoration: none; font-size: 0.95rem; font-weight: 600; transition: color 0.2s; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px;}
        .btn-back-link:hover { background: #ffffff; color: #059669; }
    </style>
@endpush

@section('konten')
<div class="f-wrap">

    <a href="{{ route('counselor.education.challenges.index') }}" class="f-back">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Kembali
    </a>

    <div class="f-page-title">
        <h1>{{ isset($challenge) ? 'Edit Challenge' : 'Buat Challenge Baru' }}</h1>
        <p>Rancang tantangan interaktif berhadiah poin untuk mendorong mahasiswa tetap aktif menjaga kesehatan mental.</p>
    </div>

    <form action="{{ isset($challenge) ? route('counselor.education.challenges.update', $challenge->id) : route('counselor.education.challenges.store') }}"
          method="POST">
        @csrf
        @if(isset($challenge)) @method('PUT') @endif

        {{-- Section 1: Informasi Challenge --}}
        <div class="f-section">
            <div class="f-section-title">Informasi Challenge</div>

            <div class="f-challenge-hint">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <p>Challenge berupa kuis interaktif berhadiah poin yang dapat dimainkan mahasiswa. Pastikan judul dan deskripsi cukup menarik agar mahasiswa tertarik untuk mengikutinya.</p>
            </div>

            {{-- Judul --}}
            <div class="f-group">
                <label class="f-label" for="title">Judul Challenge</label>
                <input id="title" class="f-input" type="text" name="title"
                       value="{{ old('title', $challenge->title ?? '') }}"
                       placeholder="Contoh: Kuis Manajemen Stres Dasar" required>
                @error('title') <div class="f-error">⚠ {{ $message }}</div> @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="f-group">
                <label class="f-label" for="description">Deskripsi / Aturan Challenge</label>
                <textarea id="description" class="f-input f-textarea" name="description"
                          placeholder="Jelaskan tentang challenge ini dan apa yang akan didapatkan mahasiswa..." required>{{ old('description', $challenge->description ?? '') }}</textarea>
                @error('description') <div class="f-error">⚠ {{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Section 2: Pengaturan --}}
        <div class="f-section">
            <div class="f-section-title">Pengaturan</div>

            <div class="f-row-3">
                <div class="f-group">
                    <label class="f-label" for="total_questions">Total Pertanyaan</label>
                    <input id="total_questions" class="f-input" type="number" name="total_questions"
                           value="{{ old('total_questions', $challenge->total_questions ?? 10) }}" min="1" required>
                    @error('total_questions') <div class="f-error">⚠ {{ $message }}</div> @enderror
                </div>
                <div class="f-group">
                    <label class="f-label" for="reward_point">Reward Point</label>
                    <input id="reward_point" class="f-input" type="number" name="reward_point"
                           value="{{ old('reward_point', $challenge->reward_point ?? 100) }}" min="0" required>
                    @error('reward_point') <div class="f-error">⚠ {{ $message }}</div> @enderror
                </div>
                <div class="f-group">
                    <label class="f-label" for="status">Status Publikasi</label>
                    <div class="f-select-wrap">
                        <select id="status" class="f-input f-select" name="status" required>
                            <option value="1" {{ old('status', $challenge->status ?? 1) == 1 ? 'selected' : '' }}>Aktif (Publis)</option>
                            <option value="0" {{ old('status', $challenge->status ?? 1) == 0 ? 'selected' : '' }}>Nonaktif (Draft)</option>
                        </select>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="f-submit-wrap">
            <button type="submit" class="f-submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                {{ isset($challenge) ? 'Simpan Perubahan' : 'Terbitkan Challenge' }}
            </button>
        </div>

    </form>
</div>
@endsection
