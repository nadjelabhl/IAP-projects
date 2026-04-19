<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use App\Models\Task;

class JuristTaskManager extends Component
{
    public Project $project;
    public $title;
    public $percentage;

    protected $rules = [
        'title' => 'required|string|min:3',
        'percentage' => 'required|numeric|between:1,100',
    ];

    public function addTask()
    {
        $this->validate();

        Task::create([
            'project_id' => $this->project->id,
            'created_by' => auth()->id(),
            'title' => $this->title,
            'percentage' => $this->percentage,
        ]);

        $this->reset(['title', 'percentage']);
        $this->project->refresh();
    }

    public function toggleComplete($taskId)
    {
        $task = Task::find($taskId);
        $task->update([
            'is_completed' => !$task->is_completed,
            'completed_at' => !$task->is_completed ? now() : null
        ]);

        // Vérification automatique du déblocage
        if ($this->project->progress_percentage == 100) {
            $this->project->update(['chef_access_unlocked' => true, 'status' => 'En Cours']);
        }
        
        $this->project->refresh();
    }

    public function render()
    {
        return view('livewire.jurist-task-manager');
    }
}