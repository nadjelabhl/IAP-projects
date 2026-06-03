<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Bienvenue, {{ auth()->user()->name }}</h1>
        </div>
        {{-- Bell icon --}}
        <a href="{{ route('notifications.index') }}"
           class="relative shrink-0 mt-1 w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-orange-500 hover:border-orange-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center">{{ min($unreadCount, 9) }}</span>
            @endif
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <p class="text-sm font-semibold text-red-600">{{ session('error') }}</p>
    @endif

    {{-- Notification bar --}}
    @if($notifications->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h2 class="text-sm font-black text-slate-900">Notifications</h2>
                @if($unreadCount > 0)
                <span class="text-[11px] font-bold bg-orange-100 text-orange-600 px-2.5 py-0.5 rounded-full">{{ $unreadCount }} non lue{{ $unreadCount > 1 ? 's' : '' }}</span>
                @endif
            </div>
            <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-orange-500 hover:text-orange-700 transition-colors">Voir tout</a>
        </div>
        <div class="divide-y divide-slate-50 max-h-52 overflow-y-auto">
            @foreach($notifications as $notif)
            <div class="px-6 py-3 flex items-start gap-3 {{ $notif->is_read ? 'opacity-60' : '' }}">
                <div class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notif->is_read ? 'bg-slate-200' : 'bg-orange-400' }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $notif->message }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Project selector --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Nom de projet</h2>
        </div>
        <div class="p-4 space-y-3">
            @forelse($myProjects as $proj)
            <button wire:click="selectProject({{ $proj->id }})"
                class="w-full text-left p-4 rounded-xl border-2 transition-all {{ $selectedProjectId == $proj->id ? 'border-orange-400 bg-orange-50' : 'border-slate-100 bg-white hover:border-slate-200' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-bold text-sm text-slate-800">{{ $proj->title }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $proj->school->name }} &bull; {{ $proj->nature->name ?? '—' }}</p>
                    </div>
                    <x-status-badge :status="$proj->status"/>
                </div>
            </button>
            @empty
            <div class="text-center py-10">
                <p class="text-sm font-semibold text-slate-400">Aucun projet actif.</p>
                <p class="text-xs text-slate-400 mt-1">Votre accès est déverrouillé après l'ODS de Démarrage.</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($selectedProject)

    {{-- Fiche de projet --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-900">Fiche de projet</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $selectedProject->school->name }} &bull; {{ $selectedProject->nature->name ?? '—' }}</p>
                </div>
            </div>
            <button wire:click="openFicheModal"
               class="flex items-center gap-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 font-bold text-sm px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Voir fiche de projet
            </button>
        </div>
    </div>

    {{-- Budget gauge --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Consommation budgétaire</h3>
            <span class="text-3xl font-black tabular-nums {{ $budgetPct >= 80 ? 'text-red-600' : ($budgetPct >= 60 ? 'text-amber-600' : 'text-blue-600') }}">
                {{ number_format($budgetPct, 0) }}%
            </span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-5 overflow-hidden">
            <div class="h-5 rounded-full transition-all duration-500 {{ $budgetPct >= 80 ? 'bg-red-500' : ($budgetPct >= 60 ? 'bg-amber-500' : 'bg-blue-500') }}"
                style="width:{{ min($budgetPct, 100) }}%"></div>
        </div>
        @if($budgetPct >= 80)
        <div class="mt-3 flex items-center gap-3 p-3 bg-red-50 rounded-xl border border-red-200">
            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <p class="text-red-700 text-sm font-bold">ALERTE — Seuil de 80% atteint. Les parties prenantes ont été notifiées.</p>
        </div>
        @endif
        <div class="mt-4 grid grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-400 font-medium">Budget total</p>
                <p class="font-black text-slate-700 mt-1 tabular-nums">{{ number_format($selectedProject->budget, 0, ',', ' ') }} KDA</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-400 font-medium">Dépensé</p>
                <p class="font-black mt-1 tabular-nums {{ $budgetPct >= 80 ? 'text-red-600' : 'text-slate-700' }}">{{ number_format($totalSpent, 0, ',', ' ') }} KDA</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-xs text-slate-400 font-medium">Restant</p>
                <p class="font-black mt-1 tabular-nums {{ $budgetPct >= 80 ? 'text-red-600' : 'text-emerald-600' }}">{{ number_format(max(0, $selectedProject->budget - $totalSpent), 0, ',', ' ') }} KDA</p>
            </div>
        </div>
    </div>

    {{-- Add expense --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-4">Enregistrer une dépense</h3>
        @if($expensesLocked)
        <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            <p class="text-sm font-bold text-red-700">Ajout de dépense bloqué — ODS d'Arrêt en cours. En attente de l'ODS de Reprise du juriste.</p>
        </div>
        @else
        <form wire:submit.prevent="addExpense" class="space-y-4">
            <div>
                <label class="text-xs font-bold text-slate-600 mb-1.5 block">Description <span class="text-red-400">*</span></label>
                <input wire:model="expenseDescription" type="text" placeholder="Description de la dépense…"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                @error('expenseDescription') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-600 mb-1.5 block">Montant (KDA) <span class="text-red-400">*</span></label>
                    <input wire:model="expenseAmount" type="number" min="1" step="0.01" placeholder="0"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('expenseAmount') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-600 mb-1.5 block">Date d'engagement <span class="text-red-400">*</span></label>
                    <input wire:model="expenseDate" type="date"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    @error('expenseDate') <p class="text-sm text-red-700 mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>
            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-xl transition-colors">
                Enregistrer la dépense
            </button>
        </form>
        @endif
    </div>

    {{-- Expenses list --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Historique des dépenses</h3>
            <span class="text-xs text-slate-400 font-semibold">{{ count($expenses) }} dépense(s)</span>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($expenses as $expense)
            <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50/50 transition-colors">
                <div>
                    <p class="text-sm font-semibold text-slate-800">{{ $expense->description }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y') }}</p>
                </div>
                <span class="font-black text-slate-700 tabular-nums">{{ number_format($expense->amount, 0, ',', ' ') }} KDA</span>
            </div>
            @empty
            <div class="px-6 py-12 text-center text-slate-400 text-sm">Aucune dépense enregistrée.</div>
            @endforelse
        </div>
    </div>

    {{-- ODS History --}}
    @if($odsHistory->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Ordres de Service</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach($odsHistory as $ods)
            @php
                [$bg, $dot, $label] = match($ods->type) {
                    'Demarrage' => ['bg-emerald-50', 'bg-emerald-500', 'ODS de Démarrage'],
                    'Arret'     => ['bg-red-50',     'bg-red-500',     "ODS d'Arrêt"],
                    'Reprise'   => ['bg-blue-50',     'bg-blue-500',    'ODS de Reprise'],
                    default     => ['bg-slate-50',    'bg-slate-400',   $ods->type],
                };
            @endphp
            <div class="flex items-center gap-3 px-6 py-3.5 {{ $bg }}">
                <div class="w-2 h-2 rounded-full shrink-0 {{ $dot }}"></div>
                <span class="text-sm font-bold text-slate-800 shrink-0">{{ $label }}</span>
                <span class="text-xs text-slate-400 shrink-0 tabular-nums">{{ $ods->issued_at->locale('fr')->translatedFormat('d M Y') }}</span>
                @if($ods->issuedBy)
                <span class="text-xs text-slate-500 flex-1 truncate">{{ $ods->issuedBy->name }}</span>
                @else
                <span class="flex-1"></span>
                @endif
                {{-- File icon: always clickable, opens modal --}}
                <button wire:click="openOdsModal({{ $ods->id }})"
                        title="Consulter la fiche ODS"
                        class="shrink-0 w-8 h-8 rounded-lg bg-white border border-blue-200 flex items-center justify-center text-blue-600 hover:bg-blue-50 hover:border-blue-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Close project --}}
    <div class="bg-white rounded-2xl border-2 border-red-100 shadow-sm p-6">
        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide mb-4">Clôturer le projet</h3>
        @if(!$confirmClose)
        <button wire:click="$set('confirmClose', true)"
            class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition-colors">
            Clôturer le projet
        </button>
        @else
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 space-y-3">
            <p class="text-red-700 font-bold text-sm">Confirmez-vous la clôture définitive de ce projet ?</p>
            <div class="flex gap-3">
                <button wire:click="closeProject" class="bg-red-600 text-white font-bold px-6 py-2.5 rounded-xl hover:bg-red-700 transition-colors">
                    Oui, clôturer
                </button>
                <button wire:click="$set('confirmClose', false)" class="bg-slate-100 text-slate-700 font-bold px-6 py-2.5 rounded-xl hover:bg-slate-200 transition-colors">
                    Annuler
                </button>
            </div>
        </div>
        @endif
    </div>

    @endif

    {{-- MODALE FICHE ODS --}}
    @if($odsModalOpen && $odsModalRecord)
    @php
        [$bgBadge, $textBadge, $label] = match($odsModalRecord->type) {
            'Demarrage' => ['bg-emerald-100', 'text-emerald-700', 'ODS de Démarrage'],
            'Arret'     => ['bg-red-100',     'text-red-700',     "ODS d'Arrêt"],
            'Reprise'   => ['bg-blue-100',    'text-blue-700',    'ODS de Reprise'],
            default     => ['bg-slate-100',   'text-slate-700',   $odsModalRecord->type],
        };
    @endphp
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-auto overflow-hidden">

            {{-- Header --}}
            <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100">
                <div>
                    <span class="inline-block text-xs font-bold px-3 py-1 rounded-full {{ $bgBadge }} {{ $textBadge }} mb-1">{{ $label }}</span>
                    <p class="text-sm text-slate-500">{{ $odsModalRecord->issued_at->locale('fr')->translatedFormat('d M Y') }} à {{ $odsModalRecord->issued_at->format('H:i') }}</p>
                </div>
                <button wire:click="closeOdsModal" class="shrink-0 ml-4 w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-3">
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-28 text-[10px] font-bold text-slate-400 uppercase tracking-wider pt-0.5">Émis par</span>
                    <span class="flex-1 text-sm font-semibold text-slate-800">{{ $odsModalRecord->issuedBy->name ?? '—' }}</span>
                </div>
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-28 text-[10px] font-bold text-slate-400 uppercase tracking-wider pt-0.5">Date</span>
                    <span class="flex-1 text-sm font-semibold text-slate-800">{{ $odsModalRecord->issued_at->locale('fr')->translatedFormat('d M Y') }}</span>
                </div>
                @if($odsModalRecord->notes)
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-28 text-[10px] font-bold text-slate-400 uppercase tracking-wider pt-0.5">Notes</span>
                    <p class="flex-1 text-sm text-slate-700 leading-relaxed">{{ $odsModalRecord->notes }}</p>
                </div>
                @endif

                @if($odsModalPdfPath)
                <div class="pt-2">
                    <a href="{{ asset('storage/' . $odsModalPdfPath) }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 font-bold text-sm px-4 py-2.5 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Télécharger la fiche PDF
                    </a>
                </div>
                @else
                <div class="pt-2 flex items-center gap-2 text-xs text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Aucune fiche PDF jointe à cet ODS.
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button wire:click="closeOdsModal"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm px-5 py-2 rounded-xl transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- MODALE FICHE DE PROJET --}}
    @if($ficheModalOpen && $selectedProject)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         wire:click.self="closeFicheModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden">
            <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-slate-900 text-lg leading-tight">{{ $selectedProject->title }}</h3>
                    <x-status-badge :status="$selectedProject->status" class="mt-1"/>
                </div>
                <button wire:click="closeFicheModal" class="shrink-0 ml-4 w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-3 max-h-[70vh] overflow-y-auto">
                @php
                    $ficheRows = [
                        ['École',      $selectedProject->school->name ?? '—'],
                        ['Nature',     $selectedProject->nature->name ?? '—'],
                        ['Type',       $selectedProject->type],
                        ['Budget',     number_format($selectedProject->budget, 0, ',', ' ') . ' KDA'],
                        ['Durée',      $selectedProject->duration_months . ' mois'],
                        ['Période',    ($selectedProject->start_year ?? '—') . ' → ' . ($selectedProject->end_year ?? '—')],
                        ['Adresse',    $selectedProject->address ?? '—'],
                    ];
                @endphp
                @foreach($ficheRows as [$key, $val])
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-28 text-[10px] font-bold text-slate-400 uppercase tracking-wider pt-0.5">{{ $key }}</span>
                    <span class="flex-1 text-sm font-semibold text-slate-800">{{ $val }}</span>
                </div>
                @endforeach
                @if($selectedProject->description)
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-28 text-[10px] font-bold text-slate-400 uppercase tracking-wider pt-0.5">Description</span>
                    <p class="flex-1 text-sm text-slate-700 leading-relaxed">{{ $selectedProject->description }}</p>
                </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button wire:click="closeFicheModal"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm px-5 py-2 rounded-xl transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
