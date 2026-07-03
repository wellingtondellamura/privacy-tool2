<?php

namespace App\Strategies;

use App\Models\EvaluationRound;

class ConvergenceStrategy implements ConsensusStrategy
{
    public function resolve(EvaluationRound $round): array
    {
        // Owner can still manually record overrides if desired, otherwise falls back to normal averages.
        return $round->consolidatedResponses()
            ->pluck('final_answer', 'question_id')
            ->toArray();
    }
}
