<?php

namespace App\Enums;

enum ConsensusModel: string
{
    case OWNER_DECIDES = 'owner_decides';
    case EVALUATOR_CONVERGENCE = 'evaluator_convergence';
    case MAJORITY_VOTE = 'majority_vote';

    public function label(): string
    {
        return __('labels.consensus_model.' . $this->value);
    }

    public function strategy(): \App\Strategies\ConsensusStrategy
    {
        return match ($this) {
            self::OWNER_DECIDES => new \App\Strategies\OwnerDecideStrategy(),
            self::EVALUATOR_CONVERGENCE => new \App\Strategies\ConvergenceStrategy(),
            self::MAJORITY_VOTE => new \App\Strategies\MajorityVoteStrategy(),
        };
    }
}
