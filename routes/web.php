<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OdsController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', function () {
    return redirect()->route('login');
});

// Login routes
Route::get('/login', \App\Livewire\Auth\Login::class)->middleware('guest')->name('login');

// Logout
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');

// Dashboard principal
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'assistant_dg' => redirect()->route('assistant_dg.projects.create'),
        'dg' => redirect()->route('dg.dashboard'),
        'directeur_ecole' => redirect()->route('directeur_ecole.dashboard'),
        'juriste' => redirect()->route('juriste.dashboard'),
        'chef_projet' => redirect()->route('chef_projet.dashboard'),
        default => redirect('/login'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes d'authentification
require __DIR__.'/auth.php';

// =====================================================================
// PROFIL
// =====================================================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =====================================================================
// ADMIN
// =====================================================================
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Dashboard Admin
    Route::get('/admin/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin.dashboard');

    // Gestion des utilisateurs
    Route::get('/admin/users', \App\Livewire\Admin\ManageUsers::class)->name('admin.users.index');

    // Gestion des natures
    Route::get('/admin/natures', \App\Livewire\Admin\ManageNatures::class)->name('admin.natures.index');

    // Gestion des écoles
    Route::get('/admin/schools', [SchoolController::class, 'index'])->name('admin.schools.index');
    Route::post('/admin/schools', [SchoolController::class, 'store'])->name('admin.schools.store');
    Route::patch('/admin/schools/{school}', [SchoolController::class, 'update'])->name('admin.schools.update');
    Route::delete('/admin/schools/{school}', [SchoolController::class, 'destroy'])->name('admin.schools.destroy');
});

// =====================================================================
// ASSISTANT DG - Saisie de projets
// =====================================================================
Route::middleware(['auth', 'verified', 'role:assistant_dg'])->group(function () {
    Route::get('/assistant-dg/dashboard', \App\Livewire\AssistantDg\Dashboard::class)->name('assistant_dg.dashboard');

    // Création de projet (Livewire)
    Route::get('/assistant-dg/projects/create', \App\Livewire\AssistantDg\CreateProject::class)
        ->name('assistant_dg.projects.create');

    // Liste des projets saisis
    Route::get('/assistant-dg/projects', [ProjectController::class, 'index'])
        ->name('assistant_dg.projects.index');
});

// =====================================================================
// DG - Vue globale
// =====================================================================
Route::middleware(['auth', 'verified', 'role:dg'])->group(function () {
    // Dashboard DG avec statistiques
    Route::get('/dg/dashboard', \App\Livewire\Dg\DashboardDg::class)->name('dg.dashboard');

    // Liste de tous les projets
    Route::get('/dg/projects', [ProjectController::class, 'index'])->name('dg.projects.index');

    // Détail d'un projet
    Route::get('/dg/projects/{project}', [ProjectController::class, 'show'])->name('dg.projects.show');

    // Voir les archives
    Route::get('/dg/archives', function () {
        $projects = Project::where('status', 'Termine')->orderBy('closed_at', 'desc')->paginate(20);
        return view('livewire.archives.list', ['projects' => $projects]);
    })->name('dg.archives');

    // Gestion ODS
    Route::get('/dg/projects/{project}/ods', [OdsController::class, 'index'])
        ->name('dg.ods.index');
    Route::post('/ods/{ods}/cancel', [OdsController::class, 'cancel'])
        ->name('ods.cancel');
});

