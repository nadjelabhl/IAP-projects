<?php

namespace App\Services;

use App\Models\User;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Create a notification for users
     *
     * @param string $type
     * @param string $title
     * @param string $message
     * @param array $userIds
     * @param int|null $projectId
     * @return void
     */
    public function create(string $type, string $title, string $message, array $userIds, ?int $projectId = null): void
    {
        foreach ($userIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'project_id' => $projectId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'is_read' => false,
            ]);
        }
    }

    /**
     * Notify when a new project is created
     *
     * @param Project $project
     * @return void
     */
    public function notifyNewProject(Project $project): void
    {
        $title = 'Nouveau Projet Soumis';
        $message = "Le projet '{$project->title}' a été soumis pour validation.";
        
        // Notify DG and Assistant DG
        $users = User::whereIn('role', ['dg', 'assistant_dg'])->active()->get();
        
        $this->create('new_project', $title, $message, $users->pluck('id')->toArray(), $project->id);
    }

    /**
     * Notify when project status changes
     *
     * @param Project $project
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public function notifyStatusChange(Project $project, string $oldStatus, string $newStatus): void
    {
        $title = 'Changement de Statut';
        $message = "Le projet '{$project->title}' est passé de '{$oldStatus}' à '{$newStatus}'.";
        
        // Notify relevant users based on project
        $userIds = [];
        
        // Always notify the creator
        $userIds[] = $project->created_by;
        
        // Notify school director if applicable
        if ($project->school_id) {
            $director = User::where('role', 'directeur_ecole')
                ->where('school_id', $project->school_id)
                ->where('is_active', true)
                ->first();
            if ($director) {
                $userIds[] = $director->id;
            }
        }
        
        // Notify assigned jurist
        if ($project->juriste_id) {
            $userIds[] = $project->juriste_id;
        }
        
        // Notify assigned chef de projet
        if ($project->chef_projet_id) {
            $userIds[] = $project->chef_projet_id;
        }
        
        $userIds = array_unique($userIds);
        
        $this->create('status_change', $title, $message, $userIds, $project->id);
    }

    /**
     * Notify when budget alert threshold is reached (80%)
     *
     * @param Project $project
     * @return void
     */
    public function notifyBudgetAlert(Project $project): void
    {
        $title = 'Alerte Budget';
        $message = "Le projet '{$project->title}' a atteint 80% de son budget. Consommation: {$project->budget_consumption_percent}%.";
        
        // Notify DG, Directeur d'École, Assistant DG
        $userIds = [];
        
        // DG
        $dg = User::where('role', 'dg')->where('is_active', true)->first();
        if ($dg) {
            $userIds[] = $dg->id;
        }
        
        // Assistant DG
        $assistantDg = User::where('role', 'assistant_dg')->where('is_active', true)->first();
        if ($assistantDg) {
            $userIds[] = $assistantDg->id;
        }
        
        // Directeur d'École
        if ($project->school_id) {
            $director = User::where('role', 'directeur_ecole')
                ->where('school_id', $project->school_id)
                ->where('is_active', true)
                ->first();
            if ($director) {
                $userIds[] = $director->id;
            }
        }
        
        $this->create('budget_alert', $title, $message, $userIds, $project->id);
        
        // Mark that budget alert was sent
        $project->budget_alert_sent = true;
        $project->save();
    }

    /**
     * Notify when ODS is emitted
     *
     * @param Project $project
     * @return void
     */
    public function notifyODSEmitted(Project $project): void
    {
        $title = 'ODS Émis';
        $message = "Un ODS a été émis pour le projet '{$project->title}'.";
        
        // Notify DG, Directeur d'École, and the chef de projet
        $userIds = [];
        
        // DG
        $dg = User::where('role', 'dg')->where('is_active', true)->first();
        if ($dg) {
            $userIds[] = $dg->id;
        }
        
        // Directeur d'École
        if ($project->school_id) {
            $director = User::where('role', 'directeur_ecole')
                ->where('school_id', $project->school_id)
                ->where('is_active', true)
                ->first();
            if ($director) {
                $userIds[] = $director->id;
            }
        }
        
        // Chef de Projet (if assigned)
        if ($project->chef_projet_id) {
            $userIds[] = $project->chef_projet_id;
        }
        
        $this->create('ods_emitted', $title, $message, $userIds, $project->id);
    }

    /**
     * Notify when chef de projet access is unlocked
     *
     * @param Project $project
     * @return void
     */
    public function notifyChefAccessUnlocked(Project $project): void
    {
        if (!$project->chef_projet_id) {
            return;
        }
        
        $title = 'Accès Débloqué';
        $message = "Votre accès au projet '{$project->title}' a été débloqué. Vous pouvez maintenant gérer les dépenses.";
        
        $this->create('chef_access_unlocked', $title, $message, [$project->chef_projet_id], $project->id);
    }

    /**
     * Notify when project is archived
     *
     * @param Project $project
     * @return void
     */
    public function notifyProjectArchived(Project $project): void
    {
        $title = 'Projet Archivé';
        $message = "Le projet '{$project->title}' a été archivé.";
        
        // Notify all stakeholders
        $userIds = [];
        
        // Creator
        $userIds[] = $project->created_by;
        
        // School director
        if ($project->school_id) {
            $director = User::where('role', 'directeur_ecole')
                ->where('school_id', $project->school_id)
                ->where('is_active', true)
                ->first();
            if ($director) {
                $userIds[] = $director->id;
            }
        }
        
        // Jurist
        if ($project->juriste_id) {
            $userIds[] = $project->juriste_id;
        }
        
        // Chef de Projet
        if ($project->chef_projet_id) {
            $userIds[] = $project->chef_projet_id;
        }
        
        // DG
        $dg = User::where('role', 'dg')->where('is_active', true)->first();
        if ($dg) {
            $userIds[] = $dg->id;
        }
        
        $userIds = array_unique($userIds);
        
        $this->create('project_archived', $title, $message, $userIds, $project->id);
    }

    /**
     * Mark notification as read
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
        
        if ($notification) {
            $notification->is_read = true;
            $notification->read_at = now();
            $notification->save();
            return true;
        }
        
        return false;
    }

    /**
     * Mark all notifications as read for a user
     *
     * @param int $userId
     * @return void
     */
    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread notifications count for a user
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
