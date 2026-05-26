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
    <div class="grid grid-cols-4 gap-4">
        @foreach([
            ['MES PROJETS', $stats['total'],    'slate',  'text-slate-900',   'border-slate-200'],
            ['EN COURS',    $stats['en_cours'], 'blue',   'text-blue-500',    'border-blue-200'],
            ['CONFORMES',   $stats['conformes'],'teal',   'text-teal-500',    'border-teal-200'],
            ['ALERTES',     $stats['alertes'],  'red',    'text-red-500',     'border-red-200'],
        ] as [$label, $value, $tone, $textColor, $border])
        <div class="bg-white rounded-2xl border {{ $border }} p-5 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ $label }}</p>
            <p class="text-5xl font-black {{ $textColor }} tabular-nums leading-none">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- Notification bar under stats --}}
    @if($notifications->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h2 class="text-sm font-black text-slate-900">Notifications</h2>
                @if($unreadCount > 0)
                <span class="text-[11px] font-bold bg-orange-100 text-orange-600 px-2.5 py-0.5 rounded-full">{{ $unreadCount }} non lu{{ $unreadCount > 1 ? 'es' : '' }}</span>
                @endif
            </div>
            <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-orange-500 hover:text-orange-700 transition-colors">Voir tout</a>
        </div>
        <div class="divide-y divide-slate-50 max-h-48 overflow-y-auto">
            @foreach($notifications->take(5) as $notif)
            <div class="px-6 py-3 flex items-start gap-3 {{ $notif->is_read ? 'opacity-60' : '' }}">
                <div class="mt-0.5 w-2 h-2 rounded-full shrink-0 {{ $notif->is_read ? 'bg-slate-200' : 'bg-orange-400' }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $notif->message }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Mes projets affectés --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">Mes projets affectés</h2>
            <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1 rounded-full">{{ $stats['total'] }} projet(s)</span>
        </div>

        <div class="divide-y divide-slate-50">
            @forelse($projects as $project)
            <div class="flex items-center gap-5 px-6 py-5 hover:bg-slate-50/50 transition-colors">

                {{-- Icon --}}
                <div class="shrink-0 w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>

                {{-- Project info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap mb-1">
                        <p class="font-bold text-slate-900 text-sm">{{ $project->title }}</p>
                        <x-status-badge :status="$project->status"/>
                    </div>
                    <p class="text-xs text-slate-400">{{ $project->nature->name ?? '—' }} &bull; {{ $project->type }}</p>
                </div>

                {{-- Conformité bar --}}
                <div class="shrink-0 w-52">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Conformité</span>
                        <span class="text-xs font-black text-teal-600 tabular-nums">{{ number_format($project->legalPct, 0) }} %</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-teal-400 transition-all"
                             style="width:{{ $project->legalPct }}%"></div>
                    </div>
                </div>

                {{-- Fiche projet icon → opens detail modal --}}
                <button wire:click="openFiche({{ $project->id }})"
                    title="Consulter la fiche projet"
                    class="shrink-0 w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </button>

                {{-- Ouvrir button --}}
                <a href="{{ route('juriste.projects.referentiel', $project->id) }}"
                   class="shrink-0 flex items-center gap-1.5 text-sm font-bold text-orange-500 hover:text-orange-700 transition-colors whitespace-nowrap">
                    Ouvrir
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-sm font-semibold text-slate-400">Aucun projet assigné</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- MODALE FICHE PROJET --}}
    @if($ficheModalOpen && $ficheProject)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden">

            {{-- Modal header --}}
            <div class="flex items-start justify-between px-6 py-5 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-slate-900 text-lg leading-tight">{{ $ficheProject->title }}</h3>
                    <x-status-badge :status="$ficheProject->status" class="mt-1"/>
                </div>
                <button wire:click="closeFiche" class="shrink-0 ml-4 w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="px-6 py-5 space-y-3 max-h-[70vh] overflow-y-auto">

                @php
                    $rows = [
                        ['École',          $ficheProject->school->name ?? '—'],
                        ['Nature',         $ficheProject->nature->name ?? '—'],
                        ['Type',           $ficheProject->type],
                        ['Budget',         number_format($ficheProject->budget, 0, ',', ' ') . ' KDA'],
                        ['Durée',          $ficheProject->duration_months . ' mois'],
                        ['Période',        ($ficheProject->start_year ?? '—') . ' → ' . ($ficheProject->end_year ?? '—')],
                        ['Adresse',        $ficheProject->address ?? '—'],
                        ['Soumis par',     $ficheProject->creator->name ?? '—'],
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

                @if($ficheProject->pdf_path)
                <div class="pt-2">
                    <a href="{{ asset('storage/' . $ficheProject->pdf_path) }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 font-bold text-sm px-4 py-2 rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Télécharger la fiche PDF
                    </a>
                </div>
                @endif
            </div>

            {{-- Modal footer --}}
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
