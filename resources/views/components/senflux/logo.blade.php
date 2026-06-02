@props([
    'width'  => 28,
    'height' => 28,
])

<img
    src="{{ asset('assets/img/logo-1.png') }}"
    alt="Logo"
    width="{{ $width }}"
    height="{{ $height }}"
    style="object-fit: contain;"
>