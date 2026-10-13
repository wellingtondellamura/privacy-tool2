<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use ZipArchive;
use Illuminate\Support\Str;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DataExportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Full project export: owner only, since it contains every member's e-mail,
     * answers and observations (evaluator independence rule).
     */
    public function exportProject(Project $project)
    {
        $this->authorize('update', $project);

        $data = $this->transformProjectData($project);

        $fileName = Str::slug($project->name) . '-' . now()->format('Y-m-d-His') . '.json';

        return Response::streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $fileName, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function exportAll(Request $request)
    {
        $user = $request->user();
        $projects = $user->projects()->get();

        if ($projects->isEmpty()) {
            return back()->with('error', __('messages.no_projects_to_export'));
        }

        $zipFileName = 'todos_os_projetos-' . now()->format('Y-m-d-His') . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'export_zip');

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', __('messages.zip_creation_failed'));
        }

        foreach ($projects as $project) {
            // Owned projects are exported in full; in other projects the user only
            // gets their own data (personal data portability).
            $data = $project->getMemberRole($user) === 'owner'
                ? $this->transformProjectData($project)
                : $this->transformProjectData($project, $user);
            $fileName = Str::slug($project->name) . '.json';
            $zip->addFromString($fileName, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $zip->close();

        return Response::download($tempFile, $zipFileName)->deleteFileAfterSend(true);
    }

    private function transformProjectData(Project $project, ?User $onlyFor = null): array
    {
        $project->load([
            'evaluationRounds.inspections.responses.question.category.section',
            'evaluationRounds.inspections.responses.user',
            'owner',
            'members.user'
        ]);

        $restricted = $onlyFor !== null;

        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'url' => $project->url,
            'owner' => [
                'name' => $project->owner->name,
                // A non-owner export is personal portability, not a copy of the roster.
                'email' => (!$restricted || $project->owner_id === $onlyFor->id) ? $project->owner->email : null,
            ],
            'members' => $project->members
                ->filter(fn($member) => !$restricted || $member->user_id === $onlyFor->id)
                ->values()
                ->map(fn($member) => [
                'name' => $member->user->name,
                'email' => $member->user->email,
                'role' => $member->role,
            ]),
            'rounds' => $project->evaluationRounds->map(fn($round) => [
                'id' => $round->id,
                'name' => $round->name,
                'status' => $round->status,
                'diagnosis' => $restricted ? null : $round->diagnosis,
                'started_at' => $round->started_at?->toIso8601String(),
                'closed_at' => $round->closed_at?->toIso8601String(),
                'inspections' => $round->inspections
                    ->filter(fn($inspection) => !$restricted || $inspection->user_id === $onlyFor->id)
                    ->values()
                    ->map(fn($inspection) => [
                    'id' => $inspection->id,
                    'sequential_id' => $inspection->sequential_id,
                    'status' => $inspection->status,
                    'started_at' => $inspection->started_at?->toIso8601String(),
                    'closed_at' => $inspection->closed_at?->toIso8601String(),
                    'responses' => $inspection->responses
                        ->filter(fn($response) => !$restricted || $response->user_id === $onlyFor->id)
                        ->values()
                        ->map(fn($response) => [
                        'question' => $response->question->text,
                        'category' => $response->question->category->name,
                        'section' => $response->question->category->section->name,
                        'answer' => $response->answer,
                        'observation' => $response->observation,
                        'user' => [
                            'name' => $response->user->name,
                            'email' => $response->user->email,
                        ],
                    ]),
                ]),
            ]),
        ];
    }
}
