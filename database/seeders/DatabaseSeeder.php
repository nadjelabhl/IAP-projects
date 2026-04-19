<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectNature;
use App\Models\School;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =====================================================================
        // 1. CRÉER LES 4 ÉCOLES
        // =====================================================================
        $schools = [
            ['name' => 'École Boumerdes', 'location' => 'Boumerdes', 'annual_budget' => 500000000],
            ['name' => 'École Arzew', 'location' => 'Arzew', 'annual_budget' => 450000000],
            ['name' => 'École Skikda', 'location' => 'Skikda', 'annual_budget' => 480000000],
            ['name' => 'École Hassi Messaoud', 'location' => 'Hassi Messaoud', 'annual_budget' => 520000000],
        ];

        $schoolModels = [];
        foreach ($schools as $schoolData) {
            $schoolModels[] = School::create($schoolData);
        }

        // =====================================================================
        // 2. CRÉER LES 5 NATURES DE PROJETS
        // =====================================================================
        $natures = [
            ['name' => 'Études', 'is_active' => true],
            ['name' => 'Constructions', 'is_active' => true],
            ['name' => 'Aménagements', 'is_active' => true],
            ['name' => 'Équipements', 'is_active' => true],
            ['name' => 'Terrains', 'is_active' => true],
        ];

        $natureModels = [];
        foreach ($natures as $natureData) {
            $natureModels[] = ProjectNature::create($natureData);
        }

        // =====================================================================
        // 3. CRÉER LES UTILISATEURS (23 au total, 6 rôles)
        // =====================================================================
        $users = [];

        // Admin (1)
        $users[] = User::create([
            'name' => 'Admin IAP',
            'email' => 'admin@iap.sonatrach.dz',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'school_id' => null,
            'is_active' => true,
        ]);

        // Assistant DG (1)
        $users[] = User::create([
            'name' => 'Assistant DG',
            'email' => 'assistant.dg@iap.sonatrach.dz',
            'password' => Hash::make('password123'),
            'role' => 'assistant_dg',
            'school_id' => null,
            'is_active' => true,
        ]);

        // DG (1)
        $users[] = User::create([
            'name' => 'Directeur Général',
            'email' => 'dg@iap.sonatrach.dz',
            'password' => Hash::make('password123'),
            'role' => 'dg',
            'school_id' => null,
            'is_active' => true,
        ]);

        // Directeurs d'École (4 - un par école)
        foreach ($schoolModels as $index => $school) {
            $users[] = User::create([
                'name' => "Directeur École " . ($index + 1),
                'email' => "directeur.ecole" . ($index + 1) . "@iap.sonatrach.dz",
                'password' => Hash::make('password123'),
                'role' => 'directeur_ecole',
                'school_id' => $school->id,
                'is_active' => true,
            ]);
        }

        // Juristes (8 - 2 par école)
        foreach ($schoolModels as $schoolIndex => $school) {
            for ($i = 1; $i <= 2; $i++) {
                $users[] = User::create([
                    'name' => "Juriste École " . ($schoolIndex + 1) . " #" . $i,
                    'email' => "juriste.ecole" . ($schoolIndex + 1) . "." . $i . "@iap.sonatrach.dz",
                    'password' => Hash::make('password123'),
                    'role' => 'juriste',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);
            }
        }

        // Chefs de Projet (8 - 2 par école)
        foreach ($schoolModels as $schoolIndex => $school) {
            for ($i = 1; $i <= 2; $i++) {
                $users[] = User::create([
                    'name' => "Chef de Projet École " . ($schoolIndex + 1) . " #" . $i,
                    'email' => "chef.projet.ecole" . ($schoolIndex + 1) . "." . $i . "@iap.sonatrach.dz",
                    'password' => Hash::make('password123'),
                    'role' => 'chef_projet',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);
            }
        }

        // Indexer les utilisateurs par rôle pour faciliter les assignations
        $usersByRole = [];
        foreach ($users as $user) {
            if (!isset($usersByRole[$user->role])) {
                $usersByRole[$user->role] = [];
            }
            $usersByRole[$user->role][] = $user;
        }

        // =====================================================================
        // 4. CRÉER LES PROJETS DE TEST (4 projets dans les 4 statuts)
        // =====================================================================

        // Projet 1 : Statut NOUVEAU
        $project1 = Project::create([
            'title' => 'Étude de Faisabilité - Boumerdes',
            'nature_id' => $natureModels[0]->id, // Études
            'type' => 'Investissement',
            'school_id' => $schoolModels[0]->id, // Boumerdes
            'created_by' => $usersByRole['assistant_dg'][0]->id,
            'budget' => 50000000,
            'duration_months' => 6,
            'start_year' => 2026,
            'end_year' => 2026,
            'address' => 'Boumerdes, Algérie',
            'description' => 'Étude de faisabilité pour le projet de développement',
            'status' => 'Nouveau',
            'juriste_id' => null,
            'chef_projet_id' => null,
            'chef_access_unlocked' => false,
            'budget_alert_sent' => false,
        ]);

        // Projet 2 : Statut EN ÉTUDE (avec juriste assigné)
        $project2 = Project::create([
            'title' => 'Construction Centre de Formation - Arzew',
            'nature_id' => $natureModels[1]->id, // Constructions
            'type' => 'Investissement',
            'school_id' => $schoolModels[1]->id, // Arzew
            'created_by' => $usersByRole['assistant_dg'][0]->id,
            'budget' => 120000000,
            'duration_months' => 18,
            'start_year' => 2026,
            'end_year' => 2027,
            'address' => 'Arzew, Algérie',
            'description' => 'Construction d\'un centre de formation moderne',
            'status' => 'En Etude',
            'juriste_id' => $usersByRole['juriste'][2]->id, // Juriste Arzew #1
            'chef_projet_id' => null,
            'chef_access_unlocked' => false,
            'budget_alert_sent' => false,
        ]);

        // Projet 3 : Statut EN COURS (avec ODS émise, Chef de Projet assigné)
        $project3 = Project::create([
            'title' => 'Aménagement Laboratoire - Skikda',
            'nature_id' => $natureModels[2]->id, // Aménagements
            'type' => 'Exploitation',
            'school_id' => $schoolModels[2]->id, // Skikda
            'created_by' => $usersByRole['assistant_dg'][0]->id,
            'budget' => 80000000,
            'duration_months' => 12,
            'start_year' => 2026,
            'end_year' => 2026,
            'address' => 'Skikda, Algérie',
            'description' => 'Aménagement complet du laboratoire principal',
            'status' => 'En Cours',
            'juriste_id' => $usersByRole['juriste'][4]->id, // Juriste Skikda #1
            'chef_projet_id' => $usersByRole['chef_projet'][4]->id, // Chef Skikda #1
            'chef_access_unlocked' => true,
            'budget_alert_sent' => false,
        ]);

        // Projet 4 : Statut TERMINÉ (archivé)
        $project4 = Project::create([
            'title' => 'Installation Équipements - Hassi Messaoud',
            'nature_id' => $natureModels[3]->id, // Équipements
            'type' => 'Investissement',
            'school_id' => $schoolModels[3]->id, // Hassi Messaoud
            'created_by' => $usersByRole['assistant_dg'][0]->id,
            'budget' => 95000000,
            'duration_months' => 9,
            'start_year' => 2025,
            'end_year' => 2026,
            'address' => 'Hassi Messaoud, Algérie',
            'description' => 'Installation des équipements de recherche',
            'status' => 'Termine',
            'juriste_id' => $usersByRole['juriste'][6]->id, // Juriste Hassi #1
            'chef_projet_id' => $usersByRole['chef_projet'][6]->id, // Chef Hassi #1
            'chef_access_unlocked' => true,
            'budget_alert_sent' => false,
            'closed_at' => now()->subDays(30),
        ]);

        // =====================================================================
        // 5. CRÉER LES TÂCHES (TO-DO LIST) POUR LES PROJETS EN ÉTUDE ET EN COURS
        // =====================================================================

        // Tâches pour Projet 2 (EN ÉTUDE) - Somme = 100%
        Task::create([
            'project_id' => $project2->id,
            'created_by' => $usersByRole['juriste'][2]->id,
            'title' => 'Validation juridique des contrats',
            'percentage' => 35,
            'is_completed' => true,
            'completed_at' => now()->subDays(5),
            'sort_order' => 1,
        ]);

        Task::create([
            'project_id' => $project2->id,
            'created_by' => $usersByRole['juriste'][2]->id,
            'title' => 'Vérification conformité réglementaire',
            'percentage' => 30,
            'is_completed' => true,
            'completed_at' => now()->subDays(3),
            'sort_order' => 2,
        ]);

        Task::create([
            'project_id' => $project2->id,
            'created_by' => $usersByRole['juriste'][2]->id,
            'title' => 'Approbation des autorités compétentes',
            'percentage' => 35,
            'is_completed' => false,
            'completed_at' => null,
            'sort_order' => 3,
        ]);

        // Tâches pour Projet 3 (EN COURS) - Somme = 100% (déjà complétées pour ODS)
        Task::create([
            'project_id' => $project3->id,
            'created_by' => $usersByRole['juriste'][4]->id,
            'title' => 'Validation des plans techniques',
            'percentage' => 40,
            'is_completed' => true,
            'completed_at' => now()->subDays(15),
            'sort_order' => 1,
        ]);

        Task::create([
            'project_id' => $project3->id,
            'created_by' => $usersByRole['juriste'][4]->id,
            'title' => 'Approbation budgétaire',
            'percentage' => 30,
            'is_completed' => true,
            'completed_at' => now()->subDays(12),
            'sort_order' => 2,
        ]);

        Task::create([
            'project_id' => $project3->id,
            'created_by' => $usersByRole['juriste'][4]->id,
            'title' => 'Signature des documents',
            'percentage' => 30,
            'is_completed' => true,
            'completed_at' => now()->subDays(10),
            'sort_order' => 3,
        ]);

        // =====================================================================
        // 6. CRÉER LES DÉPENSES (EXPENSES) POUR LES PROJETS EN COURS ET TERMINÉ
        // =====================================================================

        // Dépenses pour Projet 3 (EN COURS) - 75% du budget (pas d'alerte 80%)
        Expense::create([
            'project_id' => $project3->id,
            'entered_by' => $usersByRole['chef_projet'][4]->id,
            'description' => 'Matériaux de construction',
            'amount' => 30000000,
            'expense_date' => now()->subDays(20),
        ]);

        Expense::create([
            'project_id' => $project3->id,
            'entered_by' => $usersByRole['chef_projet'][4]->id,
            'description' => 'Main d\'œuvre',
            'amount' => 25000000,
            'expense_date' => now()->subDays(15),
        ]);

        Expense::create([
            'project_id' => $project3->id,
            'entered_by' => $usersByRole['chef_projet'][4]->id,
            'description' => 'Équipements spécialisés',
            'amount' => 25000000,
            'expense_date' => now()->subDays(10),
        ]);

        // Dépenses pour Projet 4 (TERMINÉ) - 100% du budget
        Expense::create([
            'project_id' => $project4->id,
            'entered_by' => $usersByRole['chef_projet'][6]->id,
            'description' => 'Équipements de recherche',
            'amount' => 60000000,
            'expense_date' => now()->subDays(60),
        ]);

        Expense::create([
            'project_id' => $project4->id,
            'entered_by' => $usersByRole['chef_projet'][6]->id,
            'description' => 'Installation et configuration',
            'amount' => 25000000,
            'expense_date' => now()->subDays(45),
        ]);

        Expense::create([
            'project_id' => $project4->id,
            'entered_by' => $usersByRole['chef_projet'][6]->id,
            'description' => 'Formation du personnel',
            'amount' => 10000000,
            'expense_date' => now()->subDays(30),
        ]);

        // =====================================================================
        // 7. CRÉER LES NOTIFICATIONS DE TEST
        // =====================================================================

        // Notification pour DG : Nouveau projet soumis
        Notification::create([
            'user_id' => $usersByRole['dg'][0]->id,
            'project_id' => $project1->id,
            'type' => 'nouveau_projet',
            'message' => 'Un nouveau projet a été soumis : "' . $project1->title . '"',
            'priority' => 'normal',
            'is_read' => false,
        ]);

        // Notification pour Directeur Arzew : Projet transmis
        Notification::create([
            'user_id' => $schoolModels[1]->users()->where('role', 'directeur_ecole')->first()->id,
            'project_id' => $project2->id,
            'type' => 'projet_transmis',
            'message' => 'Le projet "' . $project2->title . '" vous a été transmis pour affectation.',
            'priority' => 'normal',
            'is_read' => false,
        ]);

        // Notification pour Juriste Arzew : Affectation
        Notification::create([
            'user_id' => $usersByRole['juriste'][2]->id,
            'project_id' => $project2->id,
            'type' => 'affectation',
            'message' => 'Vous avez été affecté au projet "' . $project2->title . '" en tant que Juriste.',
            'priority' => 'urgent',
            'is_read' => false,
        ]);

        // Notification pour Chef Skikda : ODS émise
        Notification::create([
            'user_id' => $usersByRole['chef_projet'][4]->id,
            'project_id' => $project3->id,
            'type' => 'ods_emise',
            'message' => 'L\'ODS du projet "' . $project3->title . '" a été émise. Votre accès est maintenant débloqué.',
            'priority' => 'urgent',
            'is_read' => false,
        ]);

        // Notification pour Directeur Skikda : Alerte budget 80%
        Notification::create([
            'user_id' => $schoolModels[2]->users()->where('role', 'directeur_ecole')->first()->id,
            'project_id' => $project3->id,
            'type' => 'alerte_budget',
            'message' => 'ALERTE : Le projet "' . $project3->title . '" a atteint 75% de son budget.',
            'priority' => 'urgent',
            'is_read' => false,
        ]);

        // Notification pour Directeur Hassi : Archivage
        Notification::create([
            'user_id' => $schoolModels[3]->users()->where('role', 'directeur_ecole')->first()->id,
            'project_id' => $project4->id,
            'type' => 'archivage',
            'message' => 'Le projet "' . $project4->title . '" a été archivé avec succès.',
            'priority' => 'normal',
            'is_read' => true,
            'read_at' => now()->subDays(30),
        ]);

        $this->command->info('✅ DatabaseSeeder exécuté avec succès !');
        $this->command->info('   - 4 écoles créées');
        $this->command->info('   - 5 natures de projets créées');
        $this->command->info('   - 23 utilisateurs créés (6 rôles)');
        $this->command->info('   - 4 projets de test créés (4 statuts)');
        $this->command->info('   - 6 tâches créées');
        $this->command->info('   - 6 dépenses créées');
        $this->command->info('   - 7 notifications créées');
    }
}
