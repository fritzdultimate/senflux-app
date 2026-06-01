{{-- resources/views/components/senflux/footer.blade.php --}}

<footer class="foot">
    <div class="foot-inner">

        <a href="{{ route('home') }}" class="flex items-center gap-2 no-underline">
            <x-senflux.logo width="22" height="22" gradient-id="sfFootLogo" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.14em]">SENFLUX</span>
        </a>

        <nav class="flex flex-wrap gap-6">
            <a href="#" class="text-[12.5px] text-[#4a4a6a] no-underline hover:text-[#c8c8e0] transition-colors">Terms of Service</a>
            <a href="#" class="text-[12.5px] text-[#4a4a6a] no-underline hover:text-[#c8c8e0] transition-colors">Privacy Policy</a>
            <a href="#" class="text-[12.5px] text-[#4a4a6a] no-underline hover:text-[#c8c8e0] transition-colors">Security</a>
            <a href="#" class="text-[12.5px] text-[#4a4a6a] no-underline hover:text-[#c8c8e0] transition-colors">Contact Support</a>
        </nav>

        <p class="text-[12px] text-[#4a4a6a]">© {{ date('Y') }} Senflux Capital Deployment System. All rights reserved.</p>

    </div>
</footer>
