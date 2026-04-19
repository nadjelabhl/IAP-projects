<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Project;
use App\Models\School;

#[Layout('layouts.app')]
class DashboardStats extends Component
{
    public function render()
    {
        $user = auth()->user();

        // Stats globales
        $stats = [
            'total_projects' => Project::count(),
            'nouveau' => Project::where('status', 'Nouveau')->count(),
            'en_etude' => Project::where('status', 'En Etude')->count(),
            'en_cours' => Project::where('status', 'En Cours')->count(),
            'termine' => Project::where('status', 'Termine')->count(),
            'budget_total' => Project::sum('budget'),
        ];

        // Projets par école
        $projectsBySchool = Project::selectRaw('school_id, COUNT(*) as count, SUM(budget) as budget')
            ->groupBy('school_id')
            ->get();

        // Projets récents (tous statuts)
        $recentProjects = Project::orderBy('created_at', 'desc')->limit(10)->get();

        return view('livewire.dashboard-stats', [
            'stats' => $stats,
            'projectsBySchool' => $projectsBySchool,
            'recentProjects' => $recentProjects,
            'schools' => School::all(),
        ]);
    }
}