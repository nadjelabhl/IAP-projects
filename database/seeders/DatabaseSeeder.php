<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\LegalStep;
use App\Models\Notification;
use App\Models\OdsRecord;
use App\Models\Project;
use App\Models\ProjectNature;
use App\Models\ProjectNatureDefault;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================================================================
        // 1. ÉCOLES
        // =====================================================================
        $schools = [];
        foreach (['Boumerdes', 'Arzew', 'Skikda', 'Hassi Messaoud'] as $name) {
            $schools[] = School::create(['name' => $name]);
        }

        // =====================================================================
        // 2. NATURES DE PROJETS
        // =====================================================================
        $natures = [];
        foreach ([
            'Études et Expertises',
            'Constructions et Infrastructures',
            'Aménagements et Rénovations',
            'Équipements et Matériels',
            'Terrains et Foncier',
        ] as $name) {
            $natures[] = ProjectNature::create(['name' => $name]);
        }

        // =====================================================================
        // 3. RÉFÉRENTIEL JURIDIQUE PAR DÉFAUT
        // =====================================================================
        $defaults = [
            [1, 'Constitution du dossier de Consultation', 15.00],
            [2, 'Validation Juridique',                    15.00],
            [3, 'Lancement de la consultation / AO',       20.00],
            [4, 'Évaluation des offres',                   15.00],
            [5, 'Désignation attributaire',                10.00],
            [6, 'Établissement du Contrat',                15.00],
            [7, 'Établissement ODS de Démarrage',          10.00],
        ];
        foreach ($defaults as [$order, $name, $pct]) {
            ProjectNatureDefault::create([
                'order_number' => $order,
                'name'         => $name,
                'percentage'   => $pct,
            ]);
        }

        // =====================================================================
        // 4. UTILISATEURS
        // =====================================================================
        $admin = User::create([
            'name'      => 'Administrateur IAP',
            'email'     => 'admin@iap.dz',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $assistantDg = User::create([
            'name'      => 'Karim Benali',
            'email'     => 'assistant.dg@iap.dz',
            'password'  => Hash::make('password'),
            'role'      => 'assistant_dg',
            'is_active' => true,
        ]);

        $dg = User::create([
            'name'      => 'Mohammed Aïssa',
            'email'     => 'dg@iap.dz',
            'password'  => Hash::make('password'),
            'role'      => 'dg',
            'is_active' => true,
        ]);

        $directors = [];
        $juristes  = [];
        $chefs     = [];

        $directorNames = ['Rachid Ferdi', 'Amel Kaci', 'Samir Hadj', 'Nadia Bouzid'];
        $juristeNames  = [
            ['Fatima Mahdi', 'Omar Sellam'],
            ['Lina Charef', 'Yacine Dali'],
            ['Samia Benkir', 'Bilal Rouane'],
            ['Amina Lounis', 'Kamel Zerga'],
        ];
        $chefNames = [
            ['Tarek Bensid', 'Meriem Ouali'],
            ['Fares Amrani', 'Sonia Haddad'],
            ['Ryad Drif', 'Dalila Mekki'],
            ['Aziz Touati', 'Nabila Chikh'],
        ];

        foreach ($schools as $i => $school) {
            $directors[$i] = User::create([
                'name'      => $directorNames[$i],
                'email'     => 'directeur.' . strtolower(str_replace(' ', '', $school->name)) . '@iap.dz',
                'password'  => Hash::make('password'),
                'role'      => 'directeur_ecole',
                'school_id' => $school->id,
                'is_active' => true,
            ]);

            foreach ($juristeNames[$i] as $j => $name) {
                $juristes[$i][$j] = User::create([
                    'name'      => $name,
                    'email'     => 'juriste' . ($j + 1) . '.' . strtolower(str_replace(' ', '', $school->name)) . '@iap.dz',
                    'password'  => Hash::make('password'),
                    'role'      => 'juriste',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);
            }

            foreach ($chefNames[$i] as $j => $name) {
                $chefs[$i][$j] = User::create([
                    'name'      => $name,
                    'email'     => 'chef' . ($j + 1) . '.' . strtolower(str_replace(' ', '', $school->name)) . '@iap.dz',
                    'password'  => Hash::make('password'),
                    'role'      => 'chef_projet',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);
            }
        }

        // =====================================================================
        // 5. PROJETS DE TEST
        // =====================================================================

        // Projet 1 — NOUVEAU (Boumerdes)
        $p1 = Project::create([
            'title'          => 'Étude de Faisabilité Infrastructure Pédagogique',
            'nature_id'      => $natures[0]->id,
            'type'           => 'Investissement',
            'school_id'      => $schools[0]->id,
            'created_by'     => $assistantDg->id,
            'budget'         => 15000000,
            'duration_months'=> 6,
            'start_year'     => 2026,
            'end_year'       => 2026,
            'description'    => 'Étude de faisabilité pour la modernisation de l\'infrastructure pédagogique.',
            'status'         => 'Nouveau',
        ]);

        // Projet 2 — EN ÉTUDE (Arzew, juriste affecté)
        $p2 = Project::create([
            'title'          => 'Construction Amphithéâtre Multimédia',
            'nature_id'      => $natures[1]->id,
            'type'           => 'Investissement',
            'school_id'      => $schools[1]->id,
            'created_by'     => $assistantDg->id,
            'budget'         => 85000000,
            'duration_months'=> 18,
            'start_year'     => 2026,
            'end_year'       => 2027,
            'description'    => 'Construction d\'un amphithéâtre moderne équipé de systèmes multimédia.',
            'status'         => 'En Etude',
            'juriste_id'     => $juristes[1][0]->id,
            'chef_projet_id' => $chefs[1][0]->id,
            'dg_consulted_at'=> now()->subDays(10),
            'school_director_viewed_at' => now()->subDays(8),
        ]);

        // Créer les phases juridiques pour p2 (depuis le référentiel par défaut)
        foreach ($defaults as [$order, $name, $pct]) {
            $isDemarrage = ($order === 7);
            LegalStep::create([
                'project_id'   => $p2->id,
                'created_by'   => $juristes[1][0]->id,
                'title'        => $name,
                'percentage'   => $pct,
                'sort_order'   => $order,
                'is_deletable' => !$isDemarrage,
                'is_completed' => $order <= 3,
                'completed_at' => $order <= 3 ? now()->subDays(7 - $order) : null,
                'checked_at'   => $order <= 3 ? now()->subDays(7 - $order) : null,
            ]);
        }

        // Projet 3 — EN COURS (Skikda, ODS émis)
        $p3 = Project::create([
            'title'                => 'Aménagement Laboratoire de Géologie',
            'nature_id'            => $natures[2]->id,
            'type'                 => 'Exploitation',
            'school_id'            => $schools[2]->id,
            'created_by'           => $assistantDg->id,
            'budget'               => 52000000,
            'duration_months'      => 10,
            'start_year'           => 2026,
            'end_year'             => 2027,
            'description'          => 'Réaménagement complet du laboratoire de géologie pétrolière.',
            'status'               => 'En Cours',
            'juriste_id'           => $juristes[2][0]->id,
            'chef_projet_id'       => $chefs[2][0]->id,
            'chef_access_unlocked' => true,
            'dg_consulted_at'      => now()->subDays(20),
            'school_director_viewed_at' => now()->subDays(18),
            'started_at'           => now()->subDays(15),
        ]);

        // Toutes les phases cochées pour p3
        foreach ($defaults as [$order, $name, $pct]) {
            $isDemarrage = ($order === 7);
            LegalStep::create([
                'project_id'   => $p3->id,
                'created_by'   => $juristes[2][0]->id,
                'title'        => $name,
                'percentage'   => $pct,
                'sort_order'   => $order,
                'is_deletable' => !$isDemarrage,
                'is_completed' => true,
                'completed_at' => now()->subDays(20 - $order),
                'checked_at'   => now()->subDays(20 - $order),
            ]);
        }

        // ODS Démarrage pour p3
        OdsRecord::create([
            'project_id' => $p3->id,
            'issued_by'  => $juristes[2][0]->id,
            'type'       => 'Demarrage',
            'notes'      => 'ODS de démarrage émis après validation complète du référentiel juridique.',
            'issued_at'  => now()->subDays(15),
        ]);

        // Dépenses pour p3 (75% du budget)
        foreach ([
            ['Matériaux de construction et finitions', 20000000, 14],
            ['Main d\'œuvre spécialisée',              15000000, 10],
            ['Équipements de laboratoire',              4000000,  5],
        ] as [$desc, $amount, $daysAgo]) {
            Expense::create([
                'project_id'   => $p3->id,
                'entered_by'   => $chefs[2][0]->id,
                'description'  => $desc,
                'amount'       => $amount,
                'expense_date' => now()->subDays($daysAgo),
            ]);
        }

        // Projet 4 — TERMINÉ (Hassi Messaoud)
        $p4 = Project::create([
            'title'                => 'Installation Équipements de Forage Pédagogique',
            'nature_id'            => $natures[3]->id,
            'type'                 => 'Investissement',
            'school_id'            => $schools[3]->id,
            'created_by'           => $assistantDg->id,
            'budget'               => 120000000,
            'duration_months'      => 12,
            'start_year'           => 2025,
            'end_year'             => 2026,
            'description'          => 'Installation des équipements de forage pédagogique de nouvelle génération.',
            'status'               => 'Termine',
            'juriste_id'           => $juristes[3][0]->id,
            'chef_projet_id'       => $chefs[3][0]->id,
            'chef_access_unlocked' => true,
            'dg_consulted_at'      => now()->subDays(90),
            'started_at'           => now()->subDays(80),
            'closed_at'            => now()->subDays(5),
            'budget_alert_sent'    => true,
        ]);

        // Dépenses pour p4 (100% du budget)
        foreach ([
            ['Équipements de forage',    80000000, 60],
            ['Installation et câblage',  25000000, 40],
            ['Formation et certification', 15000000, 20],
        ] as [$desc, $amount, $daysAgo]) {
            Expense::create([
                'project_id'   => $p4->id,
                'entered_by'   => $chefs[3][0]->id,
                'description'  => $desc,
                'amount'       => $amount,
                'expense_date' => now()->subDays($daysAgo),
            ]);
        }

        // =====================================================================
        // 6. NOTIFICATIONS DE TEST
        // =====================================================================
        Notification::create([
            'user_id'    => $dg->id,
            'project_id' => $p1->id,
            'type'       => 'nouveau_projet',
            'message'    => "Nouveau projet soumis : « {$p1->title} » (École {$schools[0]->name}).",
            'priority'   => 'normal',
            'is_read'    => false,
        ]);

        Notification::create([
            'user_id'    => $juristes[1][0]->id,
            'project_id' => $p2->id,
            'type'       => 'affectation',
            'message'    => "Vous avez été affecté(e) en tant que Juriste au projet « {$p2->title} ».",
            'priority'   => 'urgent',
            'is_read'    => false,
        ]);

        Notification::create([
            'user_id'    => $chefs[2][0]->id,
            'project_id' => $p3->id,
            'type'       => 'ods_demarrage',
            'message'    => "L'ODS de Démarrage a été émis pour « {$p3->title} ». Votre accès est maintenant actif.",
            'priority'   => 'urgent',
            'is_read'    => false,
        ]);

        Notification::create([
            'user_id'    => $directors[3]->id,
            'project_id' => $p4->id,
            'type'       => 'projet_termine',
            'message'    => "Le projet « {$p4->title} » a été clôturé et archivé.",
            'priority'   => 'normal',
            'is_read'    => true,
            'read_at'    => now()->subDays(5),
        ]);

        $this->command->info('Seeder exécuté avec succès.');
        $this->command->info('  4 écoles | 5 natures | 7 phases par défaut');
        $this->command->info('  1 admin | 1 assistant DG | 1 DG | 4 directeurs | 8 juristes | 8 chefs');
        $this->command->info('  4 projets de test (Nouveau, En Etude, En Cours, Termine)');
        $this->command->line('');
        $this->command->info('Comptes de connexion (mot de passe : password) :');
        $this->command->info('  admin@iap.dz        → Admin');
        $this->command->info('  assistant.dg@iap.dz → Assistant DG');
        $this->command->info('  dg@iap.dz           → Directeur Général');
        $this->command->info('  directeur.boumerdes@iap.dz → Directeur École Boumerdes');
        $this->command->info('  juriste1.boumerdes@iap.dz  → Juriste Boumerdes');
        $this->command->info('  chef1.boumerdes@iap.dz     → Chef de Projet Boumerdes');
    }
}
