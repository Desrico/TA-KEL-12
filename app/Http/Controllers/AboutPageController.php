<?php

namespace App\Http\Controllers;

use App\Models\AboutPageContent;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutPageController extends Controller
{
    public function show()
    {
        return view('Pages.tentang', [
            'pageContent' => $this->getMergedContent(),
        ]);
    }

    public function edit()
    {
        return view('admin.education.about-page.form', [
            'pageContent' => $this->getMergedContent(),
        ]);
    }

    public function update(Request $request)
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
