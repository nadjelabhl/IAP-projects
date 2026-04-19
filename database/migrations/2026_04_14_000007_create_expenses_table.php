<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('entered_by');
            $table->string('description', 255);
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('entered_by')->references('id')->on('users');

            $table->index(['project_id', 'expense_date']);
        });

        DB::statement(
            "ALTER TABLE expenses
             ADD CONSTRAINT chk_expenses_amount
             CHECK (amount > 0)"
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};

