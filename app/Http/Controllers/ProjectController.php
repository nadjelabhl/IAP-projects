<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\School;
use App\Models\ProjectNature;
use App\Models\Task;
use App\Models\Expense;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Liste des projets - selon le rôle
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Project::with(['school', 'nature']);

        // Filtrage par rôle
        if ($user->role === 'directeur_ecole') {
            $query->where('school_id', $user->school_id);
        } elseif (in_array($user->role, ['juriste', 'chef_projet'])) {
            $query->where(function($q) use ($user) {
                $q->where('juriste_id', $user->id)
                  ->orWhere('chef_projet_id', $user->id);
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('projects.index', [
            'projects' => $projects,
            'user' => $user
        ]);
    }

    /**
     *.show - Détail d'un projet
     */
    public function show(Request $request, Project $project)
    {
        $user = auth()->user();

        // Vérifier l'accès
        if (!$this->canAccessProject($user, $project)) {
            abort(403, 'Accès refusé à ce projet');
        }

        $project->load(['school', 'nature', 'juriste', 'chefProjet', 'tasks', 'expenses']);

        return view('projects.show', [
            'project' => $project,
            'user' => $user
        ]);
    }

    /**
     *.canAccessProject - Vérifier si l'utilisateur peut accéder au projet
     */
    private function canAccessProject($user, $project): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'dg') return true;
        if ($user->role === 'assistant_dg') return true;

        if ($user->role === 'directeur_ecole') {
            return $project->school_id === $user->school_id;
        }

        if ($user->role === 'juriste') {
            return $project->juriste_id === $user->id;
        }

        if ($user->role === 'chef_projet') {
            return $project->chef_projet_id === $user->id;
        }

        return false;
    }

    /**
     * Affecter персонала (Juriste + Chef) - Directeur d'École
     */
    public function assign(Request $request, Project $project)
    {
        $user = auth()->user();

        // Seul le directeur d'école peut affecter
        if ($user->role !== 'directeur_ecole' || $project->school_id !== $user->school_id) {
            abort(403, 'Accès refusé');
        }

        $request->validate([
            'juriste_id' => 'required|exists:users,id',
            'chef_projet_id' => 'required|exists:users,id',
        ]);

        // Vérifier que les utilisateurs sont de la même école
        $juriste = User::find($request->juriste_id);
        $chef = User::find($request->chef_projet_id);

        if ($juriste->school_id !== $user->school_id || $chef->school_id !== $user->school_id) {
            return back()->with('error', 'Les utilisateurs doivent être de la même école');
        }

        // Mettre à jour le projet
        $project->update([
            'juriste_id' => $request->juriste_id,
            'chef_projet_id' => $request->chef_projet_id,
            'status' => 'En Etude', // Passer en étude
        ]);

        // Notifications
        Notification::create([
            'user_id' => $request->juriste_id,
            'project_id' => $project->id,
            'type' => 'assignment',
            'message' => "Vous avez été assigné au projet: {$project->title}",
        ]);

        Notification::create([
            'user_id' => $request->chef_projet_id,
            'project_id' => $project->id,
            'type' => 'assignment',
            'message' => "Vous avez été assigné au projet: {$project->title}",
        ]);

        return redirect()->route('directeur_ecole.projects.show', $project)
            ->with('success', 'Personnel assigné avec succès');
    }

    /**
     * Archiver un projet - Directeur d'École
     */
    public function archive(Request $request, Project $project)
    {
        $user = auth()->user();

        // Seul le directeur d'école peut archiver
        if ($user->role !== 'directeur_ecole' || $project->school_id !== $user->school_id) {
            abort(403, 'Accès refusé');
        }

        if ($project->status !== 'En Cours') {
            return back()->with('error', 'Seuls les projets en cours peuvent être archivés');
        }

        // Archiver
        $project->update([
            'status' => 'Termine',
            'closed_at' => now(),
        ]);

        return redirect()->route('directeur_ecole.dashboard')
            ->with('success', 'Projet archivé avec succès');
    }
}