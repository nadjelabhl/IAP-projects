<?php

namespace App\Livewire\Jurist;

use App\Models\LegalStep;
use App\Models\OdsRecord;
use App\Models\Project;
use App\Models\ProjectNatureDefault;
use App\Services\OdsService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ReferentielJuridique extends Component
{
    use WithFileUploads;

    public Project $project;

    // Formulaire ajout
    public string $newTitle      = '';
    public float  $newPercentage = 0;

    // Modale édition
    public bool   $editModalOpen  = false;
    public ?int   $editingStepId  = null;
    public string $editTitle      = '';
    public float  $editPercentage = 0;
    public int    $editOrder      = 0;

    // Modale ODS Démarrage
    public bool   $odsDemarrageModalOpen = false;
    public string $odesDemarrageNotes    = '';

    // Modale ODS Arrêt / Reprise
    public bool   $odsArretModalOpen   = false;
    public bool   $odsRepriseModalOpen = false;
    public string $odsArretNotes       = '';
    public string $odsRepriseNotes     = '';

    // Upload PDF par phase (stocke l'ID de la phase à uploader)
    public ?int $uploadingStepId = null;
    public $stepPdf;

    protected OdsService          $odsService;
    protected NotificationService $notificationService;

    public function boot(OdsService $odsService, NotificationService $notificationService): void
    {
        $this->odsService          = $odsService;
        $this->notificationService = $notificationService;
    }

    public function mount(Project $project): void
    {
        $this->project = $project;

        // Vérifier que ce juriste est bien affecté au projet
        abort_if(auth()->id() !== $project->juriste_id, 403);

        // Pré-remplir depuis le référentiel par défaut si aucune phase n'existe
        if ($project->legalSteps()->count() === 0) {
            $this->seedFromDefaults();
        }
    }

    private function seedFromDefaults(): void
    {
        foreach (ProjectNatureDefault::orderBy('order_number')->get() as $default) {
            LegalStep::create([
                'project_id'   => $this->project->id,
                'created_by'   => auth()->id(),
                'title'        => $default->name,
                'percentage'   => $default->percentage,
                'sort_order'   => $default->order_number,
                'is_deletable' => $default->order_number < ProjectNatureDefault::max('order_number'),
            ]);
        }
    }

    // =========================================================================
    // AJOUT D'UNE PHASE
    // =========================================================================
    public function addStep(): void
    {
        $this->validate([
            'newTitle'      => 'required|string|min:3|max:255',
            'newPercentage' => 'required|numeric|min:0.01|max:100',
        ]);

        $currentSum = $this->getCurrentSum();
        if ($currentSum + $this->newPercentage > 100) {
            $this->addError('newPercentage', "Impossible : le total dépasserait 100 % (actuellement {$currentSum} %).");
            return;
        }

        $maxOrder = $this->project->legalSteps()->max('sort_order') ?? 0;

        LegalStep::create([
            'project_id'   => $this->project->id,
            'created_by'   => auth()->id(),
            'title'        => $this->newTitle,
            'percentage'   => $this->newPercentage,
            'sort_order'   => $maxOrder + 1,
            'is_deletable' => true,
        ]);

        $this->reset(['newTitle', 'newPercentage']);
        session()->flash('success', 'Phase ajoutée.');
    }

    // =========================================================================
    // ÉDITION D'UNE PHASE (modale)
    // =========================================================================
    public function openEditModal(int $stepId): void
    {
        $step = LegalStep::findOrFail($stepId);
        abort_if($step->project_id !== $this->project->id, 403);

        $this->editingStepId  = $stepId;
        $this->editTitle      = $step->title;
        $this->editPercentage = (float) $step->percentage;
        $this->editOrder      = $step->sort_order;
        $this->editModalOpen  = true;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editTitle'      => 'required|string|min:3|max:255',
            'editPercentage' => 'required|numeric|min:0.01|max:100',
            'editOrder'      => 'required|integer|min:1',
        ]);

        $step        = LegalStep::findOrFail($this->editingStepId);
        $currentSum  = $this->getCurrentSum($step->id);

        if ($currentSum + $this->editPercentage > 100) {
            $this->addError('editPercentage', "Impossible : le total dépasserait 100 % (actuellement {$currentSum} % sans cette phase).");
            return;
        }

        $step->update([
            'title'      => $this->editTitle,
            'percentage' => $this->editPercentage,
            'sort_order' => $this->editOrder,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'Phase modifiée.');
    }

    public function closeEditModal(): void
    {
        $this->editModalOpen  = false;
        $this->editingStepId  = null;
        $this->reset(['editTitle', 'editPercentage', 'editOrder']);
    }

    // =========================================================================
    // SUPPRESSION D'UNE PHASE
    // =========================================================================
    public function deleteStep(int $stepId): void
    {
        $step = LegalStep::findOrFail($stepId);
        abort_if($step->project_id !== $this->project->id, 403);

        if (!$step->is_deletable) {
            session()->flash('error', 'Cette phase ne peut pas être supprimée.');
            return;
        }

        if ($step->is_completed) {
            session()->flash('error', 'Impossible de supprimer une phase déjà cochée.');
            return;
        }

        $step->delete();
        session()->flash('success', 'Phase supprimée.');
    }

    // =========================================================================
    // COCHAGE SÉQUENTIEL DES PHASES
    // =========================================================================
    public function toggleStep(int $stepId): void
    {
        $step = LegalStep::findOrFail($stepId);
        abort_if($step->project_id !== $this->project->id, 403);

        if ($step->is_completed) {
            // Décocher autorisé uniquement si aucune phase suivante n'est cochée
            $nextChecked = LegalStep::where('project_id', $this->project->id)
                ->where('sort_order', '>', $step->sort_order)
                ->where('is_completed', true)
                ->exists();

            if ($nextChecked) {
                session()->flash('error', 'Impossible de décocher : des phases suivantes sont déjà cochées.');
                return;
            }

            $step->uncheck();
            return;
        }

        // Cocher : vérifier que la phase précédente est cochée
        if (!$step->isPreviousCompleted()) {
            session()->flash('error', 'Vous devez d\'abord cocher la phase précédente.');
            return;
        }

        $step->check(auth()->id());

        // Si c'est la dernière phase (ODS Démarrage), ouvrir la modale
        $isLast = !LegalStep::where('project_id', $this->project->id)
            ->where('sort_order', '>', $step->sort_order)
            ->exists();

        if ($isLast) {
            $this->odsDemarrageModalOpen = true;
        }
    }

    // =========================================================================
    // ODS DE DÉMARRAGE
    // =========================================================================
    public function emitOdsDemarrage(): void
    {
        $ods = $this->odsService->emitDemarrage(
            $this->project->fresh(),
            auth()->user(),
            $this->odesDemarrageNotes ?: null
        );

        if (!$ods) {
            session()->flash('error', 'Impossible d\'émettre l\'ODS : toutes les phases doivent être cochées (100 %).');
            $this->odsDemarrageModalOpen = false;
            return;
        }

        $this->odsDemarrageModalOpen = false;
        $this->odesDemarrageNotes    = '';
        $this->project               = $this->project->fresh();
        session()->flash('success', 'ODS de Démarrage émis. Le Chef de Projet a été notifié.');
    }

    // =========================================================================
    // ODS D'ARRÊT
    // =========================================================================
    public function emitOdsArret(): void
    {
        $this->validate(['odsArretNotes' => 'required|string|min:10']);

        $ods = $this->odsService->emitArret(
            $this->project,
            auth()->user(),
            $this->odsArretNotes
        );

        if (!$ods) {
            session()->flash('error', 'Impossible d\'émettre un ODS d\'Arrêt hors phase « En Cours ».');
        } else {
            session()->flash('success', 'ODS d\'Arrêt émis. Le Chef de Projet a été notifié en urgence.');
        }

        $this->odsArretModalOpen = false;
        $this->odsArretNotes     = '';
    }

    // =========================================================================
    // ODS DE REPRISE
    // =========================================================================
    public function emitOdsReprise(): void
    {
        $this->validate(['odsRepriseNotes' => 'required|string|min:10']);

        $ods = $this->odsService->emitReprise(
            $this->project,
            auth()->user(),
            $this->odsRepriseNotes
        );

        if (!$ods) {
            session()->flash('error', 'Impossible d\'émettre un ODS de Reprise hors phase « En Cours ».');
        } else {
            session()->flash('success', 'ODS de Reprise émis. Le Chef de Projet a été notifié en urgence.');
        }

        $this->odsRepriseModalOpen = false;
        $this->odsRepriseNotes     = '';
    }

    // =========================================================================
    // UPLOAD PDF PAR PHASE
    // =========================================================================
    public function startUpload(int $stepId): void
    {
        $this->uploadingStepId = $stepId;
    }

    public function uploadPdf(): void
    {
        $this->validate(['stepPdf' => 'required|file|mimes:pdf|max:10240']);

        $step = LegalStep::findOrFail($this->uploadingStepId);
        abort_if($step->project_id !== $this->project->id, 403);

        $path = $this->stepPdf->store('legal-steps', 'public');
        $step->update(['pdf_path' => $path]);

        $this->uploadingStepId = null;
        $this->stepPdf         = null;
        session()->flash('success', 'Fichier PDF enregistré pour la phase.');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================
    private function getCurrentSum(?int $excludeStepId = null): float
    {
        $query = LegalStep::where('project_id', $this->project->id);
        if ($excludeStepId) {
            $query->where('id', '!=', $excludeStepId);
        }
        return (float) $query->sum('percentage');
    }

    public function render()
    {
        $steps            = $this->project->legalSteps()->get();
        $totalPct         = $steps->sum('percentage');
        $completedPct     = $steps->where('is_completed', true)->sum('percentage');
        $canEmitDemarrage = $this->odsService->canEmitDemarrage($this->project);
        $odsHistory       = OdsRecord::where('project_id', $this->project->id)
                            ->with('issuedBy')->orderBy('issued_at', 'desc')->get();

        return view('livewire.jurist.referentiel-juridique', compact(
            'steps', 'totalPct', 'completedPct', 'canEmitDemarrage', 'odsHistory'
        ));
    }
}
