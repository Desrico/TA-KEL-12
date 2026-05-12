<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ketidaktersediaan_konselor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('konselor_id')
                ->constrained('konselor')
                ->cascadeOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();

            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();

            $table->string('alasan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ketidaktersediaan_konselor');
    }
};