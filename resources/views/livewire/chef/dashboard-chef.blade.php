<div class="max-w-7xl mx-auto space-y-6">

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-red-800 text-sm font-semibold">{{ session('error') }}</div>
    @endif

    {{-- Notifications ODS urgentes --}}
    @foreach($notifications as $n)
        <div class="flex items-start gap-3 px-5 py-4 rounded-xl
            @if($n->type === 'ods_arret') bg-red-50 border border-red-200
            @else bg-blue-50 border border-blue-200 @endif">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5 @if($n->type === 'ods_arret') text-red-600 @else text-blue-600 @endif"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <p class="text-sm @if($n->type === 'ods_arret') text-red-700 @else text-blue-700 @endif font-semibold">
                {{ $n->message }}
            </p>
        </div>
    @endforeach

    {{-- Sélection du projet --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="font-bold text-slate-800 mb-4">Mes projets actifs</h2>
        @forelse($myProjects as $proj)
            <button wire:click="selectProject({{ $proj->id }})"
                class="w-full text-left p-4 rounded-xl border-2 mb-3 transition
                    @if($selectedProjectId == $proj->id) border-iap-orange bg-orange-50 @else border-slate-200 hover:border-slate-300 @endif">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900">{{ $proj->title }}</p>
                        <p class="text-xs text-slate-500">{{ $proj->school->name }} &bull; {{ $proj->nature->name ?? '—' }}</p>
                    </div>
                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full">{{ $proj->status }}</span>
                </div>
            </button>
        @empty
            <div class="text-center py-8">
                <p class="text-slate-400 font-semibold">Aucun projet actif.</p>
                <p class="text-slate-400 text-sm mt-1">Votre accès est déverrouillé uniquement après l'ODS de Démarrage.</p>
            </div>
        @endforelse
    </div>

    @if($selectedProject)
        {{-- ====================================================================
             JAUGE BUDGÉTAIRE (Composant E — Section 6)
        ===================================================================== --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-bold text-slate-800">Consommation budgétaire</h3>
                <span class="font-black text-2xl @if($budgetPct >= 80) text-red-600 @elseif($budgetPct >= 60) text-amber-600 @else text-blue-600 @endif">
                    {{ number_format($budgetPct, 1) }} %
                </span>
            </div>

            <div class="w-full bg-slate-200 rounded-full h-5 overflow-hidden">
                <div class="h-5 rounded-full transition-all duration-500
                    @if($budgetPct >= 80) bg-red-500 @elseif($budgetPct >= 60) bg-amber-500 @else bg-blue-500 @endif"
                    style="width:{{ min($budgetPct, 100) }}%">
                </div>
            </div>

            @if($budgetPct >= 80)
                <div class="mt-3 flex items-center gap-2 p-3 bg-red-50 rounded-xl border border-red-200">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <p class="text-red-700 text-sm font-bold">ALERTE — Seuil de 80 % atteint. Les parties prenantes ont été notifiées.</p>
                </div>
            @endif

            <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-500">Budget total</p>
                    <p class="font-black text-slate-700 mt-1">{{ number_format($selectedProject->budget, 0, ',', ' ') }} DA</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-500">Total dépensé</p>
                    <p class="font-black @if($budgetPct >= 80) text-red-600 @else text-slate-700 @endif mt-1">
                        {{ number_format($totalSpent, 0, ',', ' ') }} DA
                    </p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-500">Restant</p>
                    <p class="font-black @if($budgetPct >= 80) text-red-600 @else text-green-600 @endif mt-1">
                        {{ number_format(max(0, $selectedProject->budget - $totalSpent), 0, ',', ' ') }} DA
                    </p>
                </div>
            </div>
        </div>

        {{-- ====================================================================
             FORMULAIRE SAISIE DÉPENSE
        ===================================================================== --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="font-bold text-slate-800 mb-4">Enregistrer une dépense</h3>
            <form wire:submit="addExpense" class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Description</label>
                    <input wire:model="expenseDescription" type="text" placeholder="Description de la dépense"
                        class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 outline-none">
                    @error('expenseDescription') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Montant (DA)</label>
                        <input wire:model="expenseAmount" type="number" min="1" step="0.01" placeholder="0"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 outline-none">
                        @error('expenseAmount') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Date d'engagement</label>
                        <input wire:model="expenseDate" type="date"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 outline-none">
                        @error('expenseDate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button type="submit"
                    class="bg-iap-orange text-white font-bold px-8 py-3 rounded-xl hover:bg-orange-600 transition shadow">
                    Enregistrer la dépense
                </button>
            </form>
        </div>

        {{-- ====================================================================
             LISTE DES DÉPENSES
        ===================================================================== --}}
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800">Historique des dépenses</h3>
                <span class="text-sm text-slate-500">{{ count($expenses) }} dépense(s)</span>
            </div>

            @forelse($expenses as $expense)
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50 hover:bg-slate-50 transition">
                    <div>
                        <p class="font-semibold text-slate-800">{{ $expense->description }}</p>
                        <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</p>
                    </div>
                    <span class="font-black text-slate-700">{{ number_format($expense->amount, 0, ',', ' ') }} DA</span>
                </div>
            @empty
                <div class="px-6 py-10 text-center text-slate-400">Aucune dépense enregistrée.</div>
            @endforelse
        </div>

        {{-- ====================================================================
             HISTORIQUE ODS
        ===================================================================== --}}
        @if($odsHistory->isNotEmpty())
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-slate-800 mb-4">Ordres de Service</h3>
                <div class="space-y-3">
                    @foreach($odsHistory as $ods)
                        <div class="flex items-center gap-4 p-3 rounded-xl border
                            @if($ods->type === 'Demarrage') bg-green-50 border-green-200
                            @elseif($ods->type === 'Arret') bg-red-50 border-red-200
                            @else bg-blue-50 border-blue-200 @endif">
                            <span class="font-bold text-sm
                                @if($ods->type === 'Demarrage') text-green-700
                                @elseif($ods->type === 'Arret') text-red-700
                                @else text-blue-700 @endif">
                                ODS {{ $ods->type }}
                            </span>
                            <span class="text-xs text-slate-500">
                                {{ $ods->issued_at->format('d/m/Y H:i') }}
                                @if($ods->issuedBy) &bull; {{ $ods->issuedBy->name }} @endif
                            </span>
                            @if($ods->notes)
                                <span class="text-xs text-slate-500 flex-1 truncate italic">{{ $ods->notes }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ====================================================================
             CLÔTURE DU PROJET
        ===================================================================== --}}
        <div class="bg-white rounded-2xl shadow p-6 border-2 border-red-100">
            <h3 class="font-bold text-slate-800 mb-2">Clôturer le projet</h3>
            <p class="text-sm text-slate-500 mb-4">
                Cette action est irréversible. Le projet sera marqué comme <strong>Terminé</strong> et archivé.
            </p>

            @if(!$confirmClose)
                <button wire:click="$set('confirmClose', true)"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition">
                    Clôturer le projet
                </button>
            @else
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-3">
                    <p class="text-red-700 font-bold text-sm">Êtes-vous certain de vouloir clôturer ce projet ?</p>
                    <div class="flex gap-3">
                        <button wire:click="closeProject"
                            class="bg-red-600 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-red-700 transition">
                            Oui, clôturer définitivement
                        </button>
                        <button wire:click="$set('confirmClose', false)"
                            class="bg-slate-100 text-slate-700 font-bold px-6 py-2.5 rounded-xl hover:bg-slate-200 transition">
                            Annuler
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
