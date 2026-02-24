<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\ResultSnapshot;
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
            return redirect()->back()->withErrors(['results' => 'Nenhum resultado encontrado para este usuário.']);
        }

        $inspection->load('project', 'questionnaireVersion');

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
            return redirect()->back()->withErrors(['results' => 'A inspeção deve estar concluída para ver resultados da equipe.']);
        }

        $snapshot = ResultSnapshot::where([
            'inspection_id' => $inspection->id,
            'user_id' => null,
        ])->first();

        if (!$snapshot) {
            return redirect()->back()->withErrors(['results' => 'Resultados consolidados não encontrados.']);
        }

        $inspection->load('project', 'questionnaireVersion');

        return Inertia::render('Results/Team', [
            'snapshot' => $snapshot->payload_json,
            'inspection' => $inspection,
        ]);
    }

    /**
     * GET /inspections/{inspection}/comparison/{other} — Compare two inspections.
     */
    public function comparison(Inspection $inspection, Inspection $other)
    {
        Gate::authorize('view', $inspection->project);

        if ($inspection->project_id !== $other->project_id) {
            return redirect()->back()->withErrors(['comparison' => 'As inspeções devem pertencer ao mesmo projeto.']);
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
            return redirect()->back()->withErrors(['comparison' => 'Ambas as inspeções devem estar concluídas.']);
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
}
