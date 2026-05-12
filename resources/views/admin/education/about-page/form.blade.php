@extends('layouts.admin')

@section('page-title', 'Konten Halaman Tentang')

@push('styles')
    <style>
        .pc-container {
            background: #f8fafc;
        }

        .about-admin-wrap {
            max-width: 1080px;
            margin: 0 auto;
            padding: 24px 20px 40px;
        }

        .about-admin-header {
            margin-bottom: 28px;
        }

        .about-admin-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .about-admin-header p {
            max-width: 760px;
            color: #475569;
            line-height: 1.8;
            margin: 0;
        }

        .about-admin-form {
            display: grid;
            gap: 22px;
        }

        .content-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.05);
        }

        .content-card h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .content-card > p {
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 22px;
        }

        .field-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field-grid.single {
            grid-template-columns: 1fr;
        }

        .field-block {
            display: grid;
            gap: 10px;
        }

        .field-block label {
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #64748b;
        }

        .field-block input,
        .field-block textarea {
            width: 100%;
            border: 1px solid #dbe7df;
            border-radius: 14px;
            background: #f8fbf8;
            padding: 14px 16px;
            color: #0f172a;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .field-block textarea {
            min-height: 110px;
            resize: vertical;
        }

        .field-block input:focus,
        .field-block textarea:focus {
            border-color: #10b981;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.10);
        }

        .slot-card {
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 20px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fcfa 100%);
        }

        .slot-card h3 {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .hint-text {
            font-size: 0.84rem;
            color: #64748b;
            line-height: 1.7;
            margin-top: -2px;
        }

        .preview-thumb {
            width: 100%;
            max-width: 220px;
            height: 132px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #dbe7df;
            background: #eef6f0;
            margin-top: 8px;
        }

        .preview-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .error-text {
            font-size: 0.82rem;
            color: #dc2626;
            margin-top: -4px;
        }

        .submit-row {
            display: flex;
            justify-content: flex-end;
        }

        .save-btn {
            border: 0;
            border-radius: 999px;
            background: #065f46;
            color: #fff;
            padding: 14px 24px;
            font-weight: 700;
            box-shadow: 0 12px 24px rgba(6, 95, 70, 0.18);
        }

        .save-btn:hover {
            background: #047857;
        }

        @media (max-width: 767.98px) {
            .about-admin-wrap {
                padding-inline: 8px;
            }

            .content-card {
                padding: 22px 18px;
                border-radius: 20px;
            }

            .field-grid {
                grid-template-columns: 1fr;
            }

            .submit-row {
                justify-content: stretch;
            }

            .save-btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('konten')
    @php
        $content = $pageContent ?? [];
        $articles = collect(data_get($content, 'articles', []))->values();
        $trendingTopics = collect(data_get($content, 'trending_topics', []))->values();
        $resolveImage = function ($path) {
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

    <div class="about-admin-wrap">
        <div class="about-admin-header">
            <h1>Kelola Konten Halaman Tentang</h1>
            <p>Perbarui section relaksasi, tiga artikel dukungan mahasiswa, serta trending topik mingguan tanpa mengubah Blade secara manual.</p>
        </div>

        <form action="{{ route('counselor.education.about-page.update') }}" method="POST" enctype="multipart/form-data" class="about-admin-form">
            @csrf
            @method('PUT')

            <section class="content-card">
                <h2>Section Relaksasi</h2>
                <p>Konten ini tampil di bagian video relaksasi visual pada halaman Tentang.</p>

                <div class="field-grid">
                    <div class="field-block">
                        <label for="video_badge">Badge</label>
                        <input id="video_badge" type="text" name="video_badge" value="{{ old('video_badge', data_get($content, 'video_badge')) }}" required>
                        @error('video_badge') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field-block">
                        <label for="video_duration">Durasi</label>
                        <input id="video_duration" type="text" name="video_duration" value="{{ old('video_duration', data_get($content, 'video_duration')) }}" required>
                        @error('video_duration') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field-grid single">
                    <div class="field-block">
                        <label for="video_title">Judul Section</label>
                        <input id="video_title" type="text" name="video_title" value="{{ old('video_title', data_get($content, 'video_title')) }}" required>
                        @error('video_title') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field-block">
                        <label for="video_description">Deskripsi</label>
                        <textarea id="video_description" name="video_description" required>{{ old('video_description', data_get($content, 'video_description')) }}</textarea>
                        @error('video_description') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field-block">
                        <label for="video_caption">Judul Dalam Player</label>
                        <input id="video_caption" type="text" name="video_caption" value="{{ old('video_caption', data_get($content, 'video_caption')) }}" required>
                        @error('video_caption') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>
            </section>

            <section class="content-card">
                <h2>Artikel Dukungan Mahasiswa</h2>
                <p>Tiga kartu artikel ini tampil tepat di bawah section relaksasi. Setiap slot akan ditampilkan sebagai satu kartu.</p>

                <div class="field-grid single">
                    <div class="field-block">
                        <label for="article_section_title">Judul Section</label>
                        <input id="article_section_title" type="text" name="article_section_title" value="{{ old('article_section_title', data_get($content, 'article_section_title')) }}" required>
                        @error('article_section_title') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field-block">
                        <label for="article_section_description">Deskripsi Section</label>
                        <textarea id="article_section_description" name="article_section_description" required>{{ old('article_section_description', data_get($content, 'article_section_description')) }}</textarea>
                        @error('article_section_description') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="about-admin-form">
                    @for ($index = 0; $index < 3; $index++)
                        @php
                            $article = $articles->get($index, []);
                            $previewImage = $resolveImage(data_get($article, 'image'));
                        @endphp

                        <div class="slot-card">
                            <h3>Artikel {{ $index + 1 }}</h3>
                            <div class="field-grid">
                                <div class="field-block">
                                    <label>Kategori</label>
                                    <input type="text" name="article_categories[]" value="{{ old('article_categories.'.$index, data_get($article, 'category')) }}" required>
                                    @error('article_categories.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="field-block">
                                    <label>Estimasi Baca</label>
                                    <input type="text" name="article_read_times[]" value="{{ old('article_read_times.'.$index, data_get($article, 'read_time')) }}" required>
                                    @error('article_read_times.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="field-grid single">
                                <div class="field-block">
                                    <label>Judul Artikel</label>
                                    <input type="text" name="article_titles[]" value="{{ old('article_titles.'.$index, data_get($article, 'title')) }}" required>
                                    @error('article_titles.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="field-block">
                                    <label>Ringkasan Artikel</label>
                                    <textarea name="article_excerpts[]" required>{{ old('article_excerpts.'.$index, data_get($article, 'excerpt')) }}</textarea>
                                    @error('article_excerpts.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="field-block">
                                    <label>Link Artikel</label>
                                    <input type="text" name="article_links[]" value="{{ old('article_links.'.$index, data_get($article, 'link', '#')) }}" placeholder="#">
                                    @error('article_links.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="field-grid">
                                <div class="field-block">
                                    <label>Upload Gambar Baru</label>
                                    <input type="file" name="article_image_files[]" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="hint-text">Kosongkan jika ingin mempertahankan gambar yang sekarang.</div>
                                    @error('article_image_files.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="field-block">
                                    <label>Atau Gunakan URL / Asset Path</label>
                                    <input type="text" name="article_image_urls[]" value="{{ old('article_image_urls.'.$index, data_get($article, 'image')) }}" placeholder="https://... atau template/dist/assets/...">
                                    <div class="hint-text">Bisa URL eksternal, file storage, atau asset lokal seperti `template/dist/assets/...`.</div>
                                    @error('article_image_urls.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            @if($previewImage)
                                <div class="preview-thumb">
                                    <img src="{{ $previewImage }}" alt="Preview artikel {{ $index + 1 }}">
                                </div>
                            @endif
                        </div>
                    @endfor
                </div>
            </section>

            <section class="content-card">
                <h2>Trending Topik</h2>
                <p>Bagian ini menampilkan tiga topik yang paling sering dibahas akhir-akhir ini, beserta hashtag populer minggu ini.</p>

                <div class="field-grid single">
                    <div class="field-block">
                        <label for="trending_section_title">Judul Section</label>
                        <input id="trending_section_title" type="text" name="trending_section_title" value="{{ old('trending_section_title', data_get($content, 'trending_section_title')) }}" required>
                        @error('trending_section_title') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field-block">
                        <label for="trending_section_description">Deskripsi Section</label>
                        <textarea id="trending_section_description" name="trending_section_description" required>{{ old('trending_section_description', data_get($content, 'trending_section_description')) }}</textarea>
                        @error('trending_section_description') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                    <div class="field-block">
                        <label for="trending_summary">Ringkasan Card Lebar</label>
                        <textarea id="trending_summary" name="trending_summary" required>{{ old('trending_summary', data_get($content, 'trending_summary')) }}</textarea>
                        @error('trending_summary') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="about-admin-form">
                    @for ($index = 0; $index < 3; $index++)
                        @php $topic = $trendingTopics->get($index, []); @endphp
                        <div class="slot-card">
                            <h3>Topik {{ $index + 1 }}</h3>
                            <div class="field-grid single">
                                <div class="field-block">
                                    <label>Nama Topik</label>
                                    <input type="text" name="trending_titles[]" value="{{ old('trending_titles.'.$index, data_get($topic, 'title')) }}" required>
                                    @error('trending_titles.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                                <div class="field-block">
                                    <label>Insight Singkat</label>
                                    <textarea name="trending_insights[]" required>{{ old('trending_insights.'.$index, data_get($topic, 'insight')) }}</textarea>
                                    @error('trending_insights.'.$index) <div class="error-text">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="field-grid single">
                    <div class="field-block">
                        <label for="weekly_hashtags">Hashtag Populer Minggu Ini</label>
                        <textarea id="weekly_hashtags" name="weekly_hashtags" required>{{ old('weekly_hashtags', collect(data_get($content, 'weekly_hashtags', []))->implode(PHP_EOL)) }}</textarea>
                        <div class="hint-text">Masukkan satu hashtag per baris atau pisahkan dengan koma.</div>
                        @error('weekly_hashtags') <div class="error-text">{{ $message }}</div> @enderror
                    </div>
                </div>
            </section>

            <div class="submit-row">
                <button type="submit" class="save-btn">Simpan Konten Halaman Tentang</button>
            </div>
        </form>
    </div>
@endsection
