<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$allFeelings = \App\Models\Feeling::all()->keyBy('_id');
$firstFeeling = $allFeelings->first();
echo "Feeling _id type: " . gettype($firstFeeling->_id) . "\n";
if (is_object($firstFeeling->_id)) {
    echo "Feeling _id class: " . get_class($firstFeeling->_id) . "\n";
}
echo "Feeling _id string: " . (string)$firstFeeling->_id . "\n\n";

$distribution = \App\Models\DailyCheckin::all()->groupBy('feeling_id');
$firstGroupId = $distribution->keys()->first();
echo "DailyCheckin feeling_id type: " . gettype($firstGroupId) . "\n";
if (is_object($firstGroupId)) {
    echo "DailyCheckin feeling_id class: " . get_class($firstGroupId) . "\n";
}
echo "DailyCheckin feeling_id string: " . (string)$firstGroupId . "\n";

echo "\nDoes it exist? " . ($allFeelings->has($firstGroupId) ? "Yes" : "No") . "\n";
echo "Does string version exist? " . ($allFeelings->has((string)$firstGroupId) ? "Yes" : "No") . "\n";
