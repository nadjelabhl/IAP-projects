<?php

namespace App\Livewire\Director;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProjectsList extends Component
{
    use WithPagination;

    public $school;
    public string $search = '';
    public string $statusFilter = '';

    public function mount(): void
    {
        $this->school = auth()->user()->school;
        abort_if(!$this->school, 403);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function render()
    {
        $sid = $this->school->id;

        $query = Project::where('school_id', $sid)
            ->with(['nature', 'juriste', 'chefProjet', 'legalSteps', 'expenses'])
            ->latest();

        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $projects = $query->paginate(15);

        $projects->getCollection()->transform(function ($p) {
            $legalPct  = (float) $p->legalSteps->where('is_completed', true)->sum('percentage');
            $spent     = (float) $p->expenses->sum('amount');
            $budgetPct = $p->budget > 0 ? min(($spent / $p->budget) * 100, 100) : 0;

            $p->legalPct  = $legalPct;
            $p->budgetPct = $budgetPct;
            $p->totalSpent = $spent;
            return $p;
        });

        return view('livewire.director.projects-list', [
            'projects' => $projects,
        ]);
    }
}
