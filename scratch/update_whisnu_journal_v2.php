<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$whisnu = DB::connection('mongodb')->table('users')
    ->where('name', 'like', '%Whisnu%')
    ->first();

if ($whisnu) {
    $targetDate = '2026-04-20';
    $start = new MongoDB\BSON\UTCDateTime(strtotime($targetDate . ' 00:00:00') * 1000);
    $end = new MongoDB\BSON\UTCDateTime(strtotime($targetDate . ' 23:59:59') * 1000);

    $updated = DB::connection('mongodb')->table('journal_texts')
        ->where('nim', $whisnu->nim)
        ->whereBetween('created_at', [$start, $end])
        ->update([
            'description' => 'Saya merasa sangat stres belakangan ini. Beban kuliah dan masalah pribadi bertumpuk sampai rasanya saya tidak sanggup lagi. Terkadang pikiran untuk ingin bunuh diri muncul karena rasa sakit di dalam dada ini tidak tertahankan. Saya merasa sendirian dan tidak ada yang mengerti.'
        ]);
    
    if ($updated) {
        echo "Successfully updated Whisnu's journal with 'ingin bunuh diri'.\n";
    } else {
        echo "Failed to update or journal not found.\n";
    }
} else {
    echo "Whisnu not found.\n";
}
