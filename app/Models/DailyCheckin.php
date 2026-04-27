<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class DailyCheckin extends Model
{
    protected $connection = 'mongodb';

    use HasFactory;

    protected $fillable = [
        'nim',
        'mood_id',
        'feeling_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }

    public function mood()
    {
        return $this->belongsTo(Mood::class, 'mood_id', 'mood_id');
    }

    public function feeling()
    {
        return $this->belongsTo(Feeling::class, 'feeling_id', 'feeling_id');
    }

    public function aiAnalysis()
    {
        return $this->hasOne(AiAnalysis::class, 'daily_checkin_id', 'daily_checkin_id');
    }
}
