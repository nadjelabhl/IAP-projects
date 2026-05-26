@props(['tone' => 'blue'])
@php
$tones = [
    'blue'   => 'bg-blue-50 text-blue-700',
    'orange' => 'bg-orange-50 text-orange-700',
    'green'  => 'bg-emerald-50 text-emerald-700',
    'slate'  => 'bg-slate-100 text-slate-600',
    'red'    => 'bg-red-50 text-red-700',
    'amber'  => 'bg-amber-50 text-amber-700',
    'teal'   => 'bg-teal-50 text-teal-700',
];
$cls = $tones[$tone] ?? $tones['blue'];
@endphp
<span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $cls }}">{{ $slot }}</span>
