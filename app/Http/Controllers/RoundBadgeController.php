<?php

namespace App\Http\Controllers;

use App\Models\EvaluationRound;
use App\Models\RoundBadge;
use App\Services\RoundBadgeService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoundBadgeController extends Controller
{
    use AuthorizesRequests;

    protected $service;

    public function __construct(RoundBadgeService $service)
    {
        $this->service = $service;
    }

    /**
     * Create a badge for the round.
     */
    public function store(EvaluationRound $round)
    {
        $this->authorize('create', [RoundBadge::class, $round]);

        try {
            $badge = $this->service->createBadge($round);
            return back()->with('success', 'Selo gerado com sucesso.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Revoke the badge.
     */
    public function destroy(RoundBadge $badge)
    {
        $this->authorize('delete', $badge);

        $this->service->revokeBadge($badge);

        return back()->with('success', 'Selo revogado.');
    }

    /**
     * Update badge style.
     */
    public function updateStyle(Request $request, RoundBadge $badge)
    {
        $this->authorize('update', $badge);

        $request->validate([
            'style' => 'required|in:default,compact,minimal',
        ]);

        $this->service->updateStyle($badge, $request->style);

        return back()->with('success', 'Estilo do selo atualizado.');
    }

    /**
     * Public endpoint for badge JSON data.
     * RN-BADGE-05: Invalidation if round is not published or private.
     */
    public function publicShow(string $token)
    {
        $badge = RoundBadge::where('public_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $round = $badge->evaluationRound;

        if (!$round->isPublished() || $round->publicDirectory?->visibility->value === 'private') {
            abort(404);
        }

        $snapshot = $round->snapshots()->latest()->firstOrFail();
        $payload = $snapshot->payload_json;

        return response()->json([
            'project_name' => $round->project->name,
            'round_name' => $round->name,
            'global_score' => $payload['global_score'],
            'medal' => $payload['medal']['name'],
            'date' => $round->closed_at?->format('d/m/Y'),
            'public_url' => route('public.tools.show', $round->publicDirectory->slug),
            'style' => $badge->style,
        ])->setCache([
            'public' => true,
            'max_age' => 300, // 5 minutes
        ]);
    }

    /**
     * Public endpoint for the embeddable script.
     */
    public function publicScript(string $token)
    {
        $badge = RoundBadge::where('public_token', $token)
            ->where('is_active', true)
            ->exists();

        if (!$badge) {
            abort(404);
        }

        $apiUrl = route('badge.show', $token);
        
        $js = <<<JS
(function() {
    const container = document.currentScript;
    fetch('{$apiUrl}')
        .then(response => response.json())
        .then(data => {
            const badgeDiv = document.createElement('div');
            badgeDiv.className = 'privacy-tool-badge privacy-tool-badge-' + data.style;
            
            let html = `
                <a href="\${data.public_url}" target="_blank" style="text-decoration:none; color:inherit; font-family:sans-serif;">
                    <div style="border:1px solid #ccc; border-radius:8px; padding:10px; display:inline-block; background:#fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div style="font-size:12px; color:#666;">\${data.project_name}</div>
                        <div style="font-weight:bold; font-size:16px; margin:4px 0;">\${data.medal} (\${data.global_score}%)</div>
                        <div style="font-size:10px; color:#999;">Auditado em \${data.date}</div>
                    </div>
                </a>
            `;

            if (data.style === 'compact') {
                html = `
                    <a href="\${data.public_url}" target="_blank" style="text-decoration:none; color:inherit; font-family:sans-serif;">
                        <div style="border:1px solid #ccc; border-radius:4px; padding:6px; display:inline-block; background:#fff;">
                             <strong>\${data.medal}</strong> (\${data.global_score}%)
                        </div>
                    </a>
                `;
            } else if (data.style === 'minimal') {
                html = `
                    <a href="\${data.public_url}" target="_blank" style="text-decoration:none; color:inherit; font-family:sans-serif;">
                         <div style="display:inline-block; font-weight:bold;">\${data.medal} (\${data.global_score}%)</div>
                    </a>
                `;
            }

            badgeDiv.innerHTML = html;
            container.parentNode.insertBefore(badgeDiv, container);
        })
        .catch(err => console.error('Privacy Tool Badge Error:', err));
})();
JS;

        return response($js)->header('Content-Type', 'application/javascript');
    }
}
