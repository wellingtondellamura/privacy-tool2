<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * GET /projects — List user's projects.
     */
    public function index()
    {
        $user = Auth::user();

        $projects = Project::whereHas('members', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with('owner')
        ->withCount('inspections')
        ->orderBy('created_at', 'desc')
        ->get();

        $pendingInvitations = \App\Models\Invitation::with('project:id,name')
            ->where('email', $user->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->get();

        return Inertia::render('Workspace/Index', [
            'projects' => $projects,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }

    /**
     * POST /projects — Create a new project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url|max:500',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        $project = Project::create([
            ...$validated,
            'owner_id' => $user->id,
        ]);

        // Automatically add creator as owner member
        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);

        return redirect()->route('projects.index')->with('success', 'Projeto criado com sucesso.');
    }

    /**
     * GET /projects/{project} — Show project details.
     */
    public function show(Project $project)
    {
        Gate::authorize('view', $project);

        $project->load([
            'members.user', 
            'invitations' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'inspections' => function ($query) {
                $query->with('user')->orderBy('created_at', 'desc');
            }
        ]);

        // Annotate invitations if the user already has an account
        $project->invitations->each(function ($invitation) {
            $invitation->has_account = \App\Models\User::where('email', $invitation->email)->exists();
        });

        return Inertia::render('Project/Show', [
            'project' => $project,
        ]);
    }

    /**
     * PUT /projects/{project} — Update project.
     */
    public function update(Request $request, Project $project)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'url' => 'nullable|url|max:500',
            'icon' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
        ]);

        $project->update($validated);

        return redirect()->back()->with('success', 'Projeto atualizado com sucesso.');
    }

    /**
     * DELETE /projects/{project} — Soft delete project.
     */
    public function destroy(Project $project)
    {
        Gate::authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Projeto removido com sucesso.');
    }

    /**
     * PUT /projects/{project}/members/{user} — Update a member's role.
     */
    public function updateMemberRole(Request $request, Project $project, \App\Models\User $user)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'role' => 'required|in:evaluator,observer',
        ]);

        $member = ProjectMember::where('project_id', $project->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Prevent changing the original owner's role
        if ($member->role === 'owner') {
            return redirect()->back()->withErrors(['role' => 'Não é possível alterar o papel do dono do projeto.']);
        }

        $member->update(['role' => $validated['role']]);

        return redirect()->back()->with('success', 'Papel do membro atualizado com sucesso.');
    }
}
