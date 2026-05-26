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
    private const PASSWORD = 'password2026';

    public function run(): void
    {
        // =====================================================================
        // 1. ÉCOLES
        // =====================================================================
        $schools = [];
        foreach (['Boumerdes', 'Arzew', 'Skikda', 'Hassi Messaoud'] as $i => $name) {
            $schools[$i] = School::create(['name' => $name]);
        }
        [$b, $a, $s, $h] = $schools;

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
        [$etudes, $constructions, $amenagements, $equipements, $terrains] = $natures;

        // =====================================================================
        // 3. RÉFÉRENTIEL JURIDIQUE PAR DÉFAUT (7 phases = 100%)
        // =====================================================================
        $defaults = [
            [1, 'Constitution du dossier de Consultation', 15],
            [2, 'Validation Juridique du dossier',         15],
            [3, 'Lancement de l\'appel d\'offres',         20],
            [4, 'Réception et ouverture des plis',         15],
            [5, 'Évaluation technique et financière',      10],
            [6, 'Établissement du Contrat',                15],
            [7, 'Émission de l\'ODS de Démarrage',         10],
        ];
        foreach ($defaults as [$order, $name, $pct]) {
            ProjectNatureDefault::create([
                'order_number' => $order,
                'name'         => $name,
                'percentage'   => $pct,
            ]);
        }

        // =====================================================================
        // 4. UTILISATEURS — 47 comptes @sonatrach.dz (mot de passe : password2026)
        // =====================================================================
        $admin = User::create([
            'name'      => 'Karim Hadj',
            'email'     => 'KarimHadj@sonatrach.dz',
            'password'  => Hash::make(self::PASSWORD),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        $dg = User::create([
            'name'      => 'Mohamed Bencherif',
            'email'     => 'MohamedBencherif@sonatrach.dz',
            'password'  => Hash::make(self::PASSWORD),
            'role'      => 'dg',
            'is_active' => true,
        ]);

        $adg = User::create([
            'name'      => 'Yasmine Ferhat',
            'email'     => 'YasmineFerhat@sonatrach.dz',
            'password'  => Hash::make(self::PASSWORD),
            'role'      => 'assistant_dg',
            'is_active' => true,
        ]);

        $dirB = $this->createUser('Omar Belkacem',  'OmarBelkacem1',  'directeur_ecole', $b->id);
        $dirA = $this->createUser('Nadia Khelifi',   'NadiaKhelifi2',  'directeur_ecole', $a->id);
        $dirS = $this->createUser('Rachid Amrani',   'RachidAmrani3',  'directeur_ecole', $s->id);
        $dirH = $this->createUser('Fatima Zidane',   'FatimaZidane4',  'directeur_ecole', $h->id);

        $juristesB = $this->createUsers([
            ['Ali Mebarki',    'AliMebarki1'],
            ['Soumia Hammou',  'SoumiaHammou1'],
            ['Kamel Bouazza',  'KamelBouazza1'],
            ['Lina Messaoud',  'LinaMessaoud1'],
        ], 'juriste', $b->id);

        $juristesA = $this->createUsers([
            ['Sarah Boudiaf',  'SarahBoudiaf2'],
            ['Youcef Akli',    'YoucefAkli2'],
            ['Nawal Sahli',    'NawalSahli2'],
            ['Hamza Zeroual',  'HamzaZeroual2'],
        ], 'juriste', $a->id);

        $juristesS = $this->createUsers([
            ['Rima Kacem',     'RimaKacem3'],
            ['Tarek Belaid',   'TarekBelaid3'],
            ['Amina Hadjadj',  'AminaHadjadj3'],
            ['Zakia Cherifi',  'ZakiaCherifi3'],
        ], 'juriste', $s->id);

        $juristesH = $this->createUsers([
            ['Fares Benamor',  'FaresBenamor4'],
            ['Ines Larbi',     'InesLarbi4'],
            ['Mourad Belhadj', 'MouradBelhadj4'],
            ['Khadija Benali', 'KhadijaBenali4'],
        ], 'juriste', $h->id);

        $chefsB = $this->createUsers([
            ['Samir Ouali',     'SamirOuali1'],
            ['Houda Bouguern',  'HoudaBouguern1'],
            ['Rachid Toumi',    'RachidToumi1'],
            ['Meriem Djebbar',  'MeriemDjebbar1'],
            ['Yacine Ziani',    'YacineZiani1'],
            ['Sofiane Belkadi', 'SofianeBelkadi1'],
        ], 'chef_projet', $b->id);

        $chefsA = $this->createUsers([
            ['Imen Bouchenak',  'ImenBouchenak2'],
            ['Rabah Sebti',     'RabahSebti2'],
            ['Nour Medjdoub',   'NourMedjdoub2'],
            ['Amar Bensaid',    'AmarBensaid2'],
            ['Salima Cherbi',   'SalimaCherbi2'],
            ['Bilel Djermoune', 'BilelDjermoune2'],
        ], 'chef_projet', $a->id);

        $chefsS = $this->createUsers([
            ['Malika Merzougui', 'MalikaMerzougui3'],
            ['Nacim Benfreha',   'NacimBenfreha3'],
            ['Walid Cheurfi',    'WalidCheurfi3'],
            ['Zohra Ait',        'ZohraAit3'],
            ['Hicham Moussa',    'HichamMoussa3'],
            ['Djamila Keddar',   'DjamilaKeddar3'],
        ], 'chef_projet', $s->id);

        $chefsH = $this->createUsers([
            ['Abdelkarim Fares', 'AbdelkarimFares4'],
            ['Sirine Benhelal',  'SirineBenhelal4'],
            ['Issam Kouider',    'IssamKouider4'],
            ['Laila Benmamar',   'LailaBenmamar4'],
            ['Riad Bentoumi',    'RiadBentoumi4'],
            ['Zineb Othmani',    'ZinebOthmani4'],
        ], 'chef_projet', $h->id);

        // =====================================================================
        // 5. PROJETS (22 projets — budgets en KDA)
        // =====================================================================

        // ── BOUMERDES (5 projets) ─────────────────────────────────────────────
        $p1 = $this->makeProject('Étude Faisabilité Infrastructure Pédagogique', $etudes->id, 'Investissement', $b->id, $adg->id, 15000, 6, 'Nouveau');

        $p2 = $this->makeProject('Construction Amphithéâtre Principal', $constructions->id, 'Investissement', $b->id, $adg->id, 80000, 18, 'En Etude', [
            'juriste_id' => $juristesB[0]->id, 'chef_projet_id' => $chefsB[0]->id,
            'dg_consulted_at' => now()->subDays(10), 'school_director_viewed_at' => now()->subDays(8),
        ]);
        $this->createLegalSteps($p2, $defaults, 3, $juristesB[0]->id);

        $p3 = $this->makeProject('Rénovation Laboratoires Informatique', $amenagements->id, 'Exploitation', $b->id, $adg->id, 12000, 4, 'En Cours', [
            'juriste_id' => $juristesB[1]->id, 'chef_projet_id' => $chefsB[1]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(30),
            'school_director_viewed_at' => now()->subDays(28), 'started_at' => now()->subDays(20),
        ]);
        $this->createLegalSteps($p3, $defaults, 7, $juristesB[1]->id);
        $this->createOds($p3, 'Demarrage', $juristesB[1]->id, now()->subDays(20));
        $this->createExpenses($p3, $chefsB[1]->id, [
            ['Matériaux et fournitures', 2500, 18],
            ['Main d\'œuvre spécialisée', 1500, 12],
            ['Câblage réseau', 500, 8],
        ]);

        $p4 = $this->makeProject('Acquisition Équipements Pédagogiques', $equipements->id, 'Exploitation', $b->id, $adg->id, 5000, 3, 'En Cours', [
            'juriste_id' => $juristesB[2]->id, 'chef_projet_id' => $chefsB[2]->id,
            'chef_access_unlocked' => true, 'budget_alert_sent' => true,
            'dg_consulted_at' => now()->subDays(60), 'school_director_viewed_at' => now()->subDays(58),
            'started_at' => now()->subDays(50),
        ]);
        $this->createLegalSteps($p4, $defaults, 7, $juristesB[2]->id);
        $this->createOds($p4, 'Demarrage', $juristesB[2]->id, now()->subDays(50));
        $this->createOds($p4, 'Arret', $juristesB[2]->id, now()->subDays(30));
        $this->createExpenses($p4, $chefsB[2]->id, [
            ['Matériel informatique lot 1', 2000, 45],
            ['Matériel informatique lot 2', 1500, 30],
            ['Accessoires et câbles', 800, 20],
        ]); // 4300/5000 = 86% → ALERTE

        $p5 = $this->makeProject('Aménagement Terrain Sportif', $terrains->id, 'Investissement', $b->id, $adg->id, 8000, 8, 'Termine', [
            'juriste_id' => $juristesB[3]->id, 'chef_projet_id' => $chefsB[3]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(200),
            'started_at' => now()->subDays(180), 'closed_at' => now()->subDays(10),
        ]);
        $this->createLegalSteps($p5, $defaults, 7, $juristesB[3]->id);
        $this->createOds($p5, 'Demarrage', $juristesB[3]->id, now()->subDays(180));
        $this->createExpenses($p5, $chefsB[3]->id, [
            ['Terrassement et nivellement', 2500, 170],
            ['Revêtement synthétique', 3000, 140],
            ['Clôtures et équipements', 1900, 100],
            ['Éclairage sportif', 500, 50],
        ]); // 7900/8000 = 98.75%

        // ── ARZEW (6 projets) ─────────────────────────────────────────────────
        $p6 = $this->makeProject('Construction Centre Formation Avancé', $constructions->id, 'Investissement', $a->id, $adg->id, 120000, 24, 'Nouveau');

        $p7 = $this->makeProject('Étude Géotechnique du Site', $etudes->id, 'Investissement', $a->id, $adg->id, 3500, 3, 'En Etude', [
            'juriste_id' => $juristesA[0]->id, 'chef_projet_id' => $chefsA[0]->id,
            'dg_consulted_at' => now()->subDays(15), 'school_director_viewed_at' => now()->subDays(13),
        ]);
        $this->createLegalSteps($p7, $defaults, 2, $juristesA[0]->id); // 30%

        $p8 = $this->makeProject('Rénovation Salles de Cours Bloc B', $amenagements->id, 'Exploitation', $a->id, $adg->id, 9000, 5, 'En Etude', [
            'juriste_id' => $juristesA[1]->id, 'chef_projet_id' => $chefsA[1]->id,
            'dg_consulted_at' => now()->subDays(25), 'school_director_viewed_at' => now()->subDays(23),
        ]);
        $this->createLegalSteps($p8, $defaults, 5, $juristesA[1]->id); // 75%

        $p9 = $this->makeProject('Équipement Laboratoire Chimie', $equipements->id, 'Exploitation', $a->id, $adg->id, 18000, 6, 'En Cours', [
            'juriste_id' => $juristesA[2]->id, 'chef_projet_id' => $chefsA[2]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(50),
            'school_director_viewed_at' => now()->subDays(48), 'started_at' => now()->subDays(40),
        ]);
        $this->createLegalSteps($p9, $defaults, 7, $juristesA[2]->id);
        $this->createOds($p9, 'Demarrage', $juristesA[2]->id, now()->subDays(40));
        $this->createExpenses($p9, $chefsA[2]->id, [
            ['Équipements analytiques', 4000, 35],
            ['Consommables chimie', 2000, 25],
            ['Installation et montage', 1200, 15],
        ]); // 7200/18000 = 40%

        $p10 = $this->makeProject('Acquisition Terrain Extension Nord', $terrains->id, 'Investissement', $a->id, $adg->id, 45000, 12, 'En Cours', [
            'juriste_id' => $juristesA[3]->id, 'chef_projet_id' => $chefsA[3]->id,
            'chef_access_unlocked' => true, 'budget_alert_sent' => true,
            'dg_consulted_at' => now()->subDays(100), 'school_director_viewed_at' => now()->subDays(98),
            'started_at' => now()->subDays(90),
        ]);
        $this->createLegalSteps($p10, $defaults, 7, $juristesA[3]->id);
        $this->createOds($p10, 'Demarrage', $juristesA[3]->id, now()->subDays(90));
        $this->createOds($p10, 'Arret', $juristesA[3]->id, now()->subDays(60));
        $this->createOds($p10, 'Reprise', $juristesA[3]->id, now()->subDays(45));
        $this->createExpenses($p10, $chefsA[3]->id, [
            ['Frais notariaux et enregistrement', 3000, 85],
            ['Acquisition parcelle lot 1', 20000, 70],
            ['Acquisition parcelle lot 2', 15000, 50],
        ]); // 38000/45000 = 84.4% → ALERTE

        $p11 = $this->makeProject('Réfection Façade et Toiture', $amenagements->id, 'Exploitation', $a->id, $adg->id, 6000, 4, 'Termine', [
            'juriste_id' => $juristesA[0]->id, 'chef_projet_id' => $chefsA[0]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(180),
            'started_at' => now()->subDays(160), 'closed_at' => now()->subDays(30),
        ]);
        $this->createLegalSteps($p11, $defaults, 7, $juristesA[0]->id);
        $this->createOds($p11, 'Demarrage', $juristesA[0]->id, now()->subDays(160));
        $this->createExpenses($p11, $chefsA[0]->id, [
            ['Travaux façade', 3000, 150],
            ['Travaux toiture', 2000, 120],
            ['Peinture et finitions', 850, 80],
        ]); // 5850/6000 = 97.5%

        // ── SKIKDA (6 projets) ────────────────────────────────────────────────
        $p12 = $this->makeProject('Aménagement Laboratoire Simulation', $amenagements->id, 'Exploitation', $s->id, $adg->id, 22000, 8, 'Termine', [
            'juriste_id' => $juristesS[0]->id, 'chef_projet_id' => $chefsS[0]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(300),
            'started_at' => now()->subDays(270), 'closed_at' => now()->subDays(60),
        ]);
        $this->createLegalSteps($p12, $defaults, 7, $juristesS[0]->id);
        $this->createOds($p12, 'Demarrage', $juristesS[0]->id, now()->subDays(270));
        $this->createExpenses($p12, $chefsS[0]->id, [
            ['Équipements simulation', 12000, 250],
            ['Travaux aménagement', 7000, 200],
            ['Installation et tests', 3000, 150],
        ]); // 22000/22000 = 100%

        $p13 = $this->makeProject('Construction Internat Étudiants', $constructions->id, 'Investissement', $s->id, $adg->id, 95000, 20, 'Nouveau');

        $p14 = $this->makeProject('Étude Réhabilitation Réseau Électrique', $etudes->id, 'Exploitation', $s->id, $adg->id, 2800, 2, 'En Etude', [
            'juriste_id' => $juristesS[1]->id, 'chef_projet_id' => $chefsS[1]->id,
            'dg_consulted_at' => now()->subDays(20), 'school_director_viewed_at' => now()->subDays(18),
        ]);
        $this->createLegalSteps($p14, $defaults, 1, $juristesS[1]->id); // 15%

        $p15 = $this->makeProject('Équipement Salles Multimédia', $equipements->id, 'Exploitation', $s->id, $adg->id, 14000, 5, 'En Cours', [
            'juriste_id' => $juristesS[2]->id, 'chef_projet_id' => $chefsS[2]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(70),
            'school_director_viewed_at' => now()->subDays(68), 'started_at' => now()->subDays(55),
        ]);
        $this->createLegalSteps($p15, $defaults, 7, $juristesS[2]->id);
        $this->createOds($p15, 'Demarrage', $juristesS[2]->id, now()->subDays(55));
        $this->createExpenses($p15, $chefsS[2]->id, [
            ['Vidéoprojecteurs et écrans', 3500, 50],
            ['Ordinateurs et postes', 2000, 40],
            ['Câblage et installation', 500, 30],
        ]); // 6000/14000 = 42.8%

        $p16 = $this->makeProject('Acquisition Terrain Parking', $terrains->id, 'Investissement', $s->id, $adg->id, 11000, 9, 'En Etude', [
            'juriste_id' => $juristesS[3]->id, 'chef_projet_id' => $chefsS[3]->id,
            'dg_consulted_at' => now()->subDays(35), 'school_director_viewed_at' => now()->subDays(33),
        ]);
        $this->createLegalSteps($p16, $defaults, 4, $juristesS[3]->id); // 65%

        $p17 = $this->makeProject('Rénovation Cuisine et Réfectoire', $amenagements->id, 'Exploitation', $s->id, $adg->id, 7500, 5, 'Termine', [
            'juriste_id' => $juristesS[0]->id, 'chef_projet_id' => $chefsS[0]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(200),
            'started_at' => now()->subDays(180), 'closed_at' => now()->subDays(25),
        ]);
        $this->createLegalSteps($p17, $defaults, 7, $juristesS[0]->id);
        $this->createOds($p17, 'Demarrage', $juristesS[0]->id, now()->subDays(180));
        $this->createExpenses($p17, $chefsS[0]->id, [
            ['Équipements cuisine industrielle', 3500, 170],
            ['Mobilier réfectoire', 2200, 130],
            ['Travaux plomberie et électricité', 1500, 90],
        ]); // 7200/7500 = 96%

        // ── HASSI MESSAOUD (5 projets) ────────────────────────────────────────
        $p18 = $this->makeProject('Construction Bloc Pédagogique C', $constructions->id, 'Investissement', $h->id, $adg->id, 55000, 15, 'Nouveau');

        $p19 = $this->makeProject('Étude Sécurité Incendie', $etudes->id, 'Exploitation', $h->id, $adg->id, 4200, 3, 'En Etude', [
            'juriste_id' => $juristesH[0]->id, 'chef_projet_id' => $chefsH[0]->id,
            'dg_consulted_at' => now()->subDays(18), 'school_director_viewed_at' => now()->subDays(16),
        ]);
        $this->createLegalSteps($p19, $defaults, 2, $juristesH[0]->id); // 30%

        $p20 = $this->makeProject('Rénovation Logements Personnel', $amenagements->id, 'Exploitation', $h->id, $adg->id, 19000, 10, 'En Cours', [
            'juriste_id' => $juristesH[1]->id, 'chef_projet_id' => $chefsH[1]->id,
            'chef_access_unlocked' => true, 'budget_alert_sent' => true,
            'dg_consulted_at' => now()->subDays(80), 'school_director_viewed_at' => now()->subDays(78),
            'started_at' => now()->subDays(70),
        ]);
        $this->createLegalSteps($p20, $defaults, 7, $juristesH[1]->id);
        $this->createOds($p20, 'Demarrage', $juristesH[1]->id, now()->subDays(70));
        $this->createExpenses($p20, $chefsH[1]->id, [
            ['Travaux gros œuvre', 6000, 65],
            ['Plomberie et sanitaires', 4000, 50],
            ['Électricité et réseau', 3000, 40],
            ['Peinture et finitions', 3000, 25],
        ]); // 16000/19000 = 84.2% → ALERTE

        $p21 = $this->makeProject('Équipement Forage Simulation', $equipements->id, 'Investissement', $h->id, $adg->id, 35000, 8, 'En Cours', [
            'juriste_id' => $juristesH[2]->id, 'chef_projet_id' => $chefsH[2]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(45),
            'school_director_viewed_at' => now()->subDays(43), 'started_at' => now()->subDays(35),
        ]);
        $this->createLegalSteps($p21, $defaults, 7, $juristesH[2]->id);
        $this->createOds($p21, 'Demarrage', $juristesH[2]->id, now()->subDays(35));
        $this->createExpenses($p21, $chefsH[2]->id, [
            ['Simulateur forage unité A', 8000, 30],
            ['Instrumentations et capteurs', 4000, 20],
        ]); // 12000/35000 = 34.2%

        $p22 = $this->makeProject('Acquisition Véhicules de Terrain', $equipements->id, 'Exploitation', $h->id, $adg->id, 8000, 4, 'Termine', [
            'juriste_id' => $juristesH[3]->id, 'chef_projet_id' => $chefsH[3]->id,
            'chef_access_unlocked' => true, 'dg_consulted_at' => now()->subDays(160),
            'started_at' => now()->subDays(145), 'closed_at' => now()->subDays(15),
        ]);
        $this->createLegalSteps($p22, $defaults, 7, $juristesH[3]->id);
        $this->createOds($p22, 'Demarrage', $juristesH[3]->id, now()->subDays(145));
        $this->createExpenses($p22, $chefsH[3]->id, [
            ['Véhicule 4x4 lot 1', 4800, 140],
            ['Véhicule 4x4 lot 2', 3000, 120],
        ]); // 7800/8000 = 97.5%

        // =====================================================================
        // 6. NOTIFICATIONS INITIALES
        // =====================================================================
        // Nouveau projet → DG
        Notification::create(['user_id' => $dg->id, 'project_id' => $p1->id, 'type' => 'nouveau_projet',
            'message' => "Nouveau projet soumis : « {$p1->title} » (École Boumerdes).", 'priority' => 'normal', 'is_read' => false]);

        // Alerte budget 80% — P4 (86%)
        foreach ([$adg, $dg, $dirB, $juristesB[2], $chefsB[2]] as $recipient) {
            Notification::create(['user_id' => $recipient->id, 'project_id' => $p4->id, 'type' => 'alerte_budget',
                'message' => "⚠️ Alerte Budget 80% — « {$p4->title} » : consommation à 86%.", 'priority' => 'urgent', 'is_read' => false]);
        }

        // Alerte budget 80% — P10 (84.4%)
        foreach ([$adg, $dg, $dirA, $juristesA[3], $chefsA[3]] as $recipient) {
            Notification::create(['user_id' => $recipient->id, 'project_id' => $p10->id, 'type' => 'alerte_budget',
                'message' => "⚠️ Alerte Budget 80% — « {$p10->title} » : consommation à 84%.", 'priority' => 'urgent', 'is_read' => false]);
        }

        // Alerte budget 80% — P20 (84.2%)
        foreach ([$adg, $dg, $dirH, $juristesH[1], $chefsH[1]] as $recipient) {
            Notification::create(['user_id' => $recipient->id, 'project_id' => $p20->id, 'type' => 'alerte_budget',
                'message' => "⚠️ Alerte Budget 80% — « {$p20->title} » : consommation à 84%.", 'priority' => 'urgent', 'is_read' => false]);
        }

        $this->command->info('✅ Seeder terminé — 47 comptes | 22 projets | budgets en KDA');
        $this->command->info('Mot de passe universel : password2026');
        $this->command->info('KarimHadj@sonatrach.dz → Admin');
        $this->command->info('MohamedBencherif@sonatrach.dz → DG');
        $this->command->info('YasmineFerhat@sonatrach.dz → Assistant DG');
        $this->command->info('OmarBelkacem1@sonatrach.dz → Directeur Boumerdes');
        $this->command->info('AliMebarki1@sonatrach.dz → Juriste Boumerdes');
        $this->command->info('SamirOuali1@sonatrach.dz → Chef Projet Boumerdes');
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function createUser(string $name, string $emailName, string $role, ?int $schoolId = null): User
    {
        return User::create([
            'name'      => $name,
            'email'     => $emailName . '@sonatrach.dz',
            'password'  => Hash::make(self::PASSWORD),
            'role'      => $role,
            'school_id' => $schoolId,
            'is_active' => true,
        ]);
    }

    private function createUsers(array $data, string $role, int $schoolId): array
    {
        $users = [];
        foreach ($data as [$name, $emailName]) {
            $users[] = $this->createUser($name, $emailName, $role, $schoolId);
        }
        return $users;
    }

    private function makeProject(
        string $title, int $natureId, string $type, int $schoolId, int $createdBy,
        float $budget, int $duration, string $status, array $extra = []
    ): Project {
        return Project::create(array_merge([
            'title'           => $title,
            'nature_id'       => $natureId,
            'type'            => $type,
            'school_id'       => $schoolId,
            'created_by'      => $createdBy,
            'budget'          => $budget,
            'duration_months' => $duration,
            'status'          => $status,
            'start_year'      => 2026,
            'end_year'        => 2027,
        ], $extra));
    }

    private function createLegalSteps(Project $project, array $defaults, int $checkedCount, int $createdBy): void
    {
        $lastOrder = count($defaults);
        foreach ($defaults as [$order, $name, $pct]) {
            $isChecked   = $order <= $checkedCount;
            $isDemarrage = $order === $lastOrder;
            LegalStep::create([
                'project_id'   => $project->id,
                'created_by'   => $createdBy,
                'title'        => $name,
                'percentage'   => $pct,
                'sort_order'   => $order,
                'is_deletable' => !$isDemarrage,
                'is_completed' => $isChecked,
                'completed_at' => $isChecked ? now()->subDays(max(1, $checkedCount - $order + 5)) : null,
                'checked_at'   => $isChecked ? now()->subDays(max(1, $checkedCount - $order + 5)) : null,
            ]);
        }
    }

    private function createOds(Project $project, string $type, int $issuedBy, \Carbon\Carbon $issuedAt): void
    {
        OdsRecord::create([
            'project_id' => $project->id,
            'issued_by'  => $issuedBy,
            'type'       => $type,
            'issued_at'  => $issuedAt,
            'notes'      => match($type) {
                'Demarrage' => 'ODS de Démarrage émis après validation complète du référentiel juridique.',
                'Arret'     => 'ODS d\'Arrêt temporaire — vérification administrative.',
                'Reprise'   => 'ODS de Reprise — travaux autorisés à reprendre.',
                default     => null,
            },
        ]);
    }

    private function createExpenses(Project $project, int $enteredBy, array $expenses): void
    {
        foreach ($expenses as [$desc, $amount, $daysAgo]) {
            Expense::create([
                'project_id'   => $project->id,
                'entered_by'   => $enteredBy,
                'description'  => $desc,
                'amount'       => $amount,
                'expense_date' => now()->subDays($daysAgo)->format('Y-m-d'),
            ]);
        }
    }
}
