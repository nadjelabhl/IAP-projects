<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdsRecord extends Model
{
    protected $table      = 'ods_records';
    protected $primaryKey = 'id_ods';

    public function getIdAttribute(): int { return $this->id_ods; }

    protected $fillable = [
        'project_id',
        'issued_by',
        'type_ods',
        'notes',
        'ods_record_pdf_path',
        'issued_at',
    ];

    protected $casts = ['issued_at' => 'datetime'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function scopeForProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type_ods', $type);
    }

    public function scopeStarting(Builder $query): Builder { return $query->ofType('Demarrage'); }
    public function scopeStopping(Builder $query): Builder { return $query->ofType('Arret'); }
    public function scopeResuming(Builder $query): Builder { return $query->ofType('Reprise'); }

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('issued_at', 'desc');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type_ods) {
            'Demarrage' => 'Démarrage',
            'Arret'     => 'Arrêt',
            'Reprise'   => 'Reprise',
            default     => $this->type_ods,
        };
    }
}
