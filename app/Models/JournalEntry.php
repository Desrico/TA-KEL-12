<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class JournalEntry extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'journal_entries';

    protected $fillable = ['nim', 'emotion_id', 'journal_text', 'ai_reply', 'date'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }

    public function emotion()
    {
        return $this->belongsTo(Emotion::class, 'emotion_id', 'id');
    }
}
