<div class="p-8 space-y-6">

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Référentiel juridique par défaut</h1>
            <p class="text-sm text-slate-400 mt-0.5">Ces phases sont pré-remplies pour chaque nouveau projet</p>
        </div>
        <button wire:click="openCreate"
            class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-orange-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            Ajouter une phase
        </button>
    </div>

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-700">{{ $defaults->count() }} phase(s) définie(s)</span>
            <span class="text-sm font-black {{ $totalPct > 100 ? 'text-red-600' : ($totalPct == 100 ? 'text-emerald-600' : 'text-amber-600') }}">
                Total : {{ number_format($totalPct, 1) }}% / 100%
                @if($totalPct > 100) ⚠ DÉPASSEMENT @elseif($totalPct == 100) ✓ @endif
            </span>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse($defaults as $d)
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/50 transition-colors">
                <span class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 font-black text-sm flex items-center justify-center shrink-0">
                    {{ $d->order_number }}
                </span>
                <span class="flex-1 font-semibold text-sm text-slate-800">{{ $d->name }}</span>
                <span class="font-black text-orange-500 text-sm tabular-nums shrink-0">{{ number_format($d->percentage, 1) }}%</span>
                <button wire:click="openEdit({{ $d->id }})"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-2.829 1.172H7v-2a4 4 0 011.172-2.828z"/></svg>
                </button>
                <button wire:click="delete({{ $d->id }})"
                    wire:confirm="Supprimer cette phase du référentiel par défaut ?"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-slate-400 text-sm">Aucune phase définie.</div>
            @endforelse
        </div>
    </div>

    {{-- Modal --}}
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-black text-slate-900">{{ $editingId ? 'Modifier' : 'Ajouter' }} une phase</h3>
                <button wire:click="closeModal" class="p-2 rounded-xl text-slate-400 hover:bg-slate-100">
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
                    <input wire:model="name" type="text" placeholder="Ex : Étude de faisabilité" class="{{ $ic }}">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1.5">Pourcentage (%) <span class="text-red-400">*</span></label>
                    <input wire:model="percentage" type="number" step="0.01" min="0.01" max="100" class="{{ $ic }}">
                    @error('percentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="closeModal" class="px-5 py-2.5 rounded-xl text-sm font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100">Annuler</button>
                <button wire:click="save" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-orange-500 hover:bg-orange-600">Enregistrer</button>
            </div>
        </div>
    </div>
    @endif

</div>
