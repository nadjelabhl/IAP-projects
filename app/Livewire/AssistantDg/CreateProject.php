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
        'title'           => 'required|min:5',
        'nature_id'       => 'required',
        'type'            => 'required|in:Investissement,Exploitation',
        'school_id'       => 'required',
        'budget'          => 'required|numeric|min:1000',
        'duration_months' => 'required|integer|min:1',
        'start_year'      => 'required|integer|min:2024|max:2050',
        'end_year'        => 'required|integer|min:2024|max:2050|gte:start_year',
        'address'         => 'required',
    ];

    protected $messages = [
        'title.required'           => 'Le titre du projet est obligatoire.',
        'title.min'                => 'Le titre doit contenir au moins 5 caractères.',
        'nature_id.required'       => 'La nature du projet est obligatoire.',
        'type.required'            => 'Le type de projet est obligatoire.',
        'school_id.required'       => 'L\'école concernée est obligatoire.',
        'budget.required'          => 'Le budget estimé est obligatoire.',
        'budget.numeric'           => 'Le budget doit être un nombre.',
        'budget.min'               => 'Le budget doit être d\'au moins 1 000 KDA.',
        'duration_months.required' => 'La durée est obligatoire.',
        'duration_months.integer'  => 'La durée doit être un nombre entier.',
        'duration_months.min'      => 'La durée doit être d\'au moins 1 mois.',
        'start_year.required'      => 'L\'année de début est obligatoire.',
        'start_year.integer'       => 'L\'année de début doit être un entier.',
        'end_year.required'        => 'L\'année de fin est obligatoire.',
        'end_year.gte'             => 'L\'année de fin doit être supérieure ou égale à l\'année de début.',
        'address.required'         => 'L\'adresse / localisation est obligatoire.',
    ];

    public function save()
    {
        $this->validate();

        $project = Project::create([
            'title'           => $this->title,
            'nature_id'       => $this->nature_id,
            'type'            => $this->type,
            'school_id'       => $this->school_id,
            'created_by'      => auth()->id(),
            'budget'          => $this->budget,
            'duration_months' => $this->duration_months,
            'start_year'      => $this->start_year,
            'end_year'        => $this->end_year,
            'address'         => $this->address,
            'description'     => $this->description,
            'status'          => 'Nouveau',
        ]);

        $this->notificationService->notifyNewProject($project);

        session()->flash('message', 'Projet enregistré avec succès et transmis au DG.');

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
