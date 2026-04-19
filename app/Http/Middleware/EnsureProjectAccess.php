<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * Vérifie que l'utilisateur a accès au projet demandé.
     * - Admin, DG, Assistant DG : accès à tous les projets
     * - Directeur d'École : accès aux projets de son école
     * - Juriste : accès aux projets où il est assigné
     * - Chef de Projet : accès aux projets où il est assigné ET chef_access_unlocked = true
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Vérifier que l'utilisateur est authentifié
        if (!$user) {
            return redirect()->route('login');
        }

        // Les rôles globaux ont accès à tous les projets
        $globalRoles = ['admin', 'assistant_dg', 'dg'];
        if (in_array($user->role, $globalRoles)) {
            return $next($request);
        }

        // Récupérer l'ID du projet depuis la route ou les paramètres
        $projectId = $request->route('project') ?? $request->input('project_id');
        if (!$projectId) {
            return $next($request);
        }

        $project = Project::find($projectId);
        if (!$project) {
            abort(404, 'Projet non trouvé.');
        }

        // Directeur d'École : accès aux projets de son école
        if ($user->role === 'directeur_ecole') {
            if ($project->school_id !== $user->school_id) {
                abort(403, 'Accès refusé. Ce projet n\'appartient pas à votre école.');
            }
            return $next($request);
        }

        // Juriste : accès aux projets où il est assigné
        if ($user->role === 'juriste') {
            if ($project->juriste_id !== $user->id) {
                abort(403, 'Accès refusé. Vous n\'êtes pas assigné à ce projet en tant que Juriste.');
            }
            return $next($request);
        }

        // Chef de Projet : accès aux projets où il est assigné ET chef_access_unlocked = true
        if ($user->role === 'chef_projet') {
            if ($project->chef_projet_id !== $user->id) {
                abort(403, 'Accès refusé. Vous n\'êtes pas assigné à ce projet en tant que Chef de Projet.');
            }
            if (!$project->chef_access_unlocked) {
                abort(403, 'Accès refusé. Votre accès à ce projet n\'a pas encore été débloqué (ODS non émise).');
            }
            return $next($request);
        }

        abort(403, 'Accès refusé.');
    }
}
