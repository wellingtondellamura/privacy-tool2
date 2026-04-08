<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\ResultSnapshot;
use App\Models\RoundSnapshot;
use App\Models\EvaluationRound;
use App\Services\ComparisonService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ResultController extends Controller
{
    /**
     * GET /inspections/{inspection}/results — Individual user results.
     */
    public function individual(Inspection $inspection)
    {
        Gate::authorize('view', $inspection->project);

        $user = Auth::user();

        $snapshot = ResultSnapshot::where([
            'inspection_id' => $inspection->id,
            'user_id' => $user->id,
        ])->first();

        if (!$snapshot) {
            return redirect()->back()->withErrors(['results' => __('messages.no_results_for_user')]);
        }

        $inspection->load('project', 'questionnaireVersion', 'evaluationRound');

        return Inertia::render('Results/Individual', [
            'snapshot' => $snapshot->payload_json,
            'inspection' => $inspection,
        ]);
    }

    /**
     * GET /inspections/{inspection}/team-results — Consolidated results.
     */
    public function team(Inspection $inspection)
    {
        Gate::authorize('view', $inspection->project);

        if (!$inspection->isClosed()) {
            return redirect()->back()->withErrors(['results' => __('messages.inspection_must_be_closed')]);
        }

        $snapshot = ResultSnapshot::where([
            'inspection_id' => $inspection->id,
            'user_id' => null,
        ])->first();

        if (!$snapshot) {
            return redirect()->back()->withErrors(['results' => __('messages.consolidated_not_found')]);
        }

        $inspection->load('project', 'questionnaireVersion', 'evaluationRound');

        return Inertia::render('Results/Team', [
            'snapshot' => $snapshot->payload_json,
            'inspection' => $inspection,
        ]);
    }

    /**
     * GET /rounds/{round}/results — Round consolidated results.
     */
    public function round(EvaluationRound $round)
    {
        Gate::authorize('view', $round->project);

        $snapshot = RoundSnapshot::where('evaluation_round_id', $round->id)
            ->latest()
            ->first();

        if (!$snapshot) {
            return redirect()->back()->withErrors(['results' => __('messages.round_consolidated_not_found')]);
        }

        $round->load('project');

        return Inertia::render('Results/Round', [
            'snapshot' => $snapshot->payload_json,
            'round' => $round,
        ]);
    }

    /**
     * GET /inspections/{inspection}/comparison/{other} — Compare two inspections.
     */
    public function comparison(Inspection $inspection, Inspection $other)
    {
        Gate::authorize('view', $inspection->project);

        if ($inspection->project_id !== $other->project_id) {
            return redirect()->back()->withErrors(['comparison' => __('messages.inspections_same_project')]);
        }

        $baseline = ResultSnapshot::where([
            'inspection_id' => $inspection->id,
            'user_id' => null,
        ])->first();

        $comparison = ResultSnapshot::where([
            'inspection_id' => $other->id,
            'user_id' => null,
        ])->first();

        if (!$baseline || !$comparison) {
            return redirect()->back()->withErrors(['comparison' => __('messages.both_inspections_closed')]);
        }

        try {
            $result = ComparisonService::compare($baseline, $comparison);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['comparison' => $e->getMessage()]);
        }

        $inspection->load('project');

        return Inertia::render('Comparison/Show', [
            'comparison' => $result,
            'baseInspection' => $inspection,
            'otherInspection' => $other,
        ]);
    }

    /**
     * GET /rounds/{round}/comparison/{other} — Compare two evaluation rounds.
     */
    public function roundComparison(EvaluationRound $round, EvaluationRound $other)
    {
        Gate::authorize('view', $round->project);

        if ($round->project_id !== $other->project_id) {
            return redirect()->back()->withErrors(['comparison' => __('messages.rounds_same_project')]);
        }

        $baseline = $round->snapshots()->latest()->first();
        $comparison = $other->snapshots()->latest()->first();

        if (!$baseline || !$comparison) {
            return redirect()->back()->withErrors(['comparison' => __('messages.both_rounds_closed')]);
        }

        try {
            $result = ComparisonService::compareRoundSnapshots($baseline, $comparison);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['comparison' => $e->getMessage()]);
        }

        $round->load('project');

        return Inertia::render('Comparison/Show', [
            'comparison' => $result,
            'baseRound' => $round,
            'otherRound' => $other,
            'isRoundComparison' => true,
        ]);
    }
}
