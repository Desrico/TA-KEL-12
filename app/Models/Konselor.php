<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KetidaktersediaanKonselor;
use App\Models\User;

class Konselor extends Model
{
    protected $table = 'konselor';

    protected $fillable = ['user_id', 'spesialisasi'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ketidaktersediaan()
    {
        return $this->hasMany(KetidaktersediaanKonselor::class, 'konselor_id');
    }
}
