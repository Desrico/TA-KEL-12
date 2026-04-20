<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('jadwal_konseling', 'anonim')) {
            Schema::table('jadwal_konseling', function (Blueprint $table) {
                $table->boolean('anonim')->default(false)->after('jenis');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jadwal_konseling', 'anonim')) {
            Schema::table('jadwal_konseling', function (Blueprint $table) {
                $table->dropColumn('anonim');
            });
        }
    }
};
