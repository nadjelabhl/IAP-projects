@props(['route', 'active' => false])
<a href="{{ route($route) }}"
   class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-[13px] font-semibold text-left transition-colors border {{ $active ? 'bg-orange-500/15 text-orange-300 border-orange-500/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white border-transparent' }}">
    <span class="w-5 h-5 shrink-0 {{ $active ? 'text-orange-400' : 'text-slate-400' }}">{{ $icon }}</span>
    <span>{{ $slot }}</span>
</a>
