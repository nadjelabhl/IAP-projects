<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    /**
     * Determine if the user can view the expense.
     */
    public function view(User $user, Expense $expense): bool
    {
        // Admin, DG, Assistant DG : peuvent voir toutes les dépenses
        if (in_array($user->role, ['admin', 'dg', 'assistant_dg'])) {
            return true;
        }

        // Directeur d'École : peut voir les dépenses des projets de son école
        if ($user->role === 'directeur_ecole') {
            return $expense->project->school_id === $user->school_id;
        }

        // Chef de Projet : peut voir les dépenses des projets où il est assigné
        if ($user->role === 'chef_projet') {
            return $expense->project->chef_projet_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create an expense.
     */
    public function create(User $user): bool
    {
        // Seul le Chef de Projet peut créer des dépenses
        return $user->role === 'chef_projet';
    }

    /**
     * Determine if the user can update an expense.
     */
    public function update(User $user, Expense $expense): bool
    {
        // Admin peut mettre à jour toutes les dépenses
        if ($user->role === 'admin') {
            return true;
        }

        // Chef de Projet : peut mettre à jour les dépenses des projets où il est assigné
        if ($user->role === 'chef_projet') {
            return $expense->project->chef_projet_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete an expense.
     */
    public function delete(User $user, Expense $expense): bool
    {
        // Admin peut supprimer toutes les dépenses
        if ($user->role === 'admin') {
            return true;
        }

        // Chef de Projet : peut supprimer les dépenses des projets où il est assigné
        if ($user->role === 'chef_projet') {
            return $expense->project->chef_projet_id === $user->id;
        }

        return false;
    }
}
