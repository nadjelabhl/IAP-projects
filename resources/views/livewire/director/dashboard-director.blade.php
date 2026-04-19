<div class="max-w-7xl mx-auto">
    <!-- Header with School Info -->
    <div class="bg-white rounded-2xl shadow-soft border-0 p-6 mb-6">
        <h1 class="text-2xl font-bold text-iap-blue">Tableau de Bord - {{ auth()->user()->school->name ?? 'École' }}</h1>
        <p class="text-slate-500">Directeur d'École - Projets de votre établissement</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4">
            <div class="text-xs text-slate-500 uppercase">Total Projets</div>
            <div class="text-2xl font-bold text-slate-900">{{ $stats['total_projects'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-blue-400">
            <div class="text-xs text-blue-600 uppercase">En Cours</div>
            <div class="text-2xl font-bold text-blue-700">{{ $stats['en_cours'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-orange-400">
            <div class="text-xs text-yellow-600 uppercase">En Étude</div>
            <div class="text-2xl font-bold text-yellow-700">{{ $stats['en_etude'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-green-400">
            <div class="text-xs text-green-600 uppercase">Terminés</div>
            <div class="text-2xl font-bold text-green-700">{{ $stats['termine'] ?? 0 }}</div>
        </div>
    </div>

    <!-- Graphiques de suivi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Graphique Status Projets -->
        <div class="bg-white rounded-2xl shadow-soft border-0 p-6">
            <h3 class="font-bold text-iap-blue mb-4">État des Projets</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">Nouveau</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-32 bg-slate-200 rounded-full h-3">
                            <div class="bg-yellow-500 h-3 rounded-full" style="width: {{ $stats['total_projects'] > 0 ? ($stats['nouveau'] / $stats['total_projects'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs font-bold">{{ $stats['nouveau'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">En Étude</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-32 bg-slate-200 rounded-full h-3">
                            <div class="bg-orange-500 h-3 rounded-full" style="width: {{ $stats['total_projects'] > 0 ? ($stats['en_etude'] / $stats['total_projects'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs font-bold">{{ $stats['en_etude'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">En Cours</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-32 bg-slate-200 rounded-full h-3">
                            <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $stats['total_projects'] > 0 ? ($stats['en_cours'] / $stats['total_projects'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs font-bold">{{ $stats['en_cours'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">Terminé</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-32 bg-slate-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full" style="width: {{ $stats['total_projects'] > 0 ? ($stats['termine'] / $stats['total_projects'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-xs font-bold">{{ $stats['termine'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique Budget -->
        <div class="bg-white rounded-2xl shadow-soft border-0 p-6">
            <h3 class="font-bold text-iap-blue mb-4">Budget Total Alloué</h3>
            <div class="text-3xl font-bold text-iap-blue mb-2">{{ number_format($stats['budget_total'] ?? 0, 0, ',', ' ') }} DA</div>
            <div class="text-xs text-slate-500">Budget annuel de l'école</div>
            @if(($stats['budget_total'] ?? 0) > 0)
            <div class="mt-4">
                <div class="text-xs text-slate-500 mb-1">Répartition par projet</div>
                <div class="space-y-2">
                    @foreach($projects->take(5) as $proj)
                    <div class="flex items-center justify-between text-xs">
                        <span class="truncate w-1/2">{{ $proj->title }}</span>
                        <span class="font-mono">{{ number_format($proj->budget, 0, ',', ' ') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Projects with Assignment -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
            <div class="bg-iap-blue p-4">
                <h2 class="text-lg font-bold text-white">Projets de l'École</h2>
                <p class="text-slate-400 text-xs">Section 3 - Affectation du personnel</p>
            </div>
            <div class="p-4">
                <table class="w-full">
                    <thead>
                        <tr class="bg-iap-bg border-b border-0">
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Projet</th>
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Statut</th>
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Juriste</th>
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Chef Projet</th>
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr class="border-b border-0 hover:bg-iap-bg transition-colors">
                                <td class="p-3">
                                    <div class="font-medium">{{ $project->title }}</div>
                                    <div class="text-xs text-slate-500">{{ number_format($project->budget, 0, ',', ' ') }} DA</div>
                                </td>
                                <td class="p-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                        @if($project->status === 'Nouveau') bg-yellow-100 text-yellow-700
                                        @elseif($project->status === 'En Etude') bg-orange-100 text-orange-700
                                        @else bg-blue-100 text-blue-700 @endif">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    @if($project->juriste)
                                        <div class="text-sm">{{ $project->juriste->name }}</div>
                                    @else
                                        <span class="text-xs text-slate-400">Non assigné</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if($project->chefProjet)
                                        <div class="text-sm">{{ $project->chefProjet->name }}</div>
                                    @else
                                        <span class="text-xs text-slate-400">Non assigné</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    @if(!$project->juriste || !$project->chef_projet_id)
                                        <button wire:click="openAssignModal({{ $project->id }})"
                                            class="text-xs bg-iap-orange text-white px-3 py-1 rounded-xl hover:bg-iap-blue transition-colors">
                                            Assigner
                                        </button>
                                    @else
                                        <span class="text-xs text-green-600">✓ Assigné</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-4 text-center text-slate-500">Aucun projet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $projects->links() }}</div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
            <div class="bg-iap-blue p-4 flex justify-between items-center">
                <h2 class="text-lg font-bold text-white">Notifications</h2>
                @if($unreadNotifications > 0)
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $unreadNotifications }}</span>
                @endif
            </div>
            <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div class="p-3 rounded-2xl border-0 shadow-soft {{ $notification->is_read ? 'bg-iap-bg' : 'bg-blue-50' }}">
                        <div class="text-sm">{{ $notification->message }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500">Aucune notification</div>
                @endforelse
            </div>
        </div>
    </div>
</div>