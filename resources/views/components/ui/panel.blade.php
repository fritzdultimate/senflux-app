@props(['eyebrow' => null, 'title' => null, 'tone' => 'default'])

@php
    $toneStyles = match($tone) {
        'warning' => ['border' => 'border-[#F0A93D]/25', 'bg' => 'bg-[#F0A93D]/[0.04]', 'accent' => 'from-[#F0A93D] via-[#F0A93D]/40 to-transparent'],
        'brand'   => ['border' => 'border-[#8B7CF6]/25', 'bg' => 'bg-[#8B7CF6]/[0.04]', 'accent' => 'from-[#8B7CF6] via-[#8B7CF6]/40 to-transparent'],
        default   => ['border' => 'border-white/10', 'bg' => 'bg-white/[0.02]', 'accent' => 'from-white/20 via-white/5 to-transparent'],
    };
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden rounded-2xl border {$toneStyles['border']} {$toneStyles['bg']} shadow-[inset_0_1px_0_0_rgba(255,255,255,0.05)]"]) }}>
    {{-- top accent line — the "live system" signature on every panel --}}
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r {{ $toneStyles['accent'] }}"></div>

    @if($eyebrow || $title || isset($actions))
        <div class="flex items-center justify-between gap-3 border-b border-white/[0.06] px-6 py-4">
            <div>
                @if($eyebrow)
                    <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-[#565B6E]">{{ $eyebrow }}</p>
                @endif
                @if($title)
                    <h2 class="{{ $eyebrow ? 'mt-1' : '' }} font-['Sora'] text-[15px] font-semibold text-[#F2F3F7]">{{ $title }}</h2>
                @endif
            </div>
            @isset($actions)
                <div>{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
