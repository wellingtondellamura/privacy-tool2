<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use ZipArchive;
use Illuminate\Support\Str;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DataExportController extends Controller
{
    use AuthorizesRequests;

    public function exportProject(Project $project)
    {
        $this->authorize('view', $project);

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
            return back()->with('error', 'Você não possui projetos para exportar.');
        }

        $zipFileName = 'todos_os_projetos-' . now()->format('Y-m-d-His') . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'export_zip');

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', 'Não foi possível criar o arquivo ZIP.');
        }

        foreach ($projects as $project) {
            $data = $this->transformProjectData($project);
            $fileName = Str::slug($project->name) . '.json';
            $zip->addFromString($fileName, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $zip->close();

        return Response::download($tempFile, $zipFileName)->deleteFileAfterSend(true);
    }

    private function transformProjectData(Project $project): array
    {
        $project->load([
            'evaluationRounds.inspections.responses.question.category.section',
            'evaluationRounds.inspections.responses.user',
            'owner',
            'members.user'
        ]);

        return [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'url' => $project->url,
            'owner' => [
                'name' => $project->owner->name,
                'email' => $project->owner->email,
            ],
            'members' => $project->members->map(fn($member) => [
                'name' => $member->user->name,
                'email' => $member->user->email,
                'role' => $member->role,
            ]),
            'rounds' => $project->evaluationRounds->map(fn($round) => [
                'id' => $round->id,
                'name' => $round->name,
                'status' => $round->status,
                'diagnosis' => $round->diagnosis,
                'started_at' => $round->started_at?->toIso8601String(),
                'closed_at' => $round->closed_at?->toIso8601String(),
                'inspections' => $round->inspections->map(fn($inspection) => [
                    'id' => $inspection->id,
                    'sequential_id' => $inspection->sequential_id,
                    'status' => $inspection->status,
                    'started_at' => $inspection->started_at?->toIso8601String(),
                    'closed_at' => $inspection->closed_at?->toIso8601String(),
                    'responses' => $inspection->responses->map(fn($response) => [
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
