<?php

namespace App\Strategies;

use App\Models\EvaluationRound;
use App\Models\Response;

class MajorityVoteStrategy implements ConsensusStrategy
{
    public function resolve(EvaluationRound $round): array
    {
        $inspectionIds = $round->inspections()
            ->where('status', 'closed')
            ->pluck('id')
            ->toArray();

        if (empty($inspectionIds)) {
            return [];
        }

        // Get all responses in these inspections
        $responses = Response::whereIn('inspection_id', $inspectionIds)->get();

        // Group by question_id
        $grouped = $responses->groupBy('question_id');
        $resolved = [];

        foreach ($grouped as $questionId => $questionResponses) {
            $votes = [
                'low' => 0,
                'medium' => 0,
                'high' => 0,
                'other' => 0
            ];

            foreach ($questionResponses as $response) {
                $ans = $response->answer;
                $ansValue = $ans instanceof \App\Enums\AnswerLevel ? $ans->value : (string) $ans;
                if (array_key_exists($ansValue, $votes)) {
                    $votes[$ansValue]++;
                }
            }

            // Find the maximum number of votes received
            $maxVotes = max($votes);
            if ($maxVotes === 0) {
                continue;
            }

            // Get all answers with this max vote count
            $candidates = [];
            foreach ($votes as $ans => $count) {
                if ($count === $maxVotes) {
                    $candidates[] = $ans;
                }
            }

            // If there's a tie, choose the most conservative (lowest score):
            // low (0) > medium (50) > high (100) > other (null)
            if (in_array('low', $candidates)) {
                $winner = 'low';
            } elseif (in_array('medium', $candidates)) {
                $winner = 'medium';
            } elseif (in_array('high', $candidates)) {
                $winner = 'high';
            } else {
                $winner = 'other';
            }

            $resolved[$questionId] = $winner;
        }

        return $resolved;
    }
}
