<?php

namespace App\Livewire\Archives;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ArchivesList extends Component
{
    use WithPagination;

    public function render()
    {
        $user  = auth()->user();
        $query = Project::where('status', 'Termine')
            ->with('school', 'nature', 'expenses')
            ->orderBy('closed_at', 'desc');

        if ($user->role === 'directeur_ecole') {
            $query->where('school_id', $user->school_id);
        }

        $exportRoute = match($user->role) {
            'directeur_ecole' => 'directeur_ecole.archives.export',
            'assistant_dg'    => 'assistant_dg.archives.export',
            default           => 'dg.archives.export',
        };

        return view('livewire.archives.archives-list', [
            'projects'  => $query->paginate(20),
            'exportUrl' => route($exportRoute),
        ]);
    }
}
