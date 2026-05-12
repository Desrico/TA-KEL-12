<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    protected $table = 'laporan';

    protected $fillable = [
        'sesi_id',
        'konselor_id',
        'isi_laporan',
        'file_path',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SesiKonseling::class, 'sesi_id');
    }

    public function konselor(): BelongsTo
    {
        return $this->belongsTo(Konselor::class, 'konselor_id');
    }
}
