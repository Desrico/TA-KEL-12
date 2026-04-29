<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Student extends Model
{
    protected $connection = 'mongodb';

    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim', 'name', 'gender', 'prodi', 'password', 'point', 'energy_score', 'phone_number',
        'mental_level', 'mental_label', 'mental_confidence', 'mental_red_flag', 'mental_scanned_at',
    ];

    protected $casts = [
        'mental_scanned_at' => 'datetime',
    ];

    protected $hidden = ['password'];

    public function journalTexts()
    {
        return $this->hasMany(JournalText::class, 'nim', 'nim');
    }

    public function dailyCheckins()
    {
        return $this->hasMany(DailyCheckin::class, 'nim', 'nim');
    }
}
