<?php

namespace App\Livewire\AssistantDg;

use App\Models\Project;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.assistant-dg.dashboard', [
            'totalProjects' => Project::where('created_by', auth()->id())->count(),
            'recentProjects' => Project::where('created_by', auth()->id())->latest()->take(5)->get(),
        ]);
    }
}
