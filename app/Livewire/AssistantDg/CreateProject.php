<?php

namespace App\Livewire\AssistantDg;

use App\Models\Project;
use App\Models\ProjectNature;
use App\Models\School;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CreateProject extends Component
{
    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService): void
    {
        $this->notificationService = $notificationService;
    }

    public string $title_project   = '';
    public ?int   $nature_id       = null;
    public string $type_project    = 'Investissement';
    public ?int   $school_id       = null;
    public ?float $budget          = null;
    public ?int   $duration_months = null;
    public ?int   $start_year      = null;
    public ?int   $end_year        = null;
    public string $localisation    = '';
    public string $description     = '';

    protected function rules(): array
    {
        return [
            'title_project'   => 'required|min:5',
            'nature_id'       => 'required',
            'type_project'    => 'required|in:Investissement,Exploitation',
            'school_id'       => 'required',
            'budget'          => 'required|numeric|min:1000',
            'duration_months' => 'required|integer|min:1',
            'start_year'      => 'required|integer|min:2024|max:2050',
            'end_year'        => 'required|integer|min:2024|max:2050|gte:start_year',
            'localisation'    => 'required',
        ];
    }

    protected $messages = [
        'title_project.required'   => 'Le titre du projet est obligatoire.',
        'title_project.min'        => 'Le titre doit contenir au moins 5 caractères.',
        'nature_id.required'       => 'La nature du projet est obligatoire.',
        'type_project.required'    => 'Le type de projet est obligatoire.',
        'school_id.required'       => 'L\'école concernée est obligatoire.',
        'budget.required'          => 'Le budget estimé est obligatoire.',
        'budget.numeric'           => 'Le budget doit être un nombre.',
        'budget.min'               => 'Le budget doit être d\'au moins 1 000 KDA.',
        'duration_months.required' => 'La durée est obligatoire.',
        'duration_months.min'      => 'La durée doit être d\'au moins 1 mois.',
        'start_year.required'      => 'L\'année de début est obligatoire.',
        'end_year.required'        => 'L\'année de fin est obligatoire.',
        'end_year.gte'             => 'L\'année de fin doit être supérieure ou égale à l\'année de début.',
        'localisation.required'    => 'L\'adresse / localisation est obligatoire.',
    ];

    public function save(): void
    {
        $this->validate();

        $project = Project::create([
            'title_project'   => $this->title_project,
            'nature_id'       => $this->nature_id,
            'type_project'    => $this->type_project,
            'school_id'       => $this->school_id,
            'created_by'      => auth()->id(),
            'budget'          => $this->budget,
            'duration_months' => $this->duration_months,
            'start_date'      => $this->start_year . '-01-01',
            'end_date'        => $this->end_year   . '-01-01',
            'localisation'    => $this->localisation,
            'description'     => $this->description ?: null,
            'status'          => 'Nouveau',
        ]);

        $this->notificationService->notifyNewProject($project);

        $this->reset();
        session()->flash('success', 'Projet enregistré avec succès et transmis au DG.');
        $this->redirect(route('assistant_dg.dashboard'));
    }

    public function render()
    {
        return view('livewire.assistant-dg.create-project', [
            'schools' => School::orderBy('name_school')->get(),
            'natures' => ProjectNature::orderBy('name_nature')->get(),
        ]);
    }
}
