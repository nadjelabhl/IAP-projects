<div class="p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-iap-blue">Tableau de Bord - IAP</h1>
        <p class="text-slate-500">Vue d'ensemble des projets - Institut Algérien du Pétrole</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4">
            <div class="text-xs text-slate-500 uppercase">Total Projets</div>
            <div class="text-2xl font-bold text-slate-900">{{ $stats['total_projects'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-yellow-400">
            <div class="text-xs text-gray-600 uppercase">Nouveau</div>
            <div class="text-2xl font-bold text-gray-700">{{ $stats['nouveau'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-orange-400">
            <div class="text-xs text-yellow-600 uppercase">En Étude</div>
            <div class="text-2xl font-bold text-yellow-700">{{ $stats['en_etude'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-blue-400">
            <div class="text-xs text-blue-600 uppercase">En Cours</div>
            <div class="text-2xl font-bold text-blue-700">{{ $stats['en_cours'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-green-400">
            <div class="text-xs text-green-600 uppercase">Terminés</div>
            <div class="text-2xl font-bold text-green-700">{{ $stats['termine'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Budget Total -->
    <div class="bg-gradient-to-r from-iap-blue to-blue-900 rounded-2xl shadow-soft p-6 mb-8 text-white">
        <div class="text-sm opacity-80 uppercase tracking-wider">Budget Total des Projets</div>
        <div class="text-3xl font-black">{{ number_format($stats['budget_total'] ?? 0, 0, ',', ' ') }} DA</div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Graphique: Projets par École -->
        <div class="bg-white p-6 rounded-2xl shadow-soft border-0">
            <h3 class="text-lg font-bold text-iap-blue mb-4">Projets par École</h3>
            <div class="space-y-4">
                @foreach($schools as $school)
                    @php
                        $schoolProjects = $projectsBySchool->where('school_id', $school->id)->first();
                        $count = $schoolProjects ? $schoolProjects->count : 0;
                        $budget = $schoolProjects ? $schoolProjects->budget : 0;
                        $total = $stats['total_projects'] ?? 1;
                        $pct = $total > 0 ? ($count / $total * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium">{{ $school->name }}</span>
                            <span class="text-slate-500">{{ $count }} projets ({{ number_format($budget/1000000, 0) }}M DA)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3">
                            <div class="bg-iap-orange h-3 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Graphique: Projets Récents -->
        <div class="bg-white p-6 rounded-2xl shadow-soft border-0">
            <h3 class="text-lg font-bold text-iap-blue mb-4">Projets Récents</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2 uppercase tracking-wider text-xs font-bold text-slate-700">Projet</th>
                            <th class="text-left py-2 uppercase tracking-wider text-xs font-bold text-slate-700">École</th>
                            <th class="text-left py-2 uppercase tracking-wider text-xs font-bold text-slate-700">Statut</th>
                            <th class="text-right py-2 uppercase tracking-wider text-xs font-bold text-slate-700">Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentProjects as $project)
                        <tr class="border-b border-slate-100 hover:bg-iap-bg transition-colors">
                            <td class="py-2 truncate max-w-[150px]">{{ $project->title }}</td>
                            <td class="py-2">{{ $project->school->name ?? '-' }}</td>
                            <td class="py-2">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    @if($project->status === 'Nouveau') bg-yellow-100 text-yellow-700
                                    @elseif($project->status === 'En Etude') bg-orange-100 text-orange-700
                                    @elseif($project->status === 'En Cours') bg-blue-100 text-blue-700
                                    @else bg-green-100 text-green-700 @endif">
                                    {{ $project->status }}
                                </span>
                            </td>
                            <td class="py-2 text-right font-mono font-bold text-iap-blue">{{ number_format($project->budget, 0, ',', ' ') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Link to Archives -->
    <div class="mt-8 p-6 bg-white rounded-2xl shadow-soft border-0">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-iap-blue">Projets Archivés</h3>
                <p class="text-sm text-slate-500">Historique des projets terminés</p>
            </div>
            <a href="{{ route('archives') }}" class="px-4 py-2 bg-iap-blue text-white rounded-xl hover:bg-iap-orange transition-colors">
                Voir les Archives
            </a>
        </div>
    </div>
</div>