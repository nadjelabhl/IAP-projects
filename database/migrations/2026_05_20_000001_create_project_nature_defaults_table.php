<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_nature_defaults', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_number');
            $table->string('name', 255);
            $table->decimal('percentage', 5, 2);
            $table->timestamps();

            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_nature_defaults');
    }
};
