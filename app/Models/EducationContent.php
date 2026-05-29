<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationContent extends Model
{
    protected $table = 'web_education_contents';

    protected $fillable = [
        'judul',
        'topik',
        'tipe_konten',
        'ringkasan',
        'isi_konten',
        'nama_sumber',
        'url_sumber',
        'thumbnail',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getTitleAttribute()
    {
        return $this->judul;
    }

    public function getTopicAttribute()
    {
        return $this->topik;
    }

    public function getTypeAttribute()
    {
        return $this->tipe_konten;
    }

    public function getExcerptAttribute()
    {
        return $this->ringkasan;
    }

    public function getContentAttribute()
    {
        return $this->isi_konten;
    }

    public function getSourceUrlAttribute()
    {
        return $this->url_sumber;
    }

    public function getReadingTimeAttribute()
    {
        return $this->tipe_konten === 'Video' ? '3 menit tonton' : '5 menit baca';
    }
}