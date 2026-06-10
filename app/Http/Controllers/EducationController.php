<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\EducationContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\AboutPageContent;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class EducationController extends Controller
{
    /**
     * Display the education landing page.
     */
    public function index()
    {
        $moduleCount = Module::count();
        $webContentCount = EducationContent::count();

        return view('admin.education.index', compact(
            'moduleCount',
            'webContentCount'
        ));
    }

    // --- MODULE CRUD ---

    public function moduleIndex(\Illuminate\Http\Request $request)
    {
        $filter = $request->query('filter', 'semua'); // semua | aktif | draft
        $sort   = $request->query('sort', 'terbaru'); // terbaru | terlama | az | za

        $query = Module::query();

        if ($filter === 'aktif') {
            $query->whereIn('status', [true, 1, '1']);
        }
        if ($filter === 'draft') {
            $query->where(function($q) {
                $q->whereIn('status', [false, 0, '0'])
                  ->orWhereNull('status');
            });
        }

        match ($sort) {
            'terlama' => $query->oldest(),
            'az'      => $query->orderBy('title', 'asc'),
            'za'      => $query->orderBy('title', 'desc'),
            default   => $query->latest(),
        };

        $modules = $query->paginate(5)->withQueryString();
        return view('admin.education.modules.index', compact('modules', 'filter', 'sort'));
    }

    public function moduleCreate()
    {
        return view('admin.education.modules.form');
    }

    public function moduleStore(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_url'  => 'nullable|url',
            'description'    => 'required|string',
            'content_file'   => 'nullable|file|mimes:pdf,mp4,png,jpg,jpeg|max:51200',
            'content_url'    => 'nullable|url',
            'reward_point'   => 'required|integer|min:0',
            'status'         => 'required|boolean',
            'kategori'       => 'nullable|string|max:100',
            'target_audiens' => 'nullable|string|max:100',
        ]);

        $data = $validated;
        $data['status'] = filter_var($validated['status'], FILTER_VALIDATE_BOOLEAN);

        // Handle Thumbnail
        if ($request->hasFile('thumbnail_file')) {
            $result = Cloudinary::uploadApi()->upload($request->file('thumbnail_file')->getRealPath(), ['folder' => 'modules/thumbnails']);
            $data['thumbnail'] = $result['secure_url'];
        } else {
            $data['thumbnail'] = $request->thumbnail_url;
        }

        // Handle Content
        if ($request->hasFile('content_file')) {
            $result = Cloudinary::uploadApi()->upload($request->file('content_file')->getRealPath(), ['folder' => 'modules/content', 'resource_type' => 'raw']);
            $data['content_url'] = $result['secure_url'];
        } else {
            $data['content_url'] = $request->content_url;
        }

        Module::create($data);

        return redirect()->route('counselor.education.modules.index')
            ->with('success', 'Modul berhasil ditambahkan.');
    }

    public function moduleEdit(Module $module)
    {
        return view('admin.education.modules.form', compact('module'));
    }

    public function moduleUpdate(Request $request, Module $module)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'thumbnail_url'  => 'nullable|url',
            'description'    => 'required|string',
            'content_file'   => 'nullable|file|mimes:pdf,mp4,png,jpg,jpeg|max:51200',
            'content_url'    => 'nullable|url',
            'reward_point'   => 'required|integer|min:0',
            'status'         => 'required|boolean',
            'kategori'       => 'nullable|string|max:100',
            'target_audiens' => 'nullable|string|max:100',
        ]);

        $data = $validated;
        $data['status'] = filter_var($validated['status'], FILTER_VALIDATE_BOOLEAN);

        // Handle Thumbnail Update
        if ($request->hasFile('thumbnail_file')) {
            if ($module->thumbnail) {
                if (Str::contains($module->thumbnail, 'res.cloudinary.com')) {
                    $this->deleteFromCloudinary($module->thumbnail);
                } elseif (Storage::disk('public')->exists($module->thumbnail)) {
                    Storage::disk('public')->delete($module->thumbnail);
                }
            }
            $result = Cloudinary::uploadApi()->upload($request->file('thumbnail_file')->getRealPath(), ['folder' => 'modules/thumbnails']);
            $data['thumbnail'] = $result['secure_url'];
        } elseif ($request->filled('thumbnail_url')) {
            if ($module->thumbnail) {
                if (Str::contains($module->thumbnail, 'res.cloudinary.com')) {
                    $this->deleteFromCloudinary($module->thumbnail);
                } elseif (Storage::disk('public')->exists($module->thumbnail)) {
                    Storage::disk('public')->delete($module->thumbnail);
                }
            }
            $data['thumbnail'] = $request->thumbnail_url;
        }

        // Handle Content Update
        if ($request->hasFile('content_file')) {
            if ($module->content_url) {
                if (Str::contains($module->content_url, 'res.cloudinary.com')) {
                    $this->deleteFromCloudinary($module->content_url);
                } elseif (Storage::disk('public')->exists($module->content_url)) {
                    Storage::disk('public')->delete($module->content_url);
                }
            }
            $result = Cloudinary::uploadApi()->upload($request->file('content_file')->getRealPath(), ['folder' => 'modules/content', 'resource_type' => 'raw']);
            $data['content_url'] = $result['secure_url'];
        } elseif ($request->filled('content_url')) {
            if ($module->content_url) {
                if (Str::contains($module->content_url, 'res.cloudinary.com')) {
                    $this->deleteFromCloudinary($module->content_url);
                } elseif (Storage::disk('public')->exists($module->content_url)) {
                    Storage::disk('public')->delete($module->content_url);
                }
            }
            $data['content_url'] = $request->content_url;
        }

        $module->update($data);

        return redirect()->route('counselor.education.modules.index')
            ->with('success', 'Modul berhasil diperbarui.');
    }

    public function moduleDestroy(Module $module)
    {
        // Delete files if exist
        if ($module->thumbnail) {
            if (Str::contains($module->thumbnail, 'res.cloudinary.com')) {
                $this->deleteFromCloudinary($module->thumbnail);
            } elseif (Storage::disk('public')->exists($module->thumbnail)) {
                Storage::disk('public')->delete($module->thumbnail);
            }
        }
        if ($module->content_url) {
            if (Str::contains($module->content_url, 'res.cloudinary.com')) {
                $this->deleteFromCloudinary($module->content_url);
            } elseif (Storage::disk('public')->exists($module->content_url)) {
                Storage::disk('public')->delete($module->content_url);
            }
        }

        $module->delete();
        return redirect()->route('counselor.education.modules.index')
            ->with('success', 'Modul berhasil dihapus.');
    }
    
    protected function deleteFromCloudinary($url)
    {
        if (!$url || !str_contains($url, 'res.cloudinary.com')) return;
        
        try {
            $parts = explode('/upload/', parse_url($url, PHP_URL_PATH));
            if (count($parts) > 1) {
                $path = $parts[1];
                $segments = explode('/', $path);
                if (preg_match('/^v\d+$/', $segments[0])) {
                    array_shift($segments);
                }
                $publicIdWithExt = implode('/', $segments);
                $publicId = preg_replace('/\.[^.]+$/', '', $publicIdWithExt);
                
                // Cek apakah video
                if (str_contains($url, '/video/upload/')) {
                    Cloudinary::adminApi()->deleteAssets([$publicId], ['resource_type' => 'video']);
                } else {
                    Cloudinary::uploadApi()->destroy($publicId);
                }
            }
        } catch (\Exception $e) {
            // Abaikan error jika gagal hapus
        }
    }
    // --- WEB EDUCATION CONTENT / TREND TOPIK WEB ---

    public function webContentIndex(Request $request)
    {
        $filter = $request->query('filter', 'semua');
        $sort   = $request->query('sort', 'terbaru');

        $query = EducationContent::query();

        if ($filter === 'aktif') {
            $query->where('status', true);
        }

        if ($filter === 'draft') {
            $query->where('status', false);
        }

        match ($sort) {
            'terlama' => $query->oldest(),
            'az'      => $query->orderBy('judul', 'asc'),
            'za'      => $query->orderBy('judul', 'desc'),
            default   => $query->latest(),
        };

        $webContents = $query->paginate(8)->withQueryString();

        return view('admin.education.web-contents.index', compact(
            'webContents',
            'filter',
            'sort'
        ));
    }

    public function webContentCreate()
    {
        return view('admin.education.web-contents.form');
    }

   public function webContentStore(Request $request)
{
    $request->merge([
        'type' => strtolower(str_replace(' ', '_', trim($request->type))),
    ]);

    $validator = Validator::make($request->all(), [
        'title'       => 'required|string|max:255',
        'topic'       => 'required|string|max:100',
        'type'        => 'required|in:artikel,video,materi_edukasi',
        'source_url'  => 'nullable|url|max:1000',
        'thumbnail'   => 'nullable|url|max:1000',
        'file_materi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'excerpt'     => 'required|string',
        'status'      => 'required|boolean',
    ]);

    $validator->after(function ($validator) use ($request) {
        $type = $request->type;
        $url  = $request->source_url;

        if ($type === 'video') {
            if (!$url) {
                $validator->errors()->add('source_url', 'Link video wajib diisi jika jenis konten adalah Video.');
            } elseif (!$this->isValidVideoUrl($url)) {
                $validator->errors()->add('source_url', 'Jika jenis konten adalah Video, maka link harus berupa link video seperti YouTube, Vimeo, atau file video.');
            }
        }

        if ($type === 'artikel') {
            if ($url && $this->isValidVideoUrl($url)) {
                $validator->errors()->add('source_url', 'Jika jenis konten adalah Artikel, maka link tidak boleh berupa link video.');
            }
        }

        if ($type === 'materi_edukasi') {
            if (!$request->hasFile('file_materi')) {
                $validator->errors()->add('file_materi', 'Materi edukasi wajib diunggah dalam bentuk JPG, PNG, atau PDF.');
            }
        }
    });

    $validated = $validator->validate();

    $fileMateriPath = null;

    if ($request->hasFile('file_materi')) {
        $fileMateriPath = $request->file('file_materi')->store('materi-edukasi', 'public');
    }

    $typeLabel = match ($validated['type']) {
        'artikel' => 'Artikel',
        'video' => 'Video',
        'materi_edukasi' => 'Materi Edukasi',
    };

    EducationContent::create([
        'judul'        => $validated['title'],
        'topik'        => $validated['topic'],
        'tipe_konten'  => $typeLabel,
        'ringkasan'    => $validated['excerpt'],
        'isi_konten'   => $validated['excerpt'],
        'nama_sumber'  => null,
        'url_sumber'   => $validated['type'] === 'materi_edukasi' ? null : ($validated['source_url'] ?? null),
        'thumbnail'    => $validated['thumbnail'] ?? null,
        'file_materi'  => $fileMateriPath,
        'status'       => $validated['status'],
    ]);

    return redirect()
        ->route('counselor.education.web-contents.index')
        ->with('success', 'Konten edukasi web berhasil ditambahkan.');
}

    public function webContentEdit($id)
    {
        $webContent = EducationContent::findOrFail($id);

        return view('admin.education.web-contents.form', compact('webContent'));
    }

    public function webContentUpdate(Request $request, $id)
{
    $webContent = EducationContent::findOrFail($id);

    $request->merge([
        'type' => strtolower(str_replace(' ', '_', trim($request->type))),
    ]);

    $validator = Validator::make($request->all(), [
        'title'       => 'required|string|max:255',
        'topic'       => 'required|string|max:100',
        'type'        => 'required|in:artikel,video,materi_edukasi',
        'source_url'  => 'nullable|url|max:1000',
        'thumbnail'   => 'nullable|url|max:1000',
        'file_materi' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'excerpt'     => 'required|string',
        'status'      => 'required|boolean',
    ]);

    $validator->after(function ($validator) use ($request, $webContent) {
        $type = $request->type;
        $url  = $request->source_url;

        if ($type === 'video') {
            if (!$url) {
                $validator->errors()->add('source_url', 'Link video wajib diisi jika jenis konten adalah Video.');
            } elseif (!$this->isValidVideoUrl($url)) {
                $validator->errors()->add('source_url', 'Jika jenis konten adalah Video, maka link harus berupa link video seperti YouTube, Vimeo, atau file video.');
            }
        }

        if ($type === 'artikel') {
            if ($url && $this->isValidVideoUrl($url)) {
                $validator->errors()->add('source_url', 'Jika jenis konten adalah Artikel, maka link tidak boleh berupa link video.');
            }
        }

        if ($type === 'materi_edukasi') {
            if (!$request->hasFile('file_materi') && !$webContent->file_materi) {
                $validator->errors()->add('file_materi', 'Materi edukasi wajib diunggah dalam bentuk JPG, PNG, atau PDF.');
            }
        }
    });

    $validated = $validator->validate();

    $fileMateriPath = $webContent->file_materi;

    if ($request->hasFile('file_materi')) {
        if ($webContent->file_materi && Storage::disk('public')->exists($webContent->file_materi)) {
            Storage::disk('public')->delete($webContent->file_materi);
        }

        $fileMateriPath = $request->file('file_materi')->store('materi-edukasi', 'public');
    }

    if ($validated['type'] !== 'materi_edukasi') {
        if ($webContent->file_materi && Storage::disk('public')->exists($webContent->file_materi)) {
            Storage::disk('public')->delete($webContent->file_materi);
        }

        $fileMateriPath = null;
    }

    $typeLabel = match ($validated['type']) {
        'artikel' => 'Artikel',
        'video' => 'Video',
        'materi_edukasi' => 'Materi Edukasi',
    };

    $webContent->update([
        'judul'        => $validated['title'],
        'topik'        => $validated['topic'],
        'tipe_konten'  => $typeLabel,
        'ringkasan'    => $validated['excerpt'],
        'isi_konten'   => $validated['excerpt'],
        'nama_sumber'  => null,
        'url_sumber'   => $validated['type'] === 'materi_edukasi' ? null : ($validated['source_url'] ?? null),
        'thumbnail'    => $validated['thumbnail'] ?? null,
        'file_materi'  => $fileMateriPath,
        'status'       => $validated['status'],
    ]);

    return redirect()
        ->route('counselor.education.web-contents.index')
        ->with('success', 'Konten edukasi web berhasil diperbarui.');
}

    public function webContentDestroy($id)
{
    $webContent = EducationContent::findOrFail($id);

    if ($webContent->thumbnail && Storage::disk('public')->exists($webContent->thumbnail)) {
        Storage::disk('public')->delete($webContent->thumbnail);
    }

    $webContent->delete();

    return redirect()
        ->route('counselor.education.web-contents.index')
        ->with('success', 'Konten edukasi web berhasil dihapus.');
}



