<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_laporan_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('konselor_id')->nullable()->constrained('konselor')->nullOnDelete();
            $table->string('provider', 50)->default('groq');
            $table->string('model', 100)->nullable();
            $table->longText('summary');
            $table->string('source_hash', 64);
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['mahasiswa_id', 'source_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_laporan_summaries');
    }
};
