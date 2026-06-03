<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectNature extends Model
{
    protected $primaryKey = 'id_nature';

    protected $fillable = ['name_nature', 'is_active'];

    public function getIdAttribute(): int { return $this->id_nature; }

    protected $casts = ['is_active' => 'boolean'];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'nature_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getProjectCountAttribute(): int
    {
        return $this->projects()->count();
    }

    public function getTotalBudgetAttribute(): float
    {
        return (float) $this->projects()->sum('budget');
    }

    public function getTotalSpentAttribute(): float
    {
        return (float) $this->projects()
            ->join('expenses', 'projects.id_project', '=', 'expenses.project_id')
            ->sum('expenses.amount');
    }
}
