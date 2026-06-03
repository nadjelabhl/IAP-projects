<div class="p-8 space-y-6">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-3.5 text-sm font-semibold">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
    @endif

    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Bienvenue, {{ auth()->user()->name }}</h1>
        </div>
        <div class="flex items-center gap-3 shrink-0 mt-1">
            <a href="{{ route('assistant_dg.projects.create') }}"
               class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-sm shadow-orange-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                Nouveau projet
            </a>
            <a href="{{ route('notifications.index') }}" class="relative w-11 h-11 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-orange-500 hover:border-orange-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center">{{ min($unreadCount, 9) }}</span>
                @endif
            </a>
        </div>
    </div>

    <div class="grid grid-cols-5 gap-4">
        @foreach([
            ['TOTAL PROJETS', $stats['total'],    'slate'],
            ['NOUVEAU',       $stats['nouveau'],  'amber'],
            ['EN ÉTUDE',      $stats['en_etude'], 'orange'],
            ['EN COURS',      $stats['en_cours'], 'blue'],
            ['TERMINÉ',       $stats['termine'],  'green'],
        ] as [$label, $value, $tone])
        @php
            $toneMap = [
                'slate'  => ['text-slate-900',   'border-slate-200'],
                'amber'  => ['text-amber-500',   'border-amber-200'],
                'orange' => ['text-orange-500',  'border-orange-200'],
                'blue'   => ['text-blue-500',    'border-blue-200'],
                'green'  => ['text-emerald-500', 'border-emerald-200'],
            ];
            [$textColor, $border] = $toneMap[$tone];
        @endphp
        <div class="bg-white rounded-2xl border {{ $border }} p-5 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ $label }}</p>
            <p class="text-5xl font-black {{ $textColor }} tabular-nums leading-none">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    @if($notifications->isNotEmpty())
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h2 class="text-sm font-black text-slate-900">Notifications</h2>
                @if($unreadCount > 0)
                <span class="text-[11px] font-bold bg-orange-100 text-orange-600 px-2.5 py-0.5 rounded-full">{{ $unreadCount }} non lue{{ $unreadCount > 1 ? 's' : '' }}</span>
                @endif
            </div>
        </div>
        <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
            @foreach($notifications as $notif)
            <div class="px-6 py-3 flex items-start gap-3 {{ $notif->is_read ? 'opacity-60' : '' }} cursor-pointer"
                 wire:click="markNotificationRead({{ $notif->id }})">
                <div class="mt-0.5 w-2 h-2 rounded-full shrink-0 {{ $notif->is_read ? 'bg-slate-200' : 'bg-orange-400' }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $notif->message }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @foreach($schoolsData as $item)
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">{{ $item['school']->name }}</h2>
            <span class="text-[11px] font-bold bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full">
                {{ $item['projects']->count() }} projet{{ $item['projects']->count() > 1 ? 's' : '' }} actif{{ $item['projects']->count() > 1 ? 's' : '' }}
            </span>
        </div>

        @if($item['projects']->isEmpty())
        <div class="px-6 py-10 text-center">
            <p class="text-sm font-semibold text-slate-400">Aucun projet actif pour cette école.</p>
        </div>
        @else
        <div class="divide-y divide-slate-50">
            @foreach($item['projects'] as $proj)
            <div class="flex items-center gap-6 px-6 py-5 hover:bg-slate-50/60 transition-colors">
                <x-project-split-circle :legalPct="$proj['legal_progress']" :budgetPct="$proj['budget_consumption']" :size="72"/>
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
                            <p class="text-sm font-black tabular-nums {{ $proj['budget_consumption'] >= 80 ? 'text-red-600' : 'text-slate-700' }}">{{ number_format($proj['budget_consumption'], 0) }} %</p>
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
