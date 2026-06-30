@props(['variant' => 'error'])

@php
    $styles = [
        'error'   => 'border-[#F2545B]/25 bg-[#F2545B]/[0.07] text-[#F2545B]',
        'success' => 'border-[#2DD4A7]/25 bg-[#2DD4A7]/[0.07] text-[#2DD4A7]',
    ];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-2.5 rounded-xl border px-4 py-3.5 text-sm {$styles[$variant]}"]) }}>
    @if($variant === 'error')
        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    @else
        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    @endif
    <span class="text-[#F2F3F7]/90">{{ $slot }}</span>
</div>
