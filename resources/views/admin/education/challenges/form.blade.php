@extends('layouts.admin')

@section('page-title', isset($challenge) ? 'Edit Challenge' : 'Tambah Challenge')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        .pc-container {
            background: #f8fafc;
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
    <div class="container-fluid">
        <a href="{{ route('counselor.education.challenges.index') }}" class="btn-back-link">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Batal & Kembali ke Daftar
        </a>

        <div class="container">
            <div class="card">
                <h1 class="form-title">{{ isset($challenge) ? '📝 Edit Challenge' : '✨ Buat Challenge Baru' }}</h1>

                <form action="{{ isset($challenge) ? route('counselor.education.challenges.update', $challenge->id) : route('counselor.education.challenges.store') }}" method="POST">
                    @csrf
                    @if(isset($challenge)) @method('PUT') @endif

                    <div class="form-group">
                        <label class="label">Judul Challenge</label>
                        <input type="text" name="title" value="{{ old('title', $challenge->title ?? '') }}" placeholder="Contoh: Kuis Manajemen Stres Dasar" required>
                        @error('title') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Deskripsi / Aturan Kuis</label>
                        <textarea name="description" placeholder="Jelaskan tentang kuis ini dan apa yang akan didapatkan mahasiswa..." required>{{ old('description', $challenge->description ?? '') }}</textarea>
                        @error('description') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label class="label">Total Pertanyaan</label>
                            <input type="number" name="total_questions" value="{{ old('total_questions', $challenge->total_questions ?? 10) }}" min="1" required>
                            @error('total_questions') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="label">Reward Point</label>
                            <input type="number" name="reward_point" value="{{ old('reward_point', $challenge->reward_point ?? 100) }}" min="0" required>
                            @error('reward_point') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="label">Status Publis</label>
                        <select name="status" required>
                            <option value="1" {{ old('status', $challenge->status ?? 1) == 1 ? 'selected' : '' }}>Aktif (Publis)</option>
                            <option value="0" {{ old('status', $challenge->status ?? 1) == 0 ? 'selected' : '' }}>Nonaktif (Draft)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        {{ isset($challenge) ? 'Simpan Perubahan' : 'Terbitkan Challenge' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
