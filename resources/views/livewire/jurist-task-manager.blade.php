<div class="space-y-6">
    <!-- Task Progress -->
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="text-xl font-bold text-iap-blue mb-4">Progression des Tâches</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Complété</p>
                <p class="text-2xl font-black text-green-600 mt-1">{{ $completedPercentage }}%</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total</p>
                <p class="text-2xl font-black text-iap-blue mt-1">{{ $totalPercentage }}%</p>
            </div>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3">
            <div class="bg-gradient-to-r from-iap-orange to-green-500 h-3 rounded-full transition-all" style="width: {{ $completedPercentage }}%"></div>
        </div>
    </div>

    <!-- Add Task Form -->
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="text-xl font-bold text-iap-blue mb-4">Ajouter une Tâche</h3>
        <form wire:submit="addTask" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Titre</label>
                <input wire:model="title" type="text" 
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none"
                    placeholder="Titre de la tâche">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pourcentage (%)</label>
                <input wire:model="percentage" type="number" min="1" max="100"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none"
                    placeholder="1-100">
            </div>
            <button type="submit" class="bg-iap-orange hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-iap-orange/30 hover:shadow-iap-orange/50 transition-all duration-300">
                Ajouter
            </button>
        </form>
    </div>

    <!-- Tasks List -->
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="text-xl font-bold text-iap-blue mb-4">Liste des Tâches</h3>
        @if($tasks->count() > 0)
            <div class="space-y-3">
                @foreach($tasks as $task)
                    <div class="flex items-center justify-between p-4 border border-slate-200 rounded-xl {{ $task->is_completed ? 'bg-green-50 border-green-200' : 'bg-white' }}">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox" wire:model.live="task.is_completed" wire:change="toggleComplete({{ $task->id }})" 
                                {{ $task->is_completed ? 'checked' : '' }}
                                class="w-5 h-5 rounded border-slate-300 text-iap-orange focus:ring-iap-orange">
                            <div>
                                <p class="font-bold text-slate-900 {{ $task->is_completed ? 'line-through text-slate-400' : '' }}">{{ $task->title }}</p>
                                <p class="text-sm font-bold {{ $task->is_completed ? 'text-green-600' : 'text-iap-orange' }}">{{ $task->percentage }}%</p>
                            </div>
                        </div>
                        @if($task->is_completed)
                            <span class="text-xs text-green-600 font-bold">✓ Complété</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <p class="text-slate-500">Aucune tâche</p>
            </div>
        @endif
    </div>

    <!-- Emit ODS Button -->
    @if($totalPercentage >= 100 && $completedPercentage >= 100)
        <button wire:click="emitOds" class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-bold px-6 py-4 rounded-xl shadow-lg shadow-green-500/30 hover:shadow-green-500/50 transition-all duration-300">
            <span class="flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Émettre l'ODS (Débloquer Chef)</span>
            </span>
        </button>
    @endif

    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-800 font-bold">{{ session('message') }}</p>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm text-red-800 font-bold">{{ session('error') }}</p>
        </div>
    @endif
</div>