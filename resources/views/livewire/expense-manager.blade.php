<div class="space-y-6">
    <!-- Budget Summary Card -->
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="text-xl font-bold text-iap-blue mb-4">Budget du Projet</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Budget Total</p>
                <p class="text-2xl font-black text-iap-blue mt-1">
                    {{ number_format($budgetSummary['budget'], 0, ',', ' ') }} DZD
                </p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Dépensé</p>
                <p class="text-2xl font-black text-iap-orange mt-1">
                    {{ number_format($budgetSummary['total_spent'], 0, ',', ' ') }} DZD
                </p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Restant</p>
                <p class="text-2xl font-black {{ $budgetSummary['is_over_budget'] ? 'text-red-600' : 'text-green-600' }} mt-1">
                    {{ number_format($budgetSummary['remaining'], 0, ',', ' ') }} DZD
                </p>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-6">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-bold text-slate-600">Consommation Budget</span>
                <span class="text-sm font-bold {{ $budgetSummary['is_over_budget'] ? 'text-red-600' : 'text-iap-orange' }}">
                    {{ number_format($budgetSummary['consumption_percent'], 0) }}%
                </span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-300 {{ $budgetSummary['is_over_budget'] ? 'bg-red-500' : 'bg-iap-orange' }}"
                     style="width: {{ min($budgetSummary['consumption_percent'], 100) }}%">
                </div>
            </div>
            @if($budgetSummary['alert_triggered'])
                <p class="text-xs text-red-600 font-bold mt-2">⚠️ Alerte budgétaire déclenchée</p>
            @endif
        </div>
    </div>

    <!-- Add Expense Form -->
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <h3 class="text-xl font-bold text-iap-blue mb-4">Ajouter une Dépense</h3>
        
        <form wire:submit="addExpense" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Description</label>
                <input wire:model="description" type="text" 
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none"
                       placeholder="Description de la dépense">
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Montant (DZD)</label>
                    <input wire:model="amount" type="number" step="0.01"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none"
                           placeholder="0.00">
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Date</label>
                    <input wire:model="expense_date" type="date"
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 text-sm focus:border-iap-orange focus:ring-2 focus:ring-iap-orange/20 transition-all outline-none">
                    @error('expense_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" 
                    class="bg-iap-orange hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-iap-orange/30 hover:shadow-iap-orange/50 transition-all duration-300">
                Enregistrer la Dépense
            </button>
        </form>
    </div>

    <!-- Expenses List -->
    <div class="bg-white rounded-3xl shadow-soft p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-iap-blue">Historique des Dépenses</h3>
            <button wire:click="terminateProject" 
                    class="text-sm text-red-600 hover:text-red-700 font-bold"
                    onclick="return confirm('Êtes-vous sûr de vouloir terminer ce projet ?')">
                Terminer le Projet
            </button>
        </div>
        
        @if($expenses->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="text-left py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Description</th>
                            <th class="text-right py-3 px-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="py-3 px-4 text-sm text-slate-600">
                                    {{ $expense->expense_date->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-900 font-medium">
                                    {{ $expense->description }}
                                </td>
                                <td class="py-3 px-4 text-sm text-slate-900 font-bold text-right">
                                    {{ number_format($expense->amount, 2, ',', ' ') }} DZD
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-slate-500">Aucune dépense enregistrée</p>
            </div>
        @endif
    </div>

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
