<?php
use Illuminate\Support\Facades\DB;

$nims = DB::connection('mongodb')->table('students')->pluck('nim')->toArray();
foreach($nims as $nim) {
    if (preg_match('/^\d{3}(\d{2})\d{3}$/', $nim, $m)) {
        echo $nim . ' -> 20' . $m[1] . "\n";
    } else {
        echo $nim . " -> -\n";
    }
}
