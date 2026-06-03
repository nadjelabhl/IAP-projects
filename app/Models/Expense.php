<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_attachements';

    public function getIdAttribute(): int { return $this->id_attachements; }

    protected $fillable = [
        'project_id',
        'description',
        'amount',
        'attachement_date',
    ];

    protected $casts = [
        'attachement_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeAfterDate(Builder $query, $date): Builder
    {
        return $query->where('attachement_date', '>=', $date);
    }

    public function scopeBeforeDate(Builder $query, $date): Builder
    {
        return $query->where('attachement_date', '<=', $date);
    }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('attachement_date', 'desc');
    }

    public function getBudgetConsumptionPercentAttribute(): float
    {
        if (!$this->project || $this->project->budget == 0) return 0;
        return ($this->project->total_spent / (float) $this->project->budget) * 100;
    }

    public function triggersAlert(): bool
    {
        return $this->budget_consumption_percent >= 80;
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2, ',', ' ') . ' DA';
    }
}
