<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'nama',
        'email',
        'username_cis',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profil()
    {
        return $this->hasOne(Profil::class);
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'pengirim_id');
    }

    public function isAnonim(): bool
    {
        return optional($this->profil)->anonim ?? false;
    }

    public function getNamaDisplay(): string
    {
        return $this->isAnonim() ? 'Mahasiswa Anonim' : $this->nama;
    }
}
