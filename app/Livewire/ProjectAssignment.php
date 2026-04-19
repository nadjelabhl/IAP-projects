<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class ProjectAssignment extends Component
{
    public $project; // Le projet à affecter
    public $juriste_id;
    public $chef_projet_id;

    protected NotificationService $notificationService;

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function saveAssignment()
    {
        $this->validate([
            'juriste_id' => 'required|exists:users,id',
            'chef_projet_id' => 'required|exists:users,id',
        ]);

        $this->project->update([
            'juriste_id' => $this->juriste_id,
            'chef_projet_id' => $this->chef_projet_id,
            'status' => 'En Etude', // Le projet passe en phase juridique
        ]);

        // Notify assigned personnel
        $this->notificationService->create(
            'assignment',
            'Affectation au Projet',
            "Vous avez été affecté au projet: {$this->project->title}",
            [$this->juriste_id, $this->chef_projet_id],
            $this->project->id
        );

        session()->flash('message', 'Équipe affectée avec succès ! Le projet est maintenant "En Étude".');

        return redirect()->to('/projects'); // Ou ta route vers la liste
    }

    public function render()
    {
        // On récupère uniquement les utilisateurs de la MÊME école que le projet
        $schoolId = $this->project->school_id;

        return view('livewire.project-assignment', [
            'availableJuristes' => User::where('role', 'juriste')->where('school_id', $schoolId)->get(),
            'availableCPs' => User::where('role', 'chef_projet')->where('school_id', $schoolId)->get(),
        ]);
    }
}