@extends('layouts.admin')

@section('page-title', 'Edukasi & Intervensi')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        .pc-container {
            background: #f8fafc;
        }

        .container { max-width: 1000px; margin: 0 auto; width: 100%; padding: 40px 32px; }

        .page-header { margin-bottom: 48px; text-align: center; }
        .page-header h1 { font-family: 'Outfit', sans-serif; font-size: 2.6rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 12px; color: #1e293b; }
        .page-header p { color: #475569; font-size: 1.1rem; max-width: 600px; margin: 0 auto; line-height: 1.6; }

        .choice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
            margin-top: 20px;
        }

        .choice-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 40px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .choice-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at top right, rgba(5, 150, 105, 0.05), transparent 60%);
            opacity: 0; transition: opacity 0.3s;
        }

        .choice-card:hover {
            transform: translateY(-8px);
            border-color: rgba(5, 150, 105, 0.4);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }

        .choice-card:hover::before { opacity: 1; }

        .icon-box {
            width: 80px; height: 80px;
            background: #eefdf5;
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 24px;
            border: 1px solid #d1fae5;
            transition: transform 0.3s;
        }

        .choice-card:hover .icon-box { transform: scale(1.1) rotate(5deg); background: #d1fae5; }

        .choice-title { font-size: 1.5rem; font-weight: 800; font-family: 'Outfit', sans-serif; margin-bottom: 12px; color: #1e293b; }
        .choice-desc { color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px; }

        .badge {
            background: #eefdf5; color: #059669;
            padding: 6px 16px; border-radius: 999px;
            font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
        }
    </style>
@endpush

@section('konten')
    <div class="container-fluid">
        <div class="page-header">
            <h1>Pusat Edukasi & Intervensi</h1>
            <p>Kelola konten pembelajaran dan tantangan untuk membantu mahasiswa meningkatkan kualitas kesehatan mental mereka.</p>
        </div>

        <div class="choice-grid">
            <!-- Module Choice -->
            <a href="{{ route('counselor.education.modules.index') }}" class="choice-card">
                <div class="icon-box">📖</div>
                <div class="choice-title">Manajemen Modul</div>
                <div class="choice-desc">Buat dan kelola materi edukasi seperti artikel, tips relaksasi, atau panduan psikologis untuk mahasiswa.</div>
                <div class="badge">{{ $moduleCount ?? 0 }} Terdaftar</div>
            </a>

            <!-- Challenge Choice -->
            <a href="{{ route('counselor.education.challenges.index') }}" class="choice-card">
                <div class="icon-box">🎮</div>
                <div class="choice-title">Manajemen Challenge</div>
                <div class="choice-desc">Kembangkan kuis dan tantangan interaktif berhadiah poin untuk memotivasi mahasiswa tetap aktif menjaga diri.</div>
                <div class="badge">{{ $challengeCount ?? 0 }} Terdaftar</div>
            </a>
        </div>
    </div>
@endsection
