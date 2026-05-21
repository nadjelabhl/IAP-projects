<?php

namespace App\Livewire\AssistantDg;

use App\Models\Notification;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    public function render()
    {
        $myProjects = Project::where('created_by', auth()->id())
            ->with('school', 'nature', 'expenses')
            ->latest()
            ->paginate(10);

        $stats = [
            'total'    => Project::where('created_by', auth()->id())->count(),
            'nouveau'  => Project::where('created_by', auth()->id())->where('status', 'Nouveau')->count(),
            'en_etude' => Project::where('created_by', auth()->id())->where('status', 'En Etude')->count(),
            'en_cours' => Project::where('created_by', auth()->id())->where('status', 'En Cours')->count(),
            'termine'  => Project::where('created_by', auth()->id())->where('status', 'Termine')->count(),
        ];

        $alerts = Notification::where('user_id', auth()->id())
            ->where('type', 'alerte_budget')
            ->where('is_read', false)
            ->with('project')
            ->latest()
            ->get();

        return view('livewire.assistant-dg.dashboard', compact('myProjects', 'stats', 'alerts'));
    }
}
