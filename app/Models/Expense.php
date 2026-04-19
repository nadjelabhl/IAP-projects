<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'entered_by',
        'description',
        'amount',
        'expense_date'
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2'
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES - Filtrage par projet, date, montant
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Dépenses d'un projet spécifique
     */
    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope : Dépenses saisies par un utilisateur
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('entered_by', $userId);
    }

    /**
     * Scope : Dépenses après une date spécifique
     */
    public function scopeAfterDate(Builder $query, $date): Builder
    {
        return $query->where('expense_date', '>=', $date);
    }

    /**
     * Scope : Dépenses avant une date spécifique
     */
    public function scopeBeforeDate(Builder $query, $date): Builder
    {
        return $query->where('expense_date', '<=', $date);
    }

    /**
     * Scope : Dépenses triées par date décroissante (les plus récentes d'abord)
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('expense_date', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le pourcentage de consommation du budget du projet
     */
    public function getBudgetConsumptionPercentAttribute(): float
    {
        if (!$this->project || $this->project->budget == 0) {
            return 0;
        }

        $totalSpent = $this->project->total_spent;
        return ($totalSpent / (float) $this->project->budget) * 100;
    }

    /**
     * Vérifier si cette dépense déclenche l'alerte 80%
     */
    public function triggersAlert(): bool
    {
        return $this->budget_consumption_percent >= 80;
    }

    /**
     * Obtenir le montant formaté en devise
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format((float) $this->amount, 2, ',', ' ') . ' DA';
    }
}
