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
            'software_version' => 'nullable|string|max:255',
        ]);

        $round = $project->evaluationRounds()->create([
            'name' => $validated['name'],
            'software_version' => $validated['software_version'] ?? null,
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
        Gate::authorize('view', $round->project);

        if ($round->status !== 'draft' && $round->status !== 'review') {
            return redirect()->route('rounds.show', $round->id);
        }

        $round->load([
            'project',
            'inspections.user',
            'inspections.resultSnapshots' => function ($query) {
                $query->whereNull('user_id');
            },
            'reviewComments.user',
            'consolidatedResponses.decidedBy'
        ]);

        // Helper calculation for preview (without saving snapshot)
        $previewPayload = $action->calculatePreviewPayload($round);

        $inspectionIds = $round->inspections()->where('status', 'closed')->pluck('id')->toArray();
        $evaluatorResponses = \App\Models\Response::whereIn('inspection_id', $inspectionIds)
            ->with('user')
            ->get()
            ->groupBy('question_id')
            ->map(fn($group) => $group->map(fn($r) => [
                'user_id' => $r->user_id,
                'user_name' => $r->user->name,
                'answer' => $r->answer,
                'observation' => $r->observation,
            ]));

        return Inertia::render('EvaluationRound/Review', [
            'round' => $round,
            'preview' => $previewPayload,
            'evaluatorResponses' => $evaluatorResponses,
        ]);
    }

    /**
     * POST /rounds/{round}/enter-review — Put the round into the review phase.
     */
    public function enterReview(EvaluationRound $round)
    {
        Gate::authorize('update', $round->project);

        if ($round->status !== 'draft') {
            return redirect()->back()->withErrors(['status' => 'Round is not in draft state.']);
        }

        $round->update([
            'status' => 'review',
        ]);

        return redirect()->route('rounds.show', $round->id)->with('success', 'Fase de revisão/sensemaking iniciada.');
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

        if ($round->status !== 'draft' && $round->status !== 'review') {
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

    /**
     * PUT /rounds/{round} — Update evaluation round.
     */
    public function update(Request $request, EvaluationRound $round)
    {
        Gate::authorize('update', $round->project);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'software_version' => 'nullable|string|max:255',
        ]);

        $round->update($validated);

        return redirect()->back()->with('success', __('messages.round_updated') ?? 'Rodada atualizada com sucesso.');
    }
}
