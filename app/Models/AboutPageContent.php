<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AboutPageContent extends Model
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'page_key',
        'video_badge',
        'video_title',
        'video_description',
        'video_caption',
        'video_duration',
        'article_section_title',
        'article_section_description',
        'articles',
        'trending_section_title',
        'trending_section_description',
        'trending_summary',
        'trending_topics',
        'weekly_hashtags',
    ];

    protected $casts = [
        'articles' => 'array',
        'trending_topics' => 'array',
        'weekly_hashtags' => 'array',
    ];
}
