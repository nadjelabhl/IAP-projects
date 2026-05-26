<div class="p-8 space-y-8">

    {{-- Flash --}}
    @if(session('message'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif

    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Paramètres généraux</h1>
    </div>

    {{-- ═══ NATURES DE PROJETS ════════════════════════════════════════════════ --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-black text-slate-800 uppercase tracking-wide">Natures de projets</h2>
            <button wire:click="openNatureCreate"
                class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-sm shadow-orange-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Nouvelle nature
            </button>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nom</th>
                        <th class="text-right px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($natures as $nature)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-semibold text-slate-800">{{ $nature->name }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1 justify-end">
                                <button wire:click="openNatureEdit({{ $nature->id }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.829 1.172H7v-2a4 4 0 011.172-2.828z"/></svg>
                                </button>
                                <button wire:click="confirmDeleteNature({{ $nature->id }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($natures->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $natures->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ═══ RÉFÉRENTIEL JURIDIQUE ══════════════════════════════════════════════ --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-black text-slate-800 uppercase tracking-wide">Référentiel juridique</h2>
            <button wire:click="openDefaultCreate"
                class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-sm shadow-orange-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Ajouter une phase
            </button>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50">
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-14">N°</th>
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Phase</th>
                        <th class="text-left px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-32">Pourcentage</th>
                        <th class="text-right px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-28">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($defaults as $d)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 font-black text-sm flex items-center justify-center">
                                {{ $d->order_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-semibold text-sm text-slate-800">{{ $d->name }}</td>
                        <td class="px-6 py-4 font-black text-orange-500 text-sm tabular-nums">{{ (int) $d->percentage }} %</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1 justify-end">
                                <button wire:click="openDefaultEdit({{ $d->id }})"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.829 1.172H7v-2a4 4 0 011.172-2.828z"/></svg>
                                </button>
                                <button wire:click="deleteDefault({{ $d->id }})"
                                    wire:confirm="Supprimer cette phase du référentiel par défaut ?"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 text-sm">Aucune phase définie.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-3 border-t border-slate-100 flex justify-end">
                <span class="text-sm font-black {{ $totalPct > 100 ? 'text-red-600' : ($totalPct == 100 ? 'text-emerald-600' : 'text-amber-600') }}">
                    Total : {{ (int) $totalPct }} %
                </span>
            </div>
        </div>
    </div>

    {{-- ── Modal : Nouvelle / Modifier nature ─────────────────────────────── --}}
    @if($natureModalOpen)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="closeNatureModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">{{ $natureId ? 'Modifier' : 'Nouvelle' }} nature</h3>
                <button wire:click="closeNatureModal" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5">
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Nom <span class="text-red-400">*</span></label>
                <input type="text" wire:model="natureName" placeholder="Ex : Constructions / Infrastructures"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                @error('natureName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="closeNatureModal"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100">Annuler</button>
                <button wire:click="saveNature"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600">Enregistrer</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal : Confirmer suppression nature ───────────────────────────── --}}
    @if($deleteNatureOpen)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="$set('deleteNatureOpen', false)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-base font-black text-slate-900 mb-1">Supprimer la nature</h3>
                <p class="text-sm text-slate-500">Confirmez-vous la suppression de <strong class="text-slate-700">{{ $natureToDelete?->name }}</strong> ?</p>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('deleteNatureOpen', false)"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200">Annuler</button>
                <button wire:click="deleteNature"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-red-500 hover:bg-red-600">Supprimer</button>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Modal : Ajouter / Modifier phase ───────────────────────────────── --}}
    @if($defaultModalOpen)
    <div class="fixed inset-0 flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm"
         style="z-index:99999;"
         wire:click.self="closeDefaultModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">{{ $defaultId ? 'Modifier' : 'Ajouter' }} une phase</h3>
                <button wire:click="closeDefaultModal" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                @php $ic = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400'; @endphp
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">N° d'ordre <span class="text-red-400">*</span></label>
                    <input wire:model="orderNumber" type="number" min="1" class="{{ $ic }}">
                    @error('orderNumber') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Nom de la phase <span class="text-red-400">*</span></label>
                    <input wire:model="defaultName" type="text" placeholder="Ex : Étude de faisabilité" class="{{ $ic }}">
                    @error('defaultName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Pourcentage (%) <span class="text-red-400">*</span></label>
                    <input wire:model="percentage" type="number" step="1" min="1" max="100" class="{{ $ic }}">
                    @error('percentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="closeDefaultModal"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100">Annuler</button>
                <button wire:click="saveDefault"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600">Enregistrer</button>
            </div>
        </div>
    </div>
    @endif

</div>
