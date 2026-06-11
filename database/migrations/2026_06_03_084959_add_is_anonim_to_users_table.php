<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_anonim')) {
                $table->boolean('is_anonim')->default(false)->after('role');
            }
        });

        if (Schema::hasTable('profil') && Schema::hasColumn('profil', 'anonim')) {
            DB::table('users')
                ->join('profil', 'profil.user_id', '=', 'users.id')
                ->where('profil.anonim', 1)
                ->update(['users.is_anonim' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_anonim')) {
                $table->dropColumn('is_anonim');
            }
        });
    }
};
