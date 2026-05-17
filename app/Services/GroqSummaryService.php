<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GroqSummaryService
{
    public function summarize(array $sessions): string
    {
        $apiKey = config('services.groq.api_key');
        $model = config('services.groq.model', 'llama-3.1-8b-instant');

        if (empty($apiKey)) {
            throw new RuntimeException('GROQ_API_KEY belum dikonfigurasi.');
        }

        if (empty($sessions)) {
            throw new RuntimeException('Belum ada laporan sesi konseling yang dapat diringkas.');
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout((int) config('services.groq.timeout', 60))
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Kamu adalah asisten pendukung konselor kampus. Tugasmu hanya merangkum laporan konseling berdasarkan informasi yang diberikan. Jangan membuat diagnosis medis, psikologis, atau kesimpulan yang tidak tertulis di laporan. Jangan menambah fakta baru. Gunakan bahasa Indonesia yang jelas, singkat, profesional, empatik, dan mudah dipahami.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->buildPrompt($sessions),
                    ],
                ],
                'temperature' => 0.2,
                'max_completion_tokens' => 500,
            ]);

        if ($response->failed()) {
            $message = $response->json('error.message') ?: $response->body();
            throw new RuntimeException('Gagal menghubungi Groq API: ' . $message);
        }

        $summary = trim((string) $response->json('choices.0.message.content'));

        if ($summary === '') {
            throw new RuntimeException('Groq API tidak mengembalikan ringkasan.');
        }

        return $summary;
    }

    private function buildPrompt(array $sessions): string
    {
        return implode("\n", [
            'Buat ringkasan laporan konseling berikut dengan format:',
            '1. Ringkasan Masalah Utama',
            '2. Kondisi/Perkembangan Mahasiswa',
            '3. Catatan Penting dari Konselor',
            '4. Tindak Lanjut yang Disarankan Berdasarkan Laporan',
            '',
            'Catatan:',
            '- Jangan menambah informasi di luar laporan.',
            '- Jangan membuat diagnosis.',
            "- Jika ada bagian yang tidak tersedia, tulis 'Tidak disebutkan dalam laporan'.",
            '',
            'Isi laporan:',
            $this->formatSessions($sessions),
        ]);
    }

    private function formatSessions(array $sessions): string
    {
        $lines = [];

        foreach ($sessions as $index => $session) {
            $lines[] = '';
            $lines[] = 'Sesi ' . ($index + 1) . ':';
            $lines[] = 'Topik: ' . ($session['topik'] ?: 'Tidak disebutkan dalam laporan');
            $lines[] = 'Ringkasan masalah: ' . ($session['ringkasan_masalah'] ?: 'Tidak disebutkan dalam laporan');
            $lines[] = 'Observasi konselor: ' . ($session['observasi_konselor'] ?: 'Tidak disebutkan dalam laporan');
            $lines[] = 'Progress: ' . ($session['progress'] ?: 'Tidak disebutkan dalam laporan');
            $lines[] = 'Tindak lanjut: ' . ($session['tindak_lanjut'] ?: 'Tidak disebutkan dalam laporan');
        }

        return trim(implode("\n", $lines));
    }
}
