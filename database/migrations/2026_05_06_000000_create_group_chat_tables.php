<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('topic')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('group_chat_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('group_chat_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();
            $table->unique(['room_id', 'user_id']);
        });

        Schema::create('group_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('group_chat_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('pesan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_chat_messages');
        Schema::dropIfExists('group_chat_members');
        Schema::dropIfExists('group_chat_rooms');
    }
};
