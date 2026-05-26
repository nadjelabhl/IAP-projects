<div class="p-8">
    <div class="max-w-2xl mx-auto">

        {{-- Flash --}}
        @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold mb-6">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Fiche de projet</h1>
        </div>

        {{-- Form card --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 bg-slate-900">
                <h2 class="text-sm font-black text-white uppercase tracking-wide">Informations du projet</h2>
            </div>

            <form wire:submit.prevent="save" class="p-6 space-y-5">
                @php $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-transparent transition'; @endphp

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Titre de projet <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="title" placeholder="Ex : Maintenance des pompes IAP Arzew"
                        class="{{ $inputClass }}">
                    @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nature + Type --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Nature du Projet <span class="text-red-400">*</span></label>
                        <select wire:model="nature_id" class="{{ $inputClass }}">
                            <option value="">Sélectionner…</option>
                            @foreach($natures as $nature)
                                <option value="{{ $nature->id }}">{{ $nature->name }}</option>
                            @endforeach
                        </select>
                        @error('nature_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Type de Projet <span class="text-red-400">*</span></label>
                        <select wire:model="type" class="{{ $inputClass }}">
                            <option value="Investissement">Investissement</option>
                            <option value="Exploitation">Exploitation</option>
                        </select>
                    </div>
                </div>

                {{-- School --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">École IAP concernée <span class="text-red-400">*</span></label>
                    <select wire:model="school_id" class="{{ $inputClass }}">
                        <option value="">Sélectionner l'école…</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Budget + Duration --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Budget estimé (KDA) <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="budget" min="1" placeholder="Ex : 15000"
                            class="{{ $inputClass }}">
                        @error('budget') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Durée (mois) <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="duration_months" min="1" placeholder="Ex : 12"
                            class="{{ $inputClass }}">
                        @error('duration_months') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Years --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Année de début <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="start_year" min="2020" max="2040" placeholder="Ex : 2026"
                            class="{{ $inputClass }}">
                        @error('start_year') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5">Année de fin <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="end_year" min="2020" max="2050" placeholder="Ex : 2027"
                            class="{{ $inputClass }}">
                        @error('end_year') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Adresse / Localisation</label>
                    <input type="text" wire:model="address" placeholder="Ex : Zone Industrielle Arzew, Oran"
                        class="{{ $inputClass }}">
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Description détaillée</label>
                    <textarea wire:model="description" rows="4" placeholder="Décrivez les objectifs, le périmètre et les contraintes du projet…"
                        class="{{ $inputClass }} resize-none"></textarea>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <a href="{{ route('assistant_dg.dashboard') }}" class="text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">
                        ← Retour au tableau de bord
                    </a>
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
