<div x-data="{ open: false }" class="relative">
    <!-- Notification Bell -->
    <button @click="open = !open" class="relative p-2 rounded-xl hover:bg-white/10 transition-colors">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 bg-iap-orange text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown -->
    <div x-show="open" @click.away="open = false" x-cloak
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-50">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-bold text-iap-blue">Notifications</h3>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            @if($notifications->count() > 0)
                @foreach($notifications as $notification)
                    <div class="p-4 border-b border-slate-100 hover:bg-slate-50 cursor-pointer
                        {{ $notification->is_read ? 'opacity-60' : 'bg-blue-50' }}">
                        <div class="flex items-start space-x-3">
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $notification->title }}</p>
                                <p class="text-xs text-slate-600 mt-1">{{ $notification->message }}</p>
                                <p class="text-[10px] text-slate-400 mt-2">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            @if(!$notification->is_read)
                                <span class="w-2 h-2 bg-iap-orange rounded-full"></span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <p class="text-slate-500 text-sm">Aucune notification</p>
                </div>
            @endif
        </div>

        @if($notifications->count() > 0)
            <div class="p-4 border-t border-slate-100">
                <button wire:click="markAllAsRead" class="w-full text-sm font-bold text-iap-orange hover:text-orange-600 transition-colors">
                    Tout marquer comme lu
                </button>
            </div>
        @endif
    </div>
</div>
