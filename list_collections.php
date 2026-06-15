<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$collections = \DB::connection('mongodb')->getMongoDB()->listCollections();
foreach ($collections as $collection) {
    echo $collection->getName() . PHP_EOL;
}
