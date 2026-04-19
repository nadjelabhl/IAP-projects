<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-900 p-6 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white">Gestion des Natures de Projets</h2>
                <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest">Administration</p>
            </div>
            <button wire:click="openModal" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded-xl transition">
                + Nouvelle Nature
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
                        <th class="text-right p-4 font-bold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($natures as $nature)
                        <tr class="border-b border-slate-100 hover:bg-slate-50">
                            <td class="p-4 font-medium text-slate-800">{{ $nature->name }}</td>
                            <td class="p-4 text-right space-x-4">
                                <button wire:click="edit({{ $nature->id }})" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Modifier</button>
                                <button wire:click="confirmDelete({{ $nature->id }})" class="text-red-600 hover:text-red-800 font-semibold text-sm">Supprimer</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $natures->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit -->
    @if($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <h3 class="text-lg font-bold mb-4 text-slate-800">
                    {{ $nature_id ? 'Modifier' : 'Nouvelle' }} Nature de Projet
                </h3>
                <form wire:submit.prevent="{{ $nature_id ? 'update' : 'create' }}">
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nom</label>
                        <input type="text" wire:model="name"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm focus:outline-none focus:border-blue-500"
                            placeholder="Ex: Constructions / Infrastructures">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal"
                            class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
                            Annuler
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Delete -->
    @if($deleteModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
[4/18/2026 1:58 AM] Nadjela: <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl">
                <h3 class="text-lg font-bold mb-2 text-slate-800">Confirmer la suppression</h3>
                <p class="mb-6 text-slate-600">Êtes-vous sûr de vouloir supprimer <strong>"{{ $natureToDelete?->name }}"</strong> ?</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" wire:click="$set('deleteModalOpen', false)"
                        class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50">
                        Annuler
                    </button>
                    <button type="button" wire:click="delete"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold">
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>