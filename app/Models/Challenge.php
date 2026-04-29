<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Challenge extends Model
{
    protected $connection = 'mongodb';

    // Primary key is 'id' by default

    protected $fillable = [
        'title',
        'description',
        'total_questions',
        'reward_point',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
