<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\EvaluationRound;
use App\Models\Inspection;
use App\Models\ResultSnapshot;
use App\Models\ConsolidatedResponse;

$rounds = EvaluationRound::with('inspections')->get();
foreach ($rounds as $round) {
    echo "Round ID: {$round->id}, Name: {$round->name}, Status: {$round->status}\n";
    foreach ($round->inspections as $inspection) {
        $snapshotsCount = ResultSnapshot::where('inspection_id', $inspection->id)->count();
        $consolidatedCount = ResultSnapshot::where('inspection_id', $inspection->id)->whereNull('user_id')->count();
        echo "  - Inspection ID: {$inspection->id}, User: {$inspection->user_id}, Status: {$inspection->status}, Snapshots: {$snapshotsCount}, Consolidated Snapshots: {$consolidatedCount}\n";
    }
}

echo "\nConsolidated Responses in database:\n";
$responses = ConsolidatedResponse::all();
foreach ($responses as $resp) {
    echo "  - Round ID: {$resp->evaluation_round_id}, Question ID: {$resp->question_id}, Final Answer: {$resp->final_answer}, Decided By: {$resp->decided_by}, Method: {$resp->resolution_method}\n";
}
