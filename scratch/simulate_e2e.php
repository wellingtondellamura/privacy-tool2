<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Actions\CloseInspectionAction;
use App\Actions\CloseRoundAction;

// 1. Run Seeder
echo "Seeding E2E database for evaluator_convergence...\n";
putenv('E2E_CONSENSUS_MODEL=evaluator_convergence');
$seeder = new \Database\Seeders\E2ETestSeeder();
$seeder->run();

// Find the round and inspections
$round = EvaluationRound::where('name', 'E2E Rodada de Teste E2E')->first();
echo "Created Round ID: {$round->id}\n";

$inspections = $round->inspections;
echo "Found " . $inspections->count() . " inspections.\n";

// 2. Close all inspections
$closeInspectionAction = new CloseInspectionAction();
foreach ($inspections as $index => $inspection) {
    echo "Closing inspection {$inspection->id} for user {$inspection->user_id}...\n";
    $closeInspectionAction->execute($inspection);
}

// 3. Mark the round as in review phase
$round->update(['status' => 'review']);
echo "Round status set to review.\n";

// 4. Close the round
echo "Executing CloseRoundAction...\n";
try {
    $closeRoundAction = new CloseRoundAction();
    $result = $closeRoundAction->execute($round);
    echo "Execute result: " . ($result ? "SUCCESS" : "FAIL") . "\n";
    
    // Check snapshots
    $snapshotsCount = $round->snapshots()->count();
    echo "Snapshots created: {$snapshotsCount}\n";
    if ($snapshotsCount > 0) {
        $payload = $round->snapshots()->first()->payload_json;
        echo "Global Score: {$payload['global_score']}, Medal: {$payload['medal']['name']}\n";
    }
} catch (\Exception $e) {
    echo "EXCEPTION THROWN:\n";
    echo $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
