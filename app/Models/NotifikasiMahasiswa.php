<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class NotifikasiMahasiswa extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'notifications';

    protected $fillable = [
        'nim',
        'pesan',
        'status', // 'belum', 'dibaca'
        'sql_notifikasi_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }
}
