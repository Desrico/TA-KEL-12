<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Sample from 'moods' collection:\n";
$mood = DB::connection('mongodb')->table('moods')->first();
print_r($mood);

echo "\nSample from 'feelings' collection:\n";
$feeling = DB::connection('mongodb')->table('feelings')->first();
print_r($feeling);
