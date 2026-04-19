<div class="p-8 bg-white rounded-3xl shadow-soft">
    <h2 class="text-2xl font-black mb-6 text-iap-blue uppercase tracking-tight">Affectation d'équipe</h2>
    
    <div class="bg-slate-50 rounded-xl p-4 mb-6">
        <p class="text-sm font-bold text-slate-700">Projet: {{ $project->title }}</p>
        <p class="text-xs text-slate-500 mt-1">École: {{ $project->school->name ?? 'Non assignée' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Affecter un Juriste</label>
            <select wire:model="juriste_id" 
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none">
                <option value="">-- Choisir --</option>
                @foreach($availableJuristes as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Affecter un Chef de Projet</label>
            <select wire:model="chef_projet_id" 
                    class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none">
                <option value="">-- Choisir --</option>
                @foreach($availableCPs as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <button wire:click="saveAssignment" 
            class="mt-6 bg-iap-orange hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-iap-orange/30 hover:shadow-iap-orange/50 transition-all duration-300">
        Confirmer l'affectation
    </button>

    @if(session()->has('message'))
        <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-800 font-bold">{{ session('message') }}</p>
        </div>
    @endif
</div>