<?php

namespace App\Http\Controllers;

use App\Models\EvaluationRound;
use App\Models\ConsolidatedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ConsolidatedResponseController extends Controller
{
    /**
     * POST /rounds/{round}/consolidate — Save owner consolidated answer for a question.
     */
    public function store(Request $request, EvaluationRound $round)
    {
        Gate::authorize('update', $round->project);

        if ($round->status !== 'review') {
            return redirect()->back()->withErrors(['message' => __('messages.consolidation_review_only')]);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'final_answer' => 'required|string|in:high,medium,low,other',
        ]);

        ConsolidatedResponse::updateOrCreate(
            [
                'evaluation_round_id' => $round->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'final_answer' => $validated['final_answer'],
                'decided_by' => Auth::id(),
                'resolution_method' => $round->project->consensus_model ?? 'owner_decides',
            ]
        );

        return redirect()->back()->with('success', __('messages.consolidation_saved'));
    }

    /**
     * DELETE /rounds/{round}/consolidate/{questionId} — Reset/remove consolidation.
     */
    public function destroy(EvaluationRound $round, $questionId)
    {
        Gate::authorize('update', $round->project);

        if ($round->status !== 'review') {
            return redirect()->back()->withErrors(['message' => __('messages.consolidation_review_only')]);
        }

        $round->consolidatedResponses()
            ->where('question_id', $questionId)
            ->delete();

        return redirect()->back()->with('success', __('messages.consolidation_reset'));
    }
}
