<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProjectInvitationMail;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * POST /projects/{project}/invite — Send invitation.
     */
    public function invite(Request $request, Project $project)
    {
        Gate::authorize('invite', $project);

        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'role' => 'sometimes|in:evaluator,observer',
        ]);

        // Check if user is already a member
        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser && $project->hasMember($existingUser)) {
            return redirect()->back()->withErrors(['email' => 'User is already a member of this project.']);
        }

        $invitation = Invitation::create([
            'project_id' => $project->id,
            'email' => $validated['email'],
            'token' => Str::random(64),
            'role' => $validated['role'] ?? 'evaluator',
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)->queue(new ProjectInvitationMail($invitation, $project));

        return redirect()->back()->with('success', "Convite enviado com sucesso para {$validated['email']}.");
    }

    /**
     * POST /invitations/{token}/accept — Accept an invitation.
     */
    public function accept(Request $request, string $token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            return redirect()->route('dashboard')->withErrors(['token' => 'Invalid invitation token.']);
        }

        if ($invitation->isAccepted()) {
            return redirect()->route('dashboard')->withErrors(['token' => 'Invitation has already been accepted.']);
        }

        if ($invitation->isExpired()) {
            return redirect()->route('dashboard')->withErrors(['token' => 'Invitation has expired.']);
        }

        // Find or create user
        $user = User::where('email', $invitation->email)->first();

        if (!$user) {
            // Scenario: Accept invitation with new account
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $invitation->email,
                'password' => Hash::make($validated['password']),
            ]);
            
            Auth::login($user);
        } else {
            if (!Auth::check()) {
                Auth::login($user);
            }
        }

        // Add user to project
        ProjectMember::firstOrCreate([
            'project_id' => $invitation->project_id,
            'user_id' => $user->id,
        ], [
            'role' => $invitation->role,
        ]);

        // Mark invitation as accepted
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('projects.show', $invitation->project_id)->with('success', 'Convite aceito com sucesso.');
    }

    /**
     * DELETE /invitations/{invitation} — Decline or cancel an invitation.
     */
    public function destroy(Invitation $invitation)
    {
        $user = Auth::user();

        // Allow deletion if the user is the invited person OR has permission to invite (project owner)
        if ($invitation->email !== $user->email && !$user->can('invite', $invitation->project)) {
            abort(403);
        }

        $invitation->delete();

        return redirect()->back()->with('success', 'Convite recusado/cancelado com sucesso.');
    }

    /**
     * POST /invitations/{invitation}/resend — Resend an expired invitation.
     */
    public function resend(Invitation $invitation)
    {
        Gate::authorize('invite', $invitation->project);

        $invitation->update([
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($invitation->email)->queue(new ProjectInvitationMail($invitation, $invitation->project));

        return redirect()->back()->with('success', "Convite reenviado com sucesso para {$invitation->email}.");
    }
}
