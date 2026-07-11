<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\EvaluationRound;
use App\Models\User;

$round = EvaluationRound::where('name', 'E2E Rodada de Teste E2E')->first();
if (!$round) {
    echo "E2E round not found.\n";
    exit;
}

$project = $round->project;
$owner = User::where('email', 'e2e_owner@test.com')->first();

echo "Round ID: {$round->id}\n";
echo "Project ID: {$project->id}, Name: {$project->name}\n";
echo "Project Owner ID: {$project->owner_id}\n";
echo "E2E Owner User ID: {$owner->id}\n";
echo "Are they equal? " . ($project->owner_id === $owner->id ? "YES" : "NO") . "\n";
