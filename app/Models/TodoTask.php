<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Per-project legal phase — stored in todo_tasks.
 */
class TodoTask extends Model
{
    protected $table      = 'todo_tasks';
    protected $primaryKey = 'id_phase';

    public function getIdAttribute(): int { return $this->id_phase; }

    protected $fillable = [
        'project_id',
        'title_phase',
        'percentage',
        'is_completed',
        'completed_at',
        'checked_at',
        'sort_order',
        'is_deletable',
        'todo_tasks_pdf_path',
    ];

    protected $casts = [
        'is_completed'        => 'boolean',
        'is_deletable'        => 'boolean',
        'completed_at'        => 'datetime',
        'checked_at'          => 'datetime',
        'percentage'          => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────

    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeCompleted(Builder $query): Builder  { return $query->where('is_completed', true); }
    public function scopePending(Builder $query): Builder    { return $query->where('is_completed', false); }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Business logic
    // ─────────────────────────────────────────────────────────────────────────

    public function isPreviousCompleted(): bool
    {
        if ($this->sort_order <= 1) return true;

        return static::where('project_id', $this->project_id)
            ->where('sort_order', $this->sort_order - 1)
            ->where('is_completed', true)
            ->exists();
    }

    public function check(int $userId): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'checked_at'   => now(),
        ]);
    }

    public function uncheck(): void
    {
        $this->update([
            'is_completed' => false,
            'completed_at' => null,
            'checked_at'   => null,
        ]);
    }

    public function markAsCompleted(): void { $this->check(0); }

    public function markAsPending(): void { $this->uncheck(); }

    public function updatePercentage(int $percentage): bool
    {
        if ($percentage < 0 || $percentage > 100) return false;
        $this->percentage = $percentage;
        $this->save();
        return true;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->is_completed ? 'Complétée' : 'En attente';
    }
}
