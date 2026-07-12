<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat', function (Blueprint $table) {
            if (! Schema::hasColumn('chat', 'reply_to_chat_id')) {
                $table->foreignId('reply_to_chat_id')
                    ->nullable()
                    ->after('pesan')
                    ->constrained('chat')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('chat', function (Blueprint $table) {
            if (Schema::hasColumn('chat', 'reply_to_chat_id')) {
                $table->dropConstrainedForeignId('reply_to_chat_id');
            }
        });
    }
};
