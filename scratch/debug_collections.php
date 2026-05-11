<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Sample from 'students' collection:\n";
$student = DB::connection('mongodb')->table('students')->first();
print_r($student);

echo "\nSample from 'users' collection where nim is present:\n";
$userStudent = DB::connection('mongodb')->table('users')->whereNotNull('nim')->first();
print_r($userStudent);
