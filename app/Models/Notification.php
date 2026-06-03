<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'id_notification';

    public function getIdAttribute(): int { return $this->id_notification; }

    protected $fillable = [
        'user_id',
        'project_id',
        'type_notification',
        'message',
        'priority',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeUnread(Builder $query): Builder  { return $query->where('is_read', false); }
    public function scopeRead(Builder $query): Builder    { return $query->where('is_read', true); }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type_notification', $type);
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }

    public function markAsUnread(): void
    {
        $this->update(['is_read' => false, 'read_at' => null]);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type_notification) {
            'nouveau_projet'  => 'Nouveau Projet',
            'projet_transmis' => 'Projet Transmis',
            'affectation'     => 'Affectation',
            'alerte_budget'   => 'Alerte Budget',
            'ods_emise'       => 'ODS Émise',
            'archivage'       => 'Archivage',
            default           => $this->type_notification,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'normal' => 'Normal',
            'urgent' => 'Urgent',
            default  => $this->priority,
        };
    }
}
