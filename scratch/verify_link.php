<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Finding a student in 'users' collection (where nim is present):\n";
$student = DB::connection('mongodb')->table('users')->whereNotNull('nim')->first();
print_r($student);

if ($student) {
    echo "\nFinding journals for NIM: " . $student->nim . "\n";
    $journals = DB::connection('mongodb')->table('journal_texts')->where('nim', $student->nim)->get();
    echo "Found " . count($journals) . " journals.\n";
    if (count($journals) > 0) {
        print_r($journals[0]);
    }
}
