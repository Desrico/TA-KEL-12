<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('jadwal_konseling', 'topik')) {
            Schema::table('jadwal_konseling', function (Blueprint $table) {
                $table->string('topik')->nullable()->after('jenis');
            });

            // Backfill: extract "Topik: ..." from catatan or use ringkasan_masalah
            DB::table('jadwal_konseling')->select('id', 'catatan', 'ringkasan_masalah')->orderBy('id')->chunk(100, function ($rows) {
                foreach ($rows as $row) {
                    $topik = null;
                    if (! empty($row->catatan) && preg_match('/Topik:\s*([^|]+)/i', $row->catatan, $m)) {
                        $topik = trim($m[1]);
                    }

                    if (! $topik && ! empty($row->ringkasan_masalah)) {
                        // If ringkasan_masalah looks like it contains a topik prefix, try to parse
                        if (preg_match('/Topik:\s*([^|]+)/i', $row->ringkasan_masalah, $m2)) {
                            $topik = trim($m2[1]);
                        } else {
                            // Fallback: use the whole ringkasan if short
                            $candidate = trim($row->ringkasan_masalah);
                            if ($candidate !== '') {
                                $topik = Str::limit($candidate, 255, '');
                            }
                        }
                    }

                    if ($topik !== null) {
                        DB::table('jadwal_konseling')->where('id', $row->id)->update(['topik' => $topik]);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('jadwal_konseling', 'topik')) {
            Schema::table('jadwal_konseling', function (Blueprint $table) {
                $table->dropColumn('topik');
            });
        }
    }
};
