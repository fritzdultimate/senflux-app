{{-- resources/views/livewire/pages/terminal.blade.php --}}
<div>
{{-- ═══ HERO ═══ --}}
<section class="pt-[100px] pb-12 relative min-h-[480px] flex items-center overflow-hidden">
    <div class="absolute inset-0 pointer-events-none"
         style="background:radial-gradient(ellipse 55% 55% at 70% 50%,rgba(123,92,245,.2),transparent 65%)"></div>

    <div class="max-w-[1180px] mx-auto px-6 w-full relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- LEFT: Text --}}
            <div>
                <span class="pill mb-5 inline-block">The Terminal</span>

                <h1 class="font-syne font-bold text-[clamp(1.6rem,2.8vw,2.6rem)] leading-[1.15] mb-4 max-w-[440px]">
                    The Terminal for<br/>
                    <span class="tg">Market Formation Intelligence</span>
                </h1>

                <p class="text-[14px] text-[#7a7a9a] max-w-[360px] mb-6 leading-[1.75]">
                    Real-time visibility into where participation is building, strengthening, and preparing for expansion.
                </p>

                <div class="grid grid-cols-2 gap-x-6 gap-y-2 max-w-[400px] mb-7">
                    <div class="flex items-center gap-2 text-[13px] text-[#c8c8e0]">
                        <span class="w-[6px] h-[6px] rounded-full bg-[#7B5CF5] flex-shrink-0 block"></span>
                        Live Participation Tracking
                    </div>
                    <div class="flex items-center gap-2 text-[13px] text-[#c8c8e0]">
                        <span class="w-[6px] h-[6px] rounded-full bg-[#7B5CF5] flex-shrink-0 block"></span>
                        Wallet Intelligence &amp; Clusters
                    </div>
                    <div class="flex items-center gap-2 text-[13px] text-[#c8c8e0]">
                        <span class="w-[6px] h-[6px] rounded-full bg-[#7B5CF5] flex-shrink-0 block"></span>
                        Formation States &amp; Signals
                    </div>
                    <div class="flex items-center gap-2 text-[13px] text-[#c8c8e0]">
                        <span class="w-[6px] h-[6px] rounded-full bg-[#7B5CF5] flex-shrink-0 block"></span>
                        Transparent by Design
                    </div>
                </div>

                <div class="flex gap-3 flex-wrap">
                    <a href="{{ route('register') }}" class="btn-p">Explore Intelligence →</a>
                    <button class="btn-o">View Capital Flows</button>
                </div>
            </div>

            {{-- RIGHT: Dashboard mockup --}}
            <div class="hidden lg:flex items-center justify-center">
                <div style="position:relative;width:380px;height:290px">
                    <div class="absolute inset-0 rounded-2xl overflow-hidden"
                         style="background:rgba(8,8,18,.96);border:1px solid rgba(123,92,245,.22);box-shadow:0 20px 80px rgba(123,92,245,.27)">

                        {{-- Browser bar --}}
                        <div class="flex items-center gap-2 px-4 py-2.5 border-b"
                             style="border-color:rgba(255,255,255,.07)">
                            <span class="w-2 h-2 rounded-full" style="background:rgba(244,63,94,.6)"></span>
                            <span class="w-2 h-2 rounded-full" style="background:rgba(245,158,11,.6)"></span>
                            <span class="w-2 h-2 rounded-full" style="background:rgba(16,185,129,.6)"></span>
                            <div class="flex-1 h-[17px] rounded ml-2 flex items-center px-2"
                                 style="background:rgba(123,92,245,.08)">
                                <span class="text-[10px] text-[#4a4a6a]">senflux.io/terminal</span>
                            </div>
                        </div>

                        <div class="p-3">
                            {{-- Stats row --}}
                            <div class="grid grid-cols-2 gap-2 mb-2.5">
                                <div class="rounded-lg p-2"
                                     style="background:rgba(123,92,245,.08);border:1px solid rgba(123,92,245,.15)">
                                    <p class="text-[9px] text-[#4a4a6a]">FORMATION FEED</p>
                                    <p class="text-[11px] text-white font-semibold mt-0.5">Live</p>
                                </div>
                                <div class="rounded-lg p-2"
                                     style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.15)">
                                    <p class="text-[9px] text-[#4a4a6a]">ACTIVE</p>
                                    <p class="text-[11px] text-[#10B981] font-semibold mt-0.5">14,682</p>
                                </div>
                            </div>

                            {{-- Asset rows --}}
                            <div class="flex flex-col gap-1.5 mb-2.5">
                                <div class="flex items-center justify-between py-1.5 border-b"
                                     style="border-color:rgba(255,255,255,.04)">
                                    <span class="font-syne font-bold text-white text-[11px]">WIF</span>
                                    <span class="text-[10px] rounded px-1.5 py-0.5"
                                          style="background:rgba(16,185,129,.15);color:#10B981">Active</span>
                                    <span class="text-[11px] text-[#9B7DFF] font-semibold">+214%</span>
                                </div>
                                <div class="flex items-center justify-between py-1.5 border-b"
                                     style="border-color:rgba(255,255,255,.04)">
                                    <span class="font-syne font-bold text-white text-[11px]">BONK</span>
                                    <span class="text-[10px] rounded px-1.5 py-0.5"
                                          style="background:rgba(123,92,245,.15);color:#9B7DFF">Building</span>
                                    <span class="text-[11px] text-[#9B7DFF] font-semibold">+143%</span>
                                </div>
                                <div class="flex items-center justify-between py-1.5">
                                    <span class="font-syne font-bold text-white text-[11px]">POPCAT</span>
                                    <span class="text-[10px] rounded px-1.5 py-0.5"
                                          style="background:rgba(6,182,212,.12);color:#06B6D4">Early</span>
                                    <span class="text-[11px] text-[#9B7DFF] font-semibold">+67%</span>
                                </div>
                            </div>

                            {{-- Map dots --}}
                            <div class="relative h-[42px] rounded-lg overflow-hidden"
                                 style="background:rgba(123,92,245,.05);border:1px solid rgba(123,92,245,.1)">
                                <span class="absolute w-2.5 h-2.5 rounded-full ap block"
                                      style="background:#10B981;top:10px;left:28%;box-shadow:0 0 8px rgba(16,185,129,.7)"></span>
                                <span class="absolute w-2 h-2 rounded-full ap2 block"
                                      style="background:#7B5CF5;top:18px;right:22%;box-shadow:0 0 8px rgba(123,92,245,.7)"></span>
                                <span class="absolute w-1.5 h-1.5 rounded-full ap3 block"
                                      style="background:#F59E0B;top:8px;left:54%;box-shadow:0 0 6px rgba(245,158,11,.7)"></span>
                            </div>
                        </div>

                        <div class="py-2 text-center border-t" style="border-color:rgba(255,255,255,.04)">
                            <span class="font-syne text-[10px] tracking-widest"
                                  style="color:rgba(123,92,245,.5)">SENFLUX TERMINAL</span>
                        </div>
                    </div>

                    {{-- Bottom glow --}}
                    <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-[270px] h-7 rounded-full"
                         style="background:rgba(123,92,245,.22);filter:blur(20px)"></div>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ═══ LIVE FORMATION SECTION ═══ --}}
