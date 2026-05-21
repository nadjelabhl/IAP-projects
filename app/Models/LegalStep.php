<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalStep extends Model
{
    protected $table = 'todo_tasks';

    protected $fillable = [
        'project_id',
        'created_by',
        'title',
        'percentage',
        'is_completed',
        'completed_at',
        'checked_at',
        'sort_order',
        'pdf_path',
        'is_deletable',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'is_deletable' => 'boolean',
        'completed_at' => 'datetime',
        'checked_at'   => 'datetime',
        'percentage'   => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Vérifie si la phase précédente (N-1) est cochée. */
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
}
