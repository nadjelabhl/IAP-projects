<?php

namespace App\Livewire\Director;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Project;
use App\Models\Notification;
use App\Models\User;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DashboardDirector extends Component
{
    use WithPagination;

    public $school;
    public $stats = [];
    public $selectedJuriste;
    public $selectedChef;
    public $showAssignModal = null;
    public $projectIdAssign;

    public function mount()
    {
        $this->school = auth()->user()->school;

        if (!$this->school) {
            session()->flash('error', 'Votre compte n\'est asocié à aucune école.');
            return redirect()->route('dashboard');
        }

        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_projects' => Project::where('school_id', $this->school->id)->count(),
            'nouveau' => Project::where('school_id', $this->school->id)->where('status', 'Nouveau')->count(),
            'en_etude' => Project::where('school_id', $this->school->id)->where('status', 'En Etude')->count(),
            'en_cours' => Project::where('school_id', $this->school->id)->where('status', 'En Cours')->count(),
            'termine' => Project::where('school_id', $this->school->id)->where('status', 'Termine')->count(),
            'budget_total' => Project::where('school_id', $this->school->id)->sum('budget'),
        ];
    }

    public function assignPersonnel($projectId, $juristeId, $chefId)
    {
        // Affecter le personnel au projet (Section 3 - Flux Métier)
        $project = Project::findOrFail($projectId);

        $project->update([
            'juriste_id' => $juristeId,
            'chef_projet_id' => $chefId,
            'status' => 'En Etude', // Le projet passe en étude une fois le personnel assigné
        ]);

        // Notifier le Juriste
        $juriste = User::find($juristeId);
        if ($juriste) {
            Notification::create([
                'user_id' => $juriste->id,
                'project_id' => $project->id,
                'type' => 'project_assigned',
                'priority' => 'normal',
                'message' => "Vous avez été affecté au projet : " . $project->title,
            ]);
        }

        // Notifier le Chef de Projet
        $chef = User::find($chefId);
        if ($chef) {
            Notification::create([
                'user_id' => $chef->id,
                'project_id' => $project->id,
                'type' => 'project_assigned',
                'priority' => 'normal',
                'message' => "Vous avez été affecté au projet : " . $project->title,
            ]);
        }

        session()->flash('message', 'Personnel assigné avec succès.');
        $this->loadStats();
    }

    public function markNotificationRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();
        $this->loadStats();
    }

    public function openAssignModal($projectId)
    {
        $this->projectIdAssign = $projectId;
        $this->showAssignModal = true;
    }

    public function closeAssignModal()
    {
        $this->showAssignModal = false;
        $this->projectIdAssign = null;
        $this->selectedJuriste = null;
        $this->selectedChef = null;
    }

    public function render()
    {
        // Projets de l'école (non terminés)
        $projects = Project::where('school_id', $this->school->id)
            ->where('status', '!=', 'Termine')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Liste RH - Juristes et Chefs de Projet de l'école
        $juristes = User::where('role', 'juriste')
            ->where('school_id', $this->school->id)
            ->where('is_active', true)
            ->get();

        $chefs = User::where('role', 'chef_projet')
            ->where('school_id', $this->school->id)
            ->where('is_active', true)
            ->get();

        return view('livewire.director.dashboard-director', [
            'projects' => $projects,
            'juristes' => $juristes,
            'chefs' => $chefs,
            'notifications' => Notification::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'unreadNotifications' => Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count(),
        ]);
    }
}