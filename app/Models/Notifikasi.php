<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'user_id',
        'pesan',
        'cta_target',
        'cta_label',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($notifikasi) {
            self::syncToMongo($notifikasi);
        });
    }

    public static function syncToMongo($notifikasi)
    {
        try {
            $user = $notifikasi->user;
            if ($user && $user->role === 'mahasiswa') {
                $mahasiswa = $user->mahasiswa;
                if ($mahasiswa && $mahasiswa->nim) {
                    \App\Models\NotifikasiMahasiswa::create([
                        'nim'    => $mahasiswa->nim,
                        'pesan'  => $notifikasi->pesan,
                        'cta_target' => $notifikasi->cta_target,
                        'cta_label' => $notifikasi->cta_label,
                        'status' => $notifikasi->status,
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gagal sinkronisasi notifikasi ke MongoDB: ' . $e->getMessage());
        }
    }
}
