@props(['amount', 'sign' => false, 'tone' => 'default', 'size' => 'base'])

@php
    $value = (float) $amount;
    $tones = [
        'default'  => 'text-[#F2F3F7]',
        'positive' => 'text-[#2DD4A7]',
        'negative' => 'text-[#F2545B]',
        'muted'    => 'text-[#888EA3]',
    ];
    $sizes = [
        'sm' => 'text-xs',
        'base' => 'text-sm',
        'lg' => 'text-base',
        'xl' => 'text-2xl',
        '2xl' => 'text-3xl',
    ];
    $prefix = $sign && $value > 0 ? '+' : '';
@endphp

<span {{ $attributes->merge(['class' => "font-['IBM_Plex_Mono'] tabular-nums {$sizes[$size]} {$tones[$tone]}"]) }}>{{ $prefix }}${{ number_format($value, 2) }}</span>
