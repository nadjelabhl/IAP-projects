<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectNature extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'nature_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Natures actives uniquement
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le nombre de projets de cette nature
     */
    public function getProjectCountAttribute(): int
    {
        return $this->projects()->count();
    }

    /**
     * Obtenir le budget total des projets de cette nature
     */
    public function getTotalBudgetAttribute(): float
    {
        return (float) $this->projects()->sum('budget');
    }

    /**
     * Obtenir le total des dépenses des projets de cette nature
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->projects()
            ->join('expenses', 'projects.id', '=', 'expenses.project_id')
            ->sum('expenses.amount');
    }
}