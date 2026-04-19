<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_archives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id')->unique();

            $table->string('school_name', 100);
            $table->string('project_title', 255);
            $table->string('nature_name', 100);
            $table->string('project_type', 50);

            $table->decimal('total_budget', 15, 2);
            $table->decimal('total_spent', 15, 2)->default(0);
            $table->decimal('budget_restant', 15, 2)->default(0);

            $table->year('start_year')->nullable();
            $table->year('end_year')->nullable();

            $table->string('juriste_name', 100)->nullable();
            $table->string('chef_name', 100)->nullable();

            $table->integer('ods_count')->default(0);
            $table->integer('task_count')->default(0);
            $table->integer('tasks_done')->default(0);
            $table->integer('expense_count')->default(0);

            $table->timestamp('archived_at')->useCurrent();

            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_archives');
    }
};

