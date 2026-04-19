<x-app-layout :header="'Émettre ODS'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-iap-blue uppercase tracking-tight">Émettre un ODS</h1>
            <p class="text-slate-600 mt-2">Projet: {{ $project->title }}</p>
        </div>

        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('ods.index', $project) }}" 
               class="inline-flex items-center space-x-2 text-slate-600 hover:text-iap-blue transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="font-bold">Retour</span>
            </a>
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

        <!-- ODS Form -->
        <div class="bg-white rounded-3xl shadow-soft p-8">
            <form method="POST" action="{{ route('ods.store', $project) }}">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                            Motif de l'ODS
                        </label>
                        <textarea 
                            name="reason" 
                            required
                            rows="4"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none resize-none"
                            placeholder="Expliquez le motif de l'émission de cet ODS..."
                        ></textarea>
                        @error('reason')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Information -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-bold text-blue-900">Information importante</h4>
                                <p class="text-sm text-blue-700 mt-1">
                                    L'émission de cet ODS débloquera l'accès du Chef de Projet pour gérer les dépenses du projet.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('ods.index', $project) }}" 
                           class="px-6 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-50 transition-all">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="bg-iap-orange hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-iap-orange/30 hover:shadow-iap-orange/50 transition-all duration-300">
                            Émettre l'ODS
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
