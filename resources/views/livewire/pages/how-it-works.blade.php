{{-- resources/views/livewire/pages/how-it-works.blade.php --}}
<div>

{{-- ═══ HERO ═══ --}}
<section class="pt-[100px] pb-14 relative min-h-[480px] flex items-center overflow-hidden">
    <div class="absolute inset-0 pointer-events-none"
         style="background:radial-gradient(ellipse 58% 50% at 70% 50%,rgba(123,92,245,.2),transparent 65%)"></div>

    <div class="max-w-[1180px] mx-auto px-6 w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- LEFT: Text --}}
            <div>
                <span class="pill mb-5 inline-block">How It Works</span>

                <h1 class="font-syne font-bold text-[clamp(1.6rem,2.8vw,2.6rem)] leading-[1.15] mb-4 max-w-[460px]">
                    From On-Chain Activity to
                    <span class="tg">Market Formation Intelligence</span>
                </h1>

                <p class="text-[14px] text-[#7a7a9a] max-w-[400px] mb-7 leading-[1.75]">
                    Senflux transforms millions of on-chain signals into clear, actionable insight
                    by focusing on what truly matters: participation.
                </p>

                <div class="flex gap-3 flex-wrap">
                    <a href="{{ route('terminal') }}" class="btn-p">Explore the Process →</a>
                    <a href="{{ route('terminal') }}" class="btn-o">View Terminal</a>
                </div>
            </div>

            {{-- RIGHT: 3D cube visual --}}
            <div class="hidden lg:flex items-center justify-center">
                <div class="relative w-[340px] h-[280px]">

                    {{-- Main cube --}}
                    <div class="absolute right-8 top-2 w-[138px] h-[138px] rounded-xl flex items-center justify-center"
                         style="background:linear-gradient(135deg,rgba(123,92,245,.25),rgba(79,70,229,.14));border:1px solid rgba(123,92,245,.3);transform:perspective(500px) rotateX(8deg) rotateY(-14deg);box-shadow:0 20px 60px rgba(123,92,245,.3)">
                        <div class="w-[50px] h-[50px] rounded-xl flex items-center justify-center"
                             style="background:linear-gradient(135deg,rgba(123,92,245,.55),rgba(79,70,229,.35));border:1px solid rgba(155,125,255,.4)">
                            <x-senflux.logo width="24" height="24" color="white" gradient-id="howHero" />
                        </div>
                    </div>

                    {{-- Back cube right --}}
                    <div class="absolute right-0 top-14 w-[118px] h-[118px] rounded-xl overflow-hidden"
                         style="background:linear-gradient(135deg,rgba(79,70,229,.2),rgba(123,92,245,.1));border:1px solid rgba(123,92,245,.2);transform:perspective(500px) rotateX(8deg) rotateY(-10deg);box-shadow:0 15px 40px rgba(123,92,245,.2)">
                        <div class="absolute inset-0"
                             style="background:repeating-linear-gradient(0deg,transparent,transparent 13px,rgba(123,92,245,.07) 13px,rgba(123,92,245,.07) 14px)"></div>
                    </div>

                    {{-- Back cube left --}}
                    <div class="absolute left-6 top-10 w-[126px] h-[126px] rounded-xl overflow-hidden"
                         style="background:linear-gradient(135deg,rgba(123,92,245,.2),rgba(6,182,212,.1));border:1px solid rgba(123,92,245,.22);transform:perspective(500px) rotateX(5deg) rotateY(10deg);box-shadow:0 18px 50px rgba(123,92,245,.22)">
                        <div class="absolute inset-0"
                             style="background:repeating-linear-gradient(90deg,transparent,transparent 13px,rgba(123,92,245,.06) 13px,rgba(123,92,245,.06) 14px)"></div>
                    </div>

                    {{-- Bottom glow --}}
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[210px] h-[22px] rounded-full"
                         style="background:rgba(123,92,245,.26);filter:blur(18px)"></div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══ FORMATION PROCESS ═══ --}}
