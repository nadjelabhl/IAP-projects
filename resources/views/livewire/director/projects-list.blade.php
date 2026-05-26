<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Projets de l'École</h1>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex items-center gap-3">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher un projet…"
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <select wire:model.live="statusFilter"
                class="w-48 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            <option value="">Tous les statuts</option>
            <option value="Nouveau">Nouveau</option>
            <option value="En Etude">En Étude</option>
            <option value="En Cours">En Cours</option>
            <option value="Termine">Terminé</option>
        </select>
    </div>

    {{-- Projects table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-900">{{ $projects->total() }} projet(s)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50">
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">PROJET</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">NATURE</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">BUDGET</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-40">JURIDIQUE</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-40">CONSOMMÉ</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">STATUT</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">ÉQUIPE</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($projects as $project)
                    @php
                        $lp = $project->legalPct;
                        $bp = $project->budgetPct;
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-900 text-sm leading-tight">{{ $project->title }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $project->type }} · {{ $project->duration_months }} mois</p>
                        </td>
                        <td class="px-5 py-4 text-sm text-slate-600 whitespace-nowrap">
                            {{ $project->nature->name ?? '—' }}
                        </td>
                        <td class="px-5 py-4 text-sm font-bold text-slate-800 tabular-nums whitespace-nowrap">
                            {{ number_format($project->budget, 0, ',', ' ') }} <span class="font-normal text-slate-400 text-xs">KDA</span>
                        </td>
                        {{-- Avancement juridique --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-2 min-w-[64px]">
                                    <div class="h-2 rounded-full bg-teal-400 transition-all"
                                         style="width:{{ $lp }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-600 tabular-nums shrink-0">{{ number_format($lp, 0) }}%</span>
                            </div>
                        </td>
                        {{-- Consommation budgétaire --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-slate-100 rounded-full h-2 min-w-[64px]">
                                    <div class="h-2 rounded-full transition-all {{ $bp >= 80 ? 'bg-red-400' : ($bp >= 60 ? 'bg-amber-400' : 'bg-blue-400') }}"
                                         style="width:{{ $bp }}%"></div>
                                </div>
                                <span class="text-xs font-bold {{ $bp >= 80 ? 'text-red-600' : 'text-slate-600' }} tabular-nums shrink-0">{{ number_format($bp, 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <x-status-badge :status="$project->status"/>
                        </td>
                        <td class="px-5 py-4">
                            <div class="space-y-0.5">
                                @if($project->juriste)
                                <p class="text-xs text-slate-600"><span class="text-slate-400">J:</span> {{ $project->juriste->name }}</p>
                                @endif
                                @if($project->chefProjet)
                                <p class="text-xs text-slate-600"><span class="text-slate-400">C:</span> {{ $project->chefProjet->name }}</p>
                                @endif
                                @if(!$project->juriste && !$project->chefProjet)
                                <span class="text-xs text-amber-500 font-semibold">Non affecté</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('directeur_ecole.projects.show', $project->id) }}"
                               class="inline-flex items-center gap-1.5 text-sm font-bold text-orange-500 hover:text-orange-700 transition-colors whitespace-nowrap">
                                Voir le détail
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                            </svg>
                            <p class="text-sm font-semibold text-slate-400">Aucun projet trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $projects->links() }}
        </div>
        @endif
    </div>

</div>
