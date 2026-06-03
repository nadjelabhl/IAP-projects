<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegalStep extends Model
{
    protected $table      = 'legal_steps';
    protected $primaryKey = 'id_phase';

    protected $fillable = ['order_number', 'name', 'percentage'];

    protected $casts = ['percentage' => 'decimal:2'];

    public function getIdAttribute(): int { return $this->id_phase; }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order_number', 'asc');
    }
}
