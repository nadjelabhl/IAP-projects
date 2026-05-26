<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">ARCHIVES</h1>
        </div>
        <a href="{{ $exportUrl }}"
           class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-emerald-200 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
            </svg>
            Télécharger
        </a>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Projets archivés</h2>
            <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg">{{ $projects->total() }} projet(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest w-10">N°</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Titre du projet</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">École</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nature</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Type</th>
                        <th class="text-right px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Budget (KDA)</th>
                        <th class="text-right px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dépensé (KDA)</th>
                        <th class="text-right px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Écart (KDA)</th>
                        <th class="text-left px-5 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Date clôture</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($projects as $i => $project)
                    @php
                        $spent = $project->expenses->sum('amount');
                        $ecart = $project->budget - $spent;
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3.5 text-xs text-slate-400 font-semibold tabular-nums">
                            {{ $projects->firstItem() + $i }}
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-bold text-slate-800">{{ $project->title }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-600 font-medium whitespace-nowrap">
                            {{ $project->school->name }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-500">
                            {{ $project->nature->name ?? '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">
                                {{ $project->type }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right text-sm font-black text-slate-700 tabular-nums">
                            {{ number_format($project->budget, 0, ',', ' ') }}
                        </td>
                        <td class="px-5 py-3.5 text-right text-sm font-black tabular-nums {{ $spent > $project->budget ? 'text-red-600' : 'text-slate-700' }}">
                            {{ number_format($spent, 0, ',', ' ') }}
                        </td>
                        <td class="px-5 py-3.5 text-right text-sm font-black tabular-nums {{ $ecart < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ $ecart >= 0 ? '+' : '' }}{{ number_format($ecart, 0, ',', ' ') }}
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-500 whitespace-nowrap">
                            {{ $project->closed_at?->format('d/m/Y') ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><rect x="3" y="4" width="18" height="4" rx="1"/><path stroke-linecap="round" d="M5 8v12h14V8M10 13h4"/></svg>
                            <p class="text-sm font-semibold text-slate-400">Aucun projet archivé</p>
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
