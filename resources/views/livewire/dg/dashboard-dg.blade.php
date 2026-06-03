<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Bienvenue, {{ auth()->user()->name }}</h1>
        </div>
        {{-- Bell icon → notifications --}}
        <a href="{{ route('notifications.index') }}" class="relative shrink-0 mt-1 w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-orange-500 hover:border-orange-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center">{{ min($unreadCount, 9) }}</span>
            @endif
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-5 gap-4">
        @foreach([
            ['TOTAL PROJETS', $stats['total_projects'], 'text-slate-900',   'border-slate-200'],
            ['NOUVEAU',       $stats['nouveau'],        'text-amber-500',   'border-amber-200'],
            ['EN ÉTUDE',      $stats['en_etude'],       'text-orange-500',  'border-orange-200'],
            ['EN COURS',      $stats['en_cours'],       'text-blue-500',    'border-blue-200'],
            ['TERMINÉ',       $stats['termine'],        'text-emerald-500', 'border-emerald-200'],
        ] as [$label, $value, $textColor, $border])
        <div class="bg-white rounded-2xl border {{ $border }} p-5 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ $label }}</p>
            <p class="text-5xl font-black {{ $textColor }} tabular-nums leading-none">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- Notifications --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Notifications</h3>
            @if($unreadCount > 0)
                <span class="text-xs font-bold bg-red-500 text-white px-2.5 py-1 rounded-full">{{ $unreadCount }} non lue(s)</span>
            @endif
        </div>
        <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
            @forelse($notifications as $n)
            <div class="flex items-start gap-3 px-6 py-4 {{ $n->is_read ? '' : 'bg-orange-50/40' }}">
                <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $n->priority === 'urgent' ? 'bg-red-500' : 'bg-slate-300' }}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-700 font-medium">{{ $n->message }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->is_read)
                <button wire:click="markNotificationRead({{ $n->id }})"
                    class="shrink-0 text-xs font-bold text-orange-500 hover:text-orange-700 transition-colors">
                    Marquer lu
                </button>
                @endif
            </div>
            @empty
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Aucune notification.</div>
            @endforelse
        </div>
    </div>

    {{-- Projets par école --}}
    @foreach($schoolsData as $item)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-900">
            <h3 class="text-sm font-black text-white uppercase tracking-wide">{{ $item['school']->name_school }}</h3>
            <span class="text-xs text-slate-400 font-semibold">{{ $item['projects']->count() }} projet(s) actif(s)</span>
        </div>

        @if($item['projects']->isEmpty())
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Aucun projet actif pour cette école.</div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach($item['projects'] as $proj)
                <div class="flex items-center gap-6 px-6 py-5 hover:bg-slate-50/60 transition-colors">
                    <x-project-split-circle
                        :legalPct="$proj['legal_progress']"
                        :budgetPct="$proj['budget_consumption']"
                        :size="72"/>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 truncate">{{ $proj['title_project'] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $proj['nature']['name_nature'] ?? '—' }} &bull; {{ $proj['type_project'] }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button wire:click="openFiche({{ $proj['id'] }})"
                                    title="Consulter la fiche de projet"
                                    class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 border border-blue-200 flex items-center justify-center text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </button>
                                @if($proj['status'] === 'Nouveau')
                                    @if(is_null($proj['consulted_by']))
                                    <button wire:click="consultProject({{ $proj['id'] }})"
                                        title="Valider — transmettre au directeur d'école"
                                        class="w-9 h-9 rounded bg-emerald-500 hover:bg-emerald-600 flex items-center justify-center text-white text-xs font-black transition-colors shadow-sm">
                                        OK
                                    </button>
                                    @else
                                    <span class="w-9 h-9 rounded bg-emerald-100 flex items-center justify-center" title="Transmis au directeur d'école">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    @endif
                                @endif
                                <x-status-badge :status="$proj['status']"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Budget</p>
                                <p class="text-sm font-black text-slate-700 tabular-nums">{{ number_format($proj['budget'], 0, ',', ' ') }} KDA</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Consommé</p>
                                <p class="text-sm font-black tabular-nums {{ $proj['budget_consumption'] >= 80 ? 'text-red-600' : 'text-slate-700' }}">
                                    {{ number_format($proj['budget_consumption'], 0) }} %
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Juridique</p>
                                <p class="text-sm font-black text-teal-600 tabular-nums">{{ number_format($proj['legal_progress'], 0) }} %</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endforeach

    {{-- MODAL FICHE DE PROJET --}}
    @if($ficheProject)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        wire:click.self="closeFiche">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden">
            <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-slate-900 text-lg leading-tight">{{ $ficheProject->title_project }}</h3>
                    <x-status-badge :status="$ficheProject->status" class="mt-1"/>
                </div>
                <button wire:click="closeFiche" class="shrink-0 ml-4 w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-3 max-h-[70vh] overflow-y-auto">
                @php
                    $rows = [
                        ['École',      $ficheProject->school->name_school ?? '—'],
                        ['Nature',     $ficheProject->nature->name_nature ?? '—'],
                        ['Type',       $ficheProject->type_project],
                        ['Budget',     number_format($ficheProject->budget, 0, ',', ' ') . ' KDA'],
                        ['Durée',      $ficheProject->duration_months . ' mois'],
                        ['Période',    ($ficheProject->start_year ?? '—') . ' → ' . ($ficheProject->end_year ?? '—')],
                        ['Adresse',    $ficheProject->localisation ?? '—'],
                        ['Soumis par', $ficheProject->creator->name ?? '—'],
                    ];
                @endphp
                @foreach($rows as [$key, $val])
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-28 text-[10px] font-bold text-slate-400 uppercase tracking-wider pt-0.5">{{ $key }}</span>
                    <span class="flex-1 text-sm font-semibold text-slate-800">{{ $val }}</span>
                </div>
                @endforeach
                @if($ficheProject->description)
                <div class="flex items-start gap-4">
                    <span class="shrink-0 w-28 text-[10px] font-bold text-slate-400 uppercase tracking-wider pt-0.5">Description</span>
                    <p class="flex-1 text-sm text-slate-700 leading-relaxed">{{ $ficheProject->description }}</p>
                </div>
                @endif
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end">
                <button wire:click="closeFiche"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm px-5 py-2 rounded-xl transition-colors">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
