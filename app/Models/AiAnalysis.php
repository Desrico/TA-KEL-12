<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class AiAnalysis extends Model
{
    protected $connection = 'mongodb';

    use HasFactory;

    protected $fillable = [
        'daily_checkin_id',
        'final_label',
        'text_analysis',
    ];

    public function dailyCheckin()
    {
        return $this->belongsTo(DailyCheckin::class, 'daily_checkin_id', 'daily_checkin_id');
    }
}
