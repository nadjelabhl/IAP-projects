<?php

namespace App\Livewire\Director;

use App\Models\Project;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class RessourcesRh extends Component
{
    public $school;

    public function mount(): void
    {
        $this->school = auth()->user()->school;
        abort_if(!$this->school, 403);
    }

    public function render()
    {
        $sid = $this->school->id;

        $juristes = User::where('role', 'juriste')
            ->where('school_id', $sid)
            ->where('is_active', true)
            ->get()
            ->map(function ($u) {
                $activeProjects = Project::where('juriste_id', $u->id)
                    ->whereIn('status', ['En Etude', 'En Cours'])
                    ->with('nature')
                    ->get();
                return ['user' => $u, 'projects' => $activeProjects];
            });

        $chefs = User::where('role', 'chef_projet')
            ->where('school_id', $sid)
            ->where('is_active', true)
            ->get()
            ->map(function ($u) {
                $activeProjects = Project::where('chef_projet_id', $u->id)
                    ->whereIn('status', ['En Etude', 'En Cours'])
                    ->with('nature')
                    ->get();
                return ['user' => $u, 'projects' => $activeProjects];
            });

        return view('livewire.director.ressources-rh', [
            'juristes' => $juristes,
            'chefs'    => $chefs,
        ]);
    }
}
