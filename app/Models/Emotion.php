<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Emotion extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'emotions';

    protected $fillable = ['emotion_name'];

    public function journals()
    {
        return $this->hasMany(JournalEntry::class, 'emotion_id', 'id');
    }
}
