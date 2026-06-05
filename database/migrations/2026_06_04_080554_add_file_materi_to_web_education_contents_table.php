<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_education_contents', function (Blueprint $table) {
            $table->string('file_materi')->nullable()->after('thumbnail');
        });
    }

    public function down(): void
    {
        Schema::table('web_education_contents', function (Blueprint $table) {
            $table->dropColumn('file_materi');
        });
    }
};