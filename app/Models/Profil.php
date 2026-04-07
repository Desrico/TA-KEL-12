<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profil extends Model
{
    protected $table    = 'profil';
    protected $fillable = ['user_id', 'foto', 'bio', 'anonim'];

    protected $casts = [
        'anonim' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}