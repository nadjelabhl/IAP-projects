<div class="p-8 space-y-6">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div>
        <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
        <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">BIENVENUE</h1>
    </div>

    {{-- Stat cards --}}
    <div class="grid grid-cols-3 gap-5">
        <x-stat-card label="Utilisateurs" :value="$totalUsers" tone="blue" sub="Comptes enregistrés"/>
        <x-stat-card label="Projets" :value="$totalProjects" tone="orange" sub="En cours de suivi"/>
        <x-stat-card label="Écoles" :value="$totalSchools" tone="green" sub="Instituts IAP"/>
    </div>

    {{-- Main grid --}}
    <div class="grid grid-cols-12 gap-5">

        {{-- Projets par école --}}
        <div class="col-span-5 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Projets par école</h2>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach($projetsParEcole as $ecole)
                <div class="flex items-center justify-between px-6 py-3.5">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-2 h-2 rounded-full bg-orange-400 shrink-0"></div>
                        <span class="text-sm font-semibold text-slate-700 truncate">{{ $ecole->name }}</span>
                    </div>
                    <span class="text-sm font-black text-slate-900 tabular-nums shrink-0 ml-3">{{ $ecole->projects_count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Utilisateurs table --}}
        <div class="col-span-7 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Gestion des utilisateurs</h2>
                <button wire:click="openCreate"
                    class="flex items-center gap-1.5 bg-orange-500 hover:bg-orange-600 active:bg-orange-700 text-white text-xs font-bold px-3.5 py-1.5 rounded-lg shadow-sm shadow-orange-200 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                    Nouvel utilisateur
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50">
                            <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nom</th>
                            <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rôle</th>
                            <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">École</th>
                            <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-black shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-slate-800 truncate">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-400 truncate">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $roleLabels = [
                                        'admin'            => ['label' => 'Admin',        'class' => 'bg-purple-50 text-purple-700'],
                                        'dg'               => ['label' => 'DG',           'class' => 'bg-blue-50 text-blue-700'],
                                        'assistant_dg'     => ['label' => 'Assist. DG',   'class' => 'bg-sky-50 text-sky-700'],
                                        'directeur_ecole'  => ['label' => 'Directeur',    'class' => 'bg-teal-50 text-teal-700'],
                                        'juriste'          => ['label' => 'Juriste',      'class' => 'bg-amber-50 text-amber-700'],
                                        'chef_projet'      => ['label' => 'Chef Projet',  'class' => 'bg-orange-50 text-orange-700'],
                                    ];
                                    $r = $roleLabels[$user->role] ?? ['label' => $user->role, 'class' => 'bg-slate-100 text-slate-600'];
                                @endphp
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg {{ $r['class'] }}">{{ $r['label'] }}</span>
                            </td>
                            <td class="px-5 py-3 text-xs text-slate-500 font-medium">
                                {{ $user->school?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3">
                                <button wire:click="toggleActive({{ $user->id }})" class="flex items-center gap-1.5 group">
                                    @if($user->is_active)
                                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                        <span class="text-[11px] font-bold text-emerald-600 group-hover:text-emerald-700">Actif</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                                        <span class="text-[11px] font-bold text-slate-400 group-hover:text-slate-500">Inactif</span>
                                    @endif
                                </button>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1 justify-end">
                                    <button wire:click="openEdit({{ $user->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.829 1.172H7v-2a4 4 0 011.172-2.828z"/></svg>
                                    </button>
                                    <button wire:click="openDelete({{ $user->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
            <div class="px-5 py-3 border-t border-slate-100">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- ── Modal : Créer utilisateur ───────────────────────────────────────── --}}
    @if($createOpen)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="$set('createOpen',false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">Créer un compte</h3>
                <button wire:click="$set('createOpen',false)" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                @include('livewire.admin.partials.user-form')
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('createOpen',false)"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors">
                    Annuler
                </button>
                <button wire:click="saveCreate"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 transition-colors shadow-sm shadow-orange-200">
                    Créer le compte
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal : Modifier utilisateur ──────────────────────────────────────── --}}
    @if($editOpen)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="$set('editOpen',false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">Modifier le compte</h3>
                <button wire:click="$set('editOpen',false)" class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                @include('livewire.admin.partials.user-form')
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('editOpen',false)"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 transition-colors">
                    Annuler
                </button>
                <button wire:click="saveEdit"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                    Enregistrer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal : Supprimer utilisateur ─────────────────────────────────────── --}}
    @if($deleteOpen)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="$set('deleteOpen',false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-base font-black text-slate-900 mb-1">Supprimer le compte</h3>
                @if($deletingUser)
                <p class="text-sm text-slate-500">
                    Confirmez-vous la suppression de <span class="font-bold text-slate-700">{{ $deletingUser->name }}</span> ?
                </p>
                @endif
                <p class="text-xs text-red-400 mt-2 font-semibold">Cette action est irréversible.</p>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('deleteOpen',false)"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors">
                    Annuler
                </button>
                <button wire:click="confirmDelete"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600 transition-colors">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
