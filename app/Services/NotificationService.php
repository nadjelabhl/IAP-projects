<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Project;
use App\Models\User;

class NotificationService
{
    private function notify(string $type, string $message, array $userIds, ?int $projectId = null, string $priority = 'normal'): void
    {
        foreach (array_unique($userIds) as $userId) {
            Notification::create([
                'user_id'    => $userId,
                'project_id' => $projectId,
                'type_notification' => $type,
                'message'    => $message,
                'priority'   => $priority,
                'is_read'    => false,
            ]);
        }
    }

    /** Notifie le DG qu'un nouveau projet a été soumis. */
    public function notifyNewProject(Project $project): void
    {
        $dgIds = User::where('role', 'dg')->where('is_active', true)->pluck('id')->toArray();
        $this->notify(
            'nouveau_projet',
            "Nouveau projet soumis : « {$project->title_project} » (École {$project->school->name_school}).",
            $dgIds,
            $project->id
        );
    }

    /** Notifie le Directeur d'École que le DG lui a transmis un projet. */
    public function notifyProjectTransmittedToDirector(Project $project): void
    {
        $director = User::where('role', 'directeur_ecole')
            ->where('school_id', $project->school_id)
            ->where('is_active', true)
            ->first();

        if (!$director) return;

        $this->notify(
            'projet_transmis',
            "Le DG vous a transmis le projet « {$project->title_project} » pour affectation.",
            [$director->id],
            $project->id
        );
    }

    /** Notifie le Juriste et le Chef de Projet de leur affectation. */
    public function notifyAssignment(Project $project): void
    {
        $ids = array_filter([$project->juriste_id, $project->chef_projet_id]);

        $juriste = $project->juriste_id
            ? "Vous avez été affecté(e) en tant que Juriste au projet « {$project->title_project} »."
            : null;
        $chef = $project->chef_projet_id
            ? "Vous avez été affecté(e) en tant que Chef de Projet au projet « {$project->title_project} »."
            : null;

        if ($project->juriste_id && $juriste) {
            $this->notify('affectation', $juriste, [$project->juriste_id], $project->id, 'urgent');
        }
        if ($project->chef_projet_id && $chef) {
            $this->notify('affectation', $chef, [$project->chef_projet_id], $project->id, 'urgent');
        }
    }

    /** Notifie le Chef de Projet que son accès est débloqué (ODS Démarrage). */
    public function notifyChefAccessUnlocked(Project $project): void
    {
        if (!$project->chef_projet_id) return;

        $this->notify(
            'ods_demarrage',
            "L'ODS de Démarrage a été émis pour « {$project->title_project} ». Votre accès est maintenant actif.",
            [$project->chef_projet_id],
            $project->id,
            'urgent'
        );
    }

    /** Notifie le Directeur d'École et l'Assistant DG qu'un ODS Démarrage a été émis. */
    public function notifyOdsDemarrage(Project $project): void
    {
        $ids = [];

        $director = User::where('role', 'directeur_ecole')
            ->where('school_id', $project->school_id)
            ->where('is_active', true)->first();
        if ($director) $ids[] = $director->id;

        $assistants = User::where('role', 'assistant_dg')->where('is_active', true)->pluck('id');
        foreach ($assistants as $id) $ids[] = $id;

        $this->notify(
            'ods_demarrage',
            "ODS de Démarrage émis pour le projet « {$project->title_project} ».",
            $ids,
            $project->id
        );
    }

    /** Notifie le Chef de Projet d'un ODS d'Arrêt (urgent). */
    public function notifyOdsArret(Project $project): void
    {
        $ids = array_filter([$project->chef_projet_id]);

        if (empty($ids)) return;

        $this->notify(
            'ods_arret',
            "ARRÊT — ODS d'Arrêt émis pour le projet « {$project->title_project} ».",
            $ids,
            $project->id,
            'urgent'
        );
    }

    /** Notifie le Chef de Projet d'un ODS de Reprise (urgent). */
    public function notifyOdsReprise(Project $project): void
    {
        $ids = array_filter([$project->chef_projet_id]);

        if (empty($ids)) return;

        $this->notify(
            'ods_reprise',
            "REPRISE — ODS de Reprise émis pour le projet « {$project->title_project} ».",
            $ids,
            $project->id,
            'urgent'
        );
    }

    /**
     * Alerte budgétaire 80 % : notifie Assistant DG, DG, Directeur École,
     * Juriste et Chef de Projet. L'Admin ne reçoit PAS cette alerte.
     */
    public function notifyBudgetAlert(Project $project): void
    {
        $ids = [];

        // Assistant DG
        User::where('role', 'assistant_dg')->where('is_active', true)
            ->pluck('id')->each(fn($id) => $ids[] = $id);

        // DG
        User::where('role', 'dg')->where('is_active', true)
            ->pluck('id')->each(fn($id) => $ids[] = $id);

        // Directeur École
        $director = User::where('role', 'directeur_ecole')
            ->where('school_id', $project->school_id)
            ->where('is_active', true)->first();
        if ($director) $ids[] = $director->id;

        // Juriste
        if ($project->juriste_id) $ids[] = $project->juriste_id;

        // Chef de Projet
        if ($project->chef_projet_id) $ids[] = $project->chef_projet_id;

        $pct = number_format(($project->expenses()->sum('amount') / $project->budget) * 100, 1);

        $this->notify(
            'alerte_budget',
            "ALERTE BUDGET — Le projet « {$project->title_project} » a consommé {$pct} % de son budget.",
            $ids,
            $project->id,
            'urgent'
        );

        $project->update(['budget_alert_sent' => true]);
    }

    /** Notifie les parties prenantes que le projet est terminé/archivé. */
    public function notifyProjectTerminated(Project $project): void
    {
        $ids = [];

        if ($project->created_by) $ids[] = $project->created_by;
        if ($project->juriste_id) $ids[] = $project->juriste_id;
        if ($project->chef_projet_id) $ids[] = $project->chef_projet_id;

        $director = User::where('role', 'directeur_ecole')
            ->where('school_id', $project->school_id)
            ->where('is_active', true)->first();
        if ($director) $ids[] = $director->id;

        User::where('role', 'dg')->where('is_active', true)
            ->pluck('id')->each(fn($id) => $ids[] = $id);

        $this->notify(
            'projet_termine',
            "Le projet « {$project->title_project} » a été clôturé et archivé.",
            $ids,
            $project->id
        );
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notif = Notification::where('id_notification', $notificationId)->where('user_id', $userId)->first();
        if (!$notif) return false;
        $notif->update(['is_read' => true, 'read_at' => now()]);
        return true;
    }

    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)->where('is_read', false)->count();
    }
}
