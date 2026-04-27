<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KampusApiService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.kampus_api.base_url'), '/');
        $this->timeout = (int) config('services.kampus_api.timeout', 20);
    }

    public function loginWithCredentials(string $username, string $password): array
    {
        $response = Http::asMultipart()
            ->timeout($this->timeout)
            ->post($this->baseUrl . '/jwt-api/do-auth', [
                [
                    'name' => 'username',
                    'contents' => $username,
                ],
                [
                    'name' => 'password',
                    'contents' => $password,
                ],
            ]);

        $response->throw();

        $json = $response->json();

        $token = $json['token']
            ?? $json['access_token']
            ?? $json['data']['token']
            ?? null;

        if (!$token) {
            throw new \RuntimeException('Token login CIS tidak ditemukan.');
        }

        return [
            'token' => $token,
            'raw' => $json,
        ];
    }

    public function getMahasiswaByUsername(string $username, string $token): array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->timeout($this->timeout)
            ->get($this->baseUrl . '/library-api/mahasiswa', [
                'nama' => '',
                'nim' => '',
                'angkatan' => '',
                'userid' => '',
                'username' => $username,
                'prodi' => '',
                'status' => 'Aktif',
                'limit' => 1,
            ]);

        $response->throw();

        return $response->json();
    }
}