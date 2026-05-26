@props(['value' => 0, 'color' => 'orange', 'label' => null, 'height' => 8, 'showLabel' => true])
@php
$tones = [
    'orange'  => ['bar'=>'bg-orange-500',  'track'=>'bg-orange-100',  'text'=>'text-orange-700'],
    'teal'    => ['bar'=>'bg-teal-400',    'track'=>'bg-teal-100',    'text'=>'text-teal-700'],
    'blue'    => ['bar'=>'bg-blue-500',    'track'=>'bg-blue-100',    'text'=>'text-blue-700'],
    'emerald' => ['bar'=>'bg-emerald-500', 'track'=>'bg-emerald-100', 'text'=>'text-emerald-700'],
    'red'     => ['bar'=>'bg-red-500',     'track'=>'bg-red-100',     'text'=>'text-red-700'],
];
$t = $tones[$color] ?? $tones['orange'];
$pct = min(100, max(0, (float)$value));
@endphp
<div>
    @if($showLabel && $label)
    <div class="flex justify-between items-center mb-1.5">
        <div class="text-[12px] font-semibold text-slate-700">{{ $label }}</div>
        <div class="text-[12px] font-bold {{ $t['text'] }}">{{ round($pct) }} %</div>
    </div>
    @endif
    <div class="w-full rounded-full {{ $t['track'] }}" style="height: {{ $height }}px">
        <div class="{{ $t['bar'] }} rounded-full transition-all" style="width: {{ $pct }}%; height: {{ $height }}px"></div>
    </div>
</div>
