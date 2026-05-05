<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiKonseling extends Model
{
    use HasFactory;

    protected $table = 'sesi_konseling';

    protected $fillable = [
        'jadwal_id',
        'status',
        'catatan',
    ];

    public function jadwalKonseling(): BelongsTo
    {
        return $this->belongsTo(JadwalKonseling::class, 'jadwal_id');
    }

    public function jadwal(): BelongsTo
    {
        return $this->jadwalKonseling();
    }

    public function chatMessages(): HasMany
    {
        return $this->hasMany(Chat::class, 'sesi_id');
    }
}