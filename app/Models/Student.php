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
        'mental_level', 'mental_label', 'mental_confidence', 'mental_red_flag', 'mental_insight', 'mental_scanned_at', 'mental_updated_manual_at',
        'tingkatan', 'angkatan',
    ];

    protected static function booted()
    {
        static::addGlobalScope('is_student', function ($builder) {
            $builder->whereNotNull('nim');
        });
    }

    protected $casts = [
        'mental_scanned_at' => 'datetime',
        'mental_updated_manual_at' => 'datetime',
    ];

    protected $hidden = ['password'];
    protected $appends = ['angkatan'];

    public function getAngkatanAttribute()
    {
        // Prioritas 1: Gunakan field 'tingkatan' jika ada
        if (isset($this->attributes['tingkatan']) && $this->attributes['tingkatan']) {
            return $this->attributes['tingkatan'];
        }

        // Prioritas 2: Gunakan field 'angkatan' jika ada (untuk data lama/migrasi)
        if (isset($this->attributes['angkatan']) && $this->attributes['angkatan']) {
            return $this->attributes['angkatan'];
        }

        // Prioritas 3: Parsing dari NIM (Del Institute pattern: 11421045 -> 2021)
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
