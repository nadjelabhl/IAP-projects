<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'project_id',
        'type',
        'message',
        'priority',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES - Filtrage par utilisateur, statut, type
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Notifications d'un utilisateur spécifique
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope : Notifications non lues
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope : Notifications lues
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope : Notifications d'un type spécifique
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : Notifications urgentes
     */
    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope : Notifications triées par date décroissante (les plus récentes d'abord)
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope : Notifications d'un projet spécifique
     */
    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Marquer comme lu
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Marquer comme non lu
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Obtenir le label du type de notification
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'nouveau_projet' => 'Nouveau Projet',
            'projet_transmis' => 'Projet Transmis',
            'affectation' => 'Affectation',
            'alerte_budget' => 'Alerte Budget',
            'ods_emise' => 'ODS Émise',
            'archivage' => 'Archivage',
            default => $this->type,
        };
    }

    /**
     * Obtenir le label de priorité
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'normal' => 'Normal',
            'urgent' => 'Urgent',
            default => $this->priority,
        };
    }
}
