<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Expense;
use App\Services\BudgetService;
use App\Services\NotificationService;
use App\Services\OdsService;

class ExpenseManager extends Component
{
    public Project $project;
    public $description;
    public $amount;
    public $expense_date;

    protected BudgetService $budgetService;
    protected NotificationService $notificationService;
    protected OdsService $odsService;

    public function boot(BudgetService $budgetService, NotificationService $notificationService, OdsService $odsService)
    {
        $this->budgetService = $budgetService;
        $this->notificationService = $notificationService;
        $this->odsService = $odsService;
    }

    protected $rules = [
        'description' => 'required|string|min:3',
        'amount' => 'required|numeric|min:1',
        'expense_date' => 'required|date',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->expense_date = now()->format('Y-m-d');
    }

    public function addExpense()
    {
        $this->validate();

        // Vérifier que l'accès est débloqué (ODS émise)
        if (!$this->odsService->hasChefAccess($this->project, auth()->id())) {
            session()->flash('error', 'Accès bloqué. En attente de l\'ODS du Juriste.');
            return;
        }

        // Enregistrer la dépense avec BudgetService
        $expense = $this->budgetService->addExpense($this->project, [
            'description'      => $this->description,
            'amount'           => $this->amount,
            'attachement_date' => $this->expense_date,
        ]);

        if (!$expense) {
            session()->flash('error', 'Erreur lors de l\'enregistrement de la dépense.');
            return;
        }

        $this->reset(['description', 'amount', 'expense_date']);
        $this->expense_date = now()->format('Y-m-d');
        $this->project->refresh();

        session()->flash('message', 'Dépense enregistrée.');
    }

    public function terminateProject()
    {
        if ($this->project->status !== 'Termine') {
            session()->flash('error', 'Seuls les projets au statut Terminé peuvent être clôturés.');
            return;
        }

        if (!$this->project->closed_at) {
            $this->project->update(['closed_at' => now()]);
        }

        session()->flash('message', 'Projet clôturé avec succès.');
    }

    public function render()
    {
        $budgetSummary = $this->budgetService->getBudgetSummary($this->project);

        return view('livewire.expense-manager', [
            'expenses' => $this->project->expenses()->orderBy('attachement_date', 'desc')->get(),
            'budgetSummary' => $budgetSummary,
        ]);
    }
}