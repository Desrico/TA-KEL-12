<?php
use Illuminate\Support\Facades\DB;

$c = DB::connection('mongodb')->table('daily_checkins')->where('mood_id', 'like', '69%')->first();
if ($c) {
    echo json_encode($c, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "No dirty check-ins found.\n";
}
