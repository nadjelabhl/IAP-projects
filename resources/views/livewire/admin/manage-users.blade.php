<div class="p-8 space-y-6">

    {{-- Flash --}}
    @if(session('message'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Gestion des utilisateurs</h1>
            <p class="text-sm text-slate-400 mt-0.5">Administration — comptes utilisateurs</p>
        </div>
        <button wire:click="openModal"
            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-orange-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Nouvel utilisateur
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nom</th>
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email</th>
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Rôle</th>
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">École</th>
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700">{{ $user->role_label ?? $user->role }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $user->school?->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg {{ $user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1 justify-end">
                                <button wire:click="edit({{ $user->id }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.829 1.172H7v-2a4 4 0 011.172-2.828z"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $user->id }})"
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
        <div class="px-6 py-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>

    {{-- Modal Create/Edit --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">{{ $user_id ? 'Modifier' : 'Nouvel' }} utilisateur</h3>
                <button wire:click="closeModal" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            @php $ic = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400'; @endphp
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nom</label>
                    <input type="text" wire:model="name" class="{{ $ic }}">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Email</label>
                    <input type="email" wire:model="email" class="{{ $ic }}">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Mot de passe {{ $user_id ? '(laisser vide pour conserver)' : '' }}</label>
                    <input type="password" wire:model="password" class="{{ $ic }}">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Rôle</label>
                        <select wire:model="role" class="{{ $ic }}">
                            <option value="admin">Administrateur</option>
                            <option value="assistant_dg">Assistante DG</option>
                            <option value="dg">Direction Générale</option>
                            <option value="directeur_ecole">Directeur d'École</option>
                            <option value="juriste">Juriste</option>
                            <option value="chef_projet">Chef de Projet</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">École</label>
                        <select wire:model="school_id" class="{{ $ic }}">
                            <option value="">— Aucune —</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_active" id="isActiveUser" class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
                    <label for="isActiveUser" class="text-sm font-medium text-slate-700">Compte actif</label>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100">Annuler</button>
                <button wire:click="{{ $user_id ? 'update' : 'create' }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600">Enregistrer</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Delete/Deactivate --}}
    @if($deleteModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('deleteModalOpen', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-base font-black text-slate-900 mb-1">Désactiver l'utilisateur</h3>
                <p class="text-sm text-slate-500">Confirmez-vous la désactivation de <strong class="text-slate-700">{{ $userToDelete?->name }}</strong> ?</p>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('deleteModalOpen', false)" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">Annuler</button>
                <button wire:click="delete" class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600">Désactiver</button>
            </div>
        </div>
    </div>
    @endif

</div>