public function show()
{
    $contents = EducationContent::where('status', true)
        ->latest()
        ->get()
        ->map(function ($item) {
            return [
                'type' => $item->type,
                'icon' => $item->type === 'Video' ? 'bi-play-circle' : 'bi-file-earmark-text',
                'category' => $item->topic,
                'time' => $item->reading_time,
                'title' => $item->title,
                'desc' => $item->excerpt,
                'detail' => $item->excerpt,
                'thumbnail' => $this->resolveThumbnail($item),
                'source_url' => $item->source_url,
                'points' => [
                    'Baca konten ini untuk memahami topik secara lebih sederhana.',
                    'Gunakan konten ini sebagai edukasi awal.',
                    'Hubungi konselor jika kondisi mulai mengganggu aktivitas harian.',
                ],
            ];
        });

    return view('Pages.edukasi', [
        'pageContent' => $this->getMergedContent(),
        'contents' => $contents,
    ]);
}

protected function extractYoutubeId($url)
{
    if (!$url) {
        return null;
    }

    preg_match(
        '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/',
        $url,
        $matches
    );

    return $matches[1] ?? null;
}

private function isValidVideoUrl(?string $url): bool
{
    if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $host  = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
    $path  = strtolower(parse_url($url, PHP_URL_PATH) ?? '');
    $query = parse_url($url, PHP_URL_QUERY) ?? '';

    // YouTube
    if (str_contains($host, 'youtube.com')) {
        parse_str($query, $params);

        return (
            ($path === '/watch' && !empty($params['v'])) ||
            str_starts_with($path, '/embed/') ||
            str_starts_with($path, '/shorts/')
        );
    }

    // YouTube short link
    if (str_contains($host, 'youtu.be')) {
        return trim($path, '/') !== '';
    }

    // Vimeo
    if (str_contains($host, 'vimeo.com')) {
        return preg_match('/^\/\d+/', $path);
    }

    // Direct video file
    if (preg_match('/\.(mp4|webm|ogg|mov)(\?.*)?$/i', $url)) {
        return true;
    }

    return false;
}

