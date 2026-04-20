<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('jadwal_konseling', 'jenis')) {
            Schema::table('jadwal_konseling', function (Blueprint $table) {
                $table->string('jenis')->nullable()->after('waktu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jadwal_konseling', 'jenis')) {
            Schema::table('jadwal_konseling', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
        }
    }
};
