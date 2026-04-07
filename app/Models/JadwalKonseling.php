<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKonseling extends Model
{
    protected $table = 'jadwal_konseling';

    protected $fillable = [
        'mahasiswa_id',
        'konselor_id',
        'tanggal',
        'waktu',
        'status',
    ];
}
