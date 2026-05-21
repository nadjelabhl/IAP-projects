@props([
    'legalPct'  => 0,
    'budgetPct' => 0,
    'size'      => 80,
])
@php
    $r     = ($size / 2) - 6;
    $cx    = $size / 2;
    $cy    = $size / 2;
    $circ  = 2 * M_PI * $r;
    // Demi-cercle gauche = arc du bas vers le haut (côté gauche)
    $halfC = $circ / 2;
    $legalDash  = ($legalPct  / 100) * $halfC;
    $budgetDash = ($budgetPct / 100) * $halfC;
@endphp
<div title="Juridique : {{ number_format($legalPct, 1) }}% | Budget : {{ number_format($budgetPct, 1) }}%"
    class="relative flex-shrink-0" style="width:{{ $size }}px;height:{{ $size }}px;">
    <svg viewBox="0 0 {{ $size }} {{ $size }}" class="w-full h-full -rotate-90">
        {{-- Fond gris --}}
        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
            fill="none" stroke="#e2e8f0" stroke-width="8"/>

        {{-- Demi-cercle gauche — Juridique (teal #2dd4bf) --}}
        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
            fill="none" stroke="#2dd4bf" stroke-width="8"
            stroke-dasharray="{{ $legalDash }} {{ $circ - $legalDash }}"
            stroke-dashoffset="0"
            stroke-linecap="round"/>

        {{-- Demi-cercle droit — Budget (rouge #f87171), offset = demi-cercle --}}
        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
            fill="none" stroke="#f87171" stroke-width="8"
            stroke-dasharray="{{ $budgetDash }} {{ $circ - $budgetDash }}"
            stroke-dashoffset="{{ -$halfC }}"
            stroke-linecap="round"/>
    </svg>

    {{-- Légende centre --}}
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
        <span class="text-xs font-bold text-teal-600 leading-none">{{ number_format($legalPct, 0) }}%</span>
        <span class="text-xs font-bold text-red-400 leading-none">{{ number_format($budgetPct, 0) }}%</span>
    </div>
</div>
