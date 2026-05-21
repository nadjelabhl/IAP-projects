<?php

namespace App\Livewire\Dg;

use App\Models\Notification;
use App\Models\Project;
use App\Models\School;
use App\Models\User;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DashboardDg extends Component
{
    use WithPagination;

    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Quand le DG lit une notification liée à un projet, on enregistre
     * dg_viewed_at et on transmet au Directeur d'École. Le statut reste 'Nouveau'.
     */
    public function markNotificationRead(int $id): void
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if (!$notification->is_read && $notification->project_id) {
            $project = $notification->project;

            if ($project && $project->status === 'Nouveau' && is_null($project->dg_consulted_at)) {
                $project->update(['dg_consulted_at' => now()]);
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
            'budget_total'   => Project::sum('budget'),
        ];

        // Données pour le Gantt (projets actifs avec started_at)
        $ganttProjects = Project::whereIn('status', ['En Cours', 'En Etude'])
            ->with('school')
            ->get()
            ->map(function ($p) {
                $start = $p->started_at ?? $p->created_at;
                $end   = (clone $start)->addMonths($p->duration_months);
                return [
                    'id'      => $p->id,
                    'title'   => $p->title,
                    'school'  => $p->school->name,
                    'status'  => $p->status,
                    'start'   => $start->format('Y-m-d'),
                    'end'     => $end->format('Y-m-d'),
                    'overdue' => $end->isPast(),
                ];
            });

        // Projets par école avec SplitCircle
        $schools = School::all()->map(function ($school) {
            $projects = Project::where('school_id', $school->id)
                ->whereNotIn('status', ['Termine'])
                ->with('legalSteps', 'expenses')
                ->get()
                ->map(function ($p) {
                    $legalPct  = (float) $p->legalSteps()
                        ->where('is_completed', true)->sum('percentage');
                    $totalSpent = (float) $p->expenses()->sum('amount');
                    $budgetPct  = $p->budget > 0
                        ? min(($totalSpent / $p->budget) * 100, 100)
                        : 0;

                    return array_merge($p->toArray(), [
                        'legal_progress'    => $legalPct,
                        'budget_consumption'=> $budgetPct,
                        'total_spent'       => $totalSpent,
                    ]);
                });

            return ['school' => $school, 'projects' => $projects];
        });

        return view('livewire.dg.dashboard-dg', [
            'stats'         => $stats,
            'ganttProjects' => $ganttProjects,
            'schoolsData'   => $schools,
            'notifications' => Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')->limit(15)->get(),
            'unreadCount'   => Notification::where('user_id', auth()->id())
                ->where('is_read', false)->count(),
        ]);
    }
}
