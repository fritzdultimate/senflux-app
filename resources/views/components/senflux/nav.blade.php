{{--
    resources/views/components/senflux/nav.blade.php
    Props:
        string $current  – route name of the current page e.g. 'home', 'about'
--}}

@props(['current' => ''])

@php
    // Helper: is this link the current page?
    $active   = fn(string $route) => request()->routeIs($route) ? 'text-white font-medium' : 'text-[#7a7a9a] hover:text-white transition-colors';
    $mActive  = fn(string $route) => request()->routeIs($route) ? 'text-white' : 'text-[#7a7a9a]';
@endphp

<nav class="nav">
    <div class="nav-inner">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
            <x-senflux.logo width="28" height="28" gradient-id="lgnav" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.14em] hidden sm:block">SENFLUX</span>
        </a>

        {{-- Desktop links --}}
        <ul class="hidden md:flex items-center gap-7 list-none">
            <li><a href="{{ route('home') }}"            class="text-[13.5px] no-underline {{ $active('home') }}">Home</a></li>
            <li><a href="{{ route('about') }}"           class="text-[13.5px] no-underline {{ $active('about') }}">About Us</a></li>
            <li><a href="{{ route('terminal') }}"        class="text-[13.5px] no-underline {{ $active('terminal') }}">Terminal</a></li>
            <li><a href="{{ route('how-it-works') }}"    class="text-[13.5px] no-underline {{ $active('how-it-works') }}">How it Works</a></li>
            <li><a href="{{ route('market-insights') }}" class="text-[13.5px] no-underline {{ $active('market-insights') }}">Market Insights</a></li>
        </ul>

        {{-- Desktop CTA --}}
        <div class="hidden md:flex items-center gap-2">
            <a href="{{ route('login') }}" class="btn-ghost">Sign In</a>
            <a href="{{ route('register') }}" class="btn-p">Register <span>↗</span></a>
        </div>

        {{-- Mobile hamburger --}}
        <button
            onclick="document.getElementById('sf-mm').classList.toggle('hidden')"
            class="md:hidden flex flex-col gap-[5px] bg-transparent border-0 cursor-pointer p-1"
            aria-label="Toggle menu"
        >
            <span class="block w-[22px] h-[2px] bg-[#c8c8e0] rounded"></span>
            <span class="block w-[22px] h-[2px] bg-[#c8c8e0] rounded"></span>
            <span class="block w-[22px] h-[2px] bg-[#c8c8e0] rounded"></span>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div
        id="sf-mm"
        class="hidden absolute top-14 left-0 right-0 flex-col gap-4 px-6 py-5 border-b"
        style="background:rgba(5,5,12,.97);border-color:rgba(255,255,255,.07)"
    >
        <a href="{{ route('home') }}"            class="text-sm no-underline {{ $mActive('home') }}">Home</a>
        <a href="{{ route('about') }}"           class="text-sm no-underline {{ $mActive('about') }}">About Us</a>
        <a href="{{ route('terminal') }}"        class="text-sm no-underline {{ $mActive('terminal') }}">Terminal</a>
        <a href="{{ route('how-it-works') }}"    class="text-sm no-underline {{ $mActive('how-it-works') }}">How it Works</a>
        <a href="{{ route('market-insights') }}" class="text-sm no-underline {{ $mActive('market-insights') }}">Market Insights</a>

        <div class="flex gap-3 pt-2 border-t" style="border-color:rgba(255,255,255,.07)">
            <a href="{{ route('login') }}"    class="btn-ghost">Sign In</a>
            <a href="{{ route('register') }}" class="btn-p">Register ↗</a>
        </div>
    </div>
</nav>
