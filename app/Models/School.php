<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = ['name', 'location', 'annual_budget'];

    protected $casts = [
        'annual_budget' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Écoles actives (avec au moins un projet)
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->has('projects');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le total des budgets des projets de cette école
     */
    public function getTotalProjectsBudgetAttribute(): float
    {
        return (float) $this->projects()->sum('budget');
    }

    /**
     * Obtenir le total des dépenses de tous les projets de cette école
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->projects()
            ->join('expenses', 'projects.id', '=', 'expenses.project_id')
            ->sum('expenses.amount');
    }

    /**
     * Obtenir le budget restant de cette école
     */
    public function getRemainingBudgetAttribute(): float
    {
        return (float) $this->annual_budget - $this->total_spent;
    }

    /**
     * Obtenir le pourcentage de consommation du budget annuel
     */
    public function getBudgetConsumptionPercentAttribute(): float
    {
        if ($this->annual_budget == 0) {
            return 0;
        }
        return ($this->total_spent / (float) $this->annual_budget) * 100;
    }

    /**
     * Obtenir le nombre de projets par statut
     */
    public function getProjectCountByStatusAttribute(): array
    {
        return [
            'nouveau' => $this->projects()->byStatus('Nouveau')->count(),
            'en_etude' => $this->projects()->byStatus('En Etude')->count(),
            'en_cours' => $this->projects()->byStatus('En Cours')->count(),
            'termine' => $this->projects()->byStatus('Termine')->count(),
        ];
    }

    /**
     * Obtenir le nombre d'alertes budgétaires actives
     */
    public function getActiveBudgetAlertsAttribute(): int
    {
        return $this->projects()->withBudgetAlert()->count();
    }

    /**
     * Obtenir le nombre de projets en attente d'ODS (En Étude sans accès Chef)
     */
    public function getPendingODSCountAttribute(): int
    {
        return $this->projects()
            ->byStatus('En Etude')
            ->withoutChefAccess()
            ->count();
    }
}
