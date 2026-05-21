<div class="max-w-7xl mx-auto space-y-6">

    {{-- ====================================================================
         STATISTIQUES GLOBALES
    ===================================================================== --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([
            ['label'=>'Total', 'value'=>$stats['total_projects'], 'color'=>'border-slate-300'],
            ['label'=>'Nouveaux', 'value'=>$stats['nouveau'], 'color'=>'border-yellow-400'],
            ['label'=>'En Étude', 'value'=>$stats['en_etude'], 'color'=>'border-orange-400'],
            ['label'=>'En Cours', 'value'=>$stats['en_cours'], 'color'=>'border-blue-400'],
            ['label'=>'Terminés', 'value'=>$stats['termine'], 'color'=>'border-green-400'],
        ] as $card)
            <div class="bg-white rounded-2xl shadow p-4 border-l-4 {{ $card['color'] }}">
                <div class="text-xs text-slate-500 uppercase font-semibold">{{ $card['label'] }}</div>
                <div class="text-3xl font-black text-slate-800 mt-1">{{ $card['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- ====================================================================
         GANTT TIMELINE
    ===================================================================== --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="font-bold text-slate-800 text-lg mb-4">
            Chronologie des projets &mdash; <span class="text-iap-orange">vue Gantt</span>
        </h2>

        @if($ganttProjects->isEmpty())
            <p class="text-slate-400 text-sm text-center py-8">Aucun projet actif à afficher.</p>
        @else
            <div class="overflow-x-auto" style="max-height:350px;overflow-y:auto;">
                <canvas id="ganttChart" style="min-width:800px;height:300px;"></canvas>
            </div>

            <div class="flex gap-6 mt-4 text-xs text-slate-500">
                <span class="flex items-center gap-1"><span class="inline-block w-4 h-3 rounded bg-blue-400"></span> En Cours</span>
                <span class="flex items-center gap-1"><span class="inline-block w-4 h-3 rounded bg-amber-400"></span> En Étude</span>
                <span class="flex items-center gap-1"><span class="inline-block w-4 h-3 rounded bg-red-400"></span> En retard</span>
                <span class="flex items-center gap-1"><span class="inline-block w-1 border-l-2 border-dashed border-red-600 h-3"></span> Aujourd'hui</span>
            </div>

            @push('scripts')
            <script>
            (function() {
                const projects = @json($ganttProjects);
                const today    = new Date().toISOString().split('T')[0];

                const labels = projects.map(p => p.title.length > 30 ? p.title.substring(0,28)+'…' : p.title);
                const starts = projects.map(p => p.start);
                const ends   = projects.map(p => p.end);

                function daysBetween(a, b) {
                    return (new Date(b) - new Date(a)) / 86400000;
                }

                // Trouver la plage globale
                const minDate = starts.reduce((a,b) => a < b ? a : b, today);
                const maxDate = ends.reduce((a,b) => a > b ? a : b, today);

                const offsets   = starts.map(s => Math.max(0, daysBetween(minDate, s)));
                const durations = projects.map((p,i) => Math.max(1, daysBetween(p.start, p.end)));
                const colors    = projects.map(p =>
                    p.overdue ? 'rgba(248,113,113,0.85)' :
                    p.status === 'En Cours' ? 'rgba(96,165,250,0.85)' :
                    'rgba(251,191,36,0.85)'
                );

                const totalDays = Math.max(1, daysBetween(minDate, maxDate));
                const todayOffset = daysBetween(minDate, today);

                const ctx = document.getElementById('ganttChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            {
                                label: 'Décalage',
                                data: offsets,
                                backgroundColor: 'transparent',
                                borderColor: 'transparent',
                            },
                            {
                                label: 'Durée',
                                data: durations,
                                backgroundColor: colors,
                                borderRadius: 4,
                                borderSkipped: false,
                            }
                        ]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                                min: 0,
                                max: totalDays,
                                ticks: {
                                    callback: v => {
                                        const d = new Date(minDate);
                                        d.setDate(d.getDate() + Math.round(v));
                                        return d.toLocaleDateString('fr-FR', {month:'short',year:'2-digit'});
                                    },
                                    maxTicksLimit: 10,
                                },
                                grid: { color: '#f1f5f9' }
                            },
                            y: { stacked: true, grid: { display: false } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => {
                                        const p = projects[ctx.dataIndex];
                                        if (ctx.datasetIndex === 0) return null;
                                        return ` ${p.school} | ${p.start} → ${p.end}${p.overdue ? ' ⚠ RETARD' : ''}`;
                                    }
                                }
                            },
                            annotation: {
                                annotations: {
                                    todayLine: {
                                        type: 'line',
                                        xMin: todayOffset,
                                        xMax: todayOffset,
                                        borderColor: '#ef4444',
                                        borderWidth: 2,
                                        borderDash: [6, 4],
                                        label: {
                                            content: "Aujourd'hui",
                                            display: true,
                                            position: 'start',
                                            color: '#ef4444',
                                            font: { size: 11, weight: 'bold' },
                                            backgroundColor: 'rgba(239,68,68,0.1)',
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            })();
            </script>
            @endpush
        @endif
    </div>

    {{-- ====================================================================
         PROJETS PAR ÉCOLE (avec ProjectSplitCircle)
    ===================================================================== --}}
    @foreach($schoolsData as $item)
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <div class="bg-slate-800 px-6 py-4 flex items-center justify-between">
                <h3 class="font-bold text-white text-base">
                    École {{ $item['school']->name }}
                </h3>
                <span class="text-slate-400 text-sm">{{ $item['projects']->count() }} projet(s) actif(s)</span>
            </div>

            @if($item['projects']->isEmpty())
                <div class="px-6 py-5 text-slate-400 text-sm">Aucun projet actif.</div>
            @else
                @foreach($item['projects'] as $proj)
                    <div class="flex items-start gap-4 px-6 py-5 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition">
                        {{-- SplitCircle --}}
                        <x-project-split-circle
                            :legalPct="$proj['legal_progress']"
                            :budgetPct="$proj['budget_consumption']"
                            :size="72"/>

                        {{-- Infos projet --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="font-bold text-slate-900 truncate">{{ $proj['title'] }}</p>
                                    <p class="text-xs text-slate-500">
                                        {{ $proj['nature']['name'] ?? '—' }} &bull; {{ $proj['type'] }}
                                    </p>
                                </div>
                                <span class="flex-shrink-0 px-2 py-1 rounded-full text-xs font-bold
                                    @if($proj['status'] === 'En Cours') bg-blue-100 text-blue-700
                                    @elseif($proj['status'] === 'En Etude') bg-amber-100 text-amber-700
                                    @else bg-yellow-100 text-yellow-700 @endif">
                                    {{ $proj['status'] }}
                                </span>
                            </div>

                            <div class="mt-2 grid grid-cols-3 gap-3 text-xs">
                                <div>
                                    <span class="text-slate-400">Budget</span><br>
                                    <span class="font-bold text-slate-700">{{ number_format($proj['budget'], 0, ',', ' ') }} DA</span>
                                </div>
                                <div>
                                    <span class="text-slate-400">Consommé</span><br>
                                    <span class="font-bold @if($proj['budget_consumption'] >= 80) text-red-600 @else text-slate-700 @endif">
                                        {{ number_format($proj['budget_consumption'], 1) }} %
                                    </span>
                                </div>
                                <div>
                                    <span class="text-slate-400">Avancement juridique</span><br>
                                    <span class="font-bold text-teal-600">{{ number_format($proj['legal_progress'], 1) }} %</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach

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
        <div class="divide-y divide-slate-50 max-h-96 overflow-y-auto">
            @forelse($notifications as $n)
                <div class="flex items-start gap-3 px-6 py-4 {{ $n->is_read ? '' : 'bg-blue-50' }}">
                    @if($n->priority === 'urgent')
                        <span class="flex-shrink-0 w-2 h-2 bg-red-500 rounded-full mt-2"></span>
                    @else
                        <span class="flex-shrink-0 w-2 h-2 bg-slate-300 rounded-full mt-2"></span>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-700">{{ $n->message }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$n->is_read)
                        <button wire:click="markNotificationRead({{ $n->id }})"
                            class="flex-shrink-0 text-xs text-iap-orange font-semibold hover:text-orange-600">
                            Lire
                        </button>
                    @endif
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-400 text-sm">Aucune notification.</div>
            @endforelse
        </div>
    </div>
</div>
