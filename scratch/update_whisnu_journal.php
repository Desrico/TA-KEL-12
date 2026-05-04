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
    echo "Found Whisnu! NIM: " . $whisnu->nim . "\n";
    
    $targetDate = '2026-04-20';
    $start = new MongoDB\BSON\UTCDateTime(strtotime($targetDate . ' 00:00:00') * 1000);
    $end = new MongoDB\BSON\UTCDateTime(strtotime($targetDate . ' 23:59:59') * 1000);

    $journals = DB::connection('mongodb')->table('journal_texts')
        ->where('nim', $whisnu->nim)
        ->whereBetween('created_at', [$start, $end])
        ->get();

    echo "Found " . count($journals) . " journals for Whisnu on " . $targetDate . "\n";

    if (count($journals) > 0) {
        $updated = DB::connection('mongodb')->table('journal_texts')
            ->where('nim', $whisnu->nim)
            ->whereBetween('created_at', [$start, $end])
            ->update([
                'description' => 'Saya merasa sangat stres belakangan ini. Beban kuliah dan masalah pribadi bertumpuk sampai rasanya saya tidak sanggup lagi. Terkadang pikiran untuk menyakiti diri sendiri muncul karena rasa sakit di dalam dada ini tidak tertahankan. Saya merasa sendirian dan tidak ada yang mengerti.'
            ]);
        echo "Successfully updated $updated journals.\n";
    } else {
        echo "No journals found on that date. Creating one for testing purposes...\n";
        DB::connection('mongodb')->table('journal_texts')->insert([
            'nim' => $whisnu->nim,
            'description' => 'Saya merasa sangat stres belakangan ini. Beban kuliah dan masalah pribadi bertumpuk sampai rasanya saya tidak sanggup lagi. Terkadang pikiran untuk menyakiti diri sendiri muncul karena rasa sakit di dalam dada ini tidak tertahankan. Saya merasa sendirian dan tidak ada yang mengerti.',
            'created_at' => $start,
            'updated_at' => $start
        ]);
        echo "New journal entry created for Whisnu on " . $targetDate . "\n";
    }
} else {
    echo "Whisnu not found in users collection.\n";
}
