<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Doublon de todo_tasks — LegalStep model pointe vers todo_tasks
        Schema::dropIfExists('legal_steps');

        // Tables de queue Laravel — aucune queue n'est utilisée dans cette application
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
    }

    public function down(): void
    {
        // Pas de rollback — ces tables ne font pas partie du domaine métier
    }
};
