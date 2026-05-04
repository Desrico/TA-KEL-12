<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_konseling', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_konseling', 'ringkasan_masalah')) {
                $table->text('ringkasan_masalah')->nullable();
            }
            if (!Schema::hasColumn('jadwal_konseling', 'observasi_konselor')) {
                $table->text('observasi_konselor')->nullable();
            }
            if (!Schema::hasColumn('jadwal_konseling', 'progress')) {
                $table->text('progress')->nullable();
            }
            if (!Schema::hasColumn('jadwal_konseling', 'tindak_lanjut')) {
                $table->text('tindak_lanjut')->nullable();
            }
            if (!Schema::hasColumn('jadwal_konseling', 'laporan')) {
                $table->text('laporan')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_konseling', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_konseling', 'ringkasan_masalah')) {
                $table->dropColumn('ringkasan_masalah');
            }
            if (Schema::hasColumn('jadwal_konseling', 'observasi_konselor')) {
                $table->dropColumn('observasi_konselor');
            }
            if (Schema::hasColumn('jadwal_konseling', 'progress')) {
                $table->dropColumn('progress');
            }
            if (Schema::hasColumn('jadwal_konseling', 'tindak_lanjut')) {
                $table->dropColumn('tindak_lanjut');
            }
            if (Schema::hasColumn('jadwal_konseling', 'laporan')) {
                $table->dropColumn('laporan');
            }
        });
    }
};
