<?php

namespace App\Livewire\Chef;

use App\Models\Expense;
use App\Models\LegalStep;
use App\Models\Notification;
use App\Models\OdsRecord;
use App\Models\Project;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardChef extends Component
{
    // Projet sélectionné
    public ?int $selectedProjectId = null;

    // Formulaire dépense
    public string $expenseDescription = '';
    public        $expenseAmount       = '';
    public string $expenseDate         = '';

    // Confirmation clôture
    public bool $confirmClose = false;

    // Modal fiche ODS
    public bool $odsModalOpen = false;
    public ?int $odsModalId   = null;

    // Modal fiche de projet
    public bool $ficheModalOpen = false;

    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function mount(): void
    {
        $this->expenseDate = now()->format('Y-m-d');

        // Auto-sélectionner s'il n'y a qu'un seul projet
        $projects = $this->getMyProjects();
        if ($projects->count() === 1) {
            $this->selectedProjectId = $projects->first()->id;
        }
    }

    private function getMyProjects()
    {
        return Project::where('chef_projet_id', auth()->id())
            ->where('chef_access_unlocked', true)
            ->whereIn('status', ['En Cours'])
            ->with('school', 'nature', 'expenses', 'odsRecords', 'juriste')
            ->get();
    }

    public function selectProject(int $projectId): void
    {
        $this->selectedProjectId = $projectId;
        $this->confirmClose      = false;
    }

    // =========================================================================
    // AJOUT DE DÉPENSE
    // =========================================================================
    public function addExpense(): void
    {
        $project = $this->getSelectedProject();

        if (!$project) {
            session()->flash('error', 'Sélectionnez un projet.');
            return;
        }

        // Check if last ODS is Arrêt (no Reprise after)
        $lastOds = OdsRecord::where('project_id', $project->id)
            ->orderBy('issued_at', 'desc')->first();
        if ($lastOds && $lastOds->type === 'Arret') {
            session()->flash('error', 'Ajout de dépense bloqué : un ODS d\'Arrêt est en cours. Attendez l\'ODS de Reprise.');
            return;
        }

        $this->validate([
            'expenseDescription' => 'required|string|min:3|max:255',
            'expenseAmount'      => 'required|numeric|min:1',
            'expenseDate'        => 'required|date|before_or_equal:today',
        ], [
            'expenseDescription.required' => 'La description est obligatoire.',
            'expenseAmount.required'      => 'Le montant est obligatoire.',
            'expenseAmount.min'           => 'Le montant doit être supérieur à 0.',
            'expenseDate.before_or_equal' => 'La date ne peut pas être dans le futur.',
        ]);

        Expense::create([
            'project_id'  => $project->id,
            'entered_by'  => auth()->id(),
            'description' => $this->expenseDescription,
            'amount'      => $this->expenseAmount,
            'expense_date'=> $this->expenseDate,
        ]);

        // Vérifier le seuil 80 %
        $totalSpent = $project->expenses()->sum('amount') + $this->expenseAmount;
        $pct        = $project->budget > 0 ? ($totalSpent / $project->budget) * 100 : 0;

        if ($pct >= 80 && !$project->budget_alert_sent) {
            $this->notificationService->notifyBudgetAlert($project);
        }

        $this->reset(['expenseDescription', 'expenseAmount']);
        $this->expenseDate = now()->format('Y-m-d');
        session()->flash('success', 'Dépense enregistrée.');
    }

    // =========================================================================
    // CLÔTURE PROJET
    // =========================================================================
    public function closeProject(): void
    {
        $project = $this->getSelectedProject();
        if (!$project) return;

        $project->update([
            'status'    => 'Termine',
            'closed_at' => now(),
        ]);

        $this->notificationService->notifyProjectTerminated($project->fresh());

        $this->selectedProjectId = null;
        $this->confirmClose      = false;
        session()->flash('success', 'Projet clôturé et archivé avec succès.');
    }

    public function openOdsModal(int $odsId): void
    {
        $this->odsModalId   = $odsId;
        $this->odsModalOpen = true;
    }

    public function closeOdsModal(): void
    {
        $this->odsModalOpen = false;
        $this->odsModalId   = null;
    }

    public function openFicheModal(): void
    {
        $this->ficheModalOpen = true;
    }

    public function closeFicheModal(): void
    {
        $this->ficheModalOpen = false;
    }

    private function getSelectedProject(): ?Project
    {
        if (!$this->selectedProjectId) return null;

        return Project::where('id', $this->selectedProjectId)
            ->where('chef_projet_id', auth()->id())
            ->where('chef_access_unlocked', true)
            ->first();
    }

    public function render()
    {
        $myProjects     = $this->getMyProjects();
        $selectedProject= $this->selectedProjectId
            ? $myProjects->firstWhere('id', $this->selectedProjectId)
            : null;

        $expenses       = [];
        $totalSpent     = 0;
        $budgetPct      = 0;
        $odsHistory     = collect();

        $expensesLocked = false;

        if ($selectedProject) {
            $expenses   = Expense::where('project_id', $selectedProject->id)
                ->orderBy('expense_date', 'desc')->get();
            $totalSpent = (float) $expenses->sum('amount');
            $budgetPct  = $selectedProject->budget > 0
                ? min(($totalSpent / $selectedProject->budget) * 100, 100)
                : 0;
            $odsHistory = OdsRecord::where('project_id', $selectedProject->id)
                ->with('issuedBy')->orderBy('issued_at', 'desc')->get();

            // Lock expenses if last ODS is an Arrêt (no subsequent Reprise)
            $lastOds = $odsHistory->first(); // already ordered by issued_at desc
            $expensesLocked = $lastOds && $lastOds->type === 'Arret';
        }

        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->limit(8)
            ->get();

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        $odsModalRecord  = null;
        $odsModalPdfPath = null;

        if ($this->odsModalId) {
            $odsModalRecord = OdsRecord::with('issuedBy')->find($this->odsModalId);

            if ($odsModalRecord) {
                $odsModalPdfPath = $odsModalRecord->pdf_path;

                // For Démarrage ODS with no stored PDF, fall back to the last legal step's PDF
                if (!$odsModalPdfPath && $odsModalRecord->type === 'Demarrage') {
                    $lastStep = LegalStep::where('project_id', $odsModalRecord->project_id)
                        ->orderBy('sort_order', 'desc')
                        ->first();
                    $odsModalPdfPath = $lastStep?->pdf_path;
                }
            }
        }

        return view('livewire.chef.dashboard-chef', compact(
            'myProjects', 'selectedProject', 'expenses',
            'totalSpent', 'budgetPct', 'odsHistory', 'notifications', 'unreadCount',
            'odsModalRecord', 'odsModalPdfPath', 'expensesLocked'
        ));
    }
}
