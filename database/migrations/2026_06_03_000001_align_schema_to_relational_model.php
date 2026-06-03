<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // STEP 1 : Drop FK constraints that reference PKs being renamed
        // ─────────────────────────────────────────────────────────────────────

        // FKs referencing schools.id
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_school_id_foreign');
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_school_id_foreign');
            $table->dropForeign('projects_nature_id_foreign');
        });

        // FKs referencing projects.id  (+ dropped columns)
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->dropForeign('todo_tasks_project_id_foreign');
            $table->dropForeign('todo_tasks_created_by_foreign');
        });
        Schema::table('ods_records', function (Blueprint $table) {
            $table->dropForeign('ods_records_project_id_foreign');
        });
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign('expenses_project_id_foreign');
            $table->dropForeign('expenses_entered_by_foreign');
        });
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign('notifications_project_id_foreign');
        });
        Schema::table('project_archives', function (Blueprint $table) {
            $table->dropForeign('project_archives_project_id_foreign');
        });

        // ─────────────────────────────────────────────────────────────────────
        // STEP 2 : Rename columns in schools & project_natures
        // ─────────────────────────────────────────────────────────────────────

        Schema::table('schools', function (Blueprint $table) {
            $table->renameColumn('id',   'id_school');
            $table->renameColumn('name', 'name_school');
        });

        Schema::table('project_natures', function (Blueprint $table) {
            $table->renameColumn('id',   'id_nature');
            $table->renameColumn('name', 'name_nature');
        });

        // ─────────────────────────────────────────────────────────────────────
        // STEP 3 : Rename columns in projects
        // ─────────────────────────────────────────────────────────────────────

        Schema::table('projects', function (Blueprint $table) {
            $table->renameColumn('id',      'id_project');
            $table->renameColumn('title',   'title_project');
            $table->renameColumn('type',    'type_project');
            $table->renameColumn('address', 'localisation');
            $table->renameColumn('start_year', 'start_date');
            $table->renameColumn('end_year',   'end_date');
        });

        // Drop columns removed from the new model
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['dg_consulted_at', 'school_director_viewed_at']);
        });

        // Add new columns required by the new model
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('visibility_school')->default(false)->after('budget_alert_sent');
            $table->date('date_vis_budget')->nullable()->after('visibility_school');
        });

        // ─────────────────────────────────────────────────────────────────────
        // STEP 4 : Rename columns in todo_tasks, ods_records, expenses, notifications
        // ─────────────────────────────────────────────────────────────────────

        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->renameColumn('id',       'id_phase');
            $table->renameColumn('title',    'title_phase');
            $table->renameColumn('pdf_path', 'todo_tasks_pdf_path');
        });

        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });

        Schema::table('ods_records', function (Blueprint $table) {
            $table->renameColumn('id',       'id_ods');
            $table->renameColumn('type',     'type_ods');
            $table->renameColumn('pdf_path', 'ods_record_pdf_path');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->renameColumn('id',           'id_attachements');
            $table->renameColumn('expense_date', 'attachement_date');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('entered_by');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('id',   'id_notification');
            $table->renameColumn('type', 'type_notification');
        });

        // ─────────────────────────────────────────────────────────────────────
        // STEP 5 : Re-add FK constraints pointing to the new PK column names
        // ─────────────────────────────────────────────────────────────────────

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('school_id')->references('id_school')->on('schools')->nullOnDelete();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('school_id')->references('id_school')->on('schools');
            $table->foreign('nature_id')->references('id_nature')->on('project_natures');
        });

        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete();
        });

        Schema::table('ods_records', function (Blueprint $table) {
            $table->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete();
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('project_id')->references('id_project')->on('projects')->nullOnDelete();
        });

        Schema::table('project_archives', function (Blueprint $table) {
            $table->foreign('project_id')->references('id_project')->on('projects');
        });

        // ─────────────────────────────────────────────────────────────────────
        // STEP 6 : Create the new legal_steps reference table
        // ─────────────────────────────────────────────────────────────────────

        Schema::create('legal_steps', function (Blueprint $table) {
            $table->bigIncrements('id_phase');
            $table->string('name_phase', 255);
            $table->decimal('percentage', 5, 2);
            $table->unsignedInteger('order_number')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_steps');

        // Reverse FK constraints
        Schema::table('project_archives', fn($t) => $t->dropForeign('project_archives_project_id_foreign'));
        Schema::table('notifications',    fn($t) => $t->dropForeign('notifications_project_id_foreign'));
        Schema::table('expenses',         fn($t) => $t->dropForeign('expenses_project_id_foreign'));
        Schema::table('ods_records',      fn($t) => $t->dropForeign('ods_records_project_id_foreign'));
        Schema::table('todo_tasks',       fn($t) => $t->dropForeign('todo_tasks_project_id_foreign'));
        Schema::table('projects',         function ($t) {
            $t->dropForeign('projects_nature_id_foreign');
            $t->dropForeign('projects_school_id_foreign');
        });
        Schema::table('users', fn($t) => $t->dropForeign('users_school_id_foreign'));

        // Reverse all renames
        Schema::table('notifications', fn($t) => $t->renameColumn('id_notification', 'id'));
        Schema::table('notifications', fn($t) => $t->renameColumn('type_notification', 'type'));
        Schema::table('expenses',      fn($t) => $t->renameColumn('id_attachements', 'id'));
        Schema::table('expenses',      fn($t) => $t->renameColumn('attachement_date', 'expense_date'));
        Schema::table('ods_records',   fn($t) => $t->renameColumn('id_ods', 'id'));
        Schema::table('ods_records',   fn($t) => $t->renameColumn('type_ods', 'type'));
        Schema::table('ods_records',   fn($t) => $t->renameColumn('ods_record_pdf_path', 'pdf_path'));
        Schema::table('todo_tasks',    fn($t) => $t->renameColumn('id_phase', 'id'));
        Schema::table('todo_tasks',    fn($t) => $t->renameColumn('title_phase', 'title'));
        Schema::table('todo_tasks',    fn($t) => $t->renameColumn('todo_tasks_pdf_path', 'pdf_path'));
        Schema::table('projects',      function ($t) {
            $t->renameColumn('id_project',   'id');
            $t->renameColumn('title_project','title');
            $t->renameColumn('type_project', 'type');
            $t->renameColumn('localisation', 'address');
            $t->renameColumn('start_date',   'start_year');
            $t->renameColumn('end_date',     'end_year');
        });
        Schema::table('project_natures', fn($t) => $t->renameColumn('id_nature',   'id'));
        Schema::table('project_natures', fn($t) => $t->renameColumn('name_nature', 'name'));
        Schema::table('schools',         fn($t) => $t->renameColumn('id_school',   'id'));
        Schema::table('schools',         fn($t) => $t->renameColumn('name_school', 'name'));

        // Re-add dropped columns
        Schema::table('todo_tasks', fn($t) => $t->unsignedBigInteger('created_by')->nullable()->after('project_id'));
        Schema::table('expenses',   fn($t) => $t->unsignedBigInteger('entered_by')->nullable()->after('project_id'));
        Schema::table('projects',   function ($t) {
            $t->timestamp('dg_consulted_at')->nullable();
            $t->timestamp('school_director_viewed_at')->nullable();
            $t->dropColumn(['visibility_school', 'date_vis_budget']);
        });

        // Restore FKs
        Schema::table('users',    fn($t) => $t->foreign('school_id')->references('id')->on('schools')->nullOnDelete());
        Schema::table('projects', function ($t) {
            $t->foreign('school_id')->references('id')->on('schools');
            $t->foreign('nature_id')->references('id')->on('project_natures');
        });
        Schema::table('todo_tasks',   fn($t) => $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('ods_records',  fn($t) => $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('expenses',     fn($t) => $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('notifications',fn($t) => $t->foreign('project_id')->references('id')->on('projects')->nullOnDelete());
        Schema::table('project_archives', fn($t) => $t->foreign('project_id')->references('id')->on('projects'));
    }
};
