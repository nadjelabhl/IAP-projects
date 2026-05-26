@props(['status'])
@php
$meta = [
    'Nouveau'  => ['bg'=>'bg-blue-50',   'text'=>'text-blue-700',   'dot'=>'bg-blue-500',   'label'=>'Nouveau'],
    'En Etude' => ['bg'=>'bg-amber-50',  'text'=>'text-amber-700',  'dot'=>'bg-amber-500',  'label'=>'En Étude'],
    'En Cours' => ['bg'=>'bg-sky-50',    'text'=>'text-sky-700',    'dot'=>'bg-sky-500',    'label'=>'En Cours'],
    'Termine'  => ['bg'=>'bg-emerald-50','text'=>'text-emerald-700','dot'=>'bg-emerald-500','label'=>'Terminé'],
];
$m = $meta[$status] ?? $meta['Nouveau'];
@endphp
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $m['bg'] }} {{ $m['text'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $m['dot'] }}"></span>
    {{ $m['label'] }}
</span>
