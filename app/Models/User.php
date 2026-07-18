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
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_anonim' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'array',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function hasTwoFactorAuthentication(): bool
    {
        return filled($this->two_factor_secret) && $this->two_factor_confirmed_at !== null;
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
        $alias = AnonymousIdentitySupport::buildUserAlias($this);

        return [
            'name' => $alias,
            'initials' => AnonymousIdentitySupport::initialsForAlias($alias),
        ];
    }

    public function getAnonimDisplayName(): string
    {
        $animal = $this->getAnonimAnimal();

        return $animal['name'] . ' Anonim';
    }

    public function getAnonimAvatarSvg(): string
    {
        $animal = $this->getAnonimAnimal();
        $initials = $animal['initials'] ?? AnonymousIdentitySupport::initialsForAlias($animal['name'] ?? 'Anonim');

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160">
    <rect width="160" height="160" rx="80" fill="#D1FAE5"/>
    <circle cx="80" cy="80" r="58" fill="#ECFDF5"/>
    <text x="80" y="95" text-anchor="middle" font-size="42" font-weight="700" font-family="Arial, sans-serif" fill="#065F46">{$initials}</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
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
