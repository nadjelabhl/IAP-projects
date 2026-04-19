<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <div class="bg-slate-900 p-6">
            <h2 class="text-xl font-bold text-white">Projets Archivés</h2>
            <p class="text-slate-400 text-xs mt-1">Historique des projets terminés</p>
        </div>

        <div class="p-6">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="text-left p-3 text-xs font-bold text-slate-700">Projet</th>
                        <th class="text-left p-3 text-xs font-bold text-slate-700">École</th>
                        <th class="text-left p-3 text-xs font-bold text-slate-700">Budget</th>
                        <th class="text-left p-3 text-xs font-bold text-slate-700">Date Clôture</th>
                        <th class="text-left p-3 text-xs font-bold text-slate-700">Dépenses</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr class="border-b hover:bg-slate-50">
                            <td class="p-3">
                                <div class="font-medium">{{ $project->title }}</div>
                                <div class="text-xs text-slate-500">{{ $project->nature->name ?? '-' }}</div>
                            </td>
                            <td class="p-3">{{ $project->school->name }}</td>
                            <td class="p-3 font-mono">{{ number_format($project->budget, 0, ',', ' ') }} DA</td>
                            <td class="p-3 text-sm">{{ $project->closed_at?->format('d/m/Y') ?? '-' }}</td>
                            <td class="p-3 font-mono text-red-600">{{ number_format($project->total_spent, 0, ',', ' ') }} DA</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                Aucun projet archivé
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>