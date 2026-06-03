<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $primaryKey = 'id_school';

    protected $fillable = ['name_school', 'location', 'annual_budget'];

    public function getIdAttribute(): int { return $this->id_school; }

    protected $casts = ['annual_budget' => 'decimal:2'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->has('projects');
    }

    public function getTotalProjectsBudgetAttribute(): float
    {
        return (float) $this->projects()->sum('budget');
    }

    public function getTotalSpentAttribute(): float
    {
        return (float) $this->projects()
            ->join('expenses', 'projects.id_project', '=', 'expenses.project_id')
            ->sum('expenses.amount');
    }

    public function getRemainingBudgetAttribute(): float
    {
        return (float) $this->annual_budget - $this->total_spent;
    }

    public function getBudgetConsumptionPercentAttribute(): float
    {
        if ($this->annual_budget == 0) return 0;
        return ($this->total_spent / (float) $this->annual_budget) * 100;
    }

    public function getProjectCountByStatusAttribute(): array
    {
        return [
            'nouveau'  => $this->projects()->byStatus('Nouveau')->count(),
            'en_etude' => $this->projects()->byStatus('En Etude')->count(),
            'en_cours' => $this->projects()->byStatus('En Cours')->count(),
            'termine'  => $this->projects()->byStatus('Termine')->count(),
        ];
    }

    public function getActiveBudgetAlertsAttribute(): int
    {
        return $this->projects()->withBudgetAlert()->count();
    }

    public function getPendingODSCountAttribute(): int
    {
        return $this->projects()->byStatus('En Etude')->withoutChefAccess()->count();
    }
}
