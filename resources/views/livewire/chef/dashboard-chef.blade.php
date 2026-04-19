<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-soft border-0 p-6 mb-6">
        <h1 class="text-2xl font-bold text-iap-blue">Tableau de Bord - Chef de Projet</h1>
        <p class="text-slate-500">Section 5 & 6 - Dépenses et Suivi Budget</p>
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
                            @if($project->chef_access_unlocked)
                                <span class="text-green-600">✓ Accès débloqué</span>
                            @else
                                <span class="text-red-600">🔒 Accès bloqué</span>
                            @endif
                        </div>
                    </button>
                @empty
                    <div class="text-sm text-slate-500 p-4">Aucun projet assigné</div>
                @endforelse
            </div>
        </div>

        <!-- Expense Manager -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-soft border-0 overflow-hidden">
            <div class="bg-iap-blue p-4">
                <h2 class="text-lg font-bold text-white">Suivi des Dépenses</h2>
                <p class="text-slate-400 text-xs">Section 6 - Alerte à 80%</p>
            </div>

            @if($selectedProject)
                <div class="p-4">
                    <!-- Budget Info -->
                    <div class="bg-iap-bg rounded-2xl p-3 mb-4">
                        <div class="font-bold">{{ $selectedProject->title }}</div>
                        <div class="grid grid-cols-3 gap-2 text-sm">
                            <div>Budget: <span class="font-mono">{{ number_format($selectedProject->budget, 0, ',', ' ') }} DA</span></div>
                            <div>Dépensé: <span class="font-mono text-red-600">{{ number_format($totalSpent, 0, ',', ' ') }} DA</span></div>
                            <div>Restant: <span class="font-mono {{ $remainingBudget < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($remainingBudget, 0, ',', ' ') }} DA</span></div>
                        </div>
                        @if($selectedProject->budget_alert_sent)
                            <div class="mt-2 text-xs bg-red-100 text-red-700 px-3 py-1 rounded-xl font-bold">
                                ⚠️ ALERTE: Budget接近80%
                            </div>
                        @endif
                    </div>

                    <!-- Add Expense Form -->
                    @if($selectedProject->chef_access_unlocked)
                        <div class="mb-4 p-4 border-0 rounded-2xl shadow-soft">
                            <h3 class="font-bold text-iap-blue mb-2">Enregistrer une dépense</h3>
                            <div class="grid grid-cols-4 gap-2">
                                <input type="text" wire:model="expenseDescription" placeholder="Description" class="col-span-2 rounded-xl border-0 shadow-soft bg-white px-3 py-2">
                                <input type="number" wire:model="expenseAmount" placeholder="Montant (DA)" class="rounded-xl border-0 shadow-soft bg-white px-3 py-2">
                                <input type="date" wire:model="expenseDate" class="rounded-xl border-0 shadow-soft bg-white px-3 py-2">
                            </div>
                            <button wire:click="addExpense" class="mt-2 bg-iap-orange text-white px-4 py-2 rounded-xl hover:bg-iap-blue transition-colors">Enregistrer</button>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-red-700">En attente de l'ODS du Juriste pour accéder aux dépenses.</p>
                        </div>
                    @endif

                    <!-- Expenses List -->
                    <table class="w-full">
                        <thead>
                            <tr class="bg-iap-bg border-b border-0">
                                <th class="text-left p-2 text-xs uppercase tracking-wider font-bold text-slate-700">Date</th>
                                <th class="text-left p-2 text-xs uppercase tracking-wider font-bold text-slate-700">Description</th>
                                <th class="text-right p-2 text-xs uppercase tracking-wider font-bold text-slate-700">Montant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr class="border-b">
                                    <td class="p-2 text-sm">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td class="p-2 text-sm">{{ $expense->description }}</td>
                                    <td class="p-2 text-sm text-right font-mono">{{ number_format($expense->amount, 0, ',', ' ') }} DA</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-slate-500">Aucune dépense</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Terminate Project -->
                    @if($selectedProject->status === 'En Cours')
                        <div class="mt-4 flex justify-end">
                            <button wire:click="terminateProject" class="bg-iap-orange text-white px-4 py-2 rounded-xl hover:bg-iap-blue transition-colors">
                                Terminer le Projet
                            </button>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-8 text-center text-slate-500">
                    Sélectionnez un projet pour gérer les dépenses
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