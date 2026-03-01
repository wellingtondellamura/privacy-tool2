<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Models\RoundPublication;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicDirectoryController extends Controller
{
    /**
     * List public tools.
     */
    public function index(Request $request)
    {
        $query = RoundPublication::query()
            ->where('visibility', '!=', Visibility::PRIVATE)
            ->with(['evaluationRound.project', 'evaluationRound.inspections.questionnaireVersion']);

        // Filter by Medal
        if ($request->filled('medal')) {
            $query->where('medal', $request->medal);
        }

        // Filter by Year
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by Questionnaire Version
        if ($request->filled('version')) {
            $query->where('questionnaire_version_id', $request->version);
        }

        // Sorting
        $sort = $request->get('sort', 'score_desc');
        match ($sort) {
            'score_asc' => $query->orderBy('score', 'asc'),
            'date_desc' => $query->orderBy('published_at', 'desc'),
            'date_asc' => $query->orderBy('published_at', 'asc'),
            default => $query->orderBy('score', 'desc'),
        };

        $publications = $query->paginate(15)->withQueryString()->through(fn ($pub) => [
            'slug' => $pub->slug,
            'project_name' => $pub->evaluationRound->project->name,
            'round_name' => $pub->evaluationRound->name,
            'project_url' => $pub->evaluationRound->project->url,
            'score' => $pub->score,
            'medal' => $pub->medal,
            'year' => $pub->year,
            'published_at' => $pub->published_at?->format('d/m/Y'),
            'visibility' => $pub->visibility->value,
        ]);

        return Inertia::render('PublicDirectory/Index', [
            'tools' => $publications,
            'filters' => $request->only(['medal', 'year', 'version', 'sort']),
        ]);
    }

    /**
     * Show tool details.
     */
    public function show(string $slug)
    {
        $publication = RoundPublication::where('slug', $slug)
            ->where('visibility', '!=', Visibility::PRIVATE)
            ->with(['evaluationRound.project', 'evaluationRound.snapshots'])
            ->firstOrFail();

        $snapshot = $publication->evaluationRound->snapshots()->latest()->first();
        if (!$snapshot) {
            abort(404, "Consolidated snapshot not found.");
        }

        $project = $publication->evaluationRound->project;
        $payload = $snapshot->payload_json;

        if ($publication->visibility === Visibility::SCORE_PUBLIC) {
            return Inertia::render('PublicDirectory/ShowSummary', [
                'tool' => [
                    'slug' => $publication->slug,
                    'name' => $project->name,
                    'url' => $project->url,
                    'score' => $payload['global_score'] ?? 0,
                    'medal' => $payload['medal'] ?? null,
                    'sections' => collect($payload['sections'] ?? [])->map(fn($s) => [
                        'name' => $s['name'],
                        'score' => $s['score'],
                        'medal' => $s['medal'],
                    ]),
                    'inspection_date' => $publication->evaluationRound->closed_at?->format('d/m/Y'),
                    'version' => $publication->evaluationRound->inspections()->first()?->questionnaireVersion->version,
                    'user_count' => $payload['user_count'] ?? 0,
                    'diagnosis' => $publication->evaluationRound->diagnosis,
                ]
            ]);
        }

        // FULL_PUBLIC: RN-PUB-04 — full_public
        // Never expose user_id or individual observations.
        // The consolidated payload already removes user_id but we must be careful.
        $safePayload = $this->sanitizePayload($payload);

        return Inertia::render('PublicDirectory/ShowFull', [
            'tool' => [
                'slug' => $publication->slug,
                'name' => $project->name,
                'url' => $project->url,
                'report' => $safePayload,
                'inspection_date' => $publication->evaluationRound->closed_at?->format('d/m/Y'),
                'version' => $publication->evaluationRound->inspections()->first()?->questionnaireVersion->version,
            ]
        ]);
    }

    /**
     * Ensure no sensitive data exists in the payload.
     */
    private function sanitizePayload(array $payload): array
    {
        // Recursively remove any user_id or observation if they somehow leaked into consolidated
        // According to CloseInspectionAction, they shouldn't be there, but we harden here.
        unset($payload['user_id']);
        
        if (isset($payload['sections'])) {
            foreach ($payload['sections'] as &$section) {
                if (isset($section['categories'])) {
                    foreach ($section['categories'] as &$category) {
                        if (isset($category['questions'])) {
                            foreach ($category['questions'] as &$question) {
                                unset($question['user_id']);
                                unset($question['observation']);
                                unset($question['comments']);
                            }
                        }
                    }
                }
            }
        }
        
        return $payload;
    }
}
