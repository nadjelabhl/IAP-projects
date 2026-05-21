<?php

namespace App\Livewire\Director;

use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DashboardDirector extends Component
{
    use WithPagination;

    public $school;

    // Modal affectation
    public bool  $showAssignModal  = false;
    public ?int  $projectIdAssign  = null;
    public ?int  $selectedJuriste  = null;
    public ?int  $selectedChef     = null;

    // Modal détail projet
    public bool  $showDetailModal  = false;
    public ?int  $detailProjectId  = null;

    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function mount(): void
    {
        $this->school = auth()->user()->school;
        abort_if(!$this->school, 403, 'Aucune école associée à ce compte.');
    }

    // =========================================================================
    // AFFECTATION DU PERSONNEL
    // =========================================================================
    public function openAssignModal(int $projectId): void
    {
        $this->projectIdAssign = $projectId;
        $this->selectedJuriste = null;
        $this->selectedChef    = null;
        $this->showAssignModal = true;
    }

    public function closeAssignModal(): void
    {
        $this->showAssignModal  = false;
        $this->projectIdAssign  = null;
        $this->selectedJuriste  = null;
        $this->selectedChef     = null;
    }

    public function assignPersonnel(): void
    {
        $this->validate([
            'selectedJuriste' => 'required|different:selectedChef',
            'selectedChef'    => 'required',
        ], [
            'selectedJuriste.required' => 'Veuillez sélectionner un juriste.',
            'selectedChef.required'    => 'Veuillez sélectionner un chef de projet.',
            'selectedJuriste.different'=> 'Le juriste et le chef de projet doivent être des personnes différentes.',
        ]);

        $project = Project::where('id', $this->projectIdAssign)
            ->where('school_id', $this->school->id)
            ->firstOrFail();

        $project->update([
            'juriste_id'     => $this->selectedJuriste,
            'chef_projet_id' => $this->selectedChef,
            'status'         => 'En Etude',
            'school_director_viewed_at' => now(),
        ]);

        $this->notificationService->notifyAssignment($project->fresh());

        $this->closeAssignModal();
        session()->flash('success', 'Personnel affecté avec succès. Le statut est maintenant « En Étude ».');
    }

    // =========================================================================
    // DÉTAIL PROJET (modale)
    // =========================================================================
    public function openDetailModal(int $projectId): void
    {
        $this->detailProjectId = $projectId;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->detailProjectId = null;
    }

    // =========================================================================
    // NOTIFICATIONS
    // =========================================================================
    public function markNotificationRead(int $id): void
    {
        Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    // =========================================================================
    // RENDER
    // =========================================================================
    public function render()
    {
        $projects = Project::where('school_id', $this->school->id)
            ->with('nature', 'juriste', 'chefProjet', 'legalSteps', 'expenses')
            ->orderByRaw("FIELD(status,'Nouveau','En Etude','En Cours','Termine')")
            ->paginate(10);

        // Juristes éligibles : < 2 projets actifs dans cette école
        $juristes = User::where('role', 'juriste')
            ->where('school_id', $this->school->id)
            ->where('is_active', true)
            ->get()
            ->filter(function ($u) {
                $active = Project::where('juriste_id', $u->id)
                    ->whereIn('status', ['En Etude', 'En Cours'])
                    ->count();
                return $active < 2;
            });

        // Chefs éligibles : 0 projet actif
        $chefs = User::where('role', 'chef_projet')
            ->where('school_id', $this->school->id)
            ->where('is_active', true)
            ->get()
            ->filter(function ($u) {
                $active = Project::where('chef_projet_id', $u->id)
                    ->whereIn('status', ['En Etude', 'En Cours'])
                    ->count();
                return $active === 0;
            });

        // Projet en détail
        $detailProject = $this->detailProjectId
            ? Project::with('legalSteps', 'expenses', 'odsRecords', 'juriste', 'chefProjet', 'nature', 'school')
                ->find($this->detailProjectId)
            : null;

        $stats = [
            'total'    => Project::where('school_id', $this->school->id)->count(),
            'nouveau'  => Project::where('school_id', $this->school->id)->where('status', 'Nouveau')->count(),
            'en_etude' => Project::where('school_id', $this->school->id)->where('status', 'En Etude')->count(),
            'en_cours' => Project::where('school_id', $this->school->id)->where('status', 'En Cours')->count(),
            'termine'  => Project::where('school_id', $this->school->id)->where('status', 'Termine')->count(),
        ];

        return view('livewire.director.dashboard-director', [
            'projects'        => $projects,
            'juristes'        => $juristes,
            'chefs'           => $chefs,
            'stats'           => $stats,
            'detailProject'   => $detailProject,
            'notifications'   => Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')->limit(15)->get(),
            'unreadCount'     => Notification::where('user_id', auth()->id())
                ->where('is_read', false)->count(),
        ]);
    }
}
