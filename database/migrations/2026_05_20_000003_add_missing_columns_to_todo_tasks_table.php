<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->string('pdf_path')->nullable()->after('sort_order');
            $table->boolean('is_deletable')->default(true)->after('pdf_path');
            $table->timestamp('checked_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('todo_tasks', function (Blueprint $table) {
            $table->dropColumn(['pdf_path', 'is_deletable', 'checked_at']);
        });
    }
};
