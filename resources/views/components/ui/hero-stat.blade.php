@props(['label', 'value', 'tone' => 'default', 'suffix' => null, 'icon' => 'default'])

@php
    $tones = [
        'default'  => 'text-[#F2F3F7]',
        'positive' => 'text-[#2DD4A7]',
        'brand'    => 'text-[#8B7CF6]',
    ];
    $iconBg = [
        'default'  => 'bg-white/[0.06] text-[#888EA3]',
        'positive' => 'bg-[#2DD4A7]/10 text-[#2DD4A7]',
        'brand'    => 'bg-[#8B7CF6]/10 text-[#8B7CF6]',
    ];

    $icons = [
        'banknotes' => '<path d="M3 6h18M5 6v12a2 2 0 002 2h10a2 2 0 002-2V6M9 11h6"/><circle cx="12" cy="14" r="2"/>',
        'trending'  => '<path d="M3 17l6-6 4 4 8-8M14 7h7v7"/>',
        'grid'      => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'default'   => '<circle cx="12" cy="12" r="9"/>',
    ];
    $iconPath = $icons[$icon] ?? $icons['default'];
@endphp

<div class="group relative flex-1 border-white/[0.06] px-5 py-5 transition-colors first:pl-6 last:pr-6 [&:not(:last-child)]:border-r">
    <div class="mb-3 flex h-8 w-8 items-center justify-center rounded-lg {{ $iconBg[$tone] ?? $iconBg['default'] }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $iconPath !!}</svg>
    </div>
    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-[#565B6E]">{{ $label }}</p>
    <p class="mt-1.5 font-['IBM_Plex_Mono'] text-2xl font-semibold tabular-nums {{ $tones[$tone] ?? $tones['default'] }}">
        {{ $value }}@if($suffix)<span class="ml-1.5 text-xs font-medium text-[#565B6E]">{{ $suffix }}</span>@endif
    </p>
</div>
