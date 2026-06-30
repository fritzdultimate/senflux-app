@props(['color' => '#8B7CF6', 'size' => 'md'])

@php
    $sizes = ['sm' => 'h-8 w-8', 'md' => 'h-10 w-10', 'lg' => 'h-12 w-12'];
    $iconSizes = ['sm' => '15', 'md' => '18', 'lg' => '21'];
@endphp

<div {{ $attributes->merge(['class' => "flex shrink-0 items-center justify-center rounded-lg border {$sizes[$size]}"]) }}
     style="background: {{ $color }}14; border-color: {{ $color }}30">
    <svg width="{{ $iconSizes[$size] }}" height="{{ $iconSizes[$size] }}" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 8l-9-5-9 5 9 5 9-5z"/>
        <path d="M3 8v8l9 5 9-5V8"/>
        <path d="M12 13v8"/>
    </svg>
</div>