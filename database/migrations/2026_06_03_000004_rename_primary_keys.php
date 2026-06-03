<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── PHASE 1: drop every FK that references a PK being renamed ─────────

        Schema::table('users', fn($t) =>
            $t->dropForeign(['school_id']));                     // → schools.id

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['school_id']);                  // → schools.id
            $table->dropForeign(['nature_id']);                  // → project_natures.id
        });

        Schema::table('todo_tasks', fn($t) =>
            $t->dropForeign(['project_id']));                    // → projects.id

        Schema::table('ods_records', fn($t) =>
            $t->dropForeign(['project_id']));                    // → projects.id

        Schema::table('expenses', fn($t) =>
            $t->dropForeign(['project_id']));                    // → projects.id

        Schema::table('notifications', fn($t) =>
            $t->dropForeign(['project_id']));                    // → projects.id

        // ── PHASE 2: rename PK columns ────────────────────────────────────────

        DB::statement('ALTER TABLE `schools`         RENAME COLUMN `id` TO `id_school`');
        DB::statement('ALTER TABLE `project_natures` RENAME COLUMN `id` TO `id_nature`');
        DB::statement('ALTER TABLE `legal_steps`     RENAME COLUMN `id` TO `id_phase`');
        DB::statement('ALTER TABLE `projects`        RENAME COLUMN `id` TO `id_project`');
        DB::statement('ALTER TABLE `todo_tasks`      RENAME COLUMN `id` TO `id_phase`');
        DB::statement('ALTER TABLE `ods_records`     RENAME COLUMN `id` TO `id_ods`');
        DB::statement('ALTER TABLE `expenses`        RENAME COLUMN `id` TO `id_attachements`');
        DB::statement('ALTER TABLE `notifications`   RENAME COLUMN `id` TO `id_notification`');

        // ── PHASE 3: re-add FK constraints with updated references ────────────

        Schema::table('users', fn($t) =>
            $t->foreign('school_id')->references('id_school')->on('schools')->nullOnDelete());

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('school_id')->references('id_school')->on('schools');
            $table->foreign('nature_id')->references('id_nature')->on('project_natures');
        });

        Schema::table('todo_tasks', fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete());

        Schema::table('ods_records', fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete());

        Schema::table('expenses', fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->cascadeOnDelete());

        Schema::table('notifications', fn($t) =>
            $t->foreign('project_id')->references('id_project')->on('projects')->nullOnDelete());
    }

    public function down(): void
    {
        Schema::table('users',         fn($t) => $t->dropForeign(['school_id']));
        Schema::table('projects',      function ($t) {
            $t->dropForeign(['school_id']);
            $t->dropForeign(['nature_id']);
        });
        Schema::table('todo_tasks',    fn($t) => $t->dropForeign(['project_id']));
        Schema::table('ods_records',   fn($t) => $t->dropForeign(['project_id']));
        Schema::table('expenses',      fn($t) => $t->dropForeign(['project_id']));
        Schema::table('notifications', fn($t) => $t->dropForeign(['project_id']));

        DB::statement('ALTER TABLE `schools`         RENAME COLUMN `id_school`       TO `id`');
        DB::statement('ALTER TABLE `project_natures` RENAME COLUMN `id_nature`       TO `id`');
        DB::statement('ALTER TABLE `legal_steps`     RENAME COLUMN `id_phase`        TO `id`');
        DB::statement('ALTER TABLE `projects`        RENAME COLUMN `id_project`      TO `id`');
        DB::statement('ALTER TABLE `todo_tasks`      RENAME COLUMN `id_phase`        TO `id`');
        DB::statement('ALTER TABLE `ods_records`     RENAME COLUMN `id_ods`          TO `id`');
        DB::statement('ALTER TABLE `expenses`        RENAME COLUMN `id_attachements` TO `id`');
        DB::statement('ALTER TABLE `notifications`   RENAME COLUMN `id_notification` TO `id`');

        Schema::table('users',         fn($t) => $t->foreign('school_id')->references('id')->on('schools')->nullOnDelete());
        Schema::table('projects',      function ($t) {
            $t->foreign('school_id')->references('id')->on('schools');
            $t->foreign('nature_id')->references('id')->on('project_natures');
        });
        Schema::table('todo_tasks',    fn($t) => $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('ods_records',   fn($t) => $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('expenses',      fn($t) => $t->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete());
        Schema::table('notifications', fn($t) => $t->foreign('project_id')->references('id')->on('projects')->nullOnDelete());
    }
};
