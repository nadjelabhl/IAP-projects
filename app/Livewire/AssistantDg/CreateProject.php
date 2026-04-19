<?php

namespace App\Livewire\AssistantDg;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Project;
use App\Models\School;
use App\Models\ProjectNature;
use App\Services\NotificationService;

#[Layout('layouts.app')]
class CreateProject extends Component
{
    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // Champs du formulaire (selon tes specs - Section 1)
    public $title;
    public $nature_id;
    public $type = 'Investissement';
    public $school_id;
    public $budget;
    public $duration_months;
    public $start_year;
    public $end_year;
    public $address;
    public $description;

    protected $rules = [
        'title' => 'required|min:5',
        'nature_id' => 'required',
        'type' => 'required|in:Investissement,Exploitation',
        'school_id' => 'required',
        'budget' => 'required|numeric|min:1000',
        'duration_months' => 'required|integer|min:1',
        'start_year' => 'required|integer|min:2024|max:2050',
        'end_year' => 'required|integer|min:2024|max:2050|gte:start_year',
        'address' => 'required',
    ];

    public function save()
    {
        $this->validate();

        // 1. Enregistrement du projet (Statut: Nouveau)
        $project = Project::create([
            'title' => $this->title,
            'nature_id' => $this->nature_id,
            'type' => $this->type,
            'school_id' => $this->school_id,
            'created_by' => auth()->id(),
            'budget' => $this->budget,
            'duration_months' => $this->duration_months,
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'address' => $this->address,
            'description' => $this->description,
            'status' => 'Nouveau', // Statut initial obligatoire
        ]);

        // 2. Notification automatique pour le DG (Section 7)
        $this->notificationService->notifyNewProject($project);

        // Message de succès
        session()->flash('message', 'Projet enregistré avec succès et transmis au DG.');

        // Reset du formulaire
        $this->reset();

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.assistant-dg.create-project', [
            'schools' => School::all(),
            'natures' => ProjectNature::where('is_active', true)->get()
        ]);
    }
}