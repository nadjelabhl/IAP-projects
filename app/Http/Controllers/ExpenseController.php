<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Liste des dépenses pour un projet
     */
    public function index(Project $project)
    {
        $user = auth()->user();

        // Vérifier l'accès - Chef de Projet doit avoir l'accès débloqué
        if (!$this->canAccessProject($user, $project)) {
            abort(403, 'Accès refusé');
        }

        $expenses = Expense::where('project_id', $project->id)
            ->orderBy('attachement_date', 'desc')
            ->get();

        $totalSpent = $expenses->sum('amount');
        $remainingBudget = $project->budget - $totalSpent;
        $percentageSpent = $project->budget > 0 ? ($totalSpent / $project->budget) * 100 : 0;

        return view('chef_projet.expenses', [
            'project' => $project,
            'expenses' => $expenses,
            'totalSpent' => $totalSpent,
            'remainingBudget' => $remainingBudget,
            'percentageSpent' => $percentageSpent,
            'user' => $user,
        ]);
    }

    /**
     * Enregistrer une dépense
     */
    public function store(Request $request, Project $project)
    {
        $user = auth()->user();

        // Vérifier l'accès
        if (!$this->canAccessProject($user, $project)) {
            abort(403, 'Accès refusé');
        }

        $request->validate([
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date|before_or_equal:today',
        ]);

        // Créer la dépense
        Expense::create([
            'project_id'       => $project->id,
            'description'      => $request->description,
            'amount'           => $request->amount,
            'attachement_date' => $request->expense_date,
        ]);

        // Calculer le total des dépenses
        $totalSpent = Expense::where('project_id', $project->id)->sum('amount');
        $percentageSpent = ($totalSpent / $project->budget) * 100;

        // Alerte si >= 80% du budget utilisé
        if ($percentageSpent >= 80 && !$project->budget_alert_sent) {
            // Notifier le Directeur d'ÉcoleUNIQUEMENT
            $director = User::where('role', 'directeur_ecole')
                ->where('school_id', $project->school_id)
                ->first();

            if ($director) {
                Notification::create([
                    'user_id'           => $director->id,
                    'project_id'        => $project->id,
                    'type_notification' => 'alerte_budget',
                    'priority'          => 'urgent',
                    'message'           => "ALERTE BUDGET: Le projet '{$project->title_project}' a atteint " . round($percentageSpent) . "% du budget alloué ({$totalSpent} DA sur {$project->budget} DA)",
                ]);
            }

            // Marquer l'alerte comme envoyée
            $project->update(['budget_alert_sent' => true]);
        }

        return redirect()->back()->with('success', 'Dépense enregistrée');
    }

    /**
     * Supprimer une dépense
     */
    public function destroy(Expense $expense)
    {
        $user = auth()->user();
        $project = $expense->project;

        // Seul le chef de projet qui a créé la dépense peut la supprimer
        if ($user->role !== 'chef_projet' || $project->chef_projet_id !== $user->id) {
            abort(403, 'Accès refusé');
        }

        $expense->delete();

        return redirect()->back()->with('success', 'Dépense supprimée');
    }

    /**
     * Vérifier l'accès au projet
     */
    private function canAccessProject($user, $project): bool
    {
        if ($user->role === 'admin') return true;
        if ($user->role === 'dg') return true;
        if ($user->role === 'directeur_ecole' && $project->school_id === $user->school_id) return true;

        if ($user->role === 'chef_projet') {
            return $project->chef_projet_id === $user->id && $project->chef_access_unlocked;
        }

        return false;
    }
}