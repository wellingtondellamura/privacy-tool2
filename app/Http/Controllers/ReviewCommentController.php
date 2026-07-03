<?php

namespace App\Http\Controllers;

use App\Models\EvaluationRound;
use App\Models\ReviewComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewCommentController extends Controller
{
    /**
     * POST /rounds/{round}/comments — Post a comment in a review thread.
     */
    public function store(Request $request, EvaluationRound $round)
    {
        Gate::authorize('view', $round->project);

        // Only allowed if the round is in the 'review' status
        if ($round->status !== 'review') {
            return redirect()->back()->withErrors(['message' => 'Comments can only be posted during the review phase.']);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'body' => 'required|string|max:1000',
        ]);

        $round->reviewComments()->create([
            'question_id' => $validated['question_id'],
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return redirect()->back()->with('success', 'Comentário enviado.');
    }

    /**
     * DELETE /comments/{comment} — Delete a comment.
     */
    public function destroy(ReviewComment $comment)
    {
        $round = $comment->evaluationRound;
        
        // Owner of the comment or Owner of the project can delete
        $isCommentOwner = Auth::id() === $comment->user_id;
        $isProjectOwner = Auth::id() === $round->project->owner_id;

        if (!$isCommentOwner && !$isProjectOwner) {
            abort(403, 'Unauthorized');
        }

        if ($round->status !== 'review') {
            return redirect()->back()->withErrors(['message' => 'Comments can only be deleted during the review phase.']);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comentário excluído.');
    }
}