protected function resolveThumbnail($item)
{
    if (!empty($item->thumbnail)) {
        return Str::startsWith($item->thumbnail, ['http://', 'https://'])
            ? $item->thumbnail
            : Storage::url($item->thumbnail);
    }

    if (strtolower($item->type) === 'video' && !empty($item->source_url)) {
        $youtubeId = $this->extractYoutubeId($item->source_url);

        if ($youtubeId) {
            return "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
        }
    }

    return null;
}

    public function aboutPageEdit()
    {
        return view('admin.education.about-page.form', [
            'pageContent' => $this->getMergedContent(),
        ]);
    }

    public function aboutPageUpdate(Request $request)
    {
        $validated = $request->validate([
            'video_badge' => 'required|string|max:60',
            'video_title' => 'required|string|max:120',
            'video_description' => 'required|string|max:255',
            'video_caption' => 'required|string|max:120',
            'video_duration' => 'required|string|max:10',

            'article_section_title' => 'required|string|max:120',
            'article_section_description' => 'required|string|max:255',
            'article_categories' => 'required|array|size:3',
            'article_categories.*' => 'required|string|max:60',
            'article_read_times' => 'required|array|size:3',
            'article_read_times.*' => 'required|string|max:40',
            'article_titles' => 'required|array|size:3',
            'article_titles.*' => 'required|string|max:120',
            'article_excerpts' => 'required|array|size:3',
            'article_excerpts.*' => 'required|string|max:255',
            'article_links' => 'nullable|array|size:3',
            'article_links.*' => 'nullable|string|max:255',
            'article_image_urls' => 'nullable|array|size:3',
            'article_image_urls.*' => 'nullable|string|max:255',
            'article_image_files' => 'nullable|array',
            'article_image_files.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',

            'trending_section_title' => 'required|string|max:120',
            'trending_section_description' => 'required|string|max:255',
            'trending_summary' => 'required|string|max:255',
            'trending_titles' => 'required|array|size:3',
            'trending_titles.*' => 'required|string|max:120',
            'trending_insights' => 'required|array|size:3',
            'trending_insights.*' => 'required|string|max:255',
            'weekly_hashtags' => 'required|string|max:500',
        ]);

        try {
            $content = $this->findContentDocument();
            $existingArticles = collect($content->articles ?? $this->defaultContent()['articles'])->values();

            $articles = [];

            for ($index = 0; $index < 3; $index++) {
                $existingArticle = $existingArticles->get($index, []);
                $articles[] = [
                    'category' => trim($validated['article_categories'][$index]),
                    'read_time' => trim($validated['article_read_times'][$index]),
                    'title' => trim($validated['article_titles'][$index]),
                    'excerpt' => trim($validated['article_excerpts'][$index]),
                    'link' => trim((string) ($validated['article_links'][$index] ?? '#')) ?: '#',
                    'image' => $this->resolveArticleImage(
                        $request,
                        $index,
                        data_get($existingArticle, 'image')
                    ),
                ];
            }

            $trendingTopics = [];

            for ($index = 0; $index < 3; $index++) {
                $trendingTopics[] = [
                    'title' => trim($validated['trending_titles'][$index]),
                    'insight' => trim($validated['trending_insights'][$index]),
                ];
            }

            $content->fill([
                'page_key' => 'tentang',
                'video_badge' => trim($validated['video_badge']),
                'video_title' => trim($validated['video_title']),
                'video_description' => trim($validated['video_description']),
                'video_caption' => trim($validated['video_caption']),
                'video_duration' => trim($validated['video_duration']),
                'article_section_title' => trim($validated['article_section_title']),
                'article_section_description' => trim($validated['article_section_description']),
                'articles' => $articles,
                'trending_section_title' => trim($validated['trending_section_title']),
                'trending_section_description' => trim($validated['trending_section_description']),
                'trending_summary' => trim($validated['trending_summary']),
                'trending_topics' => $trendingTopics,
                'weekly_hashtags' => $this->parseHashtags($validated['weekly_hashtags']),
            ]);

            $content->save();
        } catch (\Throwable $exception) {
            return back()
                ->withInput()
                ->with('error', 'Konten halaman Tentang belum dapat disimpan. Periksa koneksi data lalu coba lagi.');
        }

        return redirect()
            ->route('counselor.education.about-page.edit')
            ->with('success', 'Konten halaman Tentang berhasil diperbarui.');
    }

    private function getMergedContent(): array
    {
        $defaults = $this->defaultContent();

        try {
            $stored = $this->findContentDocument()->toArray();
        } catch (\Throwable $exception) {
            $stored = [];
        }

        $articles = collect(data_get($stored, 'articles', []))
            ->values()
            ->pad(3, [])
            ->take(3)
            ->map(function (array $article, int $index) use ($defaults) {
                return array_merge($defaults['articles'][$index], $article);
            })
            ->all();

        $trendingTopics = collect(data_get($stored, 'trending_topics', []))
            ->values()
            ->pad(3, [])
            ->take(3)
            ->map(function (array $topic, int $index) use ($defaults) {
                return array_merge($defaults['trending_topics'][$index], $topic);
            })
            ->all();

        return array_merge($defaults, $stored, [
            'articles' => $articles,
            'trending_topics' => $trendingTopics,
            'weekly_hashtags' => $this->mergeHashtags(
                collect(data_get($stored, 'weekly_hashtags', [])),
                collect($defaults['weekly_hashtags'])
            ),
        ]);
    }

    private function findContentDocument(): AboutPageContent
    {
        return AboutPageContent::firstOrNew(['page_key' => 'tentang']);
    }

    private function resolveArticleImage(Request $request, int $index, ?string $currentImage): string
    {
        $uploadedFile = $request->file("article_image_files.$index");
        $imageUrl = trim((string) $request->input("article_image_urls.$index", ''));

        if ($uploadedFile) {
            $this->deleteStoredImage($currentImage);

            return $uploadedFile->store('about/articles', 'public');
        }

        if ($imageUrl !== '') {
            if ($currentImage !== $imageUrl) {
                $this->deleteStoredImage($currentImage);
            }

            return $imageUrl;
        }

        return $currentImage ?: $this->defaultContent()['articles'][$index]['image'];
    }

    private function deleteStoredImage(?string $path): void
    {
        if (filled($path) && Str::startsWith($path, 'about/articles/') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function parseHashtags(string $value): array
    {
        return collect(preg_split('/[\r\n,]+/', $value) ?: [])
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->map(fn ($tag) => Str::startsWith($tag, '#') ? $tag : '#' . ltrim($tag, '#'))
            ->unique()
            ->take(8)
            ->values()
            ->all();
    }

    private function mergeHashtags(Collection $stored, Collection $defaults): array
    {
        return $stored
            ->filter(fn ($tag) => filled($tag))
            ->map(fn ($tag) => trim((string) $tag))
            ->take(8)
            ->values()
            ->whenEmpty(fn (Collection $tags) => $defaults)
            ->all();
    }

    private function defaultContent(): array
    {
        return [
            'video_badge' => 'Relaksasi 5 Menit',
            'video_title' => 'Waktunya Sejenak Relaksasi',
            'video_description' => 'Tarik napas dalam-dalam. Luangkan waktu 5 menit untuk menenangkan pikiran Anda dengan panduan meditasi visual di bawah ini.',
            'video_caption' => 'Menenangkan Pikiran Sebelum Ujian',
            'video_duration' => '05:00',
            'article_section_title' => 'Dukungan Mahasiswa',
            'article_section_description' => 'Kumpulan artikel dan panduan praktis untuk membantu Anda menavigasi tantangan kehidupan kampus.',
            'articles' => [
                [
                    'category' => 'TIPS AKADEMIK',
                    'read_time' => '5 menit baca',
                    'title' => 'Manajemen Waktu untuk Mahasiswa Sibuk',
                    'excerpt' => 'Pelajari teknik praktis menyusun prioritas, mengatur ritme belajar, dan menjaga energi selama pekan akademik yang padat.',
                    'image' => 'template/dist/assets/images/slider/img-slide-1.jpg',
                    'link' => '#',
                ],
                [
                    'category' => 'KESEHATAN MENTAL',
                    'read_time' => '5 menit baca',
                    'title' => 'Mengapa Kesehatan Mental Itu Penting?',
                    'excerpt' => 'Memahami hubungan antara kesejahteraan pikiran dan performa belajar agar Anda lebih siap menghadapi tekanan kampus.',
                    'image' => 'template/dist/assets/images/slider/img-slide-2.jpg',
                    'link' => '#',
                ],
                [
                    'category' => 'SOSIAL',
                    'read_time' => '6 menit baca',
                    'title' => 'Membangun Batasan dalam Pertemanan',
                    'excerpt' => 'Tips menjaga hubungan yang sehat dengan teman sebaya sambil tetap menghormati kebutuhan diri sendiri.',
                    'image' => 'template/dist/assets/images/slider/img-slide-3.jpg',
                    'link' => '#',
                ],
            ],
            'trending_section_title' => 'Trending Topik',
            'trending_section_description' => 'Update terkini dari aktivitas komunitas mahasiswa Campus Care.',
            'trending_summary' => 'Tiga topik ini paling sering dibahas mahasiswa dalam beberapa waktu terakhir, mulai dari relasi, tekanan akademik, hingga kestabilan finansial.',
            'trending_topics' => [
                [
                    'title' => 'Dating & Campus Life',
                    'insight' => 'Mahasiswa banyak membahas relasi yang sehat, komunikasi dengan pasangan, dan cara menjaga fokus akademik di tengah hubungan.',
                ],
                [
                    'title' => 'Stress Burnout Akademik',
                    'insight' => 'Kelelahan menjelang deadline, tugas bertumpuk, dan tekanan performa menjadi isu yang paling sering muncul pada masa perkuliahan aktif.',
                ],
                [
                    'title' => 'Masalah Finansial',
                    'insight' => 'Topik tentang pengelolaan uang saku, tekanan biaya hidup, dan rasa cemas terhadap kebutuhan harian terus meningkat pekan ini.',
                ],
            ],
            'weekly_hashtags' => [
                '#DatingTalks',
                '#AcademicRecovery',
                '#MindfulBreak',
                '#StressManagement',
                '#PeerSupport',
                '#FinancialWellness',
            ],
        ];
    }
}
