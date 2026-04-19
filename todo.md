# IAP GESTION - TODO List

## Phase 1 : Configuration Base de Données ✅

- [x] Migrations : users, schools, project_natures, projects, todo_tasks, ods_records, expenses, notifications, project_archives
- [x] Relations Eloquent : users ↔ projects, projects ↔ schools, todos ↔ projects, expenses ↔ projects

## Phase 2 : Modèles Eloquent ✅

- [x] User.php : 6 rôles, scopes (forSchool, withRole, juridtsForSchool, chefsForSchool)
- [x] Project.php : logique métier critique (canEmitODS, shouldTriggerBudgetAlert, transitionToNextStatus, emitODS, archiveProject)
- [x] Task.php : scopes et méthodes (updatePercentage, markAsCompleted)
- [x] Expense.php : scopes et calculs budget
- [x] Notification.php : scopes et labels
- [x] School.php : KPIs (budget, dépenses, alertes)
- [x] ProjectNature.php : statistiques
- [x] OdsRecord.php : historique ODS
- [x] ProjectArchive.php : archives projets

## Phase 3 : Seeders & Données de Test

- [ ] DatabaseSeeder : 4 écoles, 5 natures, 23 utilisateurs, 4 projets de test
- [ ] Tâches de test pour projets
- [ ] Dépenses de test pour projets
- [ ] Notifications de test

## Phase 4 : Authentification & RBAC

- [ ] Laravel Breeze intégré
- [ ] Policies : ProjectPolicy, TaskPolicy, ExpensePolicy
- [ ] Middleware : role-based access control
- [ ] Redirection post-login dynamique selon rôle

## Phase 5 : Controllers & Routes

- [ ] Controllers : ProjectController, TaskController, ExpenseController, NotificationController
- [ ] Routes : web.php avec groupes par rôle
- [ ] Validation : FormRequest pour chaque action
- [ ] Logique métier : Services pour ODS, Budget, Notifications

## Phase 6 : Composants Livewire - Dashboard DG

- [ ] Layout sidebar persistante
- [ ] Vue synthétique : nombre projets par statut
- [ ] KPIs globaux : budget total, alertes actives
- [ ] Graphiques : répartition projets par école, tendance budget
- [ ] Filtres : école, statut, date

## Phase 7 : Composants Livewire - Dashboard Directeur d'École

- [ ] Filtrage automatique : projets de son école uniquement
- [ ] Affichage projets avec statuts
- [ ] Suivi avancement et budget par projet
- [ ] Actions : assigner Juriste/Chef de Projet, archiver projet (TERMINÉ)
- [ ] Alertes budgétaires 80% visibles

## Phase 8 : Composants Livewire - Module Juriste

- [ ] Affichage To-Do list pour projets assignés
- [ ] Saisie % pour chaque tâche
- [ ] Validation en temps réel : somme % = 100%
- [ ] Bouton "Émettre ODS" : DÉSACTIVÉ si < 100%, ACTIVÉ si = 100%
- [ ] Transition statut EN ÉTUDE → EN COURS au clic ODS
- [ ] Notification Chef de Projet : "ODS émise, accès débloqué"

## Phase 9 : Composants Livewire - Module Chef de Projet

- [ ] Affichage budget total et dépenses par projet
- [ ] Formulaire saisie dépense
- [ ] Calcul temps réel : Budget Restant = Total - Dépenses
- [ ] Alerte 80% : déclenchement automatique notification Directeur d'École
- [ ] Historique dépenses avec détails

## Phase 10 : Système Notifications Temps Réel

- [ ] Icône cloche avec badge compteur
- [ ] Notifications temps réel (polling ou Reverb)
- [ ] Types d'alerte : projet nouveau, affectation, ODS, alerte 80%, archivage
- [ ] Marquage lu/non-lu
- [ ] Historique notifications

## Phase 11 : Design Industrial Architect

- [ ] Palette couleurs : bleu profond #022448, orange Sonatrach #fd761a, gris #f7f9fb
- [ ] Typographie : Poppins / Inter
- [ ] Coins arrondis 2XL (32px) sur cartes/conteneurs
- [ ] Ombres portées (shadow) au lieu de bordures 1px
- [ ] Contraste pour séparation zones
- [ ] Sidebar persistante avec logo et navigation
- [ ] Responsive design mobile/tablet/desktop

## Phase 12 : Tests & Finalisation

- [ ] Tests unitaires : modèles (canEmitODS, shouldTriggerBudgetAlert)
- [ ] Tests d'intégration : workflow statuts, ODS, alertes
- [ ] Validation métier : blocages ODS, alertes 80%
- [ ] Optimisations performance : requêtes DB, caching
- [ ] Documentation API
- [ ] Checkpoint final et livraison

## Notes Métier Critiques

- **Blocage ODS** : STRICT. Aucune exception. Somme % doit être exactement 100%.
- **Alerte 80%** : Déclenche notification immédiate Directeur d'École quand dépenses ≥ 80% budget.
- **Filtrage school_id** : Obligatoire pour Directeur d'École, Juriste, Chef de Projet.
- **Workflow statuts** : NOUVEAU → EN ÉTUDE (après affectation) → EN COURS (après ODS) → TERMINÉ (archivage).
