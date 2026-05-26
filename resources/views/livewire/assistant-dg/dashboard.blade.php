<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">BIENVENUE</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('assistant_dg.projects.create') }}"
                class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-orange-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                </svg>
                Nouveau projet
            </a>
            <div class="relative">
                <button class="relative p-2.5 rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($unreadCount > 0)
                        <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-black rounded-full flex items-center justify-center">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-5 gap-4">
        @foreach([
            ['label' => 'Total',    'value' => $stats['total_projects'], 'tone' => 'slate'],
            ['label' => 'Nouveaux', 'value' => $stats['nouveau'],        'tone' => 'amber'],
            ['label' => 'En Étude', 'value' => $stats['en_etude'],      'tone' => 'orange'],
            ['label' => 'En Cours', 'value' => $stats['en_cours'],      'tone' => 'blue'],
            ['label' => 'Terminés', 'value' => $stats['termine'],       'tone' => 'green'],
        ] as $card)
        @php
            $toneMap = [
                'slate'  => ['text-slate-900', 'border-slate-200'],
                'amber'  => ['text-amber-700', 'border-amber-200'],
                'orange' => ['text-orange-600','border-orange-200'],
                'blue'   => ['text-blue-700',  'border-blue-200'],
                'green'  => ['text-emerald-700','border-emerald-200'],
            ];
            [$textColor, $border] = $toneMap[$card['tone']];
        @endphp
        <div class="bg-white rounded-2xl border {{ $border }} p-5 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">{{ $card['label'] }}</p>
            <p class="text-4xl font-black {{ $textColor }} tabular-nums">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Notifications --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Notifications</h3>
            @if($unreadCount > 0)
                <span class="text-xs font-bold bg-red-500 text-white px-2.5 py-1 rounded-full">{{ $unreadCount }} non lue(s)</span>
            @endif
        </div>
        <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
            @forelse($notifications as $n)
            <div class="flex items-start gap-3 px-6 py-4 {{ $n->is_read ? '' : 'bg-orange-50/40' }}">
                <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $n->priority === 'urgent' ? 'bg-red-500' : 'bg-slate-300' }}"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-700 font-medium">{{ $n->message }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->is_read)
                <button wire:click="markNotificationRead({{ $n->id }})"
                    class="shrink-0 text-xs font-bold text-orange-500 hover:text-orange-700 transition-colors">
                    Marquer lu
                </button>
                @endif
            </div>
            @empty
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Aucune notification.</div>
            @endforelse
        </div>
    </div>

    {{-- Projets par école --}}
    @foreach($schoolsData as $item)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-900">
            <h3 class="text-sm font-black text-white uppercase tracking-wide">{{ $item['school']->name }}</h3>
            <span class="text-xs text-slate-400 font-semibold">{{ $item['projects']->count() }} projet(s) actif(s)</span>
        </div>

        @if($item['projects']->isEmpty())
            <div class="px-6 py-8 text-center text-slate-400 text-sm">Aucun projet actif pour cette école.</div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach($item['projects'] as $proj)
                <div class="flex items-center gap-6 px-6 py-5 hover:bg-slate-50/60 transition-colors">
                    <x-project-split-circle
                        :legalPct="$proj['legal_progress']"
                        :budgetPct="$proj['budget_consumption']"
                        :size="72"/>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 truncate">{{ $proj['title'] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $proj['nature']['name'] ?? '—' }} &bull; {{ $proj['type'] }}</p>
                            </div>
                            <x-status-badge :status="$proj['status']"/>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Budget</p>
                                <p class="text-sm font-black text-slate-700 tabular-nums">{{ number_format($proj['budget'], 0, ',', ' ') }} KDA</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Consommé</p>
                                <p class="text-sm font-black tabular-nums {{ $proj['budget_consumption'] >= 80 ? 'text-red-600' : 'text-slate-700' }}">
                                    {{ number_format($proj['budget_consumption'], 0) }} %
                                </p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Juridique</p>
                                <p class="text-sm font-black text-teal-600 tabular-nums">{{ number_format($proj['legal_progress'], 0) }} %</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endforeach

</div>
