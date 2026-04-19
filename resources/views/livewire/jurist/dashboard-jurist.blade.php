<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-soft border-0 p-6 mb-6">
        <h1 class="text-2xl font-bold text-iap-blue">Tableau de Bord - Juriste</h1>
        <p class="text-slate-500">Section 4 - To-Do List Pondérée</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Projects List -->
        <div class="bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
            <div class="bg-iap-blue p-4">
                <h2 class="text-lg font-bold text-white">Mes Projets</h2>
            </div>
            <div class="p-4 space-y-2">
                @forelse($projects as $project)
                    <button wire:click="selectProject({{ $project->id }})"
                        class="w-full text-left p-3 rounded-2xl border-0 shadow-soft {{ $selectedProject && $selectedProject->id === $project->id ? 'bg-iap-orange text-white' : 'bg-white hover:bg-iap-bg' }}">
                        <div class="font-medium">{{ $project->title }}</div>
                        <div class="text-xs text-slate-500">
                            {{ $project->school->name ?? '-' }} -
                            <span class="@if($project->status === 'Nouveau') text-yellow-600 @elseif($project->status === 'En Etude') text-orange-600 @else text-blue-600 @endif">
                                {{ $project->status }}
                            </span>
                        </div>
                    </button>
                @empty
                    <div class="text-sm text-slate-500 p-4">Aucun projet assigné</div>
                @endforelse
            </div>
        </div>

        <!-- Task Manager -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
            <div class="bg-iap-blue p-4">
                <h2 class="text-lg font-bold text-white">To-Do List Pondérée</h2>
                <p class="text-slate-400 text-xs">Section 5 - La somme doit être = 100%</p>
            </div>

            @if($selectedProject)
                <div class="p-4">
                    <!-- Project Info -->
                    <div class="bg-iap-bg rounded-2xl p-3 mb-4">
                        <div class="font-bold">{{ $selectedProject->title }}</div>
                        <div class="text-sm text-slate-500">Budget: {{ number_format($selectedProject->budget, 0, ',', ' ') }} DA</div>
                        <div class="text-sm text-slate-500">Statut: {{ $selectedProject->status }}</div>
                    </div>

                    <!-- Add Task Form -->
                    <div class="mb-4 p-4 border-0 rounded-2xl shadow-soft">
                        <h3 class="font-bold text-iap-blue mb-2">Ajouter une tâche</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <input type="text" wire:model="taskTitle" placeholder="Description" class="col-span-2 rounded-xl border-0 shadow-soft bg-white px-3 py-2">
                            <input type="number" wire:model="taskPercentage" placeholder="%" class="rounded-xl border-0 shadow-soft bg-white px-3 py-2">
                        </div>
                        <button wire:click="addTask" class="mt-2 bg-iap-orange text-white px-4 py-2 rounded-xl hover:bg-iap-blue transition-colors">Ajouter</button>
                    </div>

                    <!-- Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Progression: {{ $completedPercentage }}% / {{ $totalPercentage }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-iap-orange h-2 rounded-full" style="width: {{ $totalPercentage > 0 ? ($completedPercentage / $totalPercentage * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Tasks List -->
                    <table class="w-full">
                        <thead>
                            <tr class="bg-iap-bg border-b border-0">
                                <th class="text-left p-2 text-xs uppercase tracking-wider font-bold text-slate-700">Tâche</th>
                                <th class="text-left p-2 text-xs uppercase tracking-wider font-bold text-slate-700">%</th>
                                <th class="text-left p-2 text-xs uppercase tracking-wider font-bold text-slate-700">État</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks as $task)
                                <tr class="border-b border-0 {{ $task->is_completed ? 'bg-green-50' : '' }}">
                                    <td class="p-2">{{ $task->title }}</td>
                                    <td class="p-2 font-mono">{{ $task->percentage }}%</td>
                                    <td class="p-2">
                                        <button wire:click="toggleTask({{ $task->id }})"
                                            class="px-2 py-1 rounded text-xs {{ $task->is_completed ? 'bg-green-100 text-green-700' : 'bg-slate-100' }}">
                                            {{ $task->is_completed ? 'Fait' : 'En cours' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-slate-500">Aucune tâche</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($totalPercentage >= 100 && $completedPercentage >= 100 && !$selectedProject->chef_access_unlocked)
                        <div class="mt-4">
                            <button wire:click="emitOds" class="bg-iap-orange text-white px-4 py-2 rounded-xl hover:bg-iap-blue transition-colors">
                                Émettre l'ODS (Déblocage)
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-8 text-center text-slate-500">
                    Sélectionnez un projet pour gérer les tâches
                </div>
            @endif
        </div>
    </div>

    <!-- Notifications -->
    <div class="mt-6 bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
        <div class="bg-iap-blue p-4">
            <h2 class="text-lg font-bold text-white">Notifications</h2>
        </div>
        <div class="p-4">
            @forelse($notifications as $notification)
                <div class="p-3 border-b">
                    <div class="text-sm">{{ $notification->message }}</div>
                    <div class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="text-sm text-slate-500">Aucune notification</div>
            @endforelse
        </div>
    </div>
</div>