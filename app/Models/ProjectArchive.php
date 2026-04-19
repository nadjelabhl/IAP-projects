<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectArchive extends Model
{
    protected $table = 'project_archives';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'school_name',
        'project_title',
        'nature_name',
        'project_type',
        'total_budget',
        'total_spent',
        'budget_restant',
        'start_year',
        'end_year',
        'juriste_name',
        'chef_name',
        'ods_count',
        'task_count',
        'tasks_done',
        'expense_count',
        'archived_at',
    ];

    protected $casts = [
        'total_budget' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'budget_restant' => 'decimal:2',
        'archived_at' => 'datetime',
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

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Archives d'une école spécifique
     */
    public function scopeForSchool(Builder $query, string $schoolName): Builder
    {
        return $query->where('school_name', $schoolName);
    }

    /**
     * Scope : Archives triées par date décroissante
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('archived_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le pourcentage de budget consommé
     */
    public function getBudgetConsumptionPercentAttribute(): float
    {
        if ($this->total_budget == 0) {
            return 0;
        }
        return ((float) $this->total_spent / (float) $this->total_budget) * 100;
    }

    /**
     * Obtenir le pourcentage de tâches complétées
     */
    public function getTaskCompletionPercentAttribute(): float
    {
        if ($this->task_count == 0) {
            return 0;
        }
        return ($this->tasks_done / $this->task_count) * 100;
    }

    /**
     * Obtenir le montant formaté du budget total
     */
    public function getFormattedBudgetAttribute(): string
    {
        return number_format((float) $this->total_budget, 2, ',', ' ') . ' DA';
    }

    /**
     * Obtenir le montant formaté des dépenses
     */
    public function getFormattedSpentAttribute(): string
    {
        return number_format((float) $this->total_spent, 2, ',', ' ') . ' DA';
    }

    /**
     * Obtenir le montant formaté du budget restant
     */
    public function getFormattedRestantAttribute(): string
    {
        return number_format((float) $this->budget_restant, 2, ',', ' ') . ' DA';
    }
}
