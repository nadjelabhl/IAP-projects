<div class="p-8">
    <div class="max-w-2xl mx-auto">

        @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold mb-6">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Fiche d'ouverture de projet</h1>
            <a href="{{ route('assistant_dg.dashboard') }}" class="text-sm font-semibold text-slate-400 hover:text-slate-600 transition-colors">← Retour</a>
        </div>

        @php
            $f = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-400/40 focus:border-orange-400 transition';
        @endphp

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
            <form wire:submit.prevent="save" class="p-6 space-y-4">

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Titre de projet <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="title_project" placeholder="Ex : Maintenance des pompes IAP Arzew" class="{{ $f }}">
                    @error('title_project') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nature du Projet <span class="text-red-400">*</span></label>
                        <select wire:model="nature_id" class="{{ $f }}">
                            <option value="">Sélectionner…</option>
                            @foreach($natures as $nature)
                                <option value="{{ $nature->id }}">{{ $nature->name }}</option>
                            @endforeach
                        </select>
                        @error('nature_id') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Type de Projet <span class="text-red-400">*</span></label>
                        <select wire:model="type_project" class="{{ $f }}">
                            <option value="Investissement">Investissement</option>
                            <option value="Exploitation">Exploitation</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">École IAP concernée <span class="text-red-400">*</span></label>
                    <select wire:model="school_id" class="{{ $f }}">
                        <option value="">Sélectionner l'école…</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Budget estimé (KDA) <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="budget" min="1" placeholder="Ex : 15000" class="{{ $f }}">
                        @error('budget') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Durée (mois) <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="duration_months" min="1" placeholder="Ex : 12" class="{{ $f }}">
                        @error('duration_months') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Année de début <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="start_year" min="2020" max="2040" placeholder="Ex : 2026" class="{{ $f }}">
                        @error('start_year') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Année de fin <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="end_year" min="2020" max="2050" placeholder="Ex : 2027" class="{{ $f }}">
                        @error('end_year') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Adresse / Localisation <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="localisation" placeholder="Ex : Zone Industrielle Arzew, Oran" class="{{ $f }}">
                    @error('localisation') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Description détaillée</label>
                    <textarea wire:model="description" rows="4" placeholder="Décrivez les objectifs, le périmètre et les contraintes du projet…"
                        class="{{ $f }} resize-none"></textarea>
                </div>

                <div class="flex justify-end pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-xl transition-colors shadow-sm shadow-orange-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Transmettre au DG
                    </button>
                </div>

            </form>
        </div>

    </div>
</div>
