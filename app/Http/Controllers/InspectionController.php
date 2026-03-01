<?php

namespace App\Http\Controllers;

use App\Actions\CloseInspectionAction;
use App\Models\Inspection;
use App\Models\Project;
use App\Models\QuestionnaireVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class InspectionController extends Controller
{
    /**
     * POST /projects/{project}/inspections — Create new inspection.
     */
    public function store(Request $request, Project $project)
    {
        Gate::authorize('view', $project);

        $evaluationRoundId = $request->input('evaluation_round_id');
        if (!$evaluationRoundId) {
            return redirect()->back()->withErrors(['evaluation_round_id' => 'É necessário selecionar uma rodada de avaliação para iniciar uma inspeção.']);
        }

        $round = \App\Models\EvaluationRound::where('id', $evaluationRoundId)
            ->where('project_id', $project->id)
            ->firstOrFail();
        
        if ($round->status === 'closed') {
            return redirect()->back()->withErrors(['status' => 'Não é possível adicionar inspeções a uma rodada que já está fechada.']);
        }

        $activeVersion = QuestionnaireVersion::getActive();

        if (!$activeVersion) {
            return redirect()->back()->withErrors(['questionnaire' => 'No active questionnaire version available.']);
        }

        $inspection = Inspection::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'questionnaire_version_id' => $activeVersion->id,
            'evaluation_round_id' => $evaluationRoundId ?? null,
            'status' => 'draft',
        ]);

        return redirect()->route('inspections.show', $inspection->id);
    }

    /**
     * GET /inspections/{inspection} — Show inspection details.
     */
    public function show(Inspection $inspection)
    {
        Gate::authorize('view', $inspection->project);

        $inspection->load([
            'questionnaireVersion.sections.categories.questions',
            'project',
            'user',
            'evaluationRound',
            'responses' => function ($query) {
                $query->where('user_id', Auth::id());
            },
            'resultSnapshots' => function ($query) {
                $query->whereNull('user_id'); // Consolidado
            }
        ]);

        return Inertia::render('Inspection/Show', [
            'inspection' => $inspection,
        ]);
    }

    /**
     * POST /inspections/{inspection}/activate — Activate draft inspection.
     */
    public function activate(Inspection $inspection)
    {
        Gate::authorize('view', $inspection->project);

        if ($inspection->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['status' => 'Apenas o responsável pela inspeção pode mudar seu status.']);
        }

        try {
            $inspection->transitionTo('active');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Inspeção iniciada e mudou para status Ativa.');
    }

    /**
     * POST /inspections/{inspection}/close — Close active inspection.
     * Uses CloseInspectionAction to generate snapshots (RN-07).
     */
    public function close(Inspection $inspection, CloseInspectionAction $action)
    {
        Gate::authorize('view', $inspection->project);

        // Only creator can close
        if ($inspection->user_id !== Auth::id()) {
            return redirect()->back()->withErrors(['status' => 'Apenas o responsável pela inspeção pode mudar seu status.']);
        }

        // Only owner can close? The user said "não permita que outro usuário mude o status", 
        // which usually implies the one who started it. 
        // If we want to keep the owner restriction as well, we can, but the primary request is about the responsible.
        // Let's stick to the "responsible" rule as requested.

        try {
            $action->execute($inspection);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('results.individual', $inspection->id)->with('success', 'Inspeção finalizada e instantâneos gerados.');
    }
}
