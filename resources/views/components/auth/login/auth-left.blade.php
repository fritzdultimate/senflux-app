{{-- resources/views/components/auth/login-left.blade.php --}}
{{-- Left panel for login — hero copy, floating cards, ticker --}}

<aside class="hidden lg:flex flex-col w-[52%] shrink-0 relative overflow-hidden
              bg-[#080811] border-r border-white/[.07]
              justify-between px-12 py-9">

    {{-- Radial background glow --}}
    <div class="absolute inset-0 pointer-events-none"
         style="background:radial-gradient(ellipse 80% 60% at 30% 40%,rgba(123,92,245,.18),transparent 65%),radial-gradient(ellipse 50% 40% at 80% 80%,rgba(16,185,129,.1),transparent 60%)">
    </div>

    {{-- Floating orb --}}
    <div class="absolute -right-16 top-1/2 -translate-y-1/2 w-80 h-80 rounded-full pointer-events-none"
         style="background:radial-gradient(circle at 38% 32%,rgba(123,92,245,.4),rgba(79,70,229,.2) 50%,transparent);
                box-shadow:0 0 120px rgba(123,92,245,.25),0 0 240px rgba(123,92,245,.1);
                animation:float 7s ease-in-out infinite">
        <div class="absolute inset-0 rounded-full border border-[rgba(123,92,245,.18)]"
             style="animation:spinSlow 18s linear infinite"></div>
        <div class="absolute inset-5 rounded-full border border-[rgba(123,92,245,.1)]"></div>
    </div>

    {{-- ── Logo ── --}}
    <a href="{{ route('home') }}"
       class="relative z-10 flex items-center gap-2.5 no-underline shrink-0">
        <x-senflux.logo width="30" height="30" gradient-id="login-logo" />
        <span class="font-syne font-bold text-base text-white tracking-[.07em]">
            Sen<span class="text-[#9B7DFF]">flux</span>
        </span>
    </a>

    {{-- ── Hero copy ── --}}
    <div class="relative z-10 flex-1 flex flex-col justify-center py-10">

        {{-- Live pill --}}
        <div class="inline-flex items-center gap-1.5 border border-[rgba(123,92,245,.26)]
                    rounded-full px-3 py-1 mb-5 w-fit">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"
                  style="animation:pulse 2.5s ease-in-out infinite"></span>
            <span class="text-[11px] tracking-[.12em] uppercase text-[rgba(155,125,255,.85)]">
                Live Market Intelligence
            </span>
        </div>

        <h1 class="font-syne font-extrabold text-white leading-[1.12] mb-4"
            style="font-size:clamp(1.8rem,3vw,2.8rem)">
            See Formation<br />Before the<br />
            <span style="background:linear-gradient(135deg,#9B7DFF,#7B5CF5,#4F46E5);
                         -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                         background-clip:text">
                Market Does.
            </span>
        </h1>

        <p class="text-sm text-[#7a7a9a] max-w-[340px] leading-[1.7] mb-8">
            Real-time on-chain participation intelligence. Track wallet formation,
            whale clusters, and get pro signals before expansion begins.
        </p>

        {{-- Feature list --}}
        <div class="flex flex-col gap-3">
            @foreach([
                'Whale cluster & wallet cohesion intelligence',
                'Automated bots + 73% win rate signals',
            ] as $feat)
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-[7px] shrink-0 flex items-center justify-center
                                bg-emerald-500/10 border border-emerald-500/20">
                        <svg width="14" height="14" fill="none" viewBox="0 0 14 14"
                             stroke="#10B981" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 7.5L5.5 11L12 4"/>
                        </svg>
                    </div>
                    <span class="text-[13.5px] text-[#c8c8e0]">{{ $feat }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── Floating stat cards ── --}}
    <div class="absolute right-5 top-[28%] z-10
                bg-[rgba(8,8,17,.88)] border border-[rgba(123,92,245,.2)]
                rounded-xl px-4 py-3 backdrop-blur-xl"
         style="animation:float 6s ease-in-out .5s infinite">
        <div class="flex items-center gap-2 mb-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"
                  style="animation:pulse 2.5s ease-in-out infinite"></span>
            <span class="text-[10.5px] text-[#7a7a9a] font-semibold uppercase tracking-[.08em]">
                Active Wallets
            </span>
        </div>
        <div class="font-syne text-xl font-extrabold text-white">{{ number_format($activeWallets) }}</div>
        <div class="text-[11px] text-emerald-400 mt-0.5">{{ $percentageIncrease }} today</div>
    </div>

    <div class="absolute right-5 top-[52%] z-10
                bg-[rgba(8,8,17,.88)] border border-[rgba(123,92,245,.2)]
                rounded-xl px-4 py-3 backdrop-blur-xl"
         style="animation:float 8s ease-in-out 1.5s infinite">
        <div class="flex items-center gap-2 mb-1.5">
            <span class="text-[10.5px] text-[#7a7a9a] font-semibold uppercase tracking-[.08em]">
                Bot P&L Today
            </span>
        </div>
        <div class="font-syne text-xl font-extrabold text-emerald-400">+$48.14</div>
        <div class="mt-1">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-semibold
                         bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                Running
            </span>
        </div>
    </div>

    {{-- ── Ticker ── --}}
    <div class="relative z-10 shrink-0">
        <x-auth.ticker />
    </div>

</aside>

@once
    @push('styles')
        <style>
            @keyframes float    { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
            @keyframes spinSlow { to{transform:rotate(360deg)} }
            @keyframes pulse    { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.65)} }
            @keyframes fadeUp   { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:none} }

            .fade-up    { animation:fadeUp .5s ease forwards }
            .fade-up-d1 { animation:fadeUp .5s ease .08s forwards; opacity:0 }
            .fade-up-d2 { animation:fadeUp .5s ease .16s forwards; opacity:0 }
            .fade-up-d3 { animation:fadeUp .5s ease .24s forwards; opacity:0 }
            .fade-up-d4 { animation:fadeUp .5s ease .32s forwards; opacity:0 }
            .fade-up-d5 { animation:fadeUp .5s ease .40s forwards; opacity:0 }
        </style>
    @endpush
@endonce