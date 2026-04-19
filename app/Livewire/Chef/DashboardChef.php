<?php

namespace App\Livewire\Chef;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Project;
use App\Models\Expense;
use App\Models\Notification;

#[Layout('layouts.app')]
class DashboardChef extends Component
{
    public $projects = [];
    public $selectedProject;
    public $expenseDescription;
    public $expenseAmount;
    public $expenseDate;

    protected $rules = [
        'expenseDescription' => 'required|string|min:3',
        'expenseAmount' => 'required|numeric|min:1',
        'expenseDate' => 'required|date',
    ];

    public function mount()
    {
        $this->expenseDate = now()->format('Y-m-d');
        $this->loadProjects();
    }

    public function loadProjects()
    {
        // Projetsassignés au Chef de Projet (Section 5)
        $this->projects = Project::where('chef_projet_id', auth()->id())
            ->whereIn('status', ['En Cours', 'En Etude'])
            ->get();
    }

    public function selectProject($projectId)
    {
        $this->selectedProject = Project::findOrFail($projectId);
    }

    public function addExpense()
    {
        $this->validate();

        if (!$this->selectedProject) {
            session()->flash('error', 'Veuillez sélectionner un projet.');
            return;
        }

        // Vérifier que l'accès est débloqué (ODS émise)
        if (!$this->selectedProject->chef_access_unlocked) {
            session()->flash('error', 'Accès bloqué. En attente de l\'ODS du Juriste.');
            return;
        }

        // Enregistrer la dépense (Section 6 - Suivi Budget)
        Expense::create([
            'project_id' => $this->selectedProject->id,
            'entered_by' => auth()->id(),
            'description' => $this->expenseDescription,
            'amount' => $this->expenseAmount,
            'expense_date' => $this->expenseDate,
        ]);

        // Vérifier l'alerte budgétaire à 80% (Section 6)
        $totalSpent = $this->selectedProject->expenses()->sum('amount');
        $budget = $this->selectedProject->budget;
        $percentSpent = ($totalSpent / $budget) * 100;

        if ($percentSpent >= 80 && !$this->selectedProject->budget_alert_sent) {
            // Envoyer alerte au Directeur de l'école
            $directeur = \App\Models\User::where('role', 'directeur_ecole')
                ->where('school_id', $this->selectedProject->school_id)
                ->first();

            if ($directeur) {
                Notification::create([
                    'user_id' => $directeur->id,
                    'project_id' => $this->selectedProject->id,
                    'type' => 'budget_alert',
                    'priority' => 'urgent',
                    'message' => "ALERTE: Le projet '" . $this->selectedProject->title . "' a atteint 80% du budget!",
                ]);
            }

            $this->selectedProject->update(['budget_alert_sent' => true]);
        }

        $this->reset(['expenseDescription', 'expenseAmount']);
        $this->expenseDate = now()->format('Y-m-d');
        $this->selectedProject->refresh();

        session()->flash('message', 'Dépense enregistrée.');
    }

    public function terminateProject()
    {
        if (!$this->selectedProject) return;

        // Terminer le projet (Section 4 - Statut TERMINÉ)
        $this->selectedProject->update([
            'status' => 'Termine',
            'closed_at' => now(),
        ]);

        // Notifier le Directeur
        $directeur = \App\Models\User::where('role', 'directeur_ecole')
            ->where('school_id', $this->selectedProject->school_id)
            ->first();

        if ($directeur) {
            Notification::create([
                'user_id' => $directeur->id,
                'project_id' => $this->selectedProject->id,
                'type' => 'project_terminated',
                'priority' => 'normal',
                'message' => "Projet terminé : " . $this->selectedProject->title,
            ]);
        }

        session()->flash('message', 'Projet terminé et archivé.');
        $this->loadProjects();
    }

    public function render()
    {
        $expenses = [];
        $totalSpent = 0;
        $remainingBudget = 0;

        if ($this->selectedProject) {
            $expenses = Expense::where('project_id', $this->selectedProject->id)
                ->orderBy('expense_date', 'desc')
                ->get();
            $totalSpent = $expenses->sum('amount');
            $remainingBudget = $this->selectedProject->budget - $totalSpent;
        }

        return view('livewire.chef.dashboard-chef', [
            'projects' => $this->projects,
            'selectedProject' => $this->selectedProject,
            'expenses' => $expenses,
            'totalSpent' => $totalSpent,
            'remainingBudget' => $remainingBudget,
            'notifications' => \App\Models\Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ]);
    }
}