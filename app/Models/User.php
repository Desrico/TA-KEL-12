<?php

namespace App\Models;

use App\Support\AnonymousIdentitySupport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'nama',
        'email',
        'username_cis',
        'password',
        'role',
        'is_anonim',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_anonim' => 'boolean',
        ];
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function konselor(): HasOne
    {
        return $this->hasOne(Konselor::class);
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'pengirim_id');
    }

    public function groupChatMemberships(): HasMany
    {
        return $this->hasMany(GroupChatMember::class, 'user_id');
    }

    public function groupChatMessages(): HasMany
    {
        return $this->hasMany(GroupChatMessage::class, 'user_id');
    }

    public function isAnonim(): bool
    {
        return (bool) $this->is_anonim;
    }

    public function getAnonimAnimal(): array
    {
        $animals = [
            ['name' => 'Kelelawar', 'emoji' => '🦇'],
            ['name' => 'Kucing', 'emoji' => '🐱'],
            ['name' => 'Kelinci', 'emoji' => '🐰'],
            ['name' => 'Rubah', 'emoji' => '🦊'],
            ['name' => 'Panda', 'emoji' => '🐼'],
            ['name' => 'Koala', 'emoji' => '🐨'],
            ['name' => 'Beruang', 'emoji' => '🐻'],
            ['name' => 'Harimau', 'emoji' => '🐯'],
            ['name' => 'Singa', 'emoji' => '🦁'],
            ['name' => 'Burung Hantu', 'emoji' => '🦉'],
            ['name' => 'Kura-kura', 'emoji' => '🐢'],
            ['name' => 'Paus', 'emoji' => '🐳'],
        ];

        $seed = (string) ($this->id ?? $this->username_cis ?? $this->email ?? $this->nama ?? 'anon');
        $index = abs(crc32($seed)) % count($animals);

        return $animals[$index];
    }

    public function getAnonimDisplayName(): string
    {
        $animal = $this->getAnonimAnimal();

        return $animal['name'] . ' Anonim';
    }

    public function getAnonimAvatarSvg(): string
    {
        $animal = $this->getAnonimAnimal();
        $emoji = $animal['emoji'];

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160">
    <rect width="160" height="160" rx="80" fill="#D1FAE5"/>
    <circle cx="80" cy="80" r="58" fill="#ECFDF5"/>
    <text x="80" y="100" text-anchor="middle" font-size="68" font-family="Arial, sans-serif">{$emoji}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
        return AnonymousIdentitySupport::buildUserAlias($this);
    }

    public function getNamaDisplay(): string
    {
        if ($this->isAnonim()) {
            return $this->getAnonimDisplayName();
        }

        return $this->nama;
    }

    public function getFotoDisplay(): ?string
    {
        if ($this->isAnonim()) {
            return $this->getAnonimAvatarSvg();
        }

        return optional($this->profil)->foto
            ? asset('storage/' . $this->profil->foto)
            : null;
    }

    
}
