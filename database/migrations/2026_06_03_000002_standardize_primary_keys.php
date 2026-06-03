<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // STEP 1 : Drop FK constraints that reference non-standard PKs
        // ─────────────────────────────────────────────────────────────────────

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_school_id_foreign');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_school_id_foreign');
            $table->dropForeign('projects_nature_id_foreign');
        });
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->dropForeign('todo_tasks_project_id_foreign');
        });
        Schema::table('ods_records', function (Blueprint $table) {
            $table->dropForeign('ods_records_project_id_foreign');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_project_id_foreign');
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign('notifications_project_id_foreign');
        });
        Schema::table('project_archives', function (Blueprint $table) {
            $table->dropForeign('project_archives_project_id_foreign');
        });

        // ─────────────────────────────────────────────────────────────────────
        // STEP 2 : Rename all non-standard PKs back to the Laravel default `id`
        // ─────────────────────────────────────────────────────────────────────

        Schema::table('schools',        fn($t) => $t->renameColumn('id_school',      'id'));
        Schema::table('project_natures',fn($t) => $t->renameColumn('id_nature',      'id'));
        Schema::table('projects',       fn($t) => $t->renameColumn('id_project',     'id'));
        Schema::table('todo_tasks',     fn($t) => $t->renameColumn('id_phase',       'id'));
        Schema::table('ods_records',    fn($t) => $t->renameColumn('id_ods',         'id'));
        Schema::table('expenses',       fn($t) => $t->renameColumn('id_attachements','id'));
        Schema::table('notifications',  fn($t) => $t->renameColumn('id_notification','id'));
        Schema::table('legal_steps',    fn($t) => $t->renameColumn('id_phase',       'id'));

        // ─────────────────────────────────────────────────────────────────────
        // STEP 3 : Re-add FK constraints pointing to the standard `id` column
        // ─────────────────────────────────────────────────────────────────────

        Schema::table('users', fn($t) =>
            $t->foreign('school_id')->references('id')->on('schools')->nullOnDelete());

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('school_id')->references('id')->on('schools');
            $table->foreign('nature_id')->references('id')->on('project_natures');
        });

        Schema::table('todo_tasks',     fn($t) =>
            $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('ods_records',    fn($t) =>
            $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('expenses',       fn($t) =>
            $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('notifications',  fn($t) =>
            $t->foreign('project_id')->references('id')->on('projects')->nullOnDelete());
        Schema::table('project_archives', fn($t) =>
            $t->foreign('project_id')->references('id')->on('projects'));

        // ─────────────────────────────────────────────────────────────────────
        // STEP 4 : Seed legal_steps from project_nature_defaults so the admin
        //          interface and seedFromDefaults() work immediately after migrate
        // ─────────────────────────────────────────────────────────────────────

        if (DB::table('legal_steps')->count() === 0) {
            $defaults = DB::table('project_nature_defaults')
                ->orderBy('order_number')
                ->get(['name', 'percentage', 'order_number']);

            foreach ($defaults as $d) {
                DB::table('legal_steps')->insert([
                    'name_phase'   => $d->name,
                    'percentage'   => $d->percentage,
                    'order_number' => $d->order_number,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Drop FK constraints
        Schema::table('project_archives', fn($t) => $t->dropForeign('project_archives_project_id_foreign'));
        Schema::table('notifications',    fn($t) => $t->dropForeign('notifications_project_id_foreign'));
        Schema::table('expenses',         fn($t) => $t->dropForeign('expenses_project_id_foreign'));
        Schema::table('ods_records',      fn($t) => $t->dropForeign('ods_records_project_id_foreign'));
        Schema::table('todo_tasks',       fn($t) => $t->dropForeign('todo_tasks_project_id_foreign'));
        Schema::table('projects', function ($t) {
            $t->dropForeign('projects_nature_id_foreign');
            $t->dropForeign('projects_school_id_foreign');
        });
        Schema::table('users', fn($t) => $t->dropForeign('users_school_id_foreign'));

        // Reverse renames
        Schema::table('legal_steps',     fn($t) => $t->renameColumn('id', 'id_phase'));
        Schema::table('notifications',   fn($t) => $t->renameColumn('id', 'id_notification'));
        Schema::table('expenses',        fn($t) => $t->renameColumn('id', 'id_attachements'));
        Schema::table('ods_records',     fn($t) => $t->renameColumn('id', 'id_ods'));
        Schema::table('todo_tasks',      fn($t) => $t->renameColumn('id', 'id_phase'));
        Schema::table('projects',        fn($t) => $t->renameColumn('id', 'id_project'));
        Schema::table('project_natures', fn($t) => $t->renameColumn('id', 'id_nature'));
        Schema::table('schools',         fn($t) => $t->renameColumn('id', 'id_school'));

        // Restore FK constraints
        Schema::table('users', fn($t) =>
            $t->foreign('school_id')->references('id_school')->on('schools')->nullOnDelete());
        Schema::table('projects', function ($t) {
            $t->foreign('school_id')->references('id_school')->on('schools');
            $t->foreign('nature_id')->references('id_nature')->on('project_natures');
        });
        Schema::table('todo_tasks',     fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete());
        Schema::table('ods_records',    fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete());
        Schema::table('expenses',       fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete());
        Schema::table('notifications',  fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->nullOnDelete());
        Schema::table('project_archives', fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects'));
    }
};