// =====================================================================
// DIRECTEUR D'ÉCOLE
// =====================================================================
Route::middleware(['auth', 'verified', 'role:directeur_ecole'])->group(function () {
    // Dashboard Directeur École
    Route::get('/directeur-ecole/dashboard', \App\Livewire\Director\DashboardDirector::class)
        ->name('directeur_ecole.dashboard');

    // Liste des projets de l'école
    Route::get('/directeur-ecole/projects', [ProjectController::class, 'index'])
        ->name('directeur_ecole.projects.index');

    // Détail d'un projet
    Route::get('/directeur-ecole/projects/{project}', [ProjectController::class, 'show'])
        ->name('directeur_ecole.projects.show');

    // Affectation du personnel (Juriste + Chef)
    Route::post('/directeur-ecole/projects/{project}/assign', [ProjectController::class, 'assign'])
        ->name('directeur_ecole.projects.assign');

    // Archiver un projet
    Route::post('/directeur-ecole/projects/{project}/archive', [ProjectController::class, 'archive'])
        ->name('directeur_ecole.projects.archive');

    // Voir les archives de l'école
    Route::get('/directeur-ecole/archives', function () {
        $user = auth()->user();
        $projects = Project::where('school_id', $user->school_id)
            ->where('status', 'Termine')
            ->orderBy('closed_at', 'desc')
            ->paginate(20);
        return view('livewire.archives.list', ['projects' => $projects]);
    })->name('directeur_ecole.archives');

    // Obtenir la liste RH pour AJAX
    Route::get('/directeur-ecole/schools/{school}/users', [SchoolController::class, 'users'])
        ->name('directeur_ecole.schools.users');

    // Gestion ODS
    Route::get('/directeur-ecole/projects/{project}/ods', [OdsController::class, 'index'])
        ->name('directeur_ecole.ods.index');
});

// =====================================================================
// JURISTE - To-Do List & ODS
// =====================================================================
Route::middleware(['auth', 'verified', 'role:juriste'])->group(function () {
    // Dashboard Juriste
    Route::get('/juriste/dashboard', \App\Livewire\Jurist\DashboardJurist::class)
        ->name('juriste.dashboard');

    // Liste des projets assignés
    Route::get('/juriste/projects', [ProjectController::class, 'index'])
        ->name('juriste.projects.index');

    // Taches d'un projet (To-Do List)
    Route::get('/juriste/projects/{project}/tasks', [TaskController::class, 'index'])
        ->name('juriste.projects.tasks');

    // Créer une tâche
    Route::post('/juriste/projects/{project}/tasks', [TaskController::class, 'store'])
        ->name('juriste.projects.tasks.store');

    // Marquer tâche complétée
    Route::post('/juriste/tasks/{task}/toggle', [TaskController::class, 'toggle'])
        ->name('juriste.tasks.toggle');

    // Supprimer une tâche
    Route::delete('/juriste/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('juriste.tasks.destroy');

    // Émettre l'ODS (déblocage Chef)
    Route::post('/juriste/projects/{project}/emit-ods', [TaskController::class, 'emitOds'])
        ->name('juriste.projects.emit_ods');

    // Gestion ODS
    Route::get('/juriste/projects/{project}/ods', [OdsController::class, 'index'])
        ->name('ods.index');
    Route::get('/juriste/projects/{project}/ods/create', [OdsController::class, 'create'])
        ->name('ods.create');
    Route::post('/juriste/projects/{project}/ods', [OdsController::class, 'store'])
        ->name('ods.store');
});

// =====================================================================
// CHEF DE PROJET - Dépenses & Budget
// =====================================================================
Route::middleware(['auth', 'verified', 'role:chef_projet'])->group(function () {
    // Dashboard Chef
    Route::get('/chef-projet/dashboard', \App\Livewire\Chef\DashboardChef::class)
        ->name('chef_projet.dashboard');

    // Liste des projets assignés
    Route::get('/chef-projet/projects', [ProjectController::class, 'index'])
        ->name('chef_projet.projects.index');

    // Dépenses d'un projet
    Route::get('/chef-projet/projects/{project}/expenses', [ExpenseController::class, 'index'])
        ->name('chef_projet.projects.expenses');

    // Enregistrer une dépense
    Route::post('/chef-projet/projects/{project}/expenses', [ExpenseController::class, 'store'])
        ->name('chef_projet.projects.expenses.store');

    // Supprimer une dépense
    Route::delete('/chef-projet/expenses/{expense}', [ExpenseController::class, 'destroy'])
        ->name('chef_projet.expenses.destroy');
});

// =====================================================================
// NOTIFICATIONS
// =====================================================================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::get('/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

// =====================================================================
// ARCHIVES - Pour DG et Directeurs
// =====================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/archives', function () {
        $user = auth()->user();
        $query = Project::where('status', 'Termine');

        if ($user->role === 'directeur_ecole') {
            $query->where('school_id', $user->school_id);
        }

        $projects = $query->orderBy('closed_at', 'desc')->paginate(20);
        return view('livewire.archives.list', ['projects' => $projects]);
    })->name('archives');
});