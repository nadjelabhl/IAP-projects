<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectNatureDefault extends Model
{
    protected $table = 'project_nature_defaults';

    protected $fillable = [
        'order_number',
        'name',
        'percentage',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
    ];
}
