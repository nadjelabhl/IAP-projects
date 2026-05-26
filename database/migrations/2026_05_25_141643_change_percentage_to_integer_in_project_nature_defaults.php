<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('project_nature_defaults')->update([
            'percentage' => DB::raw('ROUND(percentage)'),
        ]);

        Schema::table('project_nature_defaults', function (Blueprint $table) {
            $table->unsignedSmallInteger('percentage')->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_nature_defaults', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)->change();
        });
    }
};
