@php
$user = auth()->user();
$role = $user->role ?? 'guest';
$initial = $user ? strtoupper(substr($user->name, 0, 1)) : 'U';
$subtitle = match($role) {
    'admin'          => 'ADMINISTRATEUR',
    'dg'             => 'DIRECTION GÉNÉRALE',
    'assistant_dg'   => 'ASSISTANTE DG',
    'directeur_ecole'=> 'DIRECTEUR D\'ÉCOLE',
    'juriste'        => 'PÔLE JURIDIQUE',
    'chef_projet'    => 'INGÉNIERIE',
    default          => 'UTILISATEUR',
};
@endphp

<aside class="w-[280px] shrink-0 bg-slate-900 text-slate-100 flex flex-col h-screen fixed top-0 left-0 z-40">

    {{-- Brand --}}
    <div class="px-6 pt-6 pb-5 flex items-center gap-3">
        <img src="{{ asset('images/sonatrach-logo.png.png') }}" alt="Sonatrach"
            class="w-11 h-11 object-contain shrink-0"
            style="filter: brightness(0) saturate(100%) invert(55%) sepia(90%) saturate(800%) hue-rotate(340deg) brightness(105%);">
        <div>
            <div class="font-extrabold text-[15px] leading-tight tracking-tight text-white">IAP</div>
            <div class="text-[10px] tracking-[0.18em] text-slate-400 font-semibold">GESTION PROJETS</div>
        </div>
    </div>

    {{-- User card --}}
    <div class="mx-4 mb-5 rounded-2xl bg-slate-800/60 px-4 py-3 flex items-center gap-3 border border-white/5">
        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center font-bold text-white text-sm shrink-0">
            {{ $initial }}
        </div>
        <div class="min-w-0">
            <div class="font-bold text-[13px] leading-tight text-white truncate">{{ $user->name ?? 'Utilisateur' }}</div>
            <div class="text-[10px] tracking-[0.14em] text-orange-400 font-semibold mt-0.5 truncate">{{ $subtitle }}</div>
        </div>
    </div>

    {{-- Nav label --}}
    <div class="px-6 text-[10px] tracking-[0.22em] text-slate-500 font-bold mb-2">NAVIGATION</div>

    {{-- Nav links --}}
    <nav class="flex-1 px-3 space-y-1 overflow-y-auto">

        @if($role === 'admin')
            <x-nav-item route="admin.dashboard" :active="request()->routeIs('admin.dashboard')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5 10v10h14V10"/></svg></x-slot:icon>
                Accueil
            </x-nav-item>
            <x-nav-item route="admin.natures.index" :active="request()->routeIs('admin.natures.index', 'admin.defaults.index')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.5 12.6 12.6 20.5a2 2 0 01-2.8 0L3 13.6V3.5h10.1l7.4 7.3a2 2 0 010 2.8z"/><circle cx="8" cy="8" r="1.4"/></svg></x-slot:icon>
                Paramètres généraux
            </x-nav-item>
        @elseif($role === 'dg')
            <x-nav-item route="dg.dashboard" :active="request()->routeIs('dg.dashboard')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5 10v10h14V10"/></svg></x-slot:icon>
                Accueil
            </x-nav-item>
            <x-nav-item route="dg.archives" :active="request()->routeIs('dg.archives')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="4" width="18" height="4" rx="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 8v12h14V8M10 13h4"/></svg></x-slot:icon>
                Archives
            </x-nav-item>

        @elseif($role === 'assistant_dg')
            <x-nav-item route="assistant_dg.dashboard" :active="request()->routeIs('assistant_dg.dashboard')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5 10v10h14V10"/></svg></x-slot:icon>
                Accueil
            </x-nav-item>
            <x-nav-item route="assistant_dg.projects.create" :active="request()->routeIs('assistant_dg.projects.create')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg></x-slot:icon>
                Nouveau Projet
            </x-nav-item>
            <x-nav-item route="assistant_dg.archives" :active="request()->routeIs('assistant_dg.archives')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="4" width="18" height="4" rx="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 8v12h14V8M10 13h4"/></svg></x-slot:icon>
                Archives
            </x-nav-item>

        @elseif($role === 'directeur_ecole')
            <x-nav-item route="directeur_ecole.dashboard" :active="request()->routeIs('directeur_ecole.dashboard')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5 10v10h14V10"/></svg></x-slot:icon>
                Accueil
            </x-nav-item>
            <x-nav-item route="directeur_ecole.projects.index" :active="request()->routeIs('directeur_ecole.projects.*')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></x-slot:icon>
                Projets
            </x-nav-item>
            <x-nav-item route="directeur_ecole.rh" :active="request()->routeIs('directeur_ecole.rh')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-slot:icon>
                Ressources Humaines
            </x-nav-item>
            <x-nav-item route="directeur_ecole.archives" :active="request()->routeIs('directeur_ecole.archives')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="3" y="4" width="18" height="4" rx="1"/><path stroke-linecap="round" stroke-linejoin="round" d="M5 8v12h14V8M10 13h4"/></svg></x-slot:icon>
                Archives
            </x-nav-item>

        @elseif($role === 'juriste')
            <x-nav-item route="juriste.dashboard" :active="request()->routeIs('juriste.dashboard')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><rect x="6" y="4" width="12" height="17" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 4V2.5h6V4M9 10h6M9 14h6M9 18h4"/></svg></x-slot:icon>
                Mes Projets
            </x-nav-item>

        @elseif($role === 'chef_projet')
            <x-nav-item route="chef_projet.dashboard" :active="request()->routeIs('chef_projet.dashboard')">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></x-slot:icon>
                Mon Projet
            </x-nav-item>
        @endif

        {{-- Profil commun --}}
        <x-nav-item route="profile.edit" :active="request()->routeIs('profile.edit')">
            <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg></x-slot:icon>
            Mon Profil
        </x-nav-item>
    </nav>

    {{-- Déconnexion --}}
    <div class="p-3 border-t border-slate-800">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13px] font-semibold text-slate-300 hover:bg-slate-800/60 hover:text-white transition-colors">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h12"/>
                </svg>
                Déconnexion
            </button>
        </form>
    </div>
</aside>
