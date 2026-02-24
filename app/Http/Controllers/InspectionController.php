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
    public function store(Project $project)
    {
        Gate::authorize('view', $project);

        $activeVersion = QuestionnaireVersion::getActive();

        if (!$activeVersion) {
            return redirect()->back()->withErrors(['questionnaire' => 'No active questionnaire version available.']);
        }

        $inspection = Inspection::create([
            'project_id' => $project->id,
            'questionnaire_version_id' => $activeVersion->id,
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
            'responses' => function ($query) {
                $query->where('user_id', Auth::id());
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
        Gate::authorize('update', $inspection->project);

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
    public function close(Inspection $inspection)
    {
        Gate::authorize('update', $inspection->project);

        // Only owner can close
        $user = Auth::user();
        if ($inspection->project->getMemberRole($user) !== 'owner') {
            return redirect()->back()->withErrors(['status' => 'Apenas o proprietário do projeto pode fechar a inspeção.']);
        }

        try {
            $action = new CloseInspectionAction();
            $action->execute($inspection);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('results.individual', $inspection->id)->with('success', 'Inspeção finalizada e instantâneos gerados.');
    }
}
