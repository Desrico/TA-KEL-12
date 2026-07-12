<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('konselor') && ! Schema::hasColumn('konselor', 'spesialisasi')) {
            Schema::table('konselor', function (Blueprint $table) {
                $table->string('spesialisasi', 100)->default('Psikolog / Konselor')->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('konselor') && Schema::hasColumn('konselor', 'spesialisasi')) {
            Schema::table('konselor', function (Blueprint $table) {
                $table->dropColumn('spesialisasi');
            });
        }
    }
};
