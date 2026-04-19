<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-900 p-6">
            <h2 class="text-xl font-bold text-white">Fiche d'Ouverture de Projet</h2>
            <p class="text-slate-400 text-xs mt-1 uppercase tracking-widest">Étape 01 : Saisie Assistant DG</p>
        </div>

        <form wire:submit.prevent="save" class="p-8 space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Titre de l'opération</label>
                <input type="text" wire:model="title" class="w-full rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="Ex: Maintenance des pompes IAP Arzew">
                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nature du Projet</label>
                    <select wire:model="nature_id" class="w-full rounded-xl border-slate-200">
                        <option value="">Sélectionner...</option>
                        @foreach($natures as $nature)
                            <option value="{{ $nature->id }}">{{ $nature->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Type de Projet</label>
                    <select wire:model="type" class="w-full rounded-xl border-slate-200">
                        <option value="Investissement">Investissement</option>
                        <option value="Exploitation">Exploitation</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">École IAP Concernée</label>
                <select wire:model="school_id" class="w-full rounded-xl border-slate-200">
                    <option value="">Sélectionner l'école...</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Budget Estimé (DA)</label>
                    <input type="number" wire:model="budget" class="w-full rounded-xl border-slate-200" placeholder="Ex: 5000000">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Durée (mois)</label>
                    <input type="number" wire:model="duration_months" class="w-full rounded-xl border-slate-200" placeholder="Ex: 12">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Année de Début</label>
                    <input type="number" wire:model="start_year" class="w-full rounded-xl border-slate-200" placeholder="Ex: 2026">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Année de Fin</label>
                    <input type="number" wire:model="end_year" class="w-full rounded-xl border-slate-200" placeholder="Ex: 2027">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Adresse / Localisation</label>
                <input type="text" wire:model="address" class="w-full rounded-xl border-slate-200">
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Description détaillée</label>
                <textarea wire:model="description" rows="3" class="w-full rounded-xl border-slate-200"></textarea>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition shadow-lg shadow-blue-200">
                    Enregistrer et Transmettre au DG
                </button>
            </div>
        </form>
    </div>
</div>