{{--
    resources/views/components/senflux/nav.blade.php
--}}

@props(['current' => ''])

@php
    $active  = fn(string $route) => request()->routeIs($route) ? 'text-white font-medium' : 'text-[#7a7a9a] hover:text-white transition-colors';
    $mActive = fn(string $route) => request()->routeIs($route) ? 'text-white font-medium' : 'text-[#7a7a9a]';
@endphp

<nav class="nav">
    <div class="nav-inner">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
            <x-senflux.logo width="28" height="28" gradient-id="lgnav" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.14em]">SENFLUX</span>
        </a>

        {{-- Desktop links --}}
        <ul class="hidden md:flex items-center gap-7 list-none">
            <li><a href="{{ route('home') }}" class="text-[13.5px] no-underline {{ $active('home') }}">Home</a></li>
            <li><a href="{{ route('about') }}" class="text-[13.5px] no-underline {{ $active('about') }}">About Us</a></li>
            <li style="display:none">
                <a href="{{ route('terminal') }}" class="text-[13.5px] no-underline {{ $active('terminal') }}">
                    Terminal
                </a>
            </li>

            <li>
                <a href="{{ route('why-solana') }}" class="text-[13.5px] no-underline {{ $active('why-solana') }}">
                    Why Solana
                </a>
            </li>

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
            id="sf-hamburger"
            onclick="toggleMobileMenu()"
            class="md:hidden flex flex-col justify-center gap-[5px] bg-transparent border-0 cursor-pointer p-2 rounded-lg"
            style="border: 1px solid rgba(255,255,255,.08);"
            aria-label="Toggle menu"
        >
            <span id="sf-bar1" class="block w-[20px] h-[1.5px] bg-[#c8c8e0] rounded transition-all duration-300"></span>
            <span id="sf-bar2" class="block w-[20px] h-[1.5px] bg-[#c8c8e0] rounded transition-all duration-300"></span>
            <span id="sf-bar3" class="block w-[20px] h-[1.5px] bg-[#c8c8e0] rounded transition-all duration-300"></span>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div
        id="sf-mm"
        class="hidden md:hidden"
        style="
            background: rgba(8,8,18,.98);
            border-top: 1px solid rgba(255,255,255,.06);
            border-bottom: 1px solid rgba(255,255,255,.06);
        "
    >
        {{-- Nav links --}}
        <div class="flex flex-col px-5 pt-4 pb-2">
            <a href="{{ route('home') }}"            class="text-[13.5px] no-underline py-3 border-b {{ $mActive('home') }}"            style="border-color:rgba(255,255,255,.06)">Home</a>
            <a href="{{ route('about') }}"           class="text-[13.5px] no-underline py-3 border-b {{ $mActive('about') }}"           style="border-color:rgba(255,255,255,.06)">About Us</a>
            <a 
                href="{{ route('terminal') }}" 
                class="text-[13.5px] no-underline py-3 border-b {{ $mActive('terminal') }}"        
                style="border-color:rgba(255,255,255,.06); display: none;"
            >
                Terminal
            </a>

            <a 
                href="{{ route('why-solana') }}" 
                class="text-[13.5px] no-underline py-3 border-b {{ $mActive('why-solana') }}"        
                style="border-color:rgba(255,255,255,.06)"
            >
                Why Solana
            </a>
            <a href="{{ route('how-it-works') }}"    class="text-[13.5px] no-underline py-3 border-b {{ $mActive('how-it-works') }}"    style="border-color:rgba(255,255,255,.06)">How it Works</a>
            <a href="{{ route('market-insights') }}" class="text-[13.5px] no-underline py-3 {{ $mActive('market-insights') }}"          >Market Insights</a>
        </div>

        {{-- CTA buttons --}}
        <div class="flex gap-3 px-5 py-4" style="border-top: 1px solid rgba(255,255,255,.06)">
            <a href="{{ route('login') }}"    class="btn-ghost flex-1 text-center">Sign In</a>
            <a href="{{ route('register') }}" class="btn-p flex-1 text-center">Register ↗</a>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('sf-mm');
    const bar1 = document.getElementById('sf-bar1');
    const bar2 = document.getElementById('sf-bar2');
    const bar3 = document.getElementById('sf-bar3');
    const isOpen = !menu.classList.contains('hidden');

    menu.classList.toggle('hidden');

    // Animate hamburger to X
    if (!isOpen) {
        bar1.style.transform = 'translateY(6.5px) rotate(45deg)';
        bar2.style.opacity   = '0';
        bar3.style.transform = 'translateY(-6.5px) rotate(-45deg)';
    } else {
        bar1.style.transform = '';
        bar2.style.opacity   = '1';
        bar3.style.transform = '';
    }
}
</script>