<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSchoolAccess
{
    /**
     * Handle an incoming request.
     *
     * Vérifie que l'utilisateur a accès à l'école demandée.
     * Les rôles globaux (Admin, DG, Assistant DG) ont accès à toutes les écoles.
     * Les rôles locaux (Directeur, Juriste, Chef) n'ont accès qu'à leur école.
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

        // Les rôles globaux ont accès à toutes les écoles
        $globalRoles = ['admin', 'assistant_dg', 'dg'];
        if (in_array($user->role, $globalRoles)) {
            return $next($request);
        }

        // Les rôles locaux doivent avoir une école assignée
        if (!$user->school_id) {
            abort(403, 'Accès refusé. Aucune école n\'est assignée à votre compte.');
        }

        // Vérifier que l'école demandée correspond à l'école de l'utilisateur
        $schoolId = $request->route('school') ?? $request->input('school_id');
        if ($schoolId && $schoolId != $user->school_id) {
            abort(403, 'Accès refusé. Vous n\'avez pas accès à cette école.');
        }

        return $next($request);
    }
}
