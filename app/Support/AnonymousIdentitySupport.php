<?php

namespace App\Support;

use App\Models\User;

class AnonymousIdentitySupport
{
    public static function namePool(): array
    {
        static $pool = null;

        if ($pool !== null) {
            return $pool;
        }

        $animals = [
            'Beruang',
            'Kanguru',
            'Gajah',
            'Bangau',
            'Burung Gagak',
            'Burung Hantu',
            'Rusa',
            'Kelinci',
            'Serigala',
            'Lumba-Lumba',
            'Penyu',
            'Kijang',
            'Koala',
            'Merpati',
            'Elang',
            'Rubah',
            'Harimau',
            'Panda',
            'Bebek',
            'Ikan Pari',
            'Ikan Koi',
            'Cendrawasih',
            'Tupai',
            'Kuda Laut',
            'Kupu-Kupu',
            'Jerapah',
            'Burung Pipit',
            'Salamander',
            'Bison',
            'Landak',
            'Rakun',
            'Kucing',
            'Anjing',
            'Singa',
            'Kuda',
            'Zebra',
            'Badak',
            'Unta',
            'Paus',
            'Hiu',
            'Gurita',
            'Bintang Laut',
            'Kepiting',
            'Kuda Nil',
            'Kelelawar',
            'Musang',
            'Trenggiling',
            'Komodo',
            'Iguana',
            'Kakaktua',
            'Pelikan',
            'Flamingo',
            'Rajawali',
            'Macan Tutul',
        ];

        $variants = [
            '',
            'Rimba',
            'Laut',
            'Gunung',
            'Senja',
            'Pagi',
            'Hujan',
            'Awan',
            'Bulan',
            'Bintang',
            'Cerah',
            'Teduh',
            'Damai',
            'Tenang',
            'Lincah',
            'Cermat',
            'Tangguh',
            'Bijak',
            'Riang',
            'Sahabat',
        ];

        $pool = [];

        foreach ($animals as $animal) {
            foreach ($variants as $variant) {
                $pool[] = trim($animal . ' ' . $variant);
            }
        }

        $pool = array_slice($pool, 0, 1000);

        return $pool;
    }

    // Alias anonim dibuat deterministik agar identitas online konsisten untuk user yang sama.
    public static function buildUserAlias(User $user): string
    {
        $seed = (string) ($user->id ?? $user->username_cis ?? $user->email ?? $user->nama ?? 'anon');

        return self::buildDeterministicAlias($seed);
    }

    public static function buildDeterministicAlias(int|string|null $seed): string
    {
        $seedValue = (string) ($seed ?: '0');
        $hash = hexdec(substr(hash('crc32b', $seedValue), 0, 7));
        $pool = self::namePool();
        $poolCount = count($pool);

        if ($poolCount === 0) {
            return 'Mahasiswa Anonim';
        }

        $index = $hash % $poolCount;
        $alias = $pool[$index] ?? 'Mahasiswa Anonim';

        return $alias;
    }

    public static function initialsForAlias(string $alias): string
    {
        $parts = preg_split('/\s+/', trim($alias)) ?: [];

        return collect($parts)
            ->filter()
            ->take(2)
            ->map(fn (string $part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'A';
    }
}
