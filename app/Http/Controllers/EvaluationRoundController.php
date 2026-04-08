<?php

namespace App\Http\Controllers;

use App\Models\EvaluationRound;
use App\Models\Project;
use App\Actions\CloseRoundAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class EvaluationRoundController extends Controller
{
    /**
     * POST /projects/{project}/rounds — Create a new evaluation round.
     */
    public function store(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $round = $project->evaluationRounds()->create([
            'name' => $validated['name'],
            'status' => 'draft',
        ]);

        return redirect()->route('rounds.show', $round->id)->with('success', __('messages.round_created'));
    }

    /**
     * GET /rounds/{round} — Show evaluation round details.
     */
    public function show(EvaluationRound $round)
    {
        Gate::authorize('view', $round->project);

        $round->load([
            'project',
            'inspections.user',
            'inspections.publication',
            'snapshots' => function ($query) {
                $query->latest();
            },
            'publicDirectory',
            'badge'
        ]);

        return Inertia::render('EvaluationRound/Show', [
            'round' => $round,
        ]);
    }

    /**
     * GET /rounds/{round}/review — Review and closing preparation.
     */
    public function review(EvaluationRound $round, CloseRoundAction $action)
    {
        Gate::authorize('update', $round->project);

        if ($round->status !== 'draft') {
            return redirect()->route('rounds.show', $round->id);
        }

        $round->load(['project', 'inspections.user', 'inspections.resultSnapshots' => function ($query) {
            $query->whereNull('user_id');
        }]);

        // Helper calculation for preview (without saving snapshot)
        $previewPayload = $action->calculatePreviewPayload($round);

        return Inertia::render('EvaluationRound/Review', [
            'round' => $round,
            'preview' => $previewPayload,
        ]);
    }

    /**
     * POST /rounds/{round}/close — Consolidate and close evaluation round.
     */
    public function close(Request $request, EvaluationRound $round, CloseRoundAction $action)
    {
        Gate::authorize('update', $round->project);

        $validated = $request->validate([
            'diagnosis' => 'nullable|string',
            'publish_immediately' => 'boolean',
            'visibility' => 'nullable|string|in:score_public,full_public',
        ]);

        if ($round->status !== 'draft') {
            return redirect()->back()->withErrors(['status' => __('messages.round_already_closed')]);
        }

        // We update the diagnosis first so the snapshot can include it
        $round->update([
            'diagnosis' => $validated['diagnosis'] ?? $round->diagnosis,
        ]);

        try {
            $action->execute($round);
            
            // Handle immediate publication if requested
            if ($validated['publish_immediately'] ?? false) {
                $round->publicDirectory()->create([
                    'visibility' => $validated['visibility'] ?? 'score_public',
                    'published_at' => now(),
                    'payload_json' => $round->snapshots()->latest()->first()->payload_json,
                ]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('rounds.show', $round->id)->with('success', __('messages.round_closed'));
    }
}
