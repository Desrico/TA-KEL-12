<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiLaporanSummary extends Model
{
    protected $table = 'ai_laporan_summaries';

    protected $fillable = [
        'mahasiswa_id',
        'konselor_id',
        'provider',
        'model',
        'summary',
        'source_hash',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function konselor(): BelongsTo
    {
        return $this->belongsTo(Konselor::class, 'konselor_id');
    }
}
