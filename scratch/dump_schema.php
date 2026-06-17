<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = \DB::connection('mongodb')->getMongoDB();
$collections = $db->listCollections();

$schema = [];

foreach ($collections as $collectionInfo) {
    $collectionName = $collectionInfo->getName();
    $collection = $db->selectCollection($collectionName);
    
    $document = $collection->findOne();
    
    if ($document) {
        $fields = [];
        foreach ((array) $document as $key => $value) {
            $type = gettype($value);
            if ($type === 'object') {
                if ($value instanceof MongoDB\BSON\ObjectId) {
                    $type = 'ObjectId';
                } elseif ($value instanceof MongoDB\BSON\UTCDateTime) {
                    $type = 'Date';
                } else {
                    $type = get_class($value);
                }
            }
            $fields[$key] = $type;
        }
        $schema[$collectionName] = $fields;
    } else {
        $schema[$collectionName] = 'Empty collection';
    }
}

echo json_encode($schema, JSON_PRETTY_PRINT);
