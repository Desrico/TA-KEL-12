<?php
use Illuminate\Support\Facades\DB;

$moods = DB::connection('mongodb')->collection('moods')->get();
echo "Total moods: " . count($moods) . "\n\n";

foreach ($moods->take(10) as $mood) {
    echo json_encode($mood) . "\n";
}
