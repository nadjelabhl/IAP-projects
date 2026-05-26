@props(['title' => null, 'subtitle' => null, 'right' => null, 'class' => ''])
<div class="bg-white border border-slate-200/70 rounded-2xl {{ $class }}">
    @if($title || $right)
    <div class="flex items-start justify-between px-6 pt-5 pb-3">
        <div>
            @if($title)<div class="font-bold text-slate-900 text-[17px]">{{ $title }}</div>@endif
            @if($subtitle)<div class="text-[12px] text-slate-500 mt-0.5">{{ $subtitle }}</div>@endif
        </div>
        @if($right)<div>{{ $right }}</div>@endif
    </div>
    @endif
    {{ $slot }}
</div>