<section class="py-14" style="background:rgba(8,8,18,.65)">
  <div class="max-w-[1280px] mx-auto px-6">
    <div class="text-center mb-10">
      <p class="text-[11px] text-[#4a4a6a] uppercase tracking-widest mb-2">Live Market Formation</p>
      <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Observe Participation Formation <span class="tg">in Real Time</span></h2>
      <p class="text-[14px] text-[#7a7a9a] mt-2.5 max-w-[560px] mx-auto leading-[1.7]">Senflux continuously monitors on-chain participation behavior across markets, identifying where formation is strengthening, stabilizing, or weakening before broader expansion becomes visible.</p>
    </div>

    {{-- Feed + Heatmap --}}
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_358px] gap-3.5 mb-3.5 rv">
      <div class="rounded-2xl overflow-hidden" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
        <div class="flex items-center justify-between px-5 py-3.5 border-b" style="border-color:rgba(255,255,255,.07)">
          <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-[#10B981] ap block"></span><span class="text-[12px] font-semibold text-[#c8c8e0]">LIVE FORMATION FEED</span></div>
          <span class="text-[11px] text-[#4a4a6a]">Last updated: just now</span>
        </div>
        <div class="overflow-x-auto">
          <table class="ftbl">
            <thead><tr><th>Asset</th><th>Formation State</th><th>Participation (VS 24H)</th><th>Persistence Score</th><th>Velocity 7D</th><th>Trend</th></tr></thead>
            <tbody>
              <tr><td><span class="font-syne font-bold text-white text-[13px]">WIF</span><br/><span class="text-[11px] text-[#4a4a6a]">dogwifhat</span></td><td><span class="badge badge-green">ACTIVE ▲</span></td><td><span class="text-[#9B7DFF] font-semibold">+214%</span><br/><span class="text-[11px] text-[#4a4a6a]">High</span></td><td><span class="text-white font-bold">86</span><span class="text-[#4a4a6a] text-[12px]">/100</span><br/><span class="text-[11px] text-[#10B981] font-semibold">STRONG</span></td><td><div class="spark"><span style="height:8px"></span><span style="height:13px"></span><span style="height:17px"></span><span style="height:21px;opacity:1"></span><span style="height:19px"></span></div></td><td class="text-[#9B7DFF] text-base">↗</td></tr>
              <tr><td><span class="font-syne font-bold text-white text-[13px]">BONK</span><br/><span class="text-[11px] text-[#4a4a6a]">bonk</span></td><td><span class="badge badge-purple">BUILDING ↑</span></td><td><span class="text-[#9B7DFF] font-semibold">+143%</span><br/><span class="text-[11px] text-[#4a4a6a]">High</span></td><td><span class="text-white font-bold">72</span><span class="text-[#4a4a6a] text-[12px]">/100</span><br/><span class="text-[11px] text-[#9B7DFF] font-semibold">STRONG</span></td><td><div class="spark"><span style="height:6px"></span><span style="height:10px"></span><span style="height:15px"></span><span style="height:19px;opacity:1;background:#9B7DFF"></span><span style="height:22px;opacity:1;background:#9B7DFF"></span></div></td><td class="text-[#9B7DFF] text-base">↗</td></tr>
              <tr><td><span class="font-syne font-bold text-white text-[13px]">POPCAT</span><br/><span class="text-[11px] text-[#4a4a6a]">popcat</span></td><td><span class="badge" style="background:rgba(6,182,212,.12);color:#06B6D4;border:1px solid rgba(6,182,212,.22)">EARLY ◉</span></td><td><span class="text-[#06B6D4] font-semibold">+67%</span><br/><span class="text-[11px] text-[#4a4a6a]">Moderate</span></td><td><span class="text-white font-bold">58</span><span class="text-[#4a4a6a] text-[12px]">/100</span><br/><span class="text-[11px] text-[#F59E0B] font-semibold">MODERATE</span></td><td><div class="spark"><span style="height:11px;background:#06B6D4"></span><span style="height:14px;background:#06B6D4"></span><span style="height:12px;background:#06B6D4"></span><span style="height:16px;background:#06B6D4;opacity:.8"></span><span style="height:13px;background:#06B6D4"></span></div></td><td class="text-[#06B6D4]">→</td></tr>
              <tr><td><span class="font-syne font-bold text-white text-[13px]">JTO</span><br/><span class="text-[11px] text-[#4a4a6a]">jito</span></td><td><span class="badge badge-red">WEAKENING ↓</span></td><td><span class="text-[#F43F5E] font-semibold">-23%</span><br/><span class="text-[11px] text-[#4a4a6a]">Low</span></td><td><span class="text-white font-bold">34</span><span class="text-[#4a4a6a] text-[12px]">/100</span><br/><span class="text-[11px] text-[#F43F5E] font-semibold">WEAK</span></td><td><div class="spark"><span style="height:21px;background:#F43F5E;opacity:.7"></span><span style="height:17px;background:#F43F5E;opacity:.6"></span><span style="height:12px;background:#F43F5E"></span><span style="height:7px;background:#F43F5E"></span><span style="height:4px;background:#F43F5E"></span></div></td><td class="text-[#F43F5E]">↓</td></tr>
              <tr><td><span class="font-syne font-bold text-white text-[13px]">PYTH</span><br/><span class="text-[11px] text-[#4a4a6a]">Pyth Network</span></td><td><span class="badge badge-gray">IDLE ↔</span></td><td><span class="text-[#7a7a9a] font-semibold">+5%</span><br/><span class="text-[11px] text-[#4a4a6a]">Low</span></td><td><span class="text-white font-bold">21</span><span class="text-[#4a4a6a] text-[12px]">/100</span><br/><span class="text-[11px] text-[#4a4a6a]">WEAK</span></td><td><div class="spark"><span style="height:11px;background:#4a4a6a"></span><span style="height:9px;background:#4a4a6a"></span><span style="height:11px;background:#4a4a6a"></span><span style="height:10px;background:#4a4a6a"></span><span style="height:9px;background:#4a4a6a"></span></div></td><td class="text-[#4a4a6a]">→</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      {{-- Heatmap --}}
      <div class="rounded-2xl overflow-hidden" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
        <div class="flex items-center justify-between px-4 py-3.5 border-b" style="border-color:rgba(255,255,255,.07)"><span class="text-[12px] font-semibold text-[#c8c8e0]">PARTICIPATION HEATMAP</span><span class="text-[11px] text-[#9B7DFF] rounded-md px-2 py-1" style="background:rgba(123,92,245,.1);border:1px solid rgba(123,92,245,.2)">Solana Ecosystem</span></div>
        <div class="grid grid-cols-3 gap-1.5 p-3.5">
          <div class="hx hx-vs"><p class="text-[10px] font-bold text-white leading-tight">Memecoins</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Very Strong</p></div>
          <div class="hx hx-s"><p class="text-[10px] font-bold text-white leading-tight">AI Agents</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Strong</p></div>
          <div class="hx hx-s"><p class="text-[10px] font-bold text-white leading-tight">DeFi</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Strong</p></div>
          <div class="hx hx-m"><p class="text-[10px] font-bold text-white leading-tight">DePIN</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Moderate</p></div>
          <div class="hx hx-e"><p class="text-[10px] font-bold text-white leading-tight">Gaming</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Early</p></div>
          <div class="hx hx-w"><p class="text-[10px] font-bold text-white leading-tight">RWA</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Weak</p></div>
          <div class="hx hx-i"><p class="text-[10px] font-bold text-white leading-tight">NFT</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Idle</p></div>
          <div class="hx hx-i col-span-2"><p class="text-[10px] font-bold text-white leading-tight">Infrastructure</p><p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">Monitoring</p></div>
        </div>
        <div class="grid grid-cols-4 border-t" style="border-color:rgba(255,255,255,.07)">
          <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)"><p class="font-syne font-bold text-[14px] text-white">14,682</p><p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Active Wallets</p><p class="text-[9.5px] text-[#10B981]">+18.3%</p></div>
          <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)"><p class="font-syne font-bold text-[14px] text-white">327</p><p class="text-[9.5px] text-[#4a4a6a] mt-0.5">New Wallets</p><p class="text-[9.5px] text-[#10B981]">+24.7%</p></div>
          <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)"><p class="font-syne font-bold text-[14px] text-white">$18.7M</p><p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Net Inflow 24H</p><p class="text-[9.5px] text-[#10B981]">+52.1%</p></div>
          <div class="p-3 text-center"><p class="font-syne font-bold text-[14px] text-white">2.6x</p><p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Velocity 7D</p><p class="text-[9.5px] text-[#10B981]">vs 30D avg</p></div>
        </div>
      </div>
    </div>

    {{-- Formation states --}}
    <div class="grid grid-cols-1 md:grid-cols-[285px_1fr] gap-3.5 rv">
      <div class="rounded-2xl overflow-hidden" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
        <div class="flex items-center justify-between px-4 py-3 border-b" style="border-color:rgba(255,255,255,.07)"><span class="text-[12px] font-semibold text-[#c8c8e0]">PARTICIPATION MAP</span><span class="text-[11px] text-[#9B7DFF]">↗</span></div>
        <div class="relative h-[165px] p-3" style="background:radial-gradient(circle at 42% 52%,rgba(123,92,245,.1),rgba(8,8,18,.9))">
          <div class="absolute w-[34px] h-[34px] rounded-full flex items-center justify-center text-base" style="background:rgba(245,158,11,.2);border:1px solid rgba(245,158,11,.4);top:22px;left:30px">🐕</div>
          <div class="absolute w-[30px] h-[30px] rounded-full flex items-center justify-center text-sm" style="background:rgba(16,185,129,.2);border:1px solid rgba(16,185,129,.4);top:52px;left:105px">🐱</div>
          <div class="absolute w-[26px] h-[26px] rounded-full flex items-center justify-center font-syne font-bold text-[11px] text-[#9B7DFF]" style="background:rgba(123,92,245,.2);border:1px solid rgba(123,92,245,.4);bottom:24px;right:48px">P</div>
          <div class="absolute w-[22px] h-[22px] rounded-full flex items-center justify-center font-syne font-bold text-[10px] text-[#F43F5E]" style="background:rgba(244,63,94,.15);border:1px solid rgba(244,63,94,.3);bottom:42px;left:68px">J</div>
        </div>
        <div class="px-3.5 pb-3.5">
          <div class="grid grid-cols-2 gap-1.5">
            <div class="flex items-center gap-1.5 text-[11px] text-[#c8c8e0]"><span class="w-[6px] h-[6px] rounded-full bg-[#10B981] flex-shrink-0 block"></span>Very Strong</div>
            <div class="flex items-center gap-1.5 text-[11px] text-[#c8c8e0]"><span class="w-[6px] h-[6px] rounded-full bg-[#7B5CF5] flex-shrink-0 block"></span>Strong</div>
            <div class="flex items-center gap-1.5 text-[11px] text-[#c8c8e0]"><span class="w-[6px] h-[6px] rounded-full bg-[#F59E0B] flex-shrink-0 block"></span>Moderate</div>
            <div class="flex items-center gap-1.5 text-[11px] text-[#c8c8e0]"><span class="w-[6px] h-[6px] rounded-full bg-[#06B6D4] flex-shrink-0 block"></span>Early</div>
            <div class="flex items-center gap-1.5 text-[11px] text-[#c8c8e0]"><span class="w-[6px] h-[6px] rounded-full bg-[#F43F5E] flex-shrink-0 block"></span>Weak</div>
            <div class="flex items-center gap-1.5 text-[11px] text-[#c8c8e0]"><span class="w-[6px] h-[6px] rounded-full bg-[#4a4a6a] flex-shrink-0 block"></span>Idle</div>
          </div>
        </div>
      </div>
      <div class="rounded-2xl p-5" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
        <p class="text-[12px] font-semibold text-[#c8c8e0] uppercase tracking-wider mb-4">Formation States Explained</p>
        <div class="grid grid-cols-5 gap-2 mb-4">
          <div class="fs fs-i"><p class="text-[10px] font-bold text-[#7a7a9a] uppercase tracking-wide leading-tight">IDLE</p><p class="text-[10px] text-[rgba(255,255,255,.4)] mt-1 leading-tight">Minimal meaningful participation.</p></div>
          <div class="fs fs-e"><p class="text-[10px] font-bold text-[#06B6D4] uppercase tracking-wide leading-tight">EARLY</p><p class="text-[10px] text-[rgba(255,255,255,.4)] mt-1 leading-tight">Initial activity is emerging.</p></div>
          <div class="fs fs-b"><p class="text-[10px] font-bold text-[#9B7DFF] uppercase tracking-wide leading-tight">BUILDING</p><p class="text-[10px] text-[rgba(255,255,255,.4)] mt-1 leading-tight">Participation is increasing.</p></div>
          <div class="fs fs-a"><p class="text-[10px] font-bold text-[#10B981] uppercase tracking-wide leading-tight">ACTIVE</p><p class="text-[10px] text-[rgba(255,255,255,.4)] mt-1 leading-tight">Sustained participation confirmed.</p></div>
          <div class="fs fs-w"><p class="text-[10px] font-bold text-[#F43F5E] uppercase tracking-wide leading-tight">WEAKENING</p><p class="text-[10px] text-[rgba(255,255,255,.4)] mt-1 leading-tight">Participation is declining.</p></div>
        </div>
        <div class="flex items-center gap-1.5 mb-5">
          <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#4a4a6a"></span><div class="flex-1 h-px" style="background:rgba(255,255,255,.08)"></div>
          <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#06B6D4"></span><div class="flex-1 h-px" style="background:rgba(255,255,255,.08)"></div>
          <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#7B5CF5"></span><div class="flex-1 h-px" style="background:rgba(255,255,255,.08)"></div>
          <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#10B981"></span><div class="flex-1 h-px" style="background:rgba(255,255,255,.08)"></div>
          <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:#F43F5E"></span>
        </div>
        <div class="flex gap-3.5 items-start p-4 rounded-xl" style="background:rgba(123,92,245,.06);border:1px solid rgba(123,92,245,.18)">
          <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(123,92,245,.15);border:1px solid rgba(123,92,245,.3)"><svg width="16" height="16" fill="none"><path d="M8 1L12 4V8C12 11 10.2 13.5 8 14.5C5.8 13.5 4 11 4 8V4L8 1Z" stroke="#9B7DFF" stroke-width="1.3" stroke-linejoin="round"/></svg></div>
          <div><p class="text-[13px] font-semibold text-white mb-1">Transparency is at the core of everything we build.</p><p class="text-[12px] text-[#7a7a9a]">Every signal in the Terminal is based on observable on-chain behavior.</p></div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══ EDGE ═══ --}}
