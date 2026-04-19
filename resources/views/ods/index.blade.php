<x-app-layout :header="'Gestion ODS'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-iap-blue uppercase tracking-tight">Ordres de Service</h1>
            <p class="text-slate-600 mt-2">Projet: {{ $project->title }}</p>
        </div>

        <!-- Project Info Card -->
        <div class="bg-white rounded-3xl shadow-soft p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-iap-blue">{{ $project->title }}</h3>
                    <p class="text-sm text-slate-500 mt-1">École: {{ $project->school->name ?? 'Non assignée' }}</p>
                    <p class="text-sm text-slate-500">Budget: {{ number_format($project->budget, 0, ',', ' ') }} DZD</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold
                        {{ $project->status === 'En Cours' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800' }}">
                        {{ $project->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Active ODS Status -->
        @if($activeODS)
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-3xl p-6 mb-8">
                <div class="flex items-center space-x-4">
                    <div class="bg-green-500 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-green-900">ODS Actif</h3>
                        <p class="text-sm text-green-700 mt-1">Émis le {{ $activeODS->emitted_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm text-green-600">Motif: {{ $activeODS->reason }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-slate-50 border border-slate-200 rounded-3xl p-6 mb-8">
                <div class="flex items-center space-x-4">
                    <div class="bg-slate-400 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-700">Aucun ODS Actif</h3>
                        <p class="text-sm text-slate-600 mt-1">Aucun ordre de service n'a été émis pour ce projet.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Emit ODS Button -->
        @if($canEmitODS)
            <div class="mb-8">
                <a href="{{ route('ods.create', $project) }}" 
                   class="inline-flex items-center space-x-2 bg-iap-orange hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-iap-orange/30 hover:shadow-iap-orange/50 transition-all duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Émettre un ODS</span>
                </a>
            </div>
        @endif

        <!-- ODS History -->
        <div class="bg-white rounded-3xl shadow-soft p-6">
            <h3 class="text-xl font-bold text-iap-blue mb-6">Historique des ODS</h3>
            
            @if($odsRecords->count() > 0)
                <div class="space-y-4">
                    @foreach($odsRecords as $ods)
                        <div class="border border-slate-200 rounded-xl p-4
                            {{ $ods->status === 'active' ? 'bg-green-50 border-green-200' : 'bg-slate-50' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                            {{ $ods->status === 'active' ? 'bg-green-500 text-white' : 'bg-slate-400 text-white' }}">
                                            {{ $ods->status === 'active' ? 'Actif' : 'Annulé' }}
                                        </span>
                                        <span class="text-sm text-slate-500">
                                            {{ $ods->emitted_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-800 mb-1">
                                        {{ $ods->reason }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Émis par: {{ $ods->emitter ? $ods->emitter->name : 'Système' }}
                                    </p>
                                    @if($ods->status === 'cancelled')
                                        <p class="text-xs text-red-600 mt-1">
                                            Annulé le {{ $ods->cancelled_at->format('d/m/Y H:i') }} 
                                            par {{ $ods->cancelledBy ? $ods->cancelledBy->name : 'Système' }}
                                        </p>
                                    @endif
                                </div>
                                @if($ods->status === 'active' && in_array($user->role, ['dg', 'directeur_ecole']))
                                    <form method="POST" action="{{ route('ods.cancel', $ods) }}" class="ml-4">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir annuler cet ODS ?')"
                                                class="text-red-500 hover:text-red-700 text-sm font-bold">
                                            Annuler
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-slate-500">Aucun ODS enregistré</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
