<?php

namespace App\Livewire\Dg;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Project;
use App\Models\Notification;
use App\Models\User;
use App\Models\School;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DashboardDg extends Component
{
    use WithPagination;

    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // Statistiques globales pour le DG (Section 2)
        $this->stats = [
            'total_projects' => Project::count(),
            'nouveau' => Project::where('status', 'Nouveau')->count(),
            'en_etude' => Project::where('status', 'En Etude')->count(),
            'en_cours' => Project::where('status', 'En Cours')->count(),
            'termine' => Project::where('status', 'Termine')->count(),
            'budget_total' => Project::sum('budget'),
            'depenses_total' => Project::with('expenses')->get()->sum(function($p) {
                return $p->expenses->sum('amount');
            }),
        ];
    }

    public function markNotificationRead($id)
    {
        $notification = Notification::findOrFail($id);

        // Quand le DG consulte une notification (Section 2 - Flux Métier)
        // Le projet est transmis au Directeur de l'école
        if ($notification->project_id && !$notification->is_read) {
            $notification->markAsRead();

            $project = $notification->project;
            if ($project && $project->status === 'Nouveau') {
                $project->update([
                    'dg_consulted_at' => now(),
                    'status' => 'En Etude',
                ]);

                // Notifier le Directeur de l'école concernée
                $directeur = User::where('role', 'directeur_ecole')
                    ->where('school_id', $project->school_id)
                    ->first();

                if ($directeur) {
                    Notification::create([
                        'user_id' => $directeur->id,
                        'project_id' => $project->id,
                        'type' => 'project_transferred',
                        'priority' => 'normal',
                        'message' => "Projet transféré par le DG : " . $project->title,
                    ]);
                }
            }
        } else {
            $notification->markAsRead();
        }

        $this->loadStats();
    }

    public function render()
    {
        // Liste des projets par école pour le DG
        $projectsBySchool = [];
        $schools = School::withCount(['projects' => function($query) {
            $query->where('status', '!=', 'Termine');
        }])->get();

        foreach ($schools as $school) {
            $projectsBySchool[$school->name] = Project::where('school_id', $school->id)
                ->where('status', '!=', 'Termine')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.dg.dashboard-dg', [
            'projects' => Project::where('status', '!=', 'Termine')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
            'projectsBySchool' => $projectsBySchool,
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