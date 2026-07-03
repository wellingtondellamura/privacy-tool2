<?php

namespace App\Strategies;

use App\Models\EvaluationRound;

class OwnerDecideStrategy implements ConsensusStrategy
{
    public function resolve(EvaluationRound $round): array
    {
        return $round->consolidatedResponses()
            ->pluck('final_answer', 'question_id')
            ->toArray();
    }
}
