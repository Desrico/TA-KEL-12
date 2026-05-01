<?php
use Illuminate\Support\Facades\DB;

$c = DB::connection('mongodb')->table('daily_checkins')->first();
echo "DailyCheckin mood_id: " . gettype($c->mood_id) . " value: " . $c->mood_id . "\n";

$m = DB::connection('mongodb')->table('moods')->where('mood_id', $c->mood_id)->first();
if ($m) {
    echo "Mood found! mood_id: " . gettype($m->mood_id) . " value: " . $m->mood_id . " name: " . $m->mood_name . "\n";
} else {
    echo "Mood NOT found for mood_id: " . $c->mood_id . "\n";
}

$m2 = DB::connection('mongodb')->table('moods')->where('mood_id', (int)$c->mood_id)->first();
if ($m2) {
    echo "Mood found using (int) casting! name: " . $m2->mood_name . "\n";
}
