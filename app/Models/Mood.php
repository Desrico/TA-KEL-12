<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Mood extends Model
{
    protected $connection = 'mongodb';

    use HasFactory;

    // no explicit primaryKey so MongoDB uses default _id
    protected $fillable = [
        'mood_name',
        'mood_code',
    ];

    public function dailyCheckins()
    {
        return $this->hasMany(DailyCheckin::class, 'mood_id', 'mood_id');
    }
}