<section class="py-20" style="background:rgba(8,8,18,.65)">
  <div class="max-w-[1180px] mx-auto px-6">
    <div class="text-center mb-12">
      <span class="pill mb-3.5 inline-block">Process</span>
      <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">The <span class="tg">Formation</span> Process</h2>
    </div>
    <div class="flex items-start gap-0 rv">
      <div class="flex-1 text-center px-3"><div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3" style="border-color:rgba(123,92,245,.32);background:rgba(123,92,245,.08)"><svg width="20" height="20" fill="none"><circle cx="10" cy="10" r="7" stroke="#9B7DFF" stroke-width="1.5"/><path d="M7 10L9 12L13 8" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">1. Activity Begins</p><p class="text-[12px] text-[#7a7a9a]">New wallets and capital start interacting with an asset or ecosystem.</p></div>
      <div class="sline mt-6"></div>
      <div class="flex-1 text-center px-3"><div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3" style="border-color:rgba(123,92,245,.32);background:rgba(123,92,245,.08)"><svg width="20" height="20" fill="none"><path d="M3 14L7 10L10 13L14 7L17 9" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">2. Participation Builds</p><p class="text-[12px] text-[#7a7a9a]">Participation density increases. More wallets join and remain active.</p></div>
      <div class="sline mt-6"></div>
      <div class="flex-1 text-center px-3"><div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3" style="border-color:rgba(123,92,245,.32);background:rgba(123,92,245,.08)"><svg width="20" height="20" fill="none"><circle cx="10" cy="10" r="3" fill="rgba(123,92,245,.3)"/><circle cx="10" cy="10" r="7" stroke="#9B7DFF" stroke-width="1.5"/></svg></div><p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">3. Formation Validates</p><p class="text-[12px] text-[#7a7a9a]">Persistence strengthens. Wallets hold. Liquidity grows. Conviction rises.</p></div>
      <div class="sline mt-6" style="background:linear-gradient(90deg,rgba(123,92,245,.35),rgba(245,158,11,.3))"></div>
      <div class="flex-1 text-center px-3"><div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3" style="border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.08)"><svg width="20" height="20" fill="none"><path d="M10 3L13 8H17L14 12L15 17L10 14L5 17L6 12L3 8H7L10 3Z" fill="rgba(245,158,11,.2)" stroke="#F59E0B" stroke-width="1.5" stroke-linejoin="round"/></svg></div><p class="text-[11px] text-[#F59E0B] font-semibold mb-1.5">4. Expansion Follows</p><p class="text-[12px] text-[#7a7a9a]">Broader market attention arrives. Price and volume accelerate.</p></div>
      <div class="sline mt-6" style="background:linear-gradient(90deg,rgba(245,158,11,.3),rgba(16,185,129,.3))"></div>
      <div class="flex-1 text-center px-3"><div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3" style="border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.08)"><svg width="20" height="20" fill="none"><path d="M10 3L17 10L10 17M3 10H17" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><p class="text-[11px] text-[#10B981] font-semibold mb-1.5">5. Cycle Continues</p><p class="text-[12px] text-[#7a7a9a]">New capital rotates in. The next formation begins.</p></div>
    </div>
  </div>
</section>

{{-- ═══ WHAT WE MEASURE ═══ --}}
<section class="py-20">
  <div class="max-w-[1180px] mx-auto px-6">
    <div class="text-center mb-10">
      <span class="pill mb-3.5 inline-block">Measure</span>
      <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">What We <span class="tg">Measure</span></h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 rv">
      <div class="card card-brand p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><path d="M2 9L5 6L8 9L11 5L14 8L17 4" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h3 class="font-syne text-[13px] mb-2">Participation Density</h3><p class="text-[12px] text-[#7a7a9a]">How many unique wallets are participating and how fast it's growing.</p></div>
      <div class="card card-brand p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><rect x="2" y="4" width="14" height="10" rx="2" stroke="#9B7DFF" stroke-width="1.5"/><path d="M6 9H12" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><h3 class="font-syne text-[13px] mb-2">Wallet Cohesion</h3><p class="text-[12px] text-[#7a7a9a]">Whether meaningful participants are moving together and reinforcing conviction.</p></div>
      <div class="card card-brand p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><path d="M3 15L6 10L9 13L12 7L15 9" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h3 class="font-syne text-[13px] mb-2">Persistence</h3><p class="text-[12px] text-[#7a7a9a]">Whether participants are holding or exiting positions over time.</p></div>
      <div class="card p-5 text-center" style="border-color:rgba(245,158,11,.2)"><div class="ib ib-y mx-auto mb-3.5"><svg width="18" height="18" fill="none"><path d="M9 2L11 7H16L12 10L13.5 15L9 12L4.5 15L6 10L2 7H7L9 2Z" fill="rgba(245,158,11,.2)" stroke="#F59E0B" stroke-width="1.3" stroke-linejoin="round"/></svg></div><h3 class="font-syne text-[13px] mb-2">Formation Velocity</h3><p class="text-[12px] text-[#7a7a9a]">The speed at which participation is increasing.</p></div>
      <div class="card card-green p-5 text-center"><div class="ib ib-g mx-auto mb-3.5"><svg width="18" height="18" fill="none"><rect x="2" y="7" width="4" height="9" rx="1" fill="#10B981" opacity=".6"/><rect x="7" y="4" width="4" height="12" rx="1" fill="#10B981"/><rect x="12" y="2" width="4" height="14" rx="1" fill="#10B981" opacity=".6"/></svg></div><h3 class="font-syne text-[13px] mb-2">Liquidity Strength</h3><p class="text-[12px] text-[#7a7a9a]">The depth and stability of liquidity supporting the formation.</p></div>
    </div>
  </div>
</section>

{{-- ═══ DATA SOURCES ═══ --}}
<section class="py-20" style="background:rgba(8,8,18,.65)">
  <div class="max-w-[1180px] mx-auto px-6">
    <div class="text-center mb-10">
      <span class="pill mb-3.5 inline-block">Sources</span>
      <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Our <span class="tg">Data</span> Sources</h2>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 rv">
      <div class="card p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><rect x="2" y="2" width="14" height="14" rx="3" stroke="#9B7DFF" stroke-width="1.5"/><path d="M6 9H12M9 6V12" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><h3 class="font-syne text-[12.5px]">On-Chain Transactions</h3></div>
      <div class="card p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><rect x="2" y="4" width="14" height="10" rx="2" stroke="#9B7DFF" stroke-width="1.5"/><path d="M6 9H12M8 12H10" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><h3 class="font-syne text-[12.5px]">Wallet Activity &amp; Behavior</h3></div>
      <div class="card p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><circle cx="9" cy="9" r="5" stroke="#9B7DFF" stroke-width="1.5"/><path d="M9 4V14M4 9H14" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" opacity=".5"/></svg></div><h3 class="font-syne text-[12.5px]">Liquidity Pools &amp; Flows</h3></div>
      <div class="card p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><path d="M2 14L6 10L9 13L13 7L16 9" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><h3 class="font-syne text-[12.5px]">Market Data &amp; Pricing</h3></div>
      <div class="card p-5 text-center"><div class="ib mx-auto mb-3.5"><svg width="18" height="18" fill="none"><circle cx="4" cy="9" r="2" stroke="#9B7DFF" stroke-width="1.5"/><circle cx="14" cy="5" r="2" stroke="#9B7DFF" stroke-width="1.5"/><circle cx="14" cy="13" r="2" stroke="#9B7DFF" stroke-width="1.5"/><path d="M6 9L12 6M6 9L12 12" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><h3 class="font-syne text-[12.5px]">Network &amp; Ecosystem Signals</h3></div>
    </div>
  </div>
</section>

{{-- ═══ DATA → INTELLIGENCE ═══ --}}
<section class="py-20">
  <div class="max-w-[1180px] mx-auto px-6">
    <div class="text-center mb-12">
      <span class="pill mb-3.5 inline-block">Transformation</span>
      <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">How We Turn <span class="tg">Data Into Intelligence</span></h2>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center rv">
      <div class="flex flex-col gap-0.5">
        <div class="ps active"><span class="font-syne text-[11px] font-bold text-[#9B7DFF] min-w-[24px] pt-0.5">01</span><div><h3 class="font-syne text-[14px] tg mb-1">Collect</h3><p class="text-[13px] text-[#7a7a9a]">Aggregate millions of real-time on-chain events across wallets, tokens, and protocols.</p></div></div>
        <div class="h-px mx-4" style="background:rgba(255,255,255,.06)"></div>
        <div class="ps"><span class="font-syne text-[11px] font-bold text-[#9B7DFF] min-w-[24px] pt-0.5">02</span><div><h3 class="font-syne text-[14px] text-white mb-1">Cleanse &amp; Normalize</h3><p class="text-[13px] text-[#7a7a9a]">Filter noise and structure data for accuracy and consistency.</p></div></div>
        <div class="h-px mx-4" style="background:rgba(255,255,255,.06)"></div>
        <div class="ps"><span class="font-syne text-[11px] font-bold text-[#9B7DFF] min-w-[24px] pt-0.5">03</span><div><h3 class="font-syne text-[14px] text-white mb-1">Analyze &amp; Score</h3><p class="text-[13px] text-[#7a7a9a]">Evaluate participation metrics and assign formation scores.</p></div></div>
        <div class="h-px mx-4" style="background:rgba(255,255,255,.06)"></div>
        <div class="ps"><span class="font-syne text-[11px] font-bold text-[#9B7DFF] min-w-[24px] pt-0.5">04</span><div><h3 class="font-syne text-[14px] text-white mb-1">Classify States</h3><p class="text-[13px] text-[#7a7a9a]">Assign every asset through formation states based on real-time behavior.</p></div></div>
        <div class="h-px mx-4" style="background:rgba(255,255,255,.06)"></div>
        <div class="ps"><span class="font-syne text-[11px] font-bold text-[#9B7DFF] min-w-[24px] pt-0.5">05</span><div><h3 class="font-syne text-[14px] text-white mb-1">Deliver Intelligence</h3><p class="text-[13px] text-[#7a7a9a]">Insights are visualized in the Terminal for real-time decision advantage.</p></div></div>
      </div>
      {{-- Funnel visual --}}
      <div class="flex items-center justify-center">
        <div class="relative w-[270px] h-[370px]">
          <svg viewBox="0 0 270 370" fill="none" style="position:absolute;inset:0;width:100%;height:100%">
            <path d="M18 42 L252 42 L178 205 L178 328 L92 328 L92 205 Z" fill="url(#funGrad)" opacity=".25" stroke="rgba(123,92,245,.4)" stroke-width="1.5"/>
            <defs><linearGradient id="funGrad" x1="135" y1="42" x2="135" y2="328" gradientUnits="userSpaceOnUse"><stop stop-color="#9B7DFF"/><stop offset="1" stop-color="#4F46E5" stop-opacity=".3"/></linearGradient></defs>
          </svg>
          <div class="absolute top-0 left-0 right-0 flex justify-around px-2">
            @for($i=0;$i<4;$i++)
            <div class="w-6 h-6 rounded-full flex items-center justify-center ap" style="background:rgba(123,92,245,.2);border:1px solid rgba(123,92,245,.4);animation-delay:{{ $i*0.3 }}s"><svg width="11" height="11" fill="none"><circle cx="5.5" cy="3.5" r="2" stroke="#9B7DFF" stroke-width="1.2"/><path d="M1 10.5C1 8.3 3 7 5.5 7C8 7 10 8.3 10 10.5" stroke="#9B7DFF" stroke-width="1.2" stroke-linecap="round"/></svg></div>
            @endfor
          </div>
          <div class="absolute bottom-0 left-1/2 -translate-x-1/2 text-center">
            <div class="w-[54px] h-[54px] rounded-[14px] flex items-center justify-center mx-auto" style="background:linear-gradient(135deg,rgba(123,92,245,.55),rgba(79,70,229,.35));border:1px solid rgba(155,125,255,.5);box-shadow:0 0 30px rgba(123,92,245,.55)">
              <x-senflux.logo width="26" height="26" color="white" gradient-id="howFunnel" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══ PRINCIPLES ═══ --}}
<section class="py-16" style="background:rgba(8,8,18,.65)">
  <div class="max-w-[1180px] mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 rv">
      <div>
        <p class="text-[11px] text-[#9B7DFF] uppercase tracking-wider mb-4">Our Principle</p>
        <div class="flex flex-col gap-4">
          <div class="flex items-center gap-3 font-syne font-semibold text-white text-[15px]"><span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#7B5CF5"></span>We don't predict. We observe.</div>
          <div class="flex items-center gap-3 font-syne font-semibold text-white text-[15px]"><span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#7B5CF5"></span>We don't guess. We measure.</div>
          <div class="flex items-center gap-3 font-syne font-semibold text-white text-[15px]"><span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#7B5CF5"></span>We don't react. We identify early.</div>
        </div>
      </div>
      <div class="card card-brand p-6">
        <p class="text-[15px] text-white font-medium mb-2.5">Markets expand after participation concentrates.</p>
        <p class="text-[14px] text-[#9B7DFF]">Senflux ensures you see the formation before the world catches on.</p>
      </div>
    </div>
  </div>
</section>

</div>
