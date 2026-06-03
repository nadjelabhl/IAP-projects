<?php

namespace App\Livewire\Director;

use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardDirector extends Component
{
    public $school;

    public bool $showAssignModal = false;
    public ?int $projectIdAssign = null;
    public ?int $selectedJuriste = null;
    public ?int $selectedChef    = null;

    public bool $showDetailModal = false;
    public ?int $detailProjectId = null;

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
    // AFFECTATION
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
        $this->showAssignModal = false;
        $this->projectIdAssign = null;
        $this->selectedJuriste = null;
        $this->selectedChef    = null;
    }

    public function assignPersonnel(): void
    {
        $this->validate([
            'selectedJuriste' => 'required|different:selectedChef',
            'selectedChef'    => 'required',
        ], [
            'selectedJuriste.required'  => 'Veuillez sélectionner un juriste.',
            'selectedJuriste.different' => 'Le juriste et le chef de projet doivent être différents.',
            'selectedChef.required'     => 'Veuillez sélectionner un chef de projet.',
        ]);

        $project = Project::where('id_project', $this->projectIdAssign)
            ->where('school_id', $this->school->id)
            ->firstOrFail();

        $project->update([
            'juriste_id'    => $this->selectedJuriste,
            'chef_projet_id'=> $this->selectedChef,
            'status'        => 'En Etude',
        ]);

        $this->notificationService->notifyAssignment($project->fresh());
        $this->closeAssignModal();
        session()->flash('success', 'Personnel affecté. Statut passé en « En Étude ».');
    }

    // =========================================================================
    // DETAIL MODAL
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
        Notification::where('id_notification', $id)
            ->where('user_id', auth()->id())
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    // =========================================================================
    // RENDER
    // =========================================================================
    public function render()
    {
        $sid = $this->school->id;

        $stats = [
            'total'    => Project::where('school_id', $sid)->count(),
            'nouveau'  => Project::where('school_id', $sid)->where('status', 'Nouveau')->count(),
            'en_etude' => Project::where('school_id', $sid)->where('status', 'En Etude')->count(),
            'en_cours' => Project::where('school_id', $sid)->where('status', 'En Cours')->count(),
            'termine'  => Project::where('school_id', $sid)->where('status', 'Termine')->count(),
        ];

        // New projects needing assignment
        $newProjects = Project::where('school_id', $sid)
            ->where('status', 'Nouveau')
            ->whereNotNull('consulted_by')
            ->with('nature')
            ->latest()
            ->get();

        // ---- Gantt: 5-year calendar ----
        $activeProjects = Project::where('school_id', $sid)
            ->whereNotIn('status', ['Termine'])
            ->get();

        $monthOffset = fn(Carbon $d, Carbon $base) =>
            ($d->year - $base->year) * 12 + ($d->month - $base->month);

        if ($activeProjects->isEmpty()) {
            $ganttStart = Carbon::create(now()->year, 1, 1);
        } else {
            $starts     = $activeProjects->map(fn($p) =>
                ($p->started_at ?? $p->created_at)->copy()->startOfMonth()
            );
            $ganttStart = $starts->min()->startOfYear();
        }

        $numMonths = 60; // 5 years fixed

        // Month labels with year info — fixed 3-letter abbreviations
        $shortMonths = ['JAN','FÉV','MAR','AVR','MAI','JUN','JUL','AOÛ','SEP','OCT','NOV','DÉC'];
        $ganttMonths = [];
        $cursor = $ganttStart->copy();
        for ($i = 0; $i < $numMonths; $i++) {
            $ganttMonths[] = [
                'label'     => $shortMonths[$cursor->month - 1],
                'year'      => $cursor->year,
                'isCurrent' => $cursor->isSameMonth(now()),
            ];
            $cursor->addMonth();
        }

        $yearGroups      = collect($ganttMonths)->groupBy('year')->map(fn($months) => $months->count());
        $currentMonthIdx = max(0, $monthOffset(now()->startOfMonth(), $ganttStart));

        // Project bars with integer month offsets
        $ganttProjects = $activeProjects->map(function ($p) use ($ganttStart, $numMonths, $monthOffset) {
            $start      = ($p->started_at ?? $p->created_at)->copy()->startOfMonth();
            $leftMonths = max(0, $monthOffset($start, $ganttStart));
            $widthMonths = max(1, $p->duration_months);

            // Clip if starts after window
            if ($leftMonths >= $numMonths) return null;
            // Clip width if exceeds window
            if ($leftMonths + $widthMonths > $numMonths) {
                $widthMonths = $numMonths - $leftMonths;
            }

            return [
                'title'       => $p->title_project,
                'status'      => $p->status,
                'leftMonths'  => $leftMonths,
                'widthMonths' => $widthMonths,
            ];
        })->filter()->values();

        // Modal data
        $juristes = User::where('role', 'juriste')
            ->where('school_id', $sid)
            ->where('is_active', true)
            ->get()
            ->filter(fn($u) => Project::where('juriste_id', $u->id)
                ->whereIn('status', ['En Etude', 'En Cours'])->count() < 2);

        $chefs = User::where('role', 'chef_projet')
            ->where('school_id', $sid)
            ->where('is_active', true)
            ->get()
            ->filter(fn($u) => Project::where('chef_projet_id', $u->id)
                ->whereIn('status', ['En Etude', 'En Cours'])->count() === 0);

        $detailProject = $this->detailProjectId
            ? Project::with('juriste', 'chefProjet', 'nature', 'school', 'creator')
                ->find($this->detailProjectId)
            : null;

        return view('livewire.director.dashboard-director', [
            'stats'         => $stats,
            'newProjects'   => $newProjects,
            'ganttMonths'      => $ganttMonths,
            'yearGroups'       => $yearGroups,
            'ganttProjects'    => $ganttProjects,
            'numMonths'        => $numMonths,
            'currentMonthIdx'  => $currentMonthIdx,
            'juristes'      => $juristes,
            'chefs'         => $chefs,
            'detailProject' => $detailProject,
            'notifications' => Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')->limit(10)->get(),
            'unreadCount'   => Notification::where('user_id', auth()->id())
                ->where('is_read', false)->count(),
        ]);
    }
}
