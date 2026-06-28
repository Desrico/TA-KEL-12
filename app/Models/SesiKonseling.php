<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Schema;
use App\Models\Feedback;

class SesiKonseling extends Model
{
    use HasFactory;

    protected $table = 'sesi_konseling';

    protected $fillable = [
        'jadwal_id',
        'jadwal_konseling_id',
        'status',
        'catatan',
        'catatan_sesi',
        'waktu_mulai',
        'waktu_selesai',
    ];

    public static function jadwalForeignKey(): string
    {
        static $foreignKey = null;

        if ($foreignKey) {
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
        return $this->belongsTo(JadwalKonseling::class, static::jadwalForeignKey());
    }

    public function jadwal(): BelongsTo
    {
        return $this->jadwalKonseling();
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'sesi_id');
    }

    public function latestChat(): HasOne
    {
        // Dipakai list chat admin agar cukup mengambil satu pesan terakhir per sesi.
        return $this->hasOne(Chat::class, 'sesi_id')->latestOfMany();
    }

    public function laporan(): HasOne
    {
        return $this->hasOne(Laporan::class, 'sesi_id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'sesi_id');
    }
}
