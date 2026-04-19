<div class="max-w-7xl mx-auto">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4">
            <div class="text-xs text-slate-500 uppercase">Total Projets</div>
            <div class="text-2xl font-bold text-slate-900">{{ $stats['total_projects'] ?? 0 }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-soft border-0 p-4 border-l-4 border-l-yellow-400">
            <div class="text-xs text-gray-600 uppercase">Nouveau</div>
            <div class="text-2xl font-bold text-gray-700">{{ $stats['nouveau'] ?? 0 }}</div>
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

    <!-- Graphiques DG -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Projets par école -->
        <div class="bg-white rounded-2xl shadow-soft border-0 p-6">
            <h3 class="font-bold text-iap-blue mb-4">Projets par École</h3>
            <div class="space-y-3">
                @php
                $schools = \App\Models\School::withCount('projects')->get();
                @endphp
                @foreach($schools as $school)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">{{ $school->name }}</span>
                    <span class="px-2 py-1 bg-slate-100 rounded text-xs font-bold">{{ $school->projects_count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Budget par école -->
        <div class="bg-white rounded-2xl shadow-soft border-0 p-6">
            <h3 class="font-bold text-iap-blue mb-4">Budget Total par École</h3>
            <div class="text-2xl font-bold text-iap-blue">{{ number_format($stats['budget_total'] ?? 0, 0, ',', ' ') }} DA</div>
            <div class="text-xs text-slate-500">Tous projets confondus</div>
        </div>

        <!-- Statut global -->
        <div class="bg-white rounded-2xl shadow-soft border-0 p-6">
            <h3 class="font-bold text-iap-blue mb-4">Taux d'Avancement</h3>
            <div class="text-2xl font-bold text-iap-orange">
                @php
                $total = $stats['total_projects'] ?? 1;
                $termine = $stats['termine'] ?? 0;
                $pct = $total > 0 ? round(($termine / $total) * 100) : 0;
                @endphp
                {{ $pct }}%
            </div>
            <div class="w-full bg-slate-200 rounded-full h-3 mt-2">
                <div class="bg-iap-orange h-3 rounded-full" style="width: {{ $pct }}%"></div>
            </div>
            <div class="text-xs text-slate-500 mt-1">Projets terminés</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Projects List -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
            <div class="bg-iap-blue p-4">
                <h2 class="text-lg font-bold text-white">Tous les Projets</h2>
                <p class="text-slate-400 text-xs">Direction Générale - Vue d'ensemble</p>
            </div>
            <div class="p-4">
                <table class="w-full">
                    <thead>
                        <tr class="bg-iap-bg border-b border-0">
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Projet</th>
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">École</th>
                            <th class="text-left p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Statut</th>
                            <th class="text-right p-3 text-xs font-bold text-slate-700 uppercase tracking-wider">Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr class="border-b border-0 hover:bg-iap-bg transition-colors">
                                <td class="p-3">
                                    <div class="font-medium">{{ $project->title }}</div>
                                    <div class="text-xs text-slate-500">{{ $project->nature->name ?? '-' }}</div>
                                </td>
                                <td class="p-3">{{ $project->school->name }}</td>
                                <td class="p-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                        @if($project->status === 'Nouveau') bg-yellow-100 text-yellow-700
                                        @elseif($project->status === 'En Etude') bg-orange-100 text-orange-700
                                        @elseif($project->status === 'En Cours') bg-blue-100 text-blue-700
                                        @else bg-green-100 text-green-700 @endif">
                                        {{ $project->status }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-mono font-bold text-iap-blue">{{ number_format($project->budget, 0, ',', ' ') }} DA</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $projects->links() }}</div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
            <div class="bg-iap-blue p-4 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-white">Notifications</h2>
                    <p class="text-slate-400 text-xs">Alertes et messages</p>
                </div>
                @if($unreadNotifications > 0)
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-bold">{{ $unreadNotifications }}</span>
                @endif
            </div>
            <div class="p-4 space-y-2 max-h-96 overflow-y-auto">
                @foreach($notifications as $notification)
                    <div class="p-3 rounded-2xl border-0 shadow-soft {{ $notification->is_read ? 'bg-iap-bg' : 'bg-blue-50' }}">
                        <div class="flex justify-between items-start">
                            <div class="text-sm">{{ $notification->message }}</div>
                            @if(!$notification->is_read)
                                <button wire:click="markNotificationRead({{ $notification->id }})" class="text-xs text-iap-orange font-bold hover:text-iap-blue transition-colors">Marquer lu</button>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>