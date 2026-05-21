<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'title',
        'nature_id',
        'type',
        'school_id',
        'created_by',
        'budget',
        'duration_months',
        'start_year',
        'end_year',
        'address',
        'description',
        'pdf_path',
        'status',
        'juriste_id',
        'chef_projet_id',
        'chef_access_unlocked',
        'dg_consulted_at',
        'school_director_viewed_at',
        'started_at',
        'budget_alert_sent',
        'closed_at',
    ];

    protected $casts = [
        'chef_access_unlocked'       => 'boolean',
        'budget_alert_sent'          => 'boolean',
        'dg_consulted_at'            => 'datetime',
        'school_director_viewed_at'  => 'datetime',
        'started_at'                 => 'datetime',
        'closed_at'                  => 'datetime',
        'budget'                     => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function nature(): BelongsTo
    {
        return $this->belongsTo(ProjectNature::class, 'nature_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function juriste(): BelongsTo
    {
        return $this->belongsTo(User::class, 'juriste_id');
    }

    public function chefProjet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_projet_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function legalSteps(): HasMany
    {
        return $this->hasMany(\App\Models\LegalStep::class, 'project_id')->orderBy('sort_order');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'project_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'project_id');
    }

    public function odsRecords(): HasMany
    {
        return $this->hasMany(OdsRecord::class, 'project_id');
    }

    public function archive(): HasMany
    {
        return $this->hasMany(ProjectArchive::class, 'project_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES - Filtrage par statut, école, budget
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Projets d'une école spécifique
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope : Projets avec un statut spécifique
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope : Projets en attente d'étude (statut EN ÉTUDE)
     */
    public function scopeInStudy(Builder $query): Builder
    {
        return $query->byStatus('En Etude');
    }

    /**
     * Scope : Projets en cours (statut EN COURS)
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->byStatus('En Cours');
    }

    /**
     * Scope : Projets terminés (statut TERMINÉ)
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->byStatus('Termine');
    }

    /**
     * Scope : Projets nouveaux (statut NOUVEAU)
     */
    public function scopeNew(Builder $query): Builder
    {
        return $query->byStatus('Nouveau');
    }

    /**
     * Scope : Projets avec alerte budgétaire (>= 80% consommé)
     */
    public function scopeWithBudgetAlert(Builder $query): Builder
    {
        return $query->where('budget_alert_sent', true);
    }

    /**
     * Scope : Projets sans alerte budgétaire
     */
    public function scopeWithoutBudgetAlert(Builder $query): Builder
    {
        return $query->where('budget_alert_sent', false);
    }

    /**
     * Scope : Projets avec accès Chef de Projet débloqué
     */
    public function scopeWithChefAccess(Builder $query): Builder
    {
        return $query->where('chef_access_unlocked', true);
    }

    /**
     * Scope : Projets sans accès Chef de Projet (avant ODS)
     */
    public function scopeWithoutChefAccess(Builder $query): Builder
    {
        return $query->where('chef_access_unlocked', false);
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER - Calculs et validations
    |--------------------------------------------------------------------------
    */

    /**
     * Calcul du budget consommé via la table expenses
     */
    public function getTotalSpentAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    /**
     * Calcul du budget restant
     */
    public function getRemainingBudgetAttribute(): float
    {
        return (float) $this->budget - $this->total_spent;
    }

    /**
     * Pourcentage de budget consommé
     */
    public function getBudgetConsumptionPercentAttribute(): float
    {
        if ($this->budget == 0) {
            return 0;
        }
        return ($this->total_spent / (float) $this->budget) * 100;
    }

    /**
     * Pourcentage d'avancement de la To-Do List (Juridique)
     * ATTENTION : Ceci est le % de COMPLÉTUDE des tâches, pas la somme des %
     */
    public function getProgressPercentageAttribute(): float
    {
        $total = $this->tasks()->count();
        if ($total === 0) return 0;

        $completed = $this->tasks()->where('is_completed', true)->count();
        return ($completed / $total) * 100;
    }

    /**
     * Somme des pourcentages de validation juridique (CRITIQUE)
     * Cette somme DOIT être exactement 100% avant d'émettre l'ODS
     */
    public function getTodoPercentageSumAttribute(): float
    {
        return (float) $this->tasks()->sum('percentage');
    }

    /**
     * Vérifier si la validation juridique est complète (100%)
     * STRICT : Aucune exception, doit être exactement 100%
     */
    public function canEmitODS(): bool
    {
        $sum = $this->getTodoPercentageSumAttribute();
        return $sum === 100.0;
    }

    /**
     * Vérifier si l'alerte budgétaire 80% doit être déclenchée
     * STRICT : >= 80% du budget consommé
     */
    public function shouldTriggerBudgetAlert(): bool
    {
        return $this->budget_consumption_percent >= 80;
    }

    /**
     * Vérifier si le Chef de Projet peut accéder au projet
     * Condition : ODS émise (status EN COURS) ET chef_access_unlocked = true
     */
    public function canChefAccess(): bool
    {
        return $this->status === 'En Cours' && $this->chef_access_unlocked === true;
    }

    /**
     * Transition de statut avec validation métier
     * Workflow strict : NOUVEAU -> EN ÉTUDE -> EN COURS -> TERMINÉ
     */
    public function transitionToNextStatus(): bool
    {
        $currentStatus = $this->status;

        // Déterminer le statut suivant
        $nextStatus = match($currentStatus) {
            'Nouveau' => 'En Etude',
            'En Etude' => 'En Cours',
            'En Cours' => 'Termine',
            'Termine' => null, // Pas de transition possible depuis TERMINÉ
            default => null,
        };

        // Si pas de transition possible
        if ($nextStatus === null) {
            return false;
        }

        // Validations métier strictes avant transition
        if ($nextStatus === 'En Cours') {
            // CRITIQUE : Vérifier que To-Do = 100% avant EN COURS
            if (!$this->canEmitODS()) {
                return false;
            }
        }

        // Mettre à jour le statut
        $this->status = $nextStatus;

        // Si transition vers EN COURS, débloquer l'accès Chef de Projet
        if ($nextStatus === 'En Cours') {
            $this->chef_access_unlocked = true;
        }

        // Si transition vers TERMINÉ, enregistrer la date de fermeture
        if ($nextStatus === 'Termine') {
            $this->closed_at = now();
        }

        $this->save();
        return true;
    }

    /**
     * Émettre l'ODS (Order of Start / Ordre de Démarrage)
     * Crée un enregistrement ODS et bascule le projet en EN COURS
     */
    public function emitODS(User $issuedBy, ?string $notes = null): bool
    {
        // Vérifier que To-Do = 100%
        if (!$this->canEmitODS()) {
            return false;
        }

        // Créer l'enregistrement ODS
        OdsRecord::create([
            'project_id' => $this->id,
            'issued_by' => $issuedBy->id,
            'type' => 'Demarrage',
            'notes' => $notes,
        ]);

        // Transitionner vers EN COURS
        return $this->transitionToNextStatus();
    }

    /**
     * Archiver le projet (statut TERMINÉ)
     * Crée un snapshot dans project_archives
     */
    public function archiveProject(): bool
    {
        // Vérifier que le projet est terminé
        if ($this->status !== 'Termine') {
            return false;
        }

        // Créer l'archive snapshot
        ProjectArchive::create([
            'project_id' => $this->id,
            'school_name' => $this->school->name,
            'project_title' => $this->title,
            'nature_name' => $this->nature->name,
            'project_type' => $this->type,
            'total_budget' => $this->budget,
            'total_spent' => $this->total_spent,
            'budget_restant' => $this->remaining_budget,
            'start_year' => $this->start_year,
            'end_year' => $this->end_year,
            'juriste_name' => $this->juriste?->name,
            'chef_name' => $this->chefProjet?->name,
            'ods_count' => $this->odsRecords()->count(),
            'task_count' => $this->tasks()->count(),
            'tasks_done' => $this->tasks()->where('is_completed', true)->count(),
            'expense_count' => $this->expenses()->count(),
        ]);

        return true;
    }

    /**
     * Obtenir le statut en français lisible
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'Nouveau' => 'Nouveau',
            'En Etude' => 'En Étude',
            'En Cours' => 'En Cours',
            'Termine' => 'Terminé',
            default => $this->status,
        };
    }

    /**
     * Obtenir le type en français lisible
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'Investissement' => 'Investissement',
            'Exploitation' => 'Exploitation',
            default => $this->type,
        };
    }
}
