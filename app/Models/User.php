<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'school_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Relation avec l'école
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Relation avec les projets créés (via created_by)
     */
    public function createdProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    /**
     * Relation avec les projets où c'est le juriste
     */
    public function juristProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'juriste_id');
    }

    /**
     * Relation avec les projets où c'est le chef de projet
     */
    public function chefProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'chef_projet_id');
    }

    /**
     * Relation avec les dépenses saisies
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'entered_by');
    }

    /**
     * Relation avec les tâches créées
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Relation avec les notifications
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Relation avec les ODS records émis
     */
    public function odsRecords(): HasMany
    {
        return $this->hasMany(OdsRecord::class, 'issued_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES - Filtrage par rôle et école
    |--------------------------------------------------------------------------
    */

    /**
     * Scope : Utilisateurs actifs uniquement
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope : Utilisateurs d'une école spécifique
     */
    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope : Utilisateurs avec un rôle spécifique
     */
    public function scopeWithRole(Builder $query, string $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * Scope : Juristes d'une école
     */
    public function scopeJuristsForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->forSchool($schoolId)->withRole('juriste')->active();
    }

    /**
     * Scope : Chefs de projet d'une école
     */
    public function scopeChefsForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->forSchool($schoolId)->withRole('chef_projet')->active();
    }

    /**
     * Scope : Directeurs d'école
     */
    public function scopeDirectors(Builder $query): Builder
    {
        return $query->withRole('directeur_ecole')->active();
    }

    /*
    |--------------------------------------------------------------------------
    | VÉRIFICATION DES RÔLES
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAssistantDG(): bool
    {
        return $this->role === 'assistant_dg';
    }

    public function isDG(): bool
    {
        return $this->role === 'dg';
    }

    public function isDirector(): bool
    {
        return $this->role === 'directeur_ecole';
    }

    public function isJurist(): bool
    {
        return $this->role === 'juriste';
    }

    public function isChefProjet(): bool
    {
        return $this->role === 'chef_projet';
    }

    /**
     * Get role label in French
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Admin',
            'assistant_dg' => 'Assistant DG',
            'dg' => 'Directeur Général',
            'directeur_ecole' => 'Directeur d\'École',
            'juriste' => 'Juriste',
            'chef_projet' => 'Chef de Projet',
            default => $this->role,
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
