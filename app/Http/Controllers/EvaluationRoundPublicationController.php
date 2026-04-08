<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Models\EvaluationRound;
use App\Models\RoundPublication;
use App\Services\RoundPublicationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Gate;

class EvaluationRoundPublicationController extends Controller
{
    public function __construct(
        protected RoundPublicationService $publicationService
    ) {}

    /**
     * Publish an evaluation round.
     */
    public function store(Request $request, EvaluationRound $round)
    {
        Gate::authorize('create', [RoundPublication::class, $round]);

        $validated = $request->validate([
            'visibility' => ['required', new Enum(Visibility::class)],
        ]);

        $visibility = Visibility::from($validated['visibility']);
        
        try {
            $this->publicationService->publish($round, $visibility, $request->user());
            return redirect()->back()->with('success', __('messages.round_published'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['publication' => $e->getMessage()]);
        }
    }

    /**
     * Update publication visibility.
     */
    public function update(Request $request, EvaluationRound $round)
    {
        $publication = RoundPublication::where('evaluation_round_id', $round->id)->first();
        
        if (!$publication) {
            return redirect()->back()->withErrors(['publication' => __('messages.publication_not_found')]);
        }

        Gate::authorize('update', $publication);

        $validated = $request->validate([
            'visibility' => ['required', new Enum(Visibility::class)],
        ]);

        $visibility = Visibility::from($validated['visibility']);
        
        try {
            $this->publicationService->updateVisibility($round, $visibility);
            return redirect()->back()->with('success', __('messages.publication_updated'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['publication' => $e->getMessage()]);
        }
    }

    /**
     * Revoke publication.
     */
    public function destroy(Request $request, EvaluationRound $round)
    {
        $publication = RoundPublication::where('evaluation_round_id', $round->id)->first();
        
        if (!$publication) {
            return redirect()->back();
        }

        Gate::authorize('delete', $publication);

        try {
            $this->publicationService->revoke($round);
            return redirect()->back()->with('success', __('messages.publication_removed'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['publication' => $e->getMessage()]);
        }
    }
}
