<?php
use Illuminate\Support\Facades\DB;

foreach(DB::connection('mongodb')->table('moods')->get() as $m) {
    echo "ID: " . ($m->mood_id ?? 'N/A') . " - Name: " . ($m->mood_name ?? 'N/A') . "\n";
}
