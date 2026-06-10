{{-- resources/views/livewire/onboarding/welcome.blade.php --}}

<div class="min-h-screen bg-[#05050c] flex items-center justify-center px-5 py-10 relative overflow-hidden"
     style="background-image:linear-gradient(rgba(123,92,245,.025) 1px,transparent 1px),
            linear-gradient(90deg,rgba(123,92,245,.025) 1px,transparent 1px);
            background-size:44px 44px">

    {{-- Background glows --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] rounded-full
                    bg-[#7B5CF5] opacity-[.07] blur-[100px]"></div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[300px] rounded-full
                    bg-[#4F46E5] opacity-[.05] blur-[80px]"></div>
    </div>

    <div class="relative z-10 w-full max-w-[580px] text-center">

        {{-- Logo --}}
        <div class="flex items-center justify-center gap-2.5 mb-10">
            <x-senflux.logo width="32" height="32" gradient-id="wlc-logo" />
            <span class="font-syne font-bold text-[15px] text-white tracking-[.1em]">SENFLUX</span>
        </div>

        {{-- Greeting --}}
        <div class="mb-3">
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full
                         border border-[rgba(123,92,245,.25)] bg-[rgba(123,92,245,.08)]
                         text-[11.5px] text-[rgba(155,125,255,.9)] font-medium tracking-[.08em] uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                Account activated
            </span>
        </div>

        <h1 class="font-syne font-extrabold text-white mb-4 leading-[1.1]"
            style="font-size:clamp(2rem,5vw,3rem)">
            Welcome,<br />
            <span style="background:linear-gradient(135deg,#9B7DFF,#7B5CF5,#4F46E5);
                         -webkit-background-clip:text;-webkit-text-fill-color:transparent;
                         background-clip:text">
                {{ $user->firstname ?? $user->name }}.
            </span>
        </h1>

        <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mb-10 max-w-[440px] mx-auto">
            You now have access to real-time on-chain intelligence, whale cluster tracking,
            and automated trading signals. Here's what to expect.
        </p>

        {{-- Feature cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-10 text-left">

            <div class="p-4 rounded-2xl border border-white/[.07] bg-white/[.03]
                        hover:border-[rgba(123,92,245,.3)] hover:bg-[rgba(123,92,245,.05)]
                        transition-all duration-200">
                <div class="w-9 h-9 rounded-xl mb-3 flex items-center justify-center
                            bg-[rgba(123,92,245,.12)] border border-[rgba(123,92,245,.2)]">
                    <svg width="17" height="17" fill="none" viewBox="0 0 17 17"
                         stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12l4-4 3 3 5-6"/>
                        <path d="M14 5h2v2"/>
                    </svg>
                </div>
                <div class="font-syne font-bold text-white text-[13px] mb-1">Live Signals</div>
                <div class="text-[12px] text-[#4a4a6a] leading-relaxed">
                    Up to {{ $user->plan === 'pro' ? '16' : '8' }} formation signals per day, updated in real time.
                </div>
            </div>

            <div class="p-4 rounded-2xl border border-white/[.07] bg-white/[.03]
                        hover:border-[rgba(123,92,245,.3)] hover:bg-[rgba(123,92,245,.05)]
                        transition-all duration-200">
                <div class="w-9 h-9 rounded-xl mb-3 flex items-center justify-center
                            bg-emerald-500/10 border border-emerald-500/20">
                    <svg width="17" height="17" fill="none" viewBox="0 0 17 17"
                         stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="8.5" cy="8.5" r="6.5"/>
                        <path d="M8.5 5v3.5l2.5 1.5"/>
                    </svg>
                </div>
                <div class="font-syne font-bold text-white text-[13px] mb-1">Market Terminal</div>
                <div class="text-[12px] text-[#4a4a6a] leading-relaxed">
                    On-chain data, whale movements, and wallet cohesion at a glance.
                </div>
            </div>

            <div class="p-4 rounded-2xl border border-white/[.07] bg-white/[.03]
                        hover:border-[rgba(123,92,245,.3)] hover:bg-[rgba(123,92,245,.05)]
                        transition-all duration-200">
                <div class="w-9 h-9 rounded-xl mb-3 flex items-center justify-center
                            bg-amber-500/10 border border-amber-500/20">
                    <svg width="17" height="17" fill="none" viewBox="0 0 17 17"
                         stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="13" height="11" rx="2"/>
                        <path d="M5 7h7M5 10h4"/>
                    </svg>
                </div>
                <div class="font-syne font-bold text-white text-[13px] mb-1">Trading Bots</div>
                <div class="text-[12px] text-[#4a4a6a] leading-relaxed">
                    {{ $user->plan === 'pro' ? '5 automated bots' : '1 trading bot' }} ready to configure with your strategy.
                </div>
            </div>
        </div>

        {{-- Stats row --}}
        <div class="flex items-center justify-center gap-8 mb-10 py-5
                    border-y border-white/[.06]">
            <div class="text-center">
                <div class="font-syne font-extrabold text-white text-[22px]">50K+</div>
                <div class="text-[11.5px] text-[#4a4a6a] mt-0.5">Active traders</div>
            </div>
            <div class="w-px h-8 bg-white/[.07]"></div>
            <div class="text-center">
                <div class="font-syne font-extrabold text-emerald-400 text-[22px]">73%</div>
                <div class="text-[11.5px] text-[#4a4a6a] mt-0.5">Signal win rate</div>
            </div>
            <div class="w-px h-8 bg-white/[.07]"></div>
            <div class="text-center">
                <div class="font-syne font-extrabold text-amber-400 text-[22px]">18%</div>
                <div class="text-[11.5px] text-[#4a4a6a] mt-0.5">APY staking</div>
            </div>
        </div>

        {{-- CTA --}}
        <button wire:click="dismiss"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2.5
                       bg-gradient-to-r from-[#7B5CF5] to-[#4F46E5]
                       hover:from-[#9B7DFF] hover:to-[#6056f5]
                       text-white font-syne font-bold text-[14px]
                       px-10 py-3.5 rounded-xl tracking-wide cursor-pointer
                       shadow-[0_4px_24px_rgba(123,92,245,.45)]
                       hover:shadow-[0_6px_32px_rgba(123,92,245,.65)]
                       transition-all duration-200 active:scale-[.98]">
            <span wire:loading.remove wire:target="dismiss">
                Go to my dashboard
            </span>
            <span wire:loading wire:target="dismiss">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                </svg>
            </span>
            <svg wire:loading.remove wire:target="dismiss"
                 width="15" height="15" fill="none" viewBox="0 0 15 15"
                 stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 7.5h9M8 3.5l4 4-4 4"/>
            </svg>
        </button>

        <p class="mt-4 text-[12px] text-[#3a3a5a]">
            You can revisit this guide anytime from your dashboard.
        </p>

    </div>
</div>