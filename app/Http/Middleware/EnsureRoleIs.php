<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleIs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifier que l'utilisateur est authentifié
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Vérifier que le rôle de l'utilisateur est dans la liste autorisée
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Accès refusé. Votre rôle n\'est pas autorisé pour cette action.');
        }

        return $next($request);
    }
}
