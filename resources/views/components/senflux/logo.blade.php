{{--
    resources/views/components/senflux/logo.blade.php
    Props:
        int    $width       default 28
        int    $height      default 28
        string $gradientId  must be unique per page to avoid SVG defs conflicts
        string $color       'gradient' | 'white' | 'muted'
--}}

@props([
    'width'      => 28,
    'height'     => 28,
    'gradientId' => 'sfLogo',
    'color'      => 'gradient',
])

@php
    $strokeAttr = $color === 'white'
        ? 'stroke="white"'
        : ($color === 'muted' ? 'stroke="#4a4a6a"' : "stroke=\"url(#{$gradientId})\"");
@endphp

<svg width="{{ $width }}" height="{{ $height }}" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
    @if($color === 'gradient')
    <defs>
        <linearGradient id="{{ $gradientId }}" x1="20" y1="10" x2="80" y2="90" gradientUnits="userSpaceOnUse">
            <stop stop-color="#9B7DFF"/>
            <stop offset="1" stop-color="#4F46E5"/>
        </linearGradient>
    </defs>
    @endif
    <path
        d="M65 18 C65 18 80 22 80 38 C80 52 66 58 52 55 C38 52 28 44 30 33 C32 22 46 20 52 28 C58 36 52 46 40 44 C34 43 30 38 30 38
           M30 38 C30 38 18 48 22 62 C26 76 42 82 56 76 C70 70 72 56 64 48 C58 42 48 42 44 50 C40 58 46 66 56 64"
        {!! $strokeAttr !!}
        stroke-width="9"
        stroke-linecap="round"
        stroke-linejoin="round"
        fill="none"
    />
</svg>
