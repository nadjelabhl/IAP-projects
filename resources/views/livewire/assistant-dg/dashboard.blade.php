<div class="max-w-7xl mx-auto space-y-6">

    {{-- Alertes budgétaires --}}
    @foreach($alerts as $alert)
        <div class="bg-red-50 border border-red-300 rounded-xl p-4 flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-bold text-red-700 text-sm">Alerte budgétaire 80 %</p>
                <p class="text-sm text-red-600 mt-0.5">{{ $alert->message }}</p>
            </div>
        </div>
    @endforeach

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([
            ['Soumis', $stats['total'], 'border-slate-300'],
            ['Nouveaux', $stats['nouveau'], 'border-yellow-400'],
            ['En Étude', $stats['en_etude'], 'border-orange-400'],
            ['En Cours', $stats['en_cours'], 'border-blue-400'],
            ['Terminés', $stats['termine'], 'border-green-400'],
        ] as [$label, $value, $color])
            <div class="bg-white rounded-2xl shadow p-4 border-l-4 {{ $color }}">
                <div class="text-xs text-slate-500 uppercase font-semibold">{{ $label }}</div>
                <div class="text-3xl font-black text-slate-800 mt-1">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    {{-- Action rapide --}}
    <div class="flex justify-end">
        <a href="{{ route('assistant_dg.projects.create') }}"
            class="bg-iap-orange hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl shadow transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Soumettre un nouveau projet
        </a>
    </div>

    {{-- Liste des projets --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Mes projets soumis</h2>
        </div>

        @forelse($myProjects as $project)
            @php
                $totalSpent = $project->expenses->sum('amount');
                $budgetPct  = $project->budget > 0 ? min(($totalSpent / $project->budget) * 100, 100) : 0;
            @endphp
            <div class="px-6 py-5 border-b border-slate-50 hover:bg-slate-50 transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 truncate">{{ $project->title }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $project->school->name }} &bull; {{ $project->nature->name ?? '—' }} &bull; {{ $project->type }}
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Budget : <strong>{{ number_format($project->budget, 0, ',', ' ') }} DA</strong>
                            &bull; Durée : {{ $project->duration_months }} mois
                            &bull; Soumis le {{ $project->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="flex-shrink-0 px-3 py-1 rounded-full text-xs font-bold
                        @if($project->status === 'Nouveau') bg-yellow-100 text-yellow-700
                        @elseif($project->status === 'En Etude') bg-orange-100 text-orange-700
                        @elseif($project->status === 'En Cours') bg-blue-100 text-blue-700
                        @else bg-green-100 text-green-700 @endif">
                        {{ $project->status }}
                    </span>
                </div>

                @if($project->status === 'En Cours' || $project->status === 'Termine')
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                            <span>Consommation budgétaire</span>
                            <span class="font-bold @if($budgetPct >= 80) text-red-600 @else text-slate-600 @endif">
                                {{ number_format($budgetPct, 1) }} %
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all @if($budgetPct >= 80) bg-red-500 @elseif($budgetPct >= 60) bg-amber-500 @else bg-green-500 @endif"
                                style="width:{{ min($budgetPct, 100) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="px-6 py-12 text-center text-slate-400">
                <p class="text-lg font-semibold">Aucun projet soumis</p>
                <a href="{{ route('assistant_dg.projects.create') }}"
                    class="text-iap-orange font-bold text-sm hover:text-orange-600 mt-2 inline-block">
                    Créer votre premier projet →
                </a>
            </div>
        @endforelse

        @if($myProjects->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $myProjects->links() }}</div>
        @endif
    </div>
</div>
