<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\EvaluationRound;
use App\Actions\CloseRoundAction;

$round = EvaluationRound::where('name', 'E2E Rodada de Teste E2E')->latest()->first();
if (!$round) {
    echo "E2E round not found.\n";
    exit;
}

echo "Attempting to close Round ID: {$round->id} (Status: {$round->status})...\n";

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
