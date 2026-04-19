<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Liste des tâches pour un projet (Juriste)
     */
    public function index(Project $project)
    {
        $user = auth()->user();

        // Vérifier que c'est le juriste assigné
        if ($user->role !== 'juriste' || $project->juriste_id !== $user->id) {
            abort(403, 'Accès refusé');
        }

        $tasks = Task::where('project_id', $project->id)
            ->orderBy('sort_order')
            ->get();

        return view('juriste.tasks', [
            'project' => $project,
            'tasks' => $tasks,
        ]);
    }

    /**
     * Créer une nouvelle tâche
     */
    public function store(Request $request, Project $project)
    {
        $user = auth()->user();

        // Vérifier l'accès
        if ($user->role !== 'juriste' || $project->juriste_id !== $user->id) {
            abort(403, 'Accès refusé');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'percentage' => 'required|integer|min:1|max:100',
        ]);

        // Vérifier que le total ne dépasse pas 100%
        $currentTotal = Task::where('project_id', $project->id)
            ->sum('percentage');

        if ($currentTotal + $request->percentage > 100) {
            return back()->with('error', 'La somme des pourcentages ne peut pas dépasser 100%');
        }

        $maxOrder = Task::where('project_id', $project->id)->max('sort_order') ?? 0;

        Task::create([
            'project_id' => $project->id,
            'created_by' => $user->id,
            'title' => $request->title,
            'percentage' => $request->percentage,
            'sort_order' => $maxOrder + 1,
            'is_completed' => false,
        ]);

        return redirect()->back()->with('success', 'Tâche ajoutée');
    }

    /**
     * Basculer l'état d'une tâche
     */
    public function toggle(Task $task)
    {
        $user = auth()->user();
        $project = $task->project;

        // Vérifier l'accès
        if ($user->role !== 'juriste' || $project->juriste_id !== $user->id) {
            abort(403, 'Accès refusé');
        }

        $task->update([
            'is_completed' => !$task->is_completed,
            'completed_at' => !$task->is_completed ? now() : null,
        ]);

        return redirect()->back();
    }

    /**
     * Supprimer une tâche
     */
    public function destroy(Task $task)
    {
        $user = auth()->user();
        $project = $task->project;

        if ($user->role !== 'juriste' || $project->juriste_id !== $user->id) {
            abort(403, 'Accès refusé');
        }

        $task->delete();

        return redirect()->back()->with('success', 'Tâche supprimée');
    }

    /**
     * Émettre l'ODS - Déblocage du Chef de Projet
     */
    public function emitOds(Project $project)
    {
        $user = auth()->user();

        // Vérifier l'accès
        if ($user->role !== 'juriste' || $project->juriste_id !== $user->id) {
            abort(403, 'Accès refusé');
        }

        // Vérifier que la somme = 100%
        $totalPercentage = Task::where('project_id', $project->id)
            ->sum('percentage');

        if ($totalPercentage !== 100) {
            return back()->with('error', 'La somme des pourcentages doit être exactement 100% pour émettre l\'ODS');
        }

        // Vérifier que toutes les tâches sont complétées
        $completedCount = Task::where('project_id', $project->id)
            ->where('is_completed', true)
            ->count();

        $totalCount = Task::where('project_id', $project->id)->count();

        if ($completedCount !== $totalCount) {
            return back()->with('error', 'Toutes les tâches doivent être complétées pour émettre l\'ODS');
        }

        // Émettre l'ODS
        $project->update([
            'status' => 'En Cours',
            'chef_access_unlocked' => true,
        ]);

        // Notifier le Chef de Projet
        if ($project->chef_projet_id) {
            Notification::create([
                'user_id' => $project->chef_projet_id,
                'project_id' => $project->id,
                'type' => 'ods_received',
                'message' => "ODS réceptionnée pour le projet: {$project->title}. Vous pouvez maintenant saisir les dépenses.",
                'priority' => 'high',
            ]);
        }

        return redirect()->back()->with('success', 'ODS émise ! Le Chef de Projet peut maintenant travailler.');
    }
}