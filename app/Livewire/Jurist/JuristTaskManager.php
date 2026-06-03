<?php

namespace App\Livewire\Jurist;

use Livewire\Component;
use App\Models\Project;
use App\Models\TodoTask;
use App\Services\OdsService;
use App\Services\NotificationService;

class JuristTaskManager extends Component
{
    public Project $project;
    public $title;
    public $percentage;

    protected OdsService $odsService;
    protected NotificationService $notificationService;

    public function boot(OdsService $odsService, NotificationService $notificationService)
    {
        $this->odsService = $odsService;
        $this->notificationService = $notificationService;
    }

    protected $rules = [
        'title' => 'required|string|min:3',
        'percentage' => 'required|integer|between:1,100',
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function addTask()
    {
        $this->validate();

        // Vérifier que la somme des % ne dépasse pas 100
        $currentTotal = TodoTask::where('project_id', $this->project->id)->sum('percentage');
        if ($currentTotal + $this->percentage > 100) {
            session()->flash('error', 'Attention: La somme des pourcentages ne peut pas dépasser 100%. Actuel: ' . $currentTotal . '%');
            return;
        }

        // Dernier ordre de tri
        $maxSort = TodoTask::where('project_id', $this->project->id)->max('sort_order') ?? 0;

        TodoTask::create([
            'project_id'  => $this->project->id,
            'title_phase' => $this->title,
            'percentage'  => $this->percentage,
            'sort_order'  => $maxSort + 1,
        ]);

        $this->reset(['title', 'percentage']);
        $this->project->refresh();
    }

    public function toggleComplete($taskId)
    {
        $task = TodoTask::find($taskId);
        $task->update([
            'is_completed' => !$task->is_completed,
            'completed_at' => !$task->is_completed ? now() : null
        ]);

        // Vérifier si 100% atteint pour émettre l'ODS (Section 4 - Flux Métier)
        $this->checkOdsEmission();

        $this->project->refresh();
    }

    public function checkOdsEmission()
    {
        $totalPercentage = TodoTask::where('project_id', $this->project->id)->sum('percentage');
        $completedPercentage = TodoTask::where('project_id', $this->project->id)
            ->where('is_completed', true)
            ->sum('percentage');

        // ODS émise quand 100% atteint
        if ($totalPercentage >= 100 && $completedPercentage >= 100) {
            $this->odsService->emitDemarrage($this->project, auth()->user(), 'Toutes les tâches complétées (100%)');
        }

        // Si travaux commencés mais pas terminés
        if ($completedPercentage > 0 && $completedPercentage < 100) {
            $this->project->update(['status' => 'En Etude']);
        }
    }

    public function emitOds()
    {
        // Forcer l'émission manuelle de l'ODS
        $completedPercentage = TodoTask::where('project_id', $this->project->id)
            ->where('is_completed', true)
            ->sum('percentage');

        if ($completedPercentage < 100) {
            session()->flash('error', 'Toutes les tâches doivent être complétées (100%) pour émettre l\'ODS.');
            return;
        }

        $ods = $this->odsService->emitDemarrage($this->project, auth()->user(), 'Émission manuelle par le juriste');

        if ($ods) {
            session()->flash('message', 'ODS émise! Accès débloqué pour le Chef de Projet.');
        } else {
            session()->flash('error', 'Erreur lors de l\'émission de l\'ODS.');
        }
    }

    public function render()
    {
        $tasks = TodoTask::where('project_id', $this->project->id)
            ->orderBy('sort_order')
            ->get();

        $totalPercentage = $tasks->sum('percentage');
        $completedPercentage = $tasks->where('is_completed', true)->sum('percentage');

        return view('livewire.jurist.jurist-task-manager', [
            'tasks' => $tasks,
            'totalPercentage' => $totalPercentage,
            'completedPercentage' => $completedPercentage,
        ]);
    }
}