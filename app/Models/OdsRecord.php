<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdsRecord extends Model
{
    protected $table = 'ods_records';

    protected $fillable = [
        'project_id',
        'issued_by',
        'type',
        'notes',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
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

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : ODS records d'un projet spécifique
     */
    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope : ODS records d'un type spécifique
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope : ODS records de démarrage (Demarrage)
     */
    public function scopeStarting(Builder $query): Builder
    {
        return $query->ofType('Demarrage');
    }

    /**
     * Scope : ODS records d'arrêt (Arret)
     */
    public function scopeStopping(Builder $query): Builder
    {
        return $query->ofType('Arret');
    }

    /**
     * Scope : ODS records de reprise (Reprise)
     */
    public function scopeResuming(Builder $query): Builder
    {
        return $query->ofType('Reprise');
    }

    /**
     * Scope : ODS records triés par date décroissante
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('issued_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | LOGIQUE MÉTIER
    |--------------------------------------------------------------------------
    */

    /**
     * Obtenir le label du type d'ODS
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'Demarrage' => 'Démarrage',
            'Arret' => 'Arrêt',
            'Reprise' => 'Reprise',
            default => $this->type,
        };
    }
}
