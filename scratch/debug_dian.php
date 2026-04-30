<?php
use Illuminate\Support\Facades\DB;

$nim = '11323012';
$checkins = DB::connection('mongodb')->table('daily_checkins')->where('nim', $nim)->get();

echo "Checking data for NIM: $nim\n";
foreach ($checkins as $c) {
    $m = DB::connection('mongodb')->table('moods')->where('mood_id', (int)$c->mood_id)->first();
    $name = $m ? $m->mood_name : 'NOT FOUND';
    echo "Checkin ID: {$c->_id} | Date: " . json_encode($c->created_at) . " | MoodID: {$c->mood_id} | Name: $name\n";
}

$journals = DB::connection('mongodb')->table('journal_texts')->where('nim', $nim)->get();
echo "\nChecking journals for NIM: $nim\n";
foreach ($journals as $j) {
     echo "Journal ID: {$j->_id} | Date: " . json_encode($j->created_at) . "\n";
}
