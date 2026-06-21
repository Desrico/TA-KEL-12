<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_chat_rooms', function (Blueprint $table) {
            if (! Schema::hasColumn('group_chat_rooms', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_chat_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('group_chat_rooms', 'avatar_path')) {
                $table->dropColumn('avatar_path');
            }
        });
    }
};
