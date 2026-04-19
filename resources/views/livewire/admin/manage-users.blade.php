<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-900 p-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">Gestion des Utilisateurs</h2>
                <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest">Administration - Comptes utilisateurs</p>
            </div>
            <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl transition">
                + Nouvel Utilisateur
            </button>
        </div>

        <div class="p-6">
            @if(session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('message') }}
                </div>
            @endif

            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left p-4 font-bold text-slate-700">Nom</th>
                        <th class="text-left p-4 font-bold text-slate-700">Email</th>
                        <th class="text-left p-4 font-bold text-slate-700">Rôle</th>
                        <th class="text-left p-4 font-bold text-slate-700">École</th>
                        <th class="text-left p-4 font-bold text-slate-700">Statut</th>
                        <th class="text-right p-4 font-bold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-4 font-medium">{{ $user->name }}</td>
                            <td class="p-4">{{ $user->email }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700">
                                    {{ $user->role_label }}
                                </span>
                            </td>
                            <td class="p-4">{{ $user->school?->name ?? '-' }}</td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button wire:click="edit({{ $user->id }})" class="text-blue-600 hover:text-blue-800">Modifier</button>
                                <button wire:click="confirmDelete({{ $user->id }})" class="text-red-600 hover:text-red-800">Désactiver</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 w-full max-w-lg">
                <h3 class="text-lg font-bold mb-4">{{ $user_id ? 'Modifier' : 'Nouvel' }} Utilisateur</h3>
                <form wire:submit.prevent="{{ $user_id ? 'update' : 'create' }}" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Nom</label>
                        <input type="text" wire:model="name" class="w-full rounded-xl border-slate-200">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full rounded-xl border-slate-200">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Mot de passe {{ $user_id ? '(laisser vide pour conservez)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full rounded-xl border-slate-200">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Rôle</label>
                            <select wire:model="role" class="w-full rounded-xl border-slate-200">
                                <option value="admin">Admin</option>
                                <option value="assistant_dg">Assistant DG</option>
                                <option value="dg">Directeur Général</option>
                                <option value="directeur_ecole">Directeur École</option>
                                <option value="juriste">Juriste</option>
                                <option value="chef_projet">Chef de Projet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">École</label>
                            <select wire:model="school_id" class="w-full rounded-xl border-slate-200">
                                <option value="">Sélectionner...</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="is_active" class="rounded border-slate-200">
                            <span class="ml-2 text-sm">Compte actif</span>
                        </label>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-xl">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Modal -->
    @if($deleteModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md">
                <h3 class="text-lg font-bold mb-4">Confirmer la désactivation</h3>
                <p class="mb-4">Êtes-vous sûr de vouloir désactiver "{{ $userToDelete->name }}" ?</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="deleteModalOpen = false" class="px-4 py-2 border rounded-xl">Annuler</button>
                    <button type="button" wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-xl">Désactiver</button>
                </div>
            </div>
        </div>
    @endif
</div>