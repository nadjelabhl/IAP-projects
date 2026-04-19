<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Project;
use App\Models\School;
use App\Models\ProjectNature;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalUsers' => User::count(),
            'totalProjects' => Project::count(),
            'totalSchools' => School::count(),
            'totalNatures' => ProjectNature::count(),
            'recentProjects' => Project::latest()->take(5)->get(),
        ]);
    }
}
