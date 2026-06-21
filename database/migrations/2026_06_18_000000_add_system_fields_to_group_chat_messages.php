<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_chat_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('group_chat_messages', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('pesan');
            }

            if (! Schema::hasColumn('group_chat_messages', 'system_event')) {
                $table->string('system_event', 40)->nullable()->after('is_system');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('group_chat_messages', 'system_event')) {
                $table->dropColumn('system_event');
            }

            if (Schema::hasColumn('group_chat_messages', 'is_system')) {
                $table->dropColumn('is_system');
            }
        });
    }
};
