<?php
use Illuminate\Support\Facades\DB;

echo "Memulai perbaikan data legacy di daily_checkins...\n";

// Ambil semua check-in yang mood_id-nya bukan angka (kemungkinan besar hex string)
$checkins = DB::connection('mongodb')->table('daily_checkins')->get();
$fixedCount = 0;

foreach ($checkins as $c) {
    // Jika mood_id adalah string hex 24 karakter atau mengandung huruf
    if (is_string($c->mood_id) && (strlen($c->mood_id) > 10 || preg_match('/[a-f]/i', $c->mood_id))) {
        // Reset ke ID 3 (Netral)
        DB::connection('mongodb')->table('daily_checkins')
            ->where('_id', $c->_id)
            ->update([
                'mood_id' => 3, 
                'feeling_id' => 12 // Default Netral
            ]);
        $fixedCount++;
    }
}

echo "Perbaikan selesai. $fixedCount data legacy telah di-reset ke status Netral.\n";
