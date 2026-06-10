{{--
    Trust Badges Component
    Usage: <x-auth.trust-badges />
--}}

<div class="mt-7 pt-5 border-t border-[rgba(255,255,255,.06)]">
    <div class="flex items-center justify-center gap-4 flex-wrap">

        {{-- SSL --}}
        <div class="flex items-center gap-1.5 text-[11.5px] text-[#4a4a6a]">
            <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6.5 1L11.5 3.5V7C11.5 10 9.5 12.4 6.5 13.5C3.5 12.4 1.5 10 1.5 7V3.5z"/>
            </svg>
            SSL Secured
        </div>

        {{-- No card --}}
        <div class="flex items-center gap-1.5 text-[11.5px] text-[#4a4a6a]">
            <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="6.5" cy="6.5" r="5.5"/>
                <path d="M4.5 6.5L6 8L9 5"/>
            </svg>
            No credit card required
        </div>

        {{-- Cancel anytime --}}
        <div class="flex items-center gap-1.5 text-[11.5px] text-[#4a4a6a]">
            <svg width="13" height="13" fill="none" viewBox="0 0 13 13" stroke="#4a4a6a" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6.5 1v4.5l2.5 2M11.5 6.5a5 5 0 1 1-10 0 5 5 0 0 1 10 0z"/>
            </svg>
            Cancel anytime
        </div>

    </div>
</div>