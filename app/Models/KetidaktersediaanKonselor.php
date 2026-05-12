<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetidaktersediaanKonselor extends Model
{
    use HasFactory;

    protected $table = 'ketidaktersediaan_konselor';

    protected $fillable = [
        'konselor_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'alasan',
    ];

    public function konselor()
    {
        return $this->belongsTo(Konselor::class, 'konselor_id');
    }
}