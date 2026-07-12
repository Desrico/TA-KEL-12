<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_chat_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('group_chat_messages', 'reply_to_message_id')) {
                $table->foreignId('reply_to_message_id')
                    ->nullable()
                    ->after('pesan')
                    ->constrained('group_chat_messages')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_chat_messages', function (Blueprint $table) {
            if (Schema::hasColumn('group_chat_messages', 'reply_to_message_id')) {
                $table->dropConstrainedForeignId('reply_to_message_id');
            }
        });
    }
};
