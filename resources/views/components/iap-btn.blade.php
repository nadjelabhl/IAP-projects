@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button', 'disabled' => false, 'wire' => null])
@php
$sizes = ['sm'=>'px-3 py-1.5 text-[12px]','md'=>'px-4 py-2 text-[13px]','lg'=>'px-5 py-2.5 text-sm'];
$variants = [
    'primary'    => 'bg-orange-500 hover:bg-orange-600 text-white shadow-sm shadow-orange-600/20',
    'secondary'  => 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50',
    'danger'     => 'bg-red-500 hover:bg-red-600 text-white',
    'dangerDark' => 'bg-red-900 hover:bg-red-950 text-white',
    'success'    => 'bg-emerald-500 hover:bg-emerald-600 text-white',
    'ghost'      => 'text-slate-600 hover:bg-slate-100',
];
$s = $sizes[$size] ?? $sizes['md'];
$v = $variants[$variant] ?? $variants['primary'];
@endphp
<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class'=>"inline-flex items-center justify-center gap-2 rounded-xl font-semibold transition-colors $s $v ".($disabled?'opacity-50 cursor-not-allowed':'')]) }}>
    {{ $slot }}
</button>
