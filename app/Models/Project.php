<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $primaryKey = 'id_project';

    public function getIdAttribute(): int { return $this->id_project; }

    protected $fillable = [
        'title_project',
        'type_project',
        'nature_id',
        'school_id',
        'created_by',
        'budget',
        'duration_months',
        'start_date',
        'end_date',
        'localisation',
        'description',
        'status',
        'juriste_id',
        'chef_projet_id',
        'chef_access_unlocked',
        'consulted_by',
        'visibility_school',
        'date_vis_budget',
        'started_at',
        'budget_alert_sent',
        'closed_at',
    ];

    protected $casts = [
        'chef_access_unlocked' => 'boolean',
        'budget_alert_sent'    => 'boolean',
        'visibility_school'    => 'boolean',
        'started_at'           => 'datetime',
        'closed_at'            => 'datetime',
        'date_vis_budget'      => 'date',
        'budget'               => 'decimal:2',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────

    public function school(): BelongsTo     { return $this->belongsTo(School::class); }
    public function nature(): BelongsTo     { return $this->belongsTo(ProjectNature::class, 'nature_id'); }
    public function creator(): BelongsTo    { return $this->belongsTo(User::class, 'created_by'); }
    public function consultedBy(): BelongsTo{ return $this->belongsTo(User::class, 'consulted_by'); }
    public function juriste(): BelongsTo    { return $this->belongsTo(User::class, 'juriste_id'); }
    public function chefProjet(): BelongsTo { return $this->belongsTo(User::class, 'chef_projet_id'); }

    public function tasks(): HasMany
    {
        return $this->hasMany(TodoTask::class, 'project_id');
    }

    /** Alias kept for compatibility with existing views/components. */
    public function legalSteps(): HasMany
    {
        return $this->hasMany(TodoTask::class, 'project_id')->orderBy('sort_order');
    }

    public function expenses(): HasMany     { return $this->hasMany(Expense::class, 'project_id'); }
    public function notifications(): HasMany{ return $this->hasMany(Notification::class, 'project_id'); }
    public function odsRecords(): HasMany   { return $this->hasMany(OdsRecord::class, 'project_id'); }


    // ─────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────

    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeInStudy(Builder $query): Builder    { return $query->byStatus('En Etude'); }
    public function scopeInProgress(Builder $query): Builder { return $query->byStatus('En Cours'); }
    public function scopeCompleted(Builder $query): Builder  { return $query->byStatus('Termine'); }
    public function scopeNew(Builder $query): Builder        { return $query->byStatus('Nouveau'); }

    public function scopeWithBudgetAlert(Builder $query): Builder
    {
        return $query->where('budget_alert_sent', true);
    }

    public function scopeWithoutBudgetAlert(Builder $query): Builder
    {
        return $query->where('budget_alert_sent', false);
    }

    public function scopeWithChefAccess(Builder $query): Builder
    {
        return $query->where('chef_access_unlocked', true);
    }

    public function scopeWithoutChefAccess(Builder $query): Builder
    {
        return $query->where('chef_access_unlocked', false);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Business logic
    // ─────────────────────────────────────────────────────────────────────────

    public function getTotalSpentAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function getRemainingBudgetAttribute(): float
    {
        return (float) $this->budget - $this->total_spent;
    }

    public function getBudgetConsumptionPercentAttribute(): float
    {
        if ($this->budget == 0) return 0;
        return ($this->total_spent / (float) $this->budget) * 100;
    }

    public function getProgressPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;
        return ($this->tasks()->where('is_completed', true)->count() / $total) * 100;
    }

    public function getTodoPercentageSumAttribute(): float
    {
        return (float) $this->tasks()->sum('percentage');
    }

    public function canEmitODS(): bool
    {
        return $this->getTodoPercentageSumAttribute() === 100.0;
    }

    public function shouldTriggerBudgetAlert(): bool
    {
        return $this->budget_consumption_percent >= 80;
    }

    public function getLegalProgressAttribute(): float
    {
        return (float) $this->legalSteps()->where('is_completed', true)->sum('percentage');
    }

    public function getTechniqueProgressAttribute(): float
    {
        return $this->getBudgetConsumptionPercentAttribute();
    }

    public function isAccessibleForChef(): bool
    {
        return $this->legal_progress >= 100.0 && $this->started_at !== null;
    }

    public function canChefAccess(): bool { return $this->isAccessibleForChef(); }

    public function transitionToNextStatus(): bool
    {
        $nextStatus = match($this->status) {
            'Nouveau'  => 'En Etude',
            'En Etude' => 'En Cours',
            'En Cours' => 'Termine',
            default    => null,
        };

        if ($nextStatus === null) return false;
        if ($nextStatus === 'En Cours' && !$this->canEmitODS()) return false;

        $this->status = $nextStatus;
        if ($nextStatus === 'En Cours') $this->chef_access_unlocked = true;
        if ($nextStatus === 'Termine')  $this->closed_at = now();
        $this->save();
        return true;
    }

    public function emitODS(User $issuedBy, ?string $notes = null): bool
    {
        if (!$this->canEmitODS()) return false;

        OdsRecord::create([
            'project_id' => $this->id,
            'issued_by'  => $issuedBy->id,
            'type_ods'   => 'Demarrage',
            'notes'      => $notes,
        ]);

        return $this->transitionToNextStatus();
    }


    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'Nouveau'  => 'Nouveau',
            'En Etude' => 'En Étude',
            'En Cours' => 'En Cours',
            'Termine'  => 'Terminé',
            default    => $this->status,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type_project) {
            'Investissement' => 'Investissement',
            'Exploitation'   => 'Exploitation',
            default          => $this->type_project,
        };
    }
}
