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
    Schema::create('laporan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sesi_id')->constrained('sesi_konseling')->onDelete('cascade');
        $table->foreignId('konselor_id')->constrained('konselor')->onDelete('cascade');
        $table->text('isi_laporan');
        $table->string('file_path', 255)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
