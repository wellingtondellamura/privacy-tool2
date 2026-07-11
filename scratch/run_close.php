<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\EvaluationRound;
use App\Actions\CloseRoundAction;

$round = EvaluationRound::find(1);
if (!$round) {
    echo "Round 1 not found.\n";
    exit;
}

echo "Attempting to close Round 1 (Status: {$round->status})...\n";

try {
    $action = new CloseRoundAction();
    $result = $action->execute($round);
    echo "Result of execute: " . ($result ? "TRUE" : "FALSE") . "\n";
    echo "New status of round: {$round->fresh()->status}\n";
} catch (\Exception $e) {
    echo "EXCEPTION THROWN:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
