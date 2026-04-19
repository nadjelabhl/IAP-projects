<?php

namespace App\Livewire\Jurist;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Project;
use App\Models\Task;
use App\Models\Notification;
use App\Models\User;

#[Layout('layouts.app')]
class DashboardJurist extends Component
{
    public $projects = [];
    public $selectedProject;
    public $taskTitle;
    public $taskPercentage;

    protected $rules = [
        'taskTitle' => 'required|string|min:3',
        'taskPercentage' => 'required|integer|between:1,100',
    ];

    public function mount()
    {
        $this->loadProjects();
    }

    public function loadProjects()
    {
        // Projetsassignés au juriste (Section 4 - To-Do List Pondérée)
        $this->projects = Project::where('juriste_id', auth()->id())
            ->whereIn('status', ['Nouveau', 'En Etude', 'En Cours'])
            ->get();

        if ($this->selectedProject && is_numeric($this->selectedProject)) {
            $this->selectedProject = Project::findOrFail($this->selectedProject);
        }
    }

    public function selectProject($projectId)
    {
        $this->selectedProject = Project::findOrFail($projectId);
    }

    public function addTask()
    {
        $this->validate();

        if (!$this->selectedProject) {
            session()->flash('error', 'Veuillez sélectionner un projet d\'abord.');
            return;
        }

        // Vérifier que la somme des % ne dépasse pas 100
        $currentTotal = Task::where('project_id', $this->selectedProject->id)->sum('percentage');
        if ($currentTotal + $this->taskPercentage > 100) {
            session()->flash('error', 'Attention: La somme des pourcentages ne peut pas dépasser 100%. Actuel: ' . $currentTotal . '%');
            return;
        }

        Task::create([
            'project_id' => $this->selectedProject->id,
            'created_by' => auth()->id(),
            'title' => $this->taskTitle,
            'percentage' => $this->taskPercentage,
        ]);

        $this->reset(['taskTitle', 'taskPercentage']);
        session()->flash('message', 'Tâche ajoutée.');
    }

    public function toggleTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->update([
            'is_completed' => !$task->is_completed,
            'completed_at' => !$task->is_completed ? now() : null,
        ]);

        // Vérifier si 100% atteint pour émettre l'ODS (Section 4 - Flux Métier)
        $this->checkOdsEmission();
    }

    public function checkOdsEmission()
    {
        if (!$this->selectedProject) return;

        // Calculer le pourcentage réel basé sur les tâches complétées
        $totalPercentage = Task::where('project_id', $this->selectedProject->id)->sum('percentage');
        $completedPercentage = Task::where('project_id', $this->selectedProject->id)
            ->where('is_completed', true)
            ->sum('percentage');

        // Mettre à jour le statut du projet
        if ($totalPercentage >= 100 && $completedPercentage >= 100) {
            // ODS émise - déblocage automatique du Chef de Projet
            $this->selectedProject->update([
                'status' => 'En Cours',
                'chef_access_unlocked' => true,
            ]);

            // Notifier le Chef de Projet (Section 7)
            Notification::create([
                'user_id' => $this->selectedProject->chef_projet_id,
                'project_id' => $this->selectedProject->id,
                'type' => 'ods_unlocked',
                'priority' => 'urgent',
                'message' => "ODS émise pour le projet : " . $this->selectedProject->title . " - Accès débloqué!",
            ]);
        }

        // Si certaines tâches sont complétées mais pas 100%, le projet reste en étude
        if ($completedPercentage > 0 && $completedPercentage < 100) {
            $this->selectedProject->update(['status' => 'En Etude']);
        }
    }

    public function render()
    {
        $tasks = [];
        $totalPercentage = 0;
        $completedPercentage = 0;

        if ($this->selectedProject) {
            $tasks = Task::where('project_id', $this->selectedProject->id)
                ->orderBy('sort_order')
                ->get();
            $totalPercentage = $tasks->sum('percentage');
            $completedPercentage = $tasks->where('is_completed', true)->sum('percentage');
        }

        return view('livewire.jurist.dashboard-jurist', [
            'projects' => $this->projects,
            'selectedProject' => $this->selectedProject,
            'tasks' => $tasks,
            'totalPercentage' => $totalPercentage,
            'completedPercentage' => $completedPercentage,
            'notifications' => Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ]);
    }
}