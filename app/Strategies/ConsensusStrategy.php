<?php

namespace App\Strategies;

use App\Models\EvaluationRound;

interface ConsensusStrategy
{
    /**
     * Resolves and returns the consolidated answers for the questions of the round.
     *
     * @param EvaluationRound $round
     * @return array Array mapping question_id to string ('high', 'medium', 'low', 'other')
     */
    public function resolve(EvaluationRound $round): array;
}
