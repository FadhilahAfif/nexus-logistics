<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $exists = Illuminate\Support\Facades\Storage::disk('s3')->exists('pod/01KBQMK2K91H1CZ89VK0BB98R4.png');
    echo "Exists: " . ($exists ? 'Yes' : 'No') . PHP_EOL;
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL;
    if ($e->getPrevious()) {
        echo "Previous: " . $e->getPrevious()->getMessage() . PHP_EOL;
    }
}
