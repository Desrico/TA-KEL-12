<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function jadwalKonseling(): BelongsTo

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public static function jadwalForeignKey(): string
    {
        return Schema::hasColumn('sesi_konseling', 'jadwal_konseling_id')
            ? 'jadwal_konseling_id'
            : 'jadwal_id';
    }

    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalKonseling::class, self::jadwalForeignKey());
    }

    public function laporan(): HasOne
    {
        return $this->hasOne(Laporan::class, 'sesi_id');
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(Chat::class, 'sesi_id');
    }

    public function jadwal(): BelongsTo
    {
        return $this->jadwalKonseling();
    }

    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class, 'sesi_id');
    }
}
