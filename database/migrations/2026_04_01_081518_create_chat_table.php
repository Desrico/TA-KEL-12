<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('chat', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sesi_id')->constrained('sesi_konseling')->onDelete('cascade');
        $table->foreignId('pengirim_id')->constrained('users')->onDelete('cascade');
        $table->text('pesan');
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat');
    }
};
