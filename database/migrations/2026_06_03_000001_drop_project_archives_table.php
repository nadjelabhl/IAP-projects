<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('project_archives');
    }

    public function down(): void
    {
        // L'archive est la table projects elle-même (status = 'Termine')
    }
};
