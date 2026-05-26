<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Projets de l'école</h1>
        </div>
        <a href="{{ route('notifications.index') }}" class="relative shrink-0 mt-1 w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-orange-500 hover:border-orange-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
        </a>
    </div>

    {{-- Back link --}}
    <a href="{{ route('directeur_ecole.projects.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-bold text-orange-500 hover:text-orange-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Retour à la liste
    </a>

    {{-- Project card --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- Title bar --}}
        <div class="px-8 py-6 border-b border-slate-100 flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-1.5">
                    <h2 class="text-2xl font-black text-slate-900 leading-tight">{{ $p->title }}</h2>
                    <x-status-badge :status="$p->status"/>
                </div>
                <p class="text-sm text-slate-500">
                    {{ $p->school->name }} &bull; {{ $p->nature->name ?? '—' }} &bull; {{ $p->type }} &bull; Durée {{ $p->duration_months }} mois
                </p>
            </div>
            @if($p->pdf_path)
            <a href="{{ asset('storage/' . $p->pdf_path) }}" target="_blank"
               class="shrink-0 flex items-center gap-2 bg-white border border-slate-200 hover:border-slate-300 text-slate-600 font-semibold text-sm px-4 py-2 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Fiche projet
            </a>
            @endif
        </div>

        {{-- 2-column body --}}
        <div class="grid grid-cols-5 divide-x divide-slate-100">

            {{-- LEFT (3/5): avancement technique + dépenses + équipe + timeline --}}
            <div class="col-span-3 px-8 py-6 space-y-6">

                {{-- Avancement technique --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-bold text-slate-700">Avancement technique</span>
                        <span class="text-sm font-black {{ $budgetPct >= 80 ? 'text-red-600' : 'text-orange-500' }} tabular-nums">{{ number_format($budgetPct, 0) }} %</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all {{ $budgetPct >= 80 ? 'bg-red-500' : ($budgetPct >= 60 ? 'bg-amber-500' : 'bg-orange-400') }}"
                             style="width:{{ $budgetPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2 text-xs text-slate-400">
                        @if($p->chefProjet)
                        <span>Chef de projet · <span class="font-semibold text-slate-600">{{ $p->chefProjet->name }}</span></span>
                        @endif
                        <span>Mise à jour · <span class="font-semibold text-slate-600">{{ $p->updated_at->format('d/m/Y') }}</span></span>
                    </div>
                </div>

                {{-- Dépenses (sous avancement technique, en face du référentiel) --}}
                <div class="border-t border-slate-100 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dépenses</p>
                        @if($budgetPct >= 80)
                        <span class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded-full">≥ 80 % budget</span>
                        @endif
                    </div>
                    {{-- Expense rows --}}
                    @if($expenses->isNotEmpty())
                    <div class="space-y-1.5 max-h-56 overflow-y-auto pr-1">
                        @foreach($expenses as $exp)
                        <div class="flex items-start gap-3 bg-slate-50 rounded-xl border border-slate-100 px-4 py-2.5">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-slate-700 truncate">{{ $exp->description ?: '—' }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $exp->expense_date ? $exp->expense_date->format('d/m/Y') : '—' }}</p>
                            </div>
                            <span class="shrink-0 text-xs font-black text-slate-800 tabular-nums">{{ number_format($exp->amount, 0, ',', ' ') }} <span class="text-[10px] font-normal text-slate-400">KDA</span></span>
                        </div>
                        @endforeach
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-200">
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-wider">Total dépensé</span>
                        <span class="text-sm font-black {{ $budgetPct >= 80 ? 'text-red-600' : 'text-slate-900' }} tabular-nums">{{ number_format($totalSpent, 0, ',', ' ') }} <span class="text-[10px] font-bold text-slate-400">KDA</span></span>
                    </div>
                    @else
                    <p class="text-xs text-slate-400 font-semibold text-center py-6 bg-slate-50 rounded-xl border border-slate-100">Aucune dépense enregistrée</p>
                    @endif
                </div>

                {{-- Équipe --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Équipe affectée</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
                            @if($p->juriste)
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-black text-sm shrink-0">
                                {{ mb_strtoupper(mb_substr($p->juriste->name, 0, 1)) }}{{ mb_strtoupper(mb_substr(explode(' ', $p->juriste->name)[1] ?? '', 0, 1)) }}
                            </div>
                            <div><p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Juriste</p><p class="text-sm font-bold text-slate-800">{{ $p->juriste->name }}</p></div>
                            @else
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                            <div><p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Juriste</p><p class="text-sm font-semibold text-slate-400">Non affecté</p></div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
                            @if($p->chefProjet)
                            <div class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center text-white font-black text-sm shrink-0">
                                {{ mb_strtoupper(mb_substr($p->chefProjet->name, 0, 1)) }}{{ mb_strtoupper(mb_substr(explode(' ', $p->chefProjet->name)[1] ?? '', 0, 1)) }}
                            </div>
                            <div><p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Chef de Projet</p><p class="text-sm font-bold text-slate-800">{{ $p->chefProjet->name }}</p></div>
                            @else
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center shrink-0"><svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                            <div><p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Chef de Projet</p><p class="text-sm font-semibold text-slate-400">Non affecté</p></div>
                            @endif
                        </div>
                    </div>
                </div>


            </div>

            {{-- RIGHT (2/5): avancement juridique donut + legal steps + dépenses --}}
            <div class="col-span-2 px-6 py-6 space-y-5 bg-slate-50/30">

                {{-- Donut --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 text-center">Avancement juridique</p>
                    <div class="relative mx-auto" style="width:130px;height:130px;">
                        <canvas id="donut_detail" width="130" height="130"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-black text-teal-600">{{ number_format($legalPct, 0) }}%</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Conformité</span>
                        </div>
                    </div>
                </div>

                {{-- Legal steps with PDF --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Référentiel juridique</p>
                    <div class="space-y-2">
                        @foreach($legalSteps as $step)
                        @php $isLast = $loop->last; @endphp
                        <div class="rounded-xl border {{ $step->is_completed ? 'border-teal-200 bg-teal-50/60' : 'border-slate-200 bg-white' }} px-3 py-2.5">
                            {{-- Phase title row --}}
                            <div class="flex items-center gap-2">
                                <div class="shrink-0 w-4 h-4 rounded flex items-center justify-center {{ $step->is_completed ? 'bg-teal-500' : 'bg-slate-200' }}">
                                    @if($step->is_completed)
                                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                </div>
                                <p class="flex-1 text-[11px] font-semibold {{ $step->is_completed ? 'text-teal-800' : 'text-slate-600' }} leading-snug min-w-0">
                                    {{ $step->sort_order }}. {{ $step->title }}
                                </p>
                                <span class="shrink-0 text-[10px] font-black text-slate-400 tabular-nums">{{ $step->percentage }}%</span>
                            </div>
                            {{-- PDF badge --}}
                            <div class="mt-1.5 pl-6">
                                @if($step->pdf_path)
                                <a href="{{ asset('storage/' . $step->pdf_path) }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-2 py-0.5 rounded-md transition-colors">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Voir fiche
                                </a>
                                @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 px-2 py-0.5 rounded-md">
                                    <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                    Aucune fiche uploadée
                                </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
(function() {
    const el = document.getElementById('donut_detail');
    if (!el || !window.Chart) return;
    const steps  = @json($legalSteps->map(fn($s) => (float) $s->percentage)->values());
    const colors = @json($legalSteps->map(fn($s) => $s->is_completed ? '#2dd4bf' : '#e2e8f0')->values());
    new Chart(el, {
        type: 'doughnut',
        data: { datasets: [{ data: steps.length ? steps : [100], backgroundColor: steps.length ? colors : ['#e2e8f0'], borderWidth: 2, borderColor: '#f8fafc' }] },
        options: { cutout: '70%', responsive: false, plugins: { legend: { display: false }, tooltip: { enabled: false } } }
    });
})();
</script>
@endpush
