<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$collections = DB::connection('mongodb')->listCollections();
echo "Collections in MongoDB:\n";
foreach ($collections as $c) {
    echo "- " . $c->getName() . "\n";
}

echo "\nSample from 'users' collection:\n";
$user = DB::connection('mongodb')->table('users')->first();
print_r($user);

echo "\nSample from 'journal_texts' collection:\n";
$journal = DB::connection('mongodb')->table('journal_texts')->first();
print_r($journal);
