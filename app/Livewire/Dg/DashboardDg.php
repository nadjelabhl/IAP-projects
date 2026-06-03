<?php

namespace App\Livewire\Dg;

use App\Models\Notification;
use App\Models\Project;
use App\Models\School;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardDg extends Component
{
    public ?int $ficheProjectId = null;

    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function openFiche(int $id): void
    {
        $this->ficheProjectId = $id;
    }

    public function closeFiche(): void
    {
        $this->ficheProjectId = null;
    }

    // Mark project as consulted by DG → unlocks it for the school director.
    public function consultProject(int $id): void
    {
        $project = Project::findOrFail($id);
        if ($project->status === 'Nouveau' && is_null($project->consulted_by)) {
            $project->update([
                'consulted_by' => auth()->id(),
            ]);
            $this->notificationService->notifyProjectTransmittedToDirector($project);
        }
    }

    public function markNotificationRead(int $id): void
    {
        $notification = Notification::where('id_notification', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$notification->is_read && $notification->project_id) {
            $project = $notification->project;
            if ($project && $project->status === 'Nouveau' && is_null($project->consulted_by)) {
                $project->update([
                    'consulted_by' => auth()->id(),
                ]);
                $this->notificationService->notifyProjectTransmittedToDirector($project);
            }
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);
    }

    public function render()
    {
        $stats = [
            'total_projects' => Project::count(),
            'nouveau'        => Project::where('status', 'Nouveau')->count(),
            'en_etude'       => Project::where('status', 'En Etude')->count(),
            'en_cours'       => Project::where('status', 'En Cours')->count(),
            'termine'        => Project::where('status', 'Termine')->count(),
        ];

        $allProjects = Project::whereNotIn('status', ['Termine'])
            ->with('school', 'nature', 'legalSteps', 'expenses')
            ->get()
            ->map(function ($p) {
                $legalPct   = (float) $p->legalSteps->where('is_completed', true)->sum('percentage');
                $totalSpent = (float) $p->expenses->sum('amount');
                $budgetPct  = $p->budget > 0 ? min(($totalSpent / $p->budget) * 100, 100) : 0;

                return array_merge($p->toArray(), [
                    'legal_progress'     => $legalPct,
                    'budget_consumption' => $budgetPct,
                    'total_spent'        => $totalSpent,
                ]);
            })
            ->groupBy('school_id');

        $schools = School::all()->map(fn ($school) => [
            'school'   => $school,
            'projects' => $allProjects->get($school->id_school, collect()),
        ]);

        $ficheProject = $this->ficheProjectId
            ? Project::with('nature', 'school', 'creator')->find($this->ficheProjectId)
            : null;

        return view('livewire.dg.dashboard-dg', [
            'stats'         => $stats,
            'schoolsData'   => $schools,
            'ficheProject'  => $ficheProject,
            'notifications' => Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')->limit(15)->get(),
            'unreadCount'   => Notification::where('user_id', auth()->id())
                ->where('is_read', false)->count(),
        ]);
    }
}
