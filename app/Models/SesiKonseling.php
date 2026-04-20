<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SesiKonseling extends Model
{
    use HasFactory;

    protected $table = 'sesi_konseling';

    protected $fillable = [
        'jadwal_konseling_id',
        'status',
        'catatan_sesi',
        'waktu_mulai',
        'waktu_selesai',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalKonseling::class, 'jadwal_konseling_id');
    }
}