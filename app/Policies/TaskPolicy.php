<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine if the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // Admin, DG, Assistant DG : peuvent voir toutes les tâches
        if (in_array($user->role, ['admin', 'dg', 'assistant_dg'])) {
            return true;
        }

        // Directeur d'École : peut voir les tâches des projets de son école
        if ($user->role === 'directeur_ecole') {
            return $task->project->school_id === $user->school_id;
        }

        // Juriste : peut voir les tâches des projets où il est assigné
        if ($user->role === 'juriste') {
            return $task->project->juriste_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create a task.
     */
    public function create(User $user): bool
    {
        // Seul le Juriste peut créer des tâches
        return $user->role === 'juriste';
    }

    /**
     * Determine if the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // Admin peut mettre à jour toutes les tâches
        if ($user->role === 'admin') {
            return true;
        }

        // Juriste : peut mettre à jour les tâches des projets où il est assigné
        if ($user->role === 'juriste') {
            return $task->project->juriste_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // Admin peut supprimer toutes les tâches
        if ($user->role === 'admin') {
            return true;
        }

        // Juriste : peut supprimer les tâches des projets où il est assigné
        if ($user->role === 'juriste') {
            return $task->project->juriste_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can update the percentage of the task.
     */
    public function updatePercentage(User $user, Task $task): bool
    {
        // Seul le Juriste assigné peut mettre à jour le pourcentage
        return $user->role === 'juriste' && $task->project->juriste_id === $user->id;
    }
}
