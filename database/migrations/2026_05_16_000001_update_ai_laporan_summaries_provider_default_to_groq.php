<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ai_laporan_summaries')) {
            return;
        }

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE ai_laporan_summaries MODIFY provider VARCHAR(50) NOT NULL DEFAULT 'groq'");
        }
    }

    public function down(): void
    {
        //
    }
};
