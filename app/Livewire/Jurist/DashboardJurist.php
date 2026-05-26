<?php

namespace App\Livewire\Jurist;

use App\Models\Notification;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardJurist extends Component
{
    public bool $ficheModalOpen  = false;
    public ?int $ficheProjectId  = null;

    public function openFiche(int $projectId): void
    {
        $this->ficheProjectId = $projectId;
        $this->ficheModalOpen = true;
    }

    public function closeFiche(): void
    {
        $this->ficheModalOpen = false;
        $this->ficheProjectId = null;
    }

    public function render()
    {
        $uid = auth()->id();

        $projects = Project::where('juriste_id', $uid)
            ->with(['nature', 'school', 'creator', 'legalSteps', 'expenses'])
            ->latest()
            ->get()
            ->map(function ($p) {
                $legalPct  = (float) $p->legalSteps->where('is_completed', true)->sum('percentage');
                $spent     = (float) $p->expenses->sum('amount');
                $budgetPct = $p->budget > 0 ? min(($spent / $p->budget) * 100, 100) : 0;

                $p->legalPct  = $legalPct;
                $p->budgetPct = $budgetPct;
                return $p;
            });

        $stats = [
            'total'     => $projects->count(),
            'en_cours'  => $projects->whereIn('status', ['En Cours'])->count(),
            'conformes' => $projects->filter(fn($p) => $p->legalPct >= 100)->count(),
            'alertes'   => $projects->filter(fn($p) => $p->budgetPct >= 80)->count(),
        ];

        $notifications = Notification::where('user_id', $uid)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('user_id', $uid)
            ->where('is_read', false)
            ->count();

        $ficheProject = $this->ficheProjectId
            ? $projects->firstWhere('id', $this->ficheProjectId)
            : null;

        return view('livewire.jurist.dashboard-jurist', [
            'projects'     => $projects,
            'stats'        => $stats,
            'notifications'=> $notifications,
            'unreadCount'  => $unreadCount,
            'ficheProject' => $ficheProject,
        ]);
    }
}
