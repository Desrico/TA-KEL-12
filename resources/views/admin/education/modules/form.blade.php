@extends('layouts.admin')

@section('page-title', isset($module) ? 'Edit Modul' : 'Tambah Modul')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        .pc-container {
            background: #f8fafc;
        }

        .container { max-width: 850px; margin: 0 auto; width: 100%; padding: 48px 32px; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 56px 48px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02); position: relative; overflow: hidden; }
        .card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 6px; background: linear-gradient(135deg, #059669 0%, #10b981 100%); }

        .form-header { text-align: center; margin-bottom: 48px; }
        .form-icon { width: 64px; height: 64px; background: #d1fae5; color: #059669; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 20px auto; transform: rotate(-5deg); }
        .form-title { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 800; color: #1e293b; letter-spacing: -0.02em; }
        .form-subtitle { color: #94a3b8; font-size: 1.1rem; margin-top: 10px; font-weight: 600; }

        .form-group { margin-bottom: 32px; }
        .label { display: block; font-size: 0.85rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 12px; padding-left: 4px; }
        
        input[type="text"], input[type="number"], input[type="url"], input[type="file"], textarea, select {
            width: 100%; background: #f8fafc; border: 2px solid transparent; border-radius: 12px;
            padding: 18px 24px; color: #1e293b; font-family: inherit; font-size: 1.1rem; outline: none; transition: all 0.2s;
        }
        input:focus, textarea:focus, select:focus { border-color: #d1fae5; background: #fff; box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.05); }

        textarea { min-height: 160px; resize: vertical; line-height: 1.6; }

        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 12px;
            padding: 18px 36px; border-radius: 12px;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white;
            font-weight: 800; font-size: 1.2rem; text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: none; cursor: pointer; width: 100%;
            margin-top: 24px; box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.39);
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(5, 150, 105, 0.45); opacity: 0.95; color: white;}
        .btn-primary:active { transform: translateY(0); }

        .error-msg { color: #dc2626; font-size: 0.8rem; margin-top: 8px; font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .error-msg::before { content: '⚠️'; font-size: 0.9rem; }

        /* Dual Input Styling */
        .input-dual { background: #f1f5f9; border-radius: 12px; padding: 24px; margin-top: 4px; }
        .input-divider { text-align: center; font-size: 0.7rem; font-weight: 900; color: #94a3b8; margin: 20px 0; position: relative; text-transform: uppercase; letter-spacing: 0.15em; }
        .input-divider::before, .input-divider::after { content: ''; position: absolute; top: 50%; width: 30%; height: 2px; background: #e2e8f0; }
        .input-divider::before { left: 0; }
        .input-divider::after { right: 0; }

        .current-file { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; padding: 16px; background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .current-file span { font-size: 0.9rem; color: #1e293b; font-weight: 700; }
        .current-file img { width: 56px; height: 56px; object-fit: cover; border-radius: 10px; border: 1px solid #e2e8f0; }

        input[type="file"] { padding: 12px; background: #fff; font-size: 0.9rem; }
        
        .btn-back-link { display: inline-flex; align-items: center; gap: 6px; color: #475569; text-decoration: none; font-size: 0.85rem; font-weight: 500; transition: color 0.2s; padding: 8px 12px; border-radius: 8px; margin-bottom: 16px;}
        .btn-back-link:hover { background: #ffffff; color: #059669; }
    </style>
@endpush

@section('konten')
    <div class="container-fluid">
        <a href="{{ route('counselor.education.modules.index') }}" class="btn-back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali ke Daftar Modul
        </a>

        <div class="container">
            <div class="card">
                <div class="form-header">
                    <div class="form-icon">📖</div>
                    <h1 class="form-title">{{ isset($module) ? 'Edit Modul' : 'Buat Modul Baru' }}</h1>
                    <p class="form-subtitle">Lengkapi detail materi edukasi untuk mahasiswa di bawah ini.</p>
                </div>

                <form action="{{ isset($module) ? route('counselor.education.modules.update', $module->id) : route('counselor.education.modules.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($module)) @method('PUT') @endif

                    <div class="form-group">
                        <label class="label">Judul Modul</label>
                        <input type="text" name="title" value="{{ old('title', $module->title ?? '') }}" placeholder="Contoh: Teknik Grounding 5-4-3-2-1" required>
                        @error('title') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <!-- Thumbnail Dual Input -->
                    <div class="form-group">
                        <label class="label">Gambar Sampul (Thumbnail)</label>
                        <div class="input-dual">
                            @if(isset($module) && $module->thumbnail)
                                <div class="current-file">
                                    @if(Str::startsWith($module->thumbnail, 'modules/thumbnails'))
                                        <img src="{{ Storage::url($module->thumbnail) }}" alt="Current">
                                        <span>File: {{ basename($module->thumbnail) }}</span>
                                    @else
                                        <img src="{{ $module->thumbnail }}" alt="Current">
                                        <span>URL Link Aktif</span>
                                    @endif
                                </div>
                            @endif
                            
                            <input type="file" name="thumbnail_file" accept="image/*">
                            <div class="input-divider">ATAU MASUKKAN LINK</div>
                            <input type="url" name="thumbnail_url" value="{{ old('thumbnail_url', isset($module) && !Str::startsWith($module->thumbnail, 'modules/') ? $module->thumbnail : '') }}" placeholder="https://source.unsplash.com/...">
                        </div>
                        @error('thumbnail_file') <div class="error-msg">{{ $message }}</div> @enderror
                        @error('thumbnail_url') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="label">Deskripsi / Isi Singkat</label>
                        <textarea name="description" placeholder="Jelaskan isi modul ini..." required>{{ old('description', $module->description ?? '') }}</textarea>
                        @error('description') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <!-- Content Dual Input -->
                    <div class="form-group">
                        <label class="label">Dokumen / Konten Utama</label>
                        <div class="input-dual">
                            @if(isset($module) && $module->content_url)
                                <div class="current-file">
                                    @if(Str::startsWith($module->content_url, 'modules/content'))
                                        <span style="font-size: 1.5rem;">📄</span>
                                        <span>File: {{ basename($module->content_url) }}</span>
                                    @else
                                        <span style="font-size: 1.5rem;">🔗</span>
                                        <span>Link Eksternal Aktif</span>
                                    @endif
                                </div>
                            @endif

                            <input type="file" name="content_file" accept=".pdf">
                            <div class="input-divider">ATAU MASUKKAN LINK</div>
                            <input type="url" name="content_url" value="{{ old('content_url', isset($module) && !Str::startsWith($module->content_url, 'modules/') ? $module->content_url : '') }}" placeholder="https://youtube.com/... atau link artikel">
                        </div>
                        @error('content_file') <div class="error-msg">{{ $message }}</div> @enderror
                        @error('content_url') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label class="label">Reward Point</label>
                            <input type="number" name="reward_point" value="{{ old('reward_point', $module->reward_point ?? 50) }}" min="0" required>
                            @error('reward_point') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="label">Status Publis</label>
                            <select name="status" required>
                                <option value="1" {{ old('status', $module->status ?? 1) == 1 ? 'selected' : '' }}>Aktif (Publis)</option>
                                <option value="0" {{ old('status', $module->status ?? 1) == 0 ? 'selected' : '' }}>Nonaktif (Draft)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                        {{ isset($module) ? 'Simpan Perubahan' : 'Terbitkan Modul' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
