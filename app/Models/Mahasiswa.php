<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = ['user_id', 'nim', 'jurusan', 'angkatan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalKonseling(): HasMany
    {
        return $this->hasMany(JadwalKonseling::class, 'mahasiswa_id');
    }
}
