<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->after('dg_consulted_at');
            $table->timestamp('school_director_viewed_at')->nullable()->after('started_at');
            $table->string('pdf_path')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['started_at', 'school_director_viewed_at', 'pdf_path']);
        });
    }
};
