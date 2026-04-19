<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);

            // Utilisation de foreignId pour plus de clarté
            $table->foreignId('nature_id')->constrained('project_natures');
            $table->enum('type', ['Investissement', 'Exploitation']);
            $table->foreignId('school_id')->constrained('schools');
            $table->foreignId('created_by')->constrained('users');

            $table->decimal('budget', 15, 2);
            $table->unsignedInteger('duration_months');
            $table->year('start_year');
            $table->year('end_year');
            $table->text('address')->nullable();
            $table->text('description')->nullable();

            // Status conforme à ton workflow IAP
            $table->enum('status', ['Nouveau', 'En Etude', 'En Cours', 'Termine'])->default('Nouveau');

            // Juriste et CP (nullables car assignés plus tard par le Directeur)
            $table->foreignId('juriste_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('chef_projet_id')->nullable()->constrained('users')->nullOnDelete();

            // Tes flags de logique métier (très bien pensé !)
            $table->boolean('chef_access_unlocked')->default(false); // Sera true quand l'ODS est validé
            $table->timestamp('dg_consulted_at')->nullable();
            $table->boolean('budget_alert_sent')->default(false);
            $table->timestamp('closed_at')->nullable();

            $table->timestamps(); // Remplace created_at et updated_at manuels, c'est plus propre

            // Index pour la performance des recherches Sonatrach/IAP
            $table->index(['school_id', 'nature_id']);
            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};