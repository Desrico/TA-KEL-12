<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Module;

echo "=== DAFTAR MODUL DI DATABASE ===\n\n";

$modules = Module::all();
foreach ($modules as $module) {
    echo "ID: " . $module->id . "\n";
    echo "Title: " . $module->title . "\n";
    echo "Content URL: " . $module->content_url . "\n";
    echo "Thumbnail: " . $module->thumbnail . "\n";
    echo "Status: " . $module->status . "\n";
    echo "---------------------------------\n";
}
