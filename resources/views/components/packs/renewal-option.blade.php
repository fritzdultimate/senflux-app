@props(['title', 'description', 'icon' => 'default', 'selected' => false])

@php
    $icons = [
        'withdraw' => '<path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"/>',
        'continue' => '<path d="M4 4v5h5M20 20v-5h-5M4 9a8 8 0 0114-5M20 15a8 8 0 01-14 5"/>',
        'compound' => '<path d="M12 2l3 6 6 1-4.5 4.5L17.5 20 12 17l-5.5 3 1-6.5L3 9l6-1z"/>',
        'default'  => '<circle cx="12" cy="12" r="9"/>',
    ];
    $iconPath = $icons[$icon] ?? $icons['default'];
@endphp

<button type="button"
    {{ $attributes->merge(['class' =>
        'group flex flex-col items-start gap-2.5 rounded-lg border px-4 py-4 text-left transition-colors '
        . ($selected
            ? 'border-[#8B7CF6]/40 bg-[#8B7CF6]/[0.06]'
            : 'border-white/10 bg-[#0B0D13] hover:border-white/20')
    ]) }}
>
    <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white/[0.05] text-[#888EA3] transition-colors group-hover:text-[#F2F3F7]">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $iconPath !!}</svg>
    </span>
    <div>
        <p class="text-[13px] font-semibold text-[#F2F3F7]">{{ $title }}</p>
        <p class="mt-1 text-xs leading-relaxed text-[#888EA3]">{{ $description }}</p>
    </div>
</button>