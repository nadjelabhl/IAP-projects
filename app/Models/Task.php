<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $table = 'todo_tasks';

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'percentage',
        'is_completed',
        'completed_at',
        'sort_order'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'percentage' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES - Filtrage par projet, statut, pourcentage
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Tâches d'un projet spécifique
     */
    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope : Tâches complétées
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope : Tâches non complétées
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    /**
     * Scope : Tâches triées par ordre de tri
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Marquer la tâche comme complétée
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Marquer la tâche comme non complétée
     */
    public function markAsPending(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Mettre à jour le pourcentage de validation
     * VALIDATION : Le pourcentage doit être entre 0 et 100
     */
    public function updatePercentage(int $percentage): bool
    {
        if ($percentage < 0 || $percentage > 100) {
            return false;
        }

        $this->percentage = $percentage;
        $this->save();
        return true;
    }

    /**
     * Obtenir le label de statut
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_completed ? 'Complétée' : 'En attente';
    }
}
