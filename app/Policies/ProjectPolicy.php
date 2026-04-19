<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine if the user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        // Admin, DG, Assistant DG : peuvent voir tous les projets
        if (in_array($user->role, ['admin', 'dg', 'assistant_dg'])) {
            return true;
        }

        // Directeur d'École : peut voir les projets de son école
        if ($user->role === 'directeur_ecole') {
            return $project->school_id === $user->school_id;
        }

        // Juriste : peut voir les projets où il est assigné
        if ($user->role === 'juriste') {
            return $project->juriste_id === $user->id;
        }

        // Chef de Projet : peut voir les projets où il est assigné
        if ($user->role === 'chef_projet') {
            return $project->chef_projet_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create a project.
     */
    public function create(User $user): bool
    {
        // Seuls Admin et Assistant DG peuvent créer des projets
        return in_array($user->role, ['admin', 'assistant_dg']);
    }

    /**
     * Determine if the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        // Admin peut mettre à jour tous les projets
        if ($user->role === 'admin') {
            return true;
        }

        // Assistant DG peut mettre à jour les projets en statut NOUVEAU
        if ($user->role === 'assistant_dg' && $project->status === 'Nouveau') {
            return true;
        }

        // Directeur d'École peut mettre à jour les projets de son école (sauf statut)
        if ($user->role === 'directeur_ecole' && $project->school_id === $user->school_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        // Seul Admin peut supprimer les projets
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can assign juriste or chef to the project.
     */
    public function assign(User $user, Project $project): bool
    {
        // Seul le Directeur d'École de l'école peut assigner
        return $user->role === 'directeur_ecole' && $project->school_id === $user->school_id;
    }

    /**
     * Determine if the user can emit ODS (Order of Start).
     */
    public function emitODS(User $user, Project $project): bool
    {
        // Seul le Juriste assigné peut émettre l'ODS
        return $user->role === 'juriste' && $project->juriste_id === $user->id;
    }

    /**
     * Determine if the user can enter expenses.
     */
    public function enterExpenses(User $user, Project $project): bool
    {
        // Seul le Chef de Projet assigné peut enregistrer les dépenses
        // ET le projet doit être en statut EN COURS avec accès débloqué
        return $user->role === 'chef_projet' 
            && $project->chef_projet_id === $user->id 
            && $project->chef_access_unlocked;
    }

    /**
     * Determine if the user can archive the project.
     */
    public function archive(User $user, Project $project): bool
    {
        // Seul le Directeur d'École peut archiver les projets de son école
        // ET le projet doit être en statut TERMINÉ
        return $user->role === 'directeur_ecole' 
            && $project->school_id === $user->school_id 
            && $project->status === 'Termine';
    }
}
