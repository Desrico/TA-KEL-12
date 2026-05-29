<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_education_contents', function (Blueprint $table) {
            $table->id();

            $table->string('judul');
            $table->string('topik');
            $table->string('tipe_konten');

            $table->text('ringkasan');
            $table->longText('isi_konten');

            $table->string('nama_sumber')->nullable();
            $table->string('url_sumber')->nullable();
            $table->string('thumbnail')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_education_contents');
    }
};