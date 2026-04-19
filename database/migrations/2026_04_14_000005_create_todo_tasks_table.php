<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todo_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('created_by');
            $table->string('title', 255);
            $table->decimal('percentage', 5, 2);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users');

            $table->index(['project_id', 'sort_order']);
            $table->index(['project_id', 'is_completed']);
        });

        // MySQL 8+: enforce the same CHECK constraint as in your SQL
        DB::statement(
            "ALTER TABLE todo_tasks
             ADD CONSTRAINT chk_todo_tasks_percentage
             CHECK (percentage > 0 AND percentage <= 100)"
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_tasks');
    }
};

