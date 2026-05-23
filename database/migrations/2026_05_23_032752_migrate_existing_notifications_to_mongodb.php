<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Notifikasi;
use App\Models\NotifikasiMahasiswa;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ambil semua notifikasi dari database relasional (SQL)
        $sqlNotifications = Notifikasi::with('user.mahasiswa')->get();

        foreach ($sqlNotifications as $notif) {
            $user = $notif->user;
            if ($user && $user->role === 'mahasiswa') {
                $mahasiswa = $user->mahasiswa;
                if ($mahasiswa && $mahasiswa->nim) {
                    NotifikasiMahasiswa::updateOrCreate(
                        ['sql_notifikasi_id' => $notif->id],
                        [
                            'nim'               => $mahasiswa->nim,
                            'pesan'             => $notif->pesan,
                            'status'            => $notif->status,
                            'created_at'        => $notif->created_at,
                            'updated_at'        => $notif->updated_at,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus semua notifikasi di MongoDB yang disinkronisasi dari SQL
        NotifikasiMahasiswa::whereNotNull('sql_notifikasi_id')->delete();
    }
};
