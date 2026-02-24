<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ResponseController extends Controller
{
    /**
     * POST /inspections/{inspection}/response — Submit or update a response.
     */
    public function store(Request $request, Inspection $inspection)
    {
        Gate::authorize('view', $inspection->project);

        // Only active inspections accept responses
        if (!$inspection->isActive()) {
            return response()->json(['message' => 'Inspection is not active. Cannot submit responses.'], 422);
        }

        // Only evaluators can respond
        $user = Auth::user();
        $role = $inspection->project->getMemberRole($user);

        if (!in_array($role, ['owner', 'evaluator'])) {
            return response()->json(['message' => 'Only evaluators can submit responses.'], 403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|string|in:Suficiente,Insuficiente,Inexistente,Outro,Apropriado,Necessita melhorias,Inapropriado',
            'observation' => 'nullable|string',
        ]);

        // Upsert: replace previous answer for same question/user
        $response = Response::updateOrCreate(
            [
                'inspection_id' => $inspection->id,
                'question_id' => $validated['question_id'],
                'user_id' => $user->id,
            ],
            [
                'answer' => $validated['answer'],
                'observation' => $validated['observation'] ?? null,
            ]
        );

        return response()->json($response, 201);
    }
}
