<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class SesiKonseling extends Model
{
    use HasFactory;

    protected $table = 'sesi_konseling';

    protected $fillable = [
        'jadwal_id',
        'jadwal_konseling_id',
        'status',
        'catatan',
    ];

    public static function jadwalForeignKey(): string
    {
        static $foreignKey = null;

        if ($foreignKey !== null) {
            return $foreignKey;
        }

        if (Schema::hasColumn('sesi_konseling', 'jadwal_id')) {
            return $foreignKey = 'jadwal_id';
        }

        if (Schema::hasColumn('sesi_konseling', 'jadwal_konseling_id')) {
            return $foreignKey = 'jadwal_konseling_id';
        }

        return $foreignKey = 'jadwal_id';
    }

    public function jadwalKonseling(): BelongsTo
    {
        return $this->belongsTo(JadwalKonseling::class, self::jadwalForeignKey());
    }

    public function jadwal(): BelongsTo
    {
        return $this->jadwalKonseling();
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'sesi_id');
    }
<<<<<<< Updated upstream
=======

    public function chats(): HasMany
    {
        return $this->chatMessages();
    }
>>>>>>> Stashed changes
}
