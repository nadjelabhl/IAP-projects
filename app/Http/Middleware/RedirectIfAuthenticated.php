<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  ...$guards
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirection dynamique basée sur le rôle
                return $this->redirectByRole(Auth::guard($guard)->user());
            }
        }

        return $next($request);
    }

    /**
     * Rediriger vers le dashboard approprié selon le rôle
     */
    private function redirectByRole($user): Response
    {
        $dashboardRoutes = [
            'admin' => 'admin.dashboard',
            'assistant_dg' => 'assistant_dg.dashboard',
            'dg' => 'dg.dashboard',
            'directeur_ecole' => 'directeur_ecole.dashboard',
            'juriste' => 'juriste.dashboard',
            'chef_projet' => 'chef_projet.dashboard',
        ];

        $route = $dashboardRoutes[$user->role] ?? 'dashboard';
        return redirect()->route($route);
    }
}
