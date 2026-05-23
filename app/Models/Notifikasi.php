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

        static::updated(function ($notifikasi) {
            self::syncToMongo($notifikasi);
        });

        static::deleted(function ($notifikasi) {
            try {
                \App\Models\NotifikasiMahasiswa::where('sql_notifikasi_id', $notifikasi->id)->delete();
            } catch (\Exception $e) {
                \Log::error('Gagal menghapus notifikasi MongoDB: ' . $e->getMessage());
            }
        });
    }

    public static function syncToMongo($notifikasi)
    {
        try {
            $user = $notifikasi->user;
            if ($user && $user->role === 'mahasiswa') {
                $mahasiswa = $user->mahasiswa;
                if ($mahasiswa && $mahasiswa->nim) {
                    \App\Models\NotifikasiMahasiswa::updateOrCreate(
                        ['sql_notifikasi_id' => $notifikasi->id],
                        [
                            'nim' => $mahasiswa->nim,
                            'pesan' => $notifikasi->pesan,
                            'status' => $notifikasi->status,
                        ]
                    );
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gagal sinkronisasi notifikasi ke MongoDB: ' . $e->getMessage());
        }
    }
}
