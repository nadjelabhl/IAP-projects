<?php

namespace App\Livewire\Director;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ProjectDetail extends Component
{
    public Project $project;

    public function mount(Project $project): void
    {
        $school = auth()->user()->school;
        abort_if(!$school || $project->school_id !== $school->id, 403);

        $this->project = $project->load([
            'nature', 'school', 'juriste', 'chefProjet', 'creator',
            'legalSteps', 'expenses', 'odsRecords.issuedBy',
        ]);
    }

    public function render()
    {
        $p          = $this->project;
        $legalSteps = $p->legalSteps;
        $legalPct   = (float) $legalSteps->where('is_completed', true)->sum('percentage');
        $totalSpent = (float) $p->expenses->sum('amount');
        $budgetPct  = $p->budget > 0 ? min(($totalSpent / $p->budget) * 100, 100) : 0;

        $expenses = $p->expenses->sortByDesc('expense_date');

        return view('livewire.director.project-detail', [
            'p'          => $p,
            'legalSteps' => $legalSteps,
            'legalPct'   => $legalPct,
            'budgetPct'  => $budgetPct,
            'totalSpent' => $totalSpent,
            'expenses'   => $expenses,
        ]);
    }
}
