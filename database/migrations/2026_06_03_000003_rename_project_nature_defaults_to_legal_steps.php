<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('project_nature_defaults', 'legal_steps');
    }

    public function down(): void
    {
        Schema::rename('legal_steps', 'project_nature_defaults');
    }
};
