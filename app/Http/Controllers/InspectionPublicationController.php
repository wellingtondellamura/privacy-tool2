<?php

namespace App\Http\Controllers;

use App\Enums\Visibility;
use App\Models\Inspection;
use App\Models\InspectionPublication;
use App\Services\PublicationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Gate;

class InspectionPublicationController extends Controller
{
    public function __construct(
        protected PublicationService $publicationService
    ) {}

    /**
     * Publish an inspection.
     */
    public function store(Request $request, Inspection $inspection)
    {
        Gate::authorize('create', [InspectionPublication::class, $inspection]);

        $validated = $request->validate([
            'visibility' => ['required', new Enum(Visibility::class)],
        ]);

        $visibility = Visibility::from($validated['visibility']);
        
        $publication = $this->publicationService->publish($inspection, $visibility, $request->user());

        return response()->json([
            'message' => 'Inspection published successfully.',
            'publication' => $publication,
        ], 201);
    }

    /**
     * Update publication visibility.
     */
    public function update(Request $request, Inspection $inspection)
    {
        $publication = $inspection->publication;
        
        if (!$publication) {
            return response()->json(['message' => 'No publication found.'], 404);
        }

        Gate::authorize('update', $publication);

        $validated = $request->validate([
            'visibility' => ['required', new Enum(Visibility::class)],
        ]);

        $visibility = Visibility::from($validated['visibility']);
        
        $publication = $this->publicationService->updateVisibility($inspection, $visibility);

        return response()->json([
            'message' => 'Visibility updated successfully.',
            'publication' => $publication,
        ]);
    }

    /**
     * Revoke publication.
     */
    public function destroy(Inspection $inspection)
    {
        $publication = $inspection->publication;
        
        if (!$publication) {
            return response()->json(['message' => 'No publication found.'], 404);
        }

        Gate::authorize('delete', $publication);

        $this->publicationService->revoke($inspection);

        return response()->json([
            'message' => 'Publication revoked successfully.',
        ], 204);
    }
}
