{{--
    Auth Left Panel Component
    Usage: <x-auth.left />
    Displays logo, plan chooser cards, and ticker strip.
--}}

<div class="auth-left relative flex flex-col gap-0 h-full overflow-hidden bg-[#0b0f1a] px-7 py-6 border-r border-[rgba(255,255,255,.05)]">

    {{-- Decorative background glow --}}
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-[#9B7DFF] opacity-[.07] blur-[80px]"></div>
        <div class="absolute bottom-12 right-4 w-48 h-48 rounded-full bg-[#4F46E5] opacity-[.06] blur-[60px]"></div>
    </div>

    {{-- Logo --}}
    <a href="{{ route('home') }}"
       class="relative z-10 flex items-center gap-2.5 no-underline shrink-0">
        <x-senflux.logo width="28" height="28" gradient-id="lgnav" />
        <span class="font-syne font-bold text-[13px] text-white tracking-[.14em]">SENFLUX</span>
    </a>

    {{-- Plan chooser --}}
    <div class="relative z-10 flex-1 flex flex-col justify-center py-8 gap-2.5">
        <p class="text-[11px] tracking-[.12em] uppercase text-[#4a4a6a] font-semibold mb-1">
            Choose your plan
        </p>

        <x-auth.plan-card
            id="planFree"
            plan="free"
            label="Free Plan"
            subtitle="Get started immediately"
            price="$0"
            :selected="true"
            :features="[
                ['text' => '8 free signals per day',  'enabled' => true],
                ['text' => 'Basic formation feed',     'enabled' => true],
                ['text' => '1 trading bot',            'enabled' => true],
                ['text' => 'Pro terminal access',      'enabled' => false],
                ['text' => 'Whale cluster alerts',     'enabled' => false],
            ]"
        />

        <x-auth.plan-card
            id="planPro"
            plan="pro"
            label="Pro Plan"
            subtitle="Full intelligence access"
            price="$49"
            :popular="true"
            :selected="false"
            :features="[
                ['text' => '16 pro signals per day',            'enabled' => true],
                ['text' => 'Full Terminal + BirdEye/DexScreener','enabled' => true],
                ['text' => '5 bots · Whale cluster alerts',     'enabled' => true],
                ['text' => '18% APY staking + Telegram alerts', 'enabled' => true],
                ['text' => '73% win rate · Priority support',   'enabled' => true],
            ]"
        />
    </div>

    {{-- Bottom ticker --}}
    <div class="relative z-10 shrink-0">
        <x-auth.ticker />
    </div>
</div>