<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Konselor extends Model
{
    protected $table = 'konselor';

    protected $fillable = ['user_id', 'spesialisasi'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
