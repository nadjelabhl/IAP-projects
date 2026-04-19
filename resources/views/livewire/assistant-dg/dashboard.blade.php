<x-app-layout :header="'Dashboard Assistant DG'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-iap-blue uppercase tracking-tight">Dashboard Assistant DG</h1>
            <p class="text-slate-600 mt-2">Gestion des projets soumis</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-3xl shadow-soft p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Projets Soumis</p>
                        <p class="text-3xl font-black text-iap-blue mt-2">{{ $totalProjects }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-iap-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-soft p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Action Rapide</p>
                        <a href="{{ route('assistant_dg.projects.create') }}" class="text-sm font-bold text-iap-orange hover:text-orange-600 mt-2 block">
                            Nouveau Projet →
                        </a>
                    </div>
                    <div class="bg-orange-100 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-iap-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-3xl shadow-soft p-6">
            <h3 class="text-xl font-bold text-iap-blue mb-6">Projets Récents</h3>
            
            @if($recentProjects->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Titre</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Statut</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Budget</th>
                                <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProjects as $project)
                                <tr class="border-b border-slate-100 hover:bg-slate-50">
                                    <td class="py-4 px-4 text-sm text-slate-900 font-medium">{{ $project->title }}</td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                            {{ $project->status === 'En Cours' ? 'bg-green-100 text-green-800' : 
                                               $project->status === 'Nouveau' ? 'bg-blue-100 text-blue-800' : 
                                               $project->status === 'En Etude' ? 'bg-yellow-100 text-yellow-800' : 
                                               $project->status === 'Termine' ? 'bg-gray-100 text-gray-800' : 'bg-slate-100 text-slate-800' }}">
                                            {{ $project->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-slate-900 font-bold">{{ number_format($project->budget, 0, ',', ' ') }} DZD</td>
                                    <td class="py-4 px-4 text-sm text-slate-600">{{ $project->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-slate-500">Aucun projet soumis</p>
                    <a href="{{ route('assistant_dg.projects.create') }}" class="text-sm font-bold text-iap-orange hover:text-orange-600 mt-2 inline-block">
                        Créer votre premier projet
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
