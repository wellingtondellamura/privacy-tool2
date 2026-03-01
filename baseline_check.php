<?php

use App\Models\ResultSnapshot;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$snapshots = ResultSnapshot::whereNull('user_id')->get();

echo "Total Consolidated Snapshots: " . $snapshots->count() . "\n";

$hashes = [];
foreach ($snapshots as $snapshot) {
    $payload = $snapshot->payload_json;
    // Sort keys to ensure deterministic hash if payload is an associative array
    ksort($payload);
    $json = json_encode($payload);
    $hash = hash('sha256', $json);
    $hashes[$snapshot->id] = [
        'inspection_id' => $snapshot->inspection_id,
        'hash' => $hash
    ];
    echo "Snapshot ID: {$snapshot->id}, Inspection ID: {$snapshot->inspection_id}, Hash: {$hash}\n";
}

file_put_contents('baseline_hashes.json', json_encode($hashes, JSON_PRETTY_PRINT));
echo "Hashes saved to baseline_hashes.json\n";
