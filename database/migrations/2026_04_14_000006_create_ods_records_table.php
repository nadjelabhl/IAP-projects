<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ods_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('issued_by');
            $table->enum('type', ['Demarrage', 'Arret', 'Reprise']);
            $table->text('notes')->nullable();
            $table->timestamp('issued_at')->useCurrent();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('issued_by')->references('id')->on('users');

            $table->index(['project_id', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ods_records');
    }
};

