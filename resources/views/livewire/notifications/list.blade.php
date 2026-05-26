<div class="p-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-400">{{ now()->locale('fr')->translatedFormat('l d F Y') }}</p>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mt-1">Notifications</h1>
        </div>
        @if($unreadCount > 0)
        <button wire:click="markAllRead"
                class="flex items-center gap-2 bg-white border border-slate-200 hover:border-orange-300 text-slate-600 hover:text-orange-600 text-sm font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors mt-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Tout marquer comme lu
        </button>
        @endif
    </div>

    {{-- Badge --}}
    @if($unreadCount > 0)
    <div class="flex items-center gap-2.5 bg-orange-50 border border-orange-200 rounded-2xl px-5 py-3">
        <svg class="w-4 h-4 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <p class="text-sm font-bold text-orange-700">{{ $unreadCount }} notification{{ $unreadCount > 1 ? 's' : '' }} non lue{{ $unreadCount > 1 ? 's' : '' }}</p>
    </div>
    @endif

    {{-- List --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-slate-50">
            @forelse($notifications as $notif)
            <div class="flex items-start gap-4 px-6 py-5 {{ $notif->is_read ? '' : 'bg-orange-50/40' }} hover:bg-slate-50/60 transition-colors group">
                {{-- Dot --}}
                <div class="mt-1.5 shrink-0">
                    <div class="w-2.5 h-2.5 rounded-full {{ $notif->is_read ? 'bg-slate-200' : 'bg-orange-400' }}"></div>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 leading-snug">{{ $notif->message }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }} &bull; {{ $notif->created_at->format('d/m/Y à H:i') }}</p>
                </div>

                {{-- Actions --}}
                @if(!$notif->is_read)
                <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="markRead({{ $notif->id }})"
                            class="text-xs font-bold text-orange-500 hover:text-orange-700 transition-colors whitespace-nowrap">
                        Marquer lu
                    </button>
                </div>
                @endif
            </div>
            @empty
            <div class="px-6 py-20 text-center">
                <svg class="w-12 h-12 mx-auto mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-sm font-semibold text-slate-400">Aucune notification</p>
            </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>

</div>
