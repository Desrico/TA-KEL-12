<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Module extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'title',
        'thumbnail',
        'description',
        'content_url',
        'reward_point',
        'status',
        'kategori',
        'target_audiens',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
