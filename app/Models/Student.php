<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Student extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'users';

    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim', 'name', 'jenis_kelamin', 'prodi', 'password', 'point', 'energy_score', 'phone_number',
        'mental_level', 'mental_label', 'mental_confidence', 'mental_red_flag', 'mental_insight', 'mental_scanned_at',
    ];

    protected static function booted()
    {
        static::addGlobalScope('is_student', function ($builder) {
            $builder->whereNotNull('nim');
        });
    }

    protected $casts = [
        'mental_scanned_at' => 'datetime',
    ];

    protected $hidden = ['password'];
    protected $appends = ['angkatan'];

    public function getAngkatanAttribute()
    {
        if (preg_match('/^\d{3}(\d{2})\d{3}$/', $this->nim, $matches)) {
            return '20' . $matches[1];
        }
        return '-';
    }

    public function journalTexts()
    {
        return $this->hasMany(JournalText::class, 'nim', 'nim');
    }

    public function dailyCheckins()
    {
        return $this->hasMany(DailyCheckin::class, 'nim', 'nim');
    }
}
