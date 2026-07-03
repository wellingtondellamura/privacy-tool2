<?php

namespace App\Http\Controllers;

use App\Models\EvaluationRound;
use App\Models\RoundBadge;
use App\Services\AggregationService;
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
            return back()->with('success', __('messages.badge_generated'));
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

        return back()->with('success', __('messages.badge_revoked'));
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

        return back()->with('success', __('messages.badge_style_updated'));
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

        $lang = request()->query('lang', 'en');
        if (!in_array($lang, ['pt_BR', 'en'])) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $snapshot = $round->snapshots()->latest()->firstOrFail();
        $payload = $snapshot->payload_json;

        $medalKey = $payload['medal']['name'];
        $isSelf = (bool) ($payload['is_self_assessment'] ?? $round->project->is_self_assessment);
        $auditLabel = $isSelf ? __('labels.self_assessment') : __('labels.external_audit');
        $softwareVersion = $payload['software_version'] ?? $round->software_version;

        return response()->json([
            'project_name' => $round->project->name,
            'round_name' => $round->name,
            'global_score' => $payload['global_score'],
            'medal' => AggregationService::medalLabel($medalKey),
            'medal_key' => $medalKey,
            'date' => $round->closed_at?->format('d/m/Y'),
            'audited_label' => __('labels.audited_at'),
            'public_url' => route('public.tools.show', $round->publicDirectory->slug),
            'style' => $badge->style,
            'is_self_assessment' => $isSelf,
            'audit_type_label' => $auditLabel,
            'software_version' => $softwareVersion,
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

        $lang = request()->query('lang', 'en');
        if (!in_array($lang, ['pt_BR', 'en'])) {
            $lang = 'en';
        }

        $apiUrl = route('badge.show', $token) . '?lang=' . $lang;
        
        $js = <<<JS
(function() {
    const container = document.currentScript;
    fetch('{$apiUrl}')
        .then(response => response.json())
        .then(data => {
            const badgeDiv = document.createElement('div');
            badgeDiv.className = 'privacy-tool-badge privacy-tool-badge-' + data.style;
            
            let html = `
                <a href="\${data.public_url}" target="_blank" style="text-decoration:none; color:inherit; font-family:sans-serif; display:inline-block;">
                    <div style="border:1px solid #e2e8f0; border-radius:12px; padding:16px; display:inline-block; background:#fff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03); border-top: 4px solid \${data.medal_key === 'gold' ? '#fbbf24' : (data.medal_key === 'silver' ? '#94a3b8' : '#b45309')}; min-width: 200px;">
                        <div style="font-size:12px; font-weight:600; color:#475569;">\${data.project_name}</div>
                        <div style="font-weight:bold; font-size:18px; margin:6px 0; color:#0f172a;">\${data.medal} (\${data.global_score}%)</div>
                        <div style="font-size:10px; color:#64748b; margin-bottom:4px;">\${data.audited_label} \${data.date}</div>
                        <div style="font-size:10px; font-weight:600; padding:2px 6px; border-radius:4px; display:inline-block; margin-top:2px; \${data.is_self_assessment ? 'background:#fef3c7; color:#d97706;' : 'background:#dcfce7; color:#15803d;' }">
                            \${data.audit_type_label}
                        </div>
                        \${data.software_version ? `<div style="font-size:10px; color:#94a3b8; margin-top:6px;">v\${data.software_version}</div>` : ''}
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
