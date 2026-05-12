<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_konseling', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwal_konseling', 'tindak_lanjut_tipe')) {
                $table->string('tindak_lanjut_tipe')->nullable();
            }
            if (!Schema::hasColumn('jadwal_konseling', 'tanggal_lanjut')) {
                $table->date('tanggal_lanjut')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_konseling', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_konseling', 'tindak_lanjut_tipe')) {
                $table->dropColumn('tindak_lanjut_tipe');
            }
            if (Schema::hasColumn('jadwal_konseling', 'tanggal_lanjut')) {
                $table->dropColumn('tanggal_lanjut');
            }
        });
    }
};
