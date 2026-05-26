@props(['label', 'value', 'tone' => 'slate', 'sub' => null])
@php
$textColors = [
    'slate'   => 'text-slate-900',
    'blue'    => 'text-blue-500',
    'orange'  => 'text-orange-500',
    'amber'   => 'text-amber-500',
    'green'   => 'text-emerald-500',
    'emerald' => 'text-emerald-500',
    'teal'    => 'text-teal-500',
    'red'     => 'text-red-500',
    'rose'    => 'text-rose-500',
];
$borderColors = [
    'slate'   => 'border-slate-200',
    'blue'    => 'border-blue-200',
    'orange'  => 'border-orange-200',
    'amber'   => 'border-amber-200',
    'green'   => 'border-emerald-200',
    'emerald' => 'border-emerald-200',
    'teal'    => 'border-teal-200',
    'red'     => 'border-red-200',
    'rose'    => 'border-rose-200',
];
$tc = $textColors[$tone] ?? 'text-slate-900';
$bc = $borderColors[$tone] ?? 'border-slate-200';
@endphp
<div class="bg-white rounded-2xl border {{ $bc }} p-5 shadow-sm">
    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">{{ $label }}</p>
    <p class="text-5xl font-black {{ $tc }} tabular-nums leading-none">{{ $value }}</p>
    @if($sub)<p class="text-xs text-slate-400 mt-2">{{ $sub }}</p>@endif
</div>
