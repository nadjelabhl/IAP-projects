<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Expense;
use App\Services\BudgetService;
use App\Services\ArchiveService;
use App\Services\NotificationService;
use App\Services\OdsService;

class ExpenseManager extends Component
{
    public Project $project;
    public $description;
    public $amount;
    public $expense_date;

    protected BudgetService $budgetService;
    protected ArchiveService $archiveService;
    protected NotificationService $notificationService;
    protected OdsService $odsService;

    public function boot(BudgetService $budgetService, ArchiveService $archiveService, NotificationService $notificationService, OdsService $odsService)
    {
        $this->budgetService = $budgetService;
        $this->archiveService = $archiveService;
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
            'entered_by' => auth()->id(),
            'description' => $this->description,
            'amount' => $this->amount,
            'expense_date' => $this->expense_date,
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
        // Archiver le projet avec ArchiveService
        $archive = $this->archiveService->archiveProject($this->project, 'Projet terminé par Chef de Projet', auth()->id());

        if ($archive) {
            session()->flash('message', 'Projet archivé avec succès.');
        } else {
            session()->flash('error', 'Erreur lors de l\'archivage du projet. Seuls les projets terminés peuvent être archivés.');
        }
    }

    public function render()
    {
        $budgetSummary = $this->budgetService->getBudgetSummary($this->project);

        return view('livewire.expense-manager', [
            'expenses' => $this->project->expenses()->orderBy('expense_date', 'desc')->get(),
            'budgetSummary' => $budgetSummary,
        ]);
    }
}