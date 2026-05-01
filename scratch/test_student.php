<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$s = new App\Models\Student;
echo "Collection Name: " . $s->getTable() . "\n";
$students = App\Models\Student::all();
echo "Count: " . count($students) . "\n";
foreach($students as $st) {
    echo $st->nim . " - " . $st->name . "\n";
}
