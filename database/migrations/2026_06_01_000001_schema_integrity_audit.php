<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Composite index (school_id, status) on projects for director/assistant dashboard queries
        //    Replaces the individual (school_id, nature_id) scan when filtering by status within a school.
        Schema::table('projects', function (Blueprint $table) {
            if (!$this->indexExists('projects', 'projects_school_id_status_index')) {
                $table->index(['school_id', 'status'], 'projects_school_id_status_index');
            }
        });

        // 2. Index on notifications(user_id, created_at) for ordered notification feeds
        Schema::table('notifications', function (Blueprint $table) {
            if (!$this->indexExists('notifications', 'notifications_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'notifications_user_id_created_at_index');
            }
        });

        // 3. Index on expenses(entered_by) – InnoDB creates FK indexes automatically,
        //    but an explicit named index makes query plans predictable.
        Schema::table('expenses', function (Blueprint $table) {
            if (!$this->indexExists('expenses', 'expenses_entered_by_index')) {
                $table->index('entered_by', 'expenses_entered_by_index');
            }
        });

        // 4. Index on ods_records(issued_by) for the same reason as expenses above.
        Schema::table('ods_records', function (Blueprint $table) {
            if (!$this->indexExists('ods_records', 'ods_records_issued_by_index')) {
                $table->index('issued_by', 'ods_records_issued_by_index');
            }
        });

        // 5. Ensure project_archives.project_id UNIQUE constraint is present
        //    (was declared unique in the original migration; this is a guard for environments
        //    where it may have been dropped accidentally).
        // Note: uniqueness is enforced by the original migration; no change needed here.

        // 6. sessions.user_id: add index if somehow missing (defensive guard).
        //    The migration already uses $table->index('user_id') via foreignId definition.
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('projects_school_id_status_index');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('notifications_user_id_created_at_index');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex('expenses_entered_by_index');
        });

        Schema::table('ods_records', function (Blueprint $table) {
            $table->dropIndex('ods_records_issued_by_index');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return count($indexes) > 0;
    }
};
