<?php

namespace App\Livewire\AssistantDg;

use App\Models\Notification;
use App\Models\Project;
use App\Models\School;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public function markNotificationRead(int $id): void
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

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
                $budgetPct  = $p->budget > 0
                    ? min(($totalSpent / $p->budget) * 100, 100)
                    : 0;

                return array_merge($p->toArray(), [
                    'legal_progress'     => $legalPct,
                    'budget_consumption' => $budgetPct,
                    'total_spent'        => $totalSpent,
                ]);
            })
            ->groupBy('school_id');

        $schools = School::all()->map(fn ($school) => [
            'school'   => $school,
            'projects' => $allProjects->get($school->id, collect()),
        ]);

        return view('livewire.assistant-dg.dashboard', [
            'stats'         => $stats,
            'schoolsData'   => $schools,
            'notifications' => Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')->limit(15)->get(),
            'unreadCount'   => Notification::where('user_id', auth()->id())
                ->where('is_read', false)->count(),
        ]);
    }
}
