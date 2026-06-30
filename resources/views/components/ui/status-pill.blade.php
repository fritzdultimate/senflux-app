@props(['label', 'tone' => 'neutral', 'pulse' => false])

@php
    $tones = [
        'positive' => ['bg' => 'bg-[#2DD4A7]/10', 'border' => 'border-[#2DD4A7]/25', 'text' => 'text-[#2DD4A7]', 'dot' => 'bg-[#2DD4A7]'],
        'warning'  => ['bg' => 'bg-[#F0A93D]/10', 'border' => 'border-[#F0A93D]/25', 'text' => 'text-[#F0A93D]', 'dot' => 'bg-[#F0A93D]'],
        'negative' => ['bg' => 'bg-[#F2545B]/10', 'border' => 'border-[#F2545B]/25', 'text' => 'text-[#F2545B]', 'dot' => 'bg-[#F2545B]'],
        'neutral'  => ['bg' => 'bg-white/[0.04]', 'border' => 'border-white/10', 'text' => 'text-[#888EA3]', 'dot' => 'bg-[#565B6E]'],
    ];
    $t = $tones[$tone] ?? $tones['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-[11px] font-semibold tracking-wide {$t['bg']} {$t['border']} {$t['text']}"]) }}>
    <span class="relative flex h-1.5 w-1.5">
        @if($pulse)
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full {{ $t['dot'] }} opacity-75"></span>
        @endif
        <span class="relative inline-flex h-1.5 w-1.5 rounded-full {{ $t['dot'] }}"></span>
    </span>
    {{ $label }}
</span>