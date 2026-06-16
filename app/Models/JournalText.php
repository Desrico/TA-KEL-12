<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class JournalText extends Model
{
    protected $connection = 'mongodb';

    use HasFactory;

    protected $fillable = [
        'nim',
        'description',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'nim', 'nim');
    }

    /**
     * Accessor untuk mendekripsi description secara otomatis.
     * Jika data belum dienkripsi (data lama), kembalikan nilai aslinya.
     */
    public function getDescriptionAttribute($value)
    {
        if (empty($value)) return $value;
        
        // 1. Coba dekripsi dengan kunci backend mobile (karena data dienkripsi dari sana)
        try {
            $mobileKey = config('app.mobile_app_key');
            if ($mobileKey) {
                // Parse the base64 key
                if (str_starts_with($mobileKey, 'base64:')) {
                    $key = base64_decode(substr($mobileKey, 7));
                } else {
                    $key = $mobileKey;
                }
                
                $encrypter = new \Illuminate\Encryption\Encrypter($key, config('app.cipher'));
                return $encrypter->decryptString($value);
            }
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Lanjut ke percobaan berikutnya
        } catch (\Exception $e) {
            // Lanjut ke percobaan berikutnya
        }

        // 2. Coba dekripsi dengan kunci aplikasi ini sendiri
        try {
            return \Illuminate\Support\Facades\Crypt::decryptString($value);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Gagal dekripsi = data lama yang belum dienkripsi
            return $value;
        }
    }
}
