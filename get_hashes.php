<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RoundSnapshot;

foreach (RoundSnapshot::all() as $s) {
    echo $s->id . ':' . md5(json_encode($s->payload_json)) . PHP_EOL;
}
