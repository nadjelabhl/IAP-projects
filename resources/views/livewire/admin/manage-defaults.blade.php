<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Référentiel juridique par défaut</h1>
            <p class="text-sm text-slate-500 mt-1">Ces phases sont pré-remplies pour chaque nouveau projet.</p>
        </div>
        <button wire:click="openCreate"
            class="bg-iap-orange text-white font-bold px-5 py-2.5 rounded-xl hover:bg-orange-600 transition shadow">
            + Ajouter une phase
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <span class="font-semibold text-slate-700">{{ $defaults->count() }} phases définies</span>
            <span class="font-bold text-sm @if($totalPct > 100) text-red-600 @elseif($totalPct == 100) text-green-600 @else text-amber-600 @endif">
                Total : {{ number_format($totalPct, 1) }} % / 100 %
                @if($totalPct > 100) ⚠ DÉPASSEMENT @elseif($totalPct == 100) ✓ @endif
            </span>
        </div>

        @forelse($defaults as $d)
            <div class="flex items-center gap-4 px-6 py-4 border-b border-slate-50 hover:bg-slate-50 transition">
                <span class="w-8 h-8 rounded-full bg-iap-orange/10 text-iap-orange font-bold text-sm flex items-center justify-center">
                    {{ $d->order_number }}
                </span>
                <span class="flex-1 font-medium text-slate-800">{{ $d->name }}</span>
                <span class="font-bold text-iap-orange text-sm">{{ number_format($d->percentage, 1) }} %</span>
                <button wire:click="openEdit({{ $d->id }})"
                    class="text-slate-400 hover:text-iap-orange transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </button>
                <button wire:click="delete({{ $d->id }})"
                    wire:confirm="Supprimer cette phase du référentiel par défaut ?"
                    class="text-slate-400 hover:text-red-500 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        @empty
            <div class="px-6 py-10 text-center text-slate-400">Aucune phase définie.</div>
        @endforelse
    </div>

    {{-- Modale --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4">
                <h3 class="font-bold text-slate-800 text-lg mb-4">
                    {{ $editingId ? 'Modifier' : 'Ajouter' }} une phase
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">N° d'ordre</label>
                        <input wire:model="orderNumber" type="number" min="1"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange outline-none">
                        @error('orderNumber') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nom de la phase</label>
                        <input wire:model="name" type="text"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange outline-none">
                        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pourcentage (%)</label>
                        <input wire:model="percentage" type="number" step="0.01" min="0.01" max="100"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2 text-sm focus:border-iap-orange outline-none">
                        @error('percentage') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="save"
                        class="flex-1 bg-iap-orange text-white font-bold py-2 rounded-xl hover:bg-orange-600 transition">
                        Enregistrer
                    </button>
                    <button wire:click="closeModal"
                        class="flex-1 bg-slate-100 text-slate-700 font-bold py-2 rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
