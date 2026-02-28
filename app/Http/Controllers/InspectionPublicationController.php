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
        
        try {
            $this->publicationService->publish($inspection, $visibility, $request->user());
            return redirect()->back()->with('success', 'Inspeção publicada com sucesso no diretório.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['publication' => $e->getMessage()]);
        }
    }

    /**
     * Update publication visibility.
     */
    public function update(Request $request, Inspection $inspection)
    {
        $publication = $inspection->publication;
        
        if (!$publication) {
            return redirect()->back()->withErrors(['publication' => 'Publicação não encontrada.']);
        }

        Gate::authorize('update', $publication);

        $validated = $request->validate([
            'visibility' => ['required', new Enum(Visibility::class)],
        ]);

        $visibility = Visibility::from($validated['visibility']);
        
        try {
            $this->publicationService->updateVisibility($inspection, $visibility);
            return redirect()->back()->with('success', 'Visibilidade da publicação atualizada.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['publication' => $e->getMessage()]);
        }
    }

    /**
     * Revoke publication.
     */
    public function destroy(Inspection $inspection)
    {
        $publication = $inspection->publication;
        
        if (!$publication) {
            return redirect()->back();
        }

        Gate::authorize('delete', $publication);

        try {
            $this->publicationService->revoke($inspection);
            return redirect()->back()->with('success', 'Publicação removida do diretório.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['publication' => $e->getMessage()]);
        }
    }
}
