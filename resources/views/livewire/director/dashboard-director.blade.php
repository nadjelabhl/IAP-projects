<div class="max-w-7xl mx-auto space-y-6">

    {{-- Alertes flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm font-semibold">{{ session('success') }}</div>
    @endif

    {{-- En-tête école --}}
    <div class="bg-slate-800 rounded-2xl px-6 py-5 flex items-center justify-between">
        <div>
            <p class="text-slate-400 text-xs uppercase font-bold">Tableau de bord</p>
            <h1 class="text-2xl font-black text-white mt-0.5">École {{ $school->name }}</h1>
        </div>
        @if($unreadCount > 0)
            <span class="bg-red-500 text-white text-sm font-bold px-3 py-1 rounded-full">
                {{ $unreadCount }} alerte(s) non lue(s)
            </span>
        @endif
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([
            ['Total', $stats['total'], 'border-slate-300'],
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

    {{-- ====================================================================
         PROJETS (layout gauche/droite par projet)
    ===================================================================== --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-bold text-slate-800">Projets de l'école</h2>
        </div>

        @forelse($projects as $project)
            @php
                $legalPct   = (float) $project->legalSteps->where('is_completed', true)->sum('percentage');
                $totalSpent = (float) $project->expenses->sum('amount');
                $budgetPct  = $project->budget > 0 ? min(($totalSpent / $project->budget) * 100, 100) : 0;
            @endphp
            <div class="px-6 py-5 border-b border-slate-50 last:border-0">
                <div class="flex items-start gap-6">

                    {{-- Gauche : infos + jauges --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 truncate">{{ $project->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $project->nature->name ?? '—' }} &bull; {{ $project->type }}
                                    &bull; Budget : {{ number_format($project->budget, 0, ',', ' ') }} DA
                                </p>
                                @if($project->juriste)
                                    <p class="text-xs text-slate-400 mt-1">
                                        Juriste : <span class="font-semibold text-slate-600">{{ $project->juriste->name }}</span>
                                        @if($project->chefProjet)
                                            &bull; Chef : <span class="font-semibold text-slate-600">{{ $project->chefProjet->name }}</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                            <span class="flex-shrink-0 px-2 py-1 rounded-full text-xs font-bold
                                @if($project->status === 'Nouveau') bg-yellow-100 text-yellow-700
                                @elseif($project->status === 'En Etude') bg-orange-100 text-orange-700
                                @elseif($project->status === 'En Cours') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ $project->status }}
                            </span>
                        </div>

                        {{-- Jauges --}}
                        <div class="mt-3 space-y-2">
                            <div>
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>Avancement juridique</span>
                                    <span class="font-bold text-teal-600">{{ number_format($legalPct, 1) }} %</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="h-2 rounded-full bg-teal-400" style="width:{{ min($legalPct, 100) }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>Consommation budgétaire</span>
                                    <span class="font-bold @if($budgetPct >= 80) text-red-600 @else text-slate-600 @endif">
                                        {{ number_format($budgetPct, 1) }} %
                                    </span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="h-2 rounded-full @if($budgetPct >= 80) bg-red-500 @elseif($budgetPct >= 60) bg-amber-500 @else bg-blue-400 @endif"
                                        style="width:{{ min($budgetPct, 100) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 flex gap-2">
                            @if($project->status === 'Nouveau' && !$project->juriste_id)
                                <button wire:click="openAssignModal({{ $project->id }})"
                                    class="bg-iap-orange text-white font-bold text-xs px-4 py-1.5 rounded-xl hover:bg-orange-600 transition">
                                    Affecter le personnel
                                </button>
                            @endif
                            <button wire:click="openDetailModal({{ $project->id }})"
                                class="text-xs text-slate-500 hover:text-iap-orange font-semibold underline transition">
                                Voir détails
                            </button>
                        </div>
                    </div>

                    {{-- Droite : SplitCircle --}}
                    <div class="flex-shrink-0 flex flex-col items-center gap-1 text-center">
                        <x-project-split-circle :legalPct="$legalPct" :budgetPct="$budgetPct" :size="80"/>
                        <p class="text-xs text-teal-600 font-bold leading-none">Juridique</p>
                        <p class="text-xs text-red-400 font-bold leading-none">Budget</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center text-slate-400">Aucun projet pour cette école.</div>
        @endforelse

        @if($projects->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $projects->links() }}</div>
        @endif
    </div>

    {{-- ====================================================================
         NOTIFICATIONS
    ===================================================================== --}}
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800">Notifications</h3>
            @if($unreadCount > 0)
                <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">{{ $unreadCount }}</span>
            @endif
        </div>
        <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
            @forelse($notifications as $n)
                <div class="flex items-start gap-3 px-6 py-3 {{ $n->is_read ? '' : 'bg-blue-50' }}">
                    <span class="flex-shrink-0 w-2 h-2 rounded-full mt-2 {{ $n->priority === 'urgent' ? 'bg-red-500' : 'bg-slate-300' }}"></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-700">{{ $n->message }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$n->is_read)
                        <button wire:click="markNotificationRead({{ $n->id }})"
                            class="flex-shrink-0 text-xs text-iap-orange font-semibold hover:text-orange-600">Lire</button>
                    @endif
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-400 text-sm">Aucune notification.</div>
            @endforelse
        </div>
    </div>

    {{-- ====================================================================
         MODALE AFFECTATION PERSONNEL
    ===================================================================== --}}
    @if($showAssignModal)
        @php $proj = $projects->firstWhere('id', $projectIdAssign); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 mx-4">
                <h3 class="font-bold text-slate-800 text-lg mb-1">Affecter le personnel</h3>
                @if($proj)
                    <p class="text-sm text-slate-500 mb-5">Projet : <strong>{{ $proj->title }}</strong></p>
                @endif

                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Juriste <span class="text-slate-400 font-normal normal-case">(disponibles : &lt; 2 projets actifs)</span>
                        </label>
                        <select wire:model="selectedJuriste"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-iap-orange outline-none">
                            <option value="">-- Sélectionner un juriste --</option>
                            @foreach($juristes as $j)
                                <option value="{{ $j->id }}">{{ $j->name }}</option>
                            @endforeach
                        </select>
                        @if($juristes->isEmpty())
                            <p class="text-amber-600 text-xs mt-1">Aucun juriste disponible.</p>
                        @endif
                        @error('selectedJuriste') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Chef de Projet <span class="text-slate-400 font-normal normal-case">(disponibles : 0 projet actif)</span>
                        </label>
                        <select wire:model="selectedChef"
                            class="mt-1 w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:border-iap-orange outline-none">
                            <option value="">-- Sélectionner un chef de projet --</option>
                            @foreach($chefs as $c)
                                @if((string)$c->id !== (string)$selectedJuriste)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @if($chefs->isEmpty())
                            <p class="text-amber-600 text-xs mt-1">Aucun chef de projet disponible.</p>
                        @endif
                        @error('selectedChef') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-6">
                    <button wire:click="assignPersonnel"
                        class="flex-1 bg-iap-orange text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition">
                        Confirmer l'affectation
                    </button>
                    <button wire:click="closeAssignModal"
                        class="flex-1 bg-slate-100 text-slate-700 font-bold py-3 rounded-xl hover:bg-slate-200 transition">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ====================================================================
         MODALE DÉTAIL PROJET — Composant C (gauche budget / droite donut juridique)
    ===================================================================== --}}
    @if($showDetailModal && $detailProject)
        @php
            $dp         = $detailProject;
            $legalSteps = $dp->legalSteps;
            $totalSpent = (float) $dp->expenses->sum('amount');
            $budgetPct  = $dp->budget > 0 ? min(($totalSpent / $dp->budget) * 100, 100) : 0;
            $legalPct   = (float) $legalSteps->where('is_completed', true)->sum('percentage');
            $donutData  = $legalSteps->map(fn($s) => (float) $s->percentage)->values();
            $donutColors= $legalSteps->map(fn($s) => $s->is_completed ? '#2dd4bf' : '#e2e8f0')->values();
            $chartId    = 'donut_' . $dp->id . '_' . time();
        @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-slate-100 flex items-start justify-between sticky top-0 bg-white">
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">{{ $dp->title }}</h3>
                        <p class="text-xs text-slate-500">{{ $dp->school->name }} &bull; {{ $dp->nature->name ?? '—' }} &bull; {{ $dp->type }}</p>
                    </div>
                    <button wire:click="closeDetailModal" class="text-slate-400 hover:text-slate-600 mt-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                    {{-- Gauche : Budget --}}
                    <div class="space-y-4">
                        <h4 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Avancement financier</h4>
                        <div class="bg-slate-50 rounded-xl p-4 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Budget</span>
                                <span class="font-bold">{{ number_format($dp->budget, 0, ',', ' ') }} DA</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Dépensé</span>
                                <span class="font-bold @if($budgetPct >= 80) text-red-600 @else text-slate-700 @endif">
                                    {{ number_format($totalSpent, 0, ',', ' ') }} DA
                                </span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Restant</span>
                                <span class="font-bold text-green-700">{{ number_format(max(0, $dp->budget - $totalSpent), 0, ',', ' ') }} DA</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-3">
                                <div class="h-3 rounded-full @if($budgetPct >= 80) bg-red-500 @elseif($budgetPct >= 60) bg-amber-500 @else bg-blue-400 @endif"
                                    style="width:{{ min($budgetPct, 100) }}%"></div>
                            </div>
                            <p class="text-center text-xs font-bold @if($budgetPct >= 80) text-red-600 @else text-slate-500 @endif">
                                {{ number_format($budgetPct, 1) }} % consommé
                            </p>
                        </div>

                        <div class="bg-slate-50 rounded-xl p-4 grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-slate-400">Juriste</p>
                                <p class="font-semibold text-slate-700">{{ $dp->juriste->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Chef de Projet</p>
                                <p class="font-semibold text-slate-700">{{ $dp->chefProjet->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Durée</p>
                                <p class="font-semibold text-slate-700">{{ $dp->duration_months }} mois</p>
                            </div>
                            @if($dp->started_at)
                            <div>
                                <p class="text-xs text-slate-400">Démarré le</p>
                                <p class="font-semibold text-slate-700">{{ $dp->started_at->format('d/m/Y') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Droite : Donut juridique + liste phases --}}
                    <div class="space-y-4">
                        <h4 class="font-bold text-slate-700 uppercase text-xs tracking-wider">Référentiel juridique</h4>

                        <div class="flex items-center gap-4">
                            <div style="width:120px;height:120px;position:relative;flex-shrink:0;">
                                <canvas id="{{ $chartId }}" width="120" height="120"></canvas>
                                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                                    <span class="text-lg font-black text-teal-600">{{ number_format($legalPct, 0) }}%</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-3xl font-black text-teal-600">{{ number_format($legalPct, 1) }}%</p>
                                <p class="text-xs text-slate-500">Progression juridique</p>
                            </div>
                        </div>

                        <div class="space-y-1.5 max-h-52 overflow-y-auto">
                            @foreach($legalSteps as $step)
                                <div class="flex items-center gap-2 p-2 rounded-lg {{ $step->is_completed ? 'bg-teal-50' : 'bg-slate-50' }}">
                                    <span class="flex-shrink-0 w-4 h-4 rounded-full flex items-center justify-center
                                        {{ $step->is_completed ? 'bg-teal-500' : 'bg-slate-300' }}">
                                        @if($step->is_completed)
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="text-xs text-slate-700 flex-1 truncate {{ $step->is_completed ? 'line-through text-slate-400' : '' }}">
                                        {{ $step->sort_order }}. {{ $step->title }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        @push('scripts')
                        <script>
                        (function() {
                            const el = document.getElementById('{{ $chartId }}');
                            if (!el || !window.Chart) return;
                            new Chart(el, {
                                type: 'doughnut',
                                data: {
                                    datasets: [{
                                        data: @json($donutData),
                                        backgroundColor: @json($donutColors),
                                        borderWidth: 1,
                                        borderColor: '#fff'
                                    }]
                                },
                                options: {
                                    cutout: '65%',
                                    responsive: false,
                                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                                }
                            });
                        })();
                        </script>
                        @endpush
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