<section class="py-20 relative overflow-hidden">
  <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 70% 60% at 50% 50%,rgba(123,92,245,.08),transparent 72%)"></div>
  <div class="max-w-[1180px] mx-auto px-6 relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
      <div>
        <h2 class="font-syne font-extrabold text-[clamp(1.5rem,3.5vw,2.4rem)] leading-[1.15]">The earlier you see formation,<br/>the greater your <span class="tg">edge.</span></h2>
        <p class="text-[14px] text-[#7a7a9a] mt-3.5 max-w-[320px] leading-[1.75]">Join the network of participants observing what creates the future.</p>
      </div>
      <div>
        <div class="grid grid-cols-3 gap-3 mb-5">
          <div class="card p-5 text-center"><div class="ib mx-auto mb-3"><svg width="18" height="18" fill="none"><circle cx="9" cy="9" r="7" stroke="#9B7DFF" stroke-width="1.5"/><path d="M9 5V9L12 11" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><h3 class="font-syne text-[13px] mb-1">Real Time Data</h3><p class="text-[12px] text-[#7a7a9a]">Updated every few seconds.</p></div>
          <div class="card p-5 text-center"><div class="ib ib-g mx-auto mb-3"><svg width="18" height="18" fill="none"><path d="M3 9L7 13L15 5" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="9" r="7" stroke="#10B981" stroke-width="1.5"/></svg></div><h3 class="font-syne text-[13px] mb-1">On-Chain Verified</h3><p class="text-[12px] text-[#7a7a9a]">100% verifiable on-chain activity.</p></div>
          <div class="card p-5 text-center"><div class="ib mx-auto mb-3"><svg width="18" height="18" fill="none"><circle cx="9" cy="9" r="7" stroke="#9B7DFF" stroke-width="1.5"/><path d="M9 6V9M9 12H9.01" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><h3 class="font-syne text-[13px] mb-1">No Black Box</h3><p class="text-[12px] text-[#7a7a9a]">We show what we see and why it matters.</p></div>
        </div>
        <div class="text-center">
          <a href="{{ route('register') }}" class="btn-p mx-auto" style="padding:12px 28px;font-size:14px">Request access to Terminal →</a>
          <p class="text-[12px] text-[#4a4a6a] mt-2.5">Limited access. Built for serious participants.</p>
        </div>
      </div>
    </div>
  </div>
</section>

</div>
