@php
$user = auth()->user();
$role = $user->role ?? 'guest';
@endphp

<nav style="background: linear-gradient(180deg, #0f172a 0%, #1e3a5f 100%); width: 260px; min-height: 100vh; position: fixed; left: 0; top: 0; display: flex; flex-direction: column; z-index: 50; box-shadow: 4px 0 20px rgba(0,0,0,0.3);">

    <!-- Logo -->
    <div style="padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.08);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="{{ asset('images/sonatrach-logo.png.png') }}"
                 style="width: 44px; height: 44px; object-fit: contain; filter: brightness(0) invert(1);"
                 onerror="this.style.display='none'">
            <div>
                <div style="font-size: 18px; font-weight: 900; color: white; line-height: 1;">IAP</div>
                <div style="font-size: 9px; color: rgba(255,255,255,0.5); font-weight: 700; text-transform: uppercase; letter-spacing: 2px;">Gestion Projets</div>
            </div>
        </div>
    </div>

    <!-- User Info -->
    <div style="padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #f97316, #ea580c); display: flex; align-items: center; justify-content: center; font-weight: 900; color: white; font-size: 18px; flex-shrink: 0;">
                {{ substr($user->name ?? 'U', 0, 1) }}
            </div>
            <div style="overflow: hidden;">
                <div style="font-size: 13px; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->name ?? 'Utilisateur' }}</div>
                <div style="font-size: 10px; color: #f97316; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                    @switch($role)
                        @case('admin') Administrateur @break
                        @case('assistant_dg') Assistant DG @break
                        @case('dg') Directeur Général @break
                        @case('directeur_ecole') Directeur École @break
                        @case('juriste') Juriste @break
                        @case('chef_projet') Chef de Projet @break
                        @default Invité
                    @endswitch
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div style="flex: 1; padding: 16px 12px; display: flex; flex-direction: column; gap: 4px; overflow-y: auto;">

        <div style="font-size: 9px; font-weight: 800; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 2px; padding: 0 12px; margin-bottom: 8px;">Navigation</div>

        <!-- Accueil -->
        <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('dashboard') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span style="font-size: 13px; font-weight: 600;">Accueil</span>
        </a>

        @if($role === 'admin')
            <a href="{{ route('admin.natures.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('admin.natures.index') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Natures Projets</span>
            </a>
            <a href="{{ route('admin.users.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('admin.users.index') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Utilisateurs</span>
            </a>
            <a href="{{ route('admin.defaults.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('admin.defaults.index') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Référentiel Juridique</span>
            </a>
        @endif

        @if($role === 'assistant_dg')
            <a href="{{ route('assistant_dg.projects.create') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('assistant_dg.projects.create') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Nouveau Projet</span>
            </a>
            <a href="{{ route('assistant_dg.projects.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; color: rgba(255,255,255,0.65);">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Mes Projets</span>
            </a>
        @endif

        @if($role === 'dg')
            <a href="{{ route('dg.dashboard') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('dg.dashboard') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Tous les Projets</span>
            </a>
            <a href="{{ route('dg.archives') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; color: rgba(255,255,255,0.65);">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Archives</span>
            </a>
        @endif
@if($role === 'directeur_ecole')
            <a href="{{ route('directeur_ecole.dashboard') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('directeur_ecole.dashboard') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Mes Projets</span>
            </a>
            <a href="{{ route('directeur_ecole.archives') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; color: rgba(255,255,255,0.65);">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Archives</span>
            </a>
        @endif

        @if($role === 'juriste')
            <a href="{{ route('juriste.dashboard') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('juriste.dashboard') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span style="font-size: 13px; font-weight: 600;">To-Do List</span>
            </a>
        @endif

        @if($role === 'chef_projet')
            <a href="{{ route('chef_projet.dashboard') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('chef_projet.dashboard') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
                <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Dépenses</span>
            </a>
        @endif

        <!-- Mon Profil -->
        <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; text-decoration: none; {{ request()->routeIs('profile.edit') ? 'background: linear-gradient(135deg, #f97316, #ea580c); color: white;' : 'color: rgba(255,255,255,0.65);' }}">
            <svg style="width: 18px; height: 18px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span style="font-size: 13px; font-weight: 600;">Mon Profil</span>
        </a>

    </div>

    <!-- Logout -->
    <div style="padding: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="width: 100%; display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 10px; background: none; border: none; cursor: pointer; color: rgba(255,255,255,0.5);">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span style="font-size: 13px; font-weight: 600;">Déconnexion</span>
            </button>
        </form>
    </div>

</nav>