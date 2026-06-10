<?php

namespace App\Support;

use App\Models\User;

class AnonymousIdentitySupport
{
    public static function namePool(): array
    {
        return [
            'Beruang',
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
        ];
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
        $cycle = intdiv($hash, $poolCount);
        $alias = $pool[$index] ?? 'Mahasiswa Anonim';

        if ($cycle > 0) {
            $alias .= ' ' . (($cycle % 9) + 2);
        }

        return $alias;
    }
}
