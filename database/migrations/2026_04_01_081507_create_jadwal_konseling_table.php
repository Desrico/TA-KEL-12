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
    Schema::create('jadwal_konseling', function (Blueprint $table) {
        $table->id();
        $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
        $table->foreignId('konselor_id')->constrained('konselor')->onDelete('cascade');
        $table->date('tanggal');
        $table->time('waktu');
        $table->string('status')->default('menunggu'); // menunggu, disetujui, ditolak
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_konseling');
    }
};
