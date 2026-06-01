{{-- resources/views/livewire/pages/market-insights.blade.php --}}
<div>

{{-- ═══ HERO ═══ --}}
<section class="pt-[100px] pb-14 relative min-h-[400px] flex items-center overflow-hidden">
  <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 55% 50% at 65% 44%,rgba(123,92,245,.18),transparent 65%)"></div>
  <div class="absolute right-0 top-4 w-[52%] hidden lg:flex items-center justify-end pr-4 pointer-events-none">
    <div class="relative w-[420px] h-[310px]">
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex items-end gap-2.5">
        <div class="w-[28px] rounded-t-sm" style="height:105px;background:linear-gradient(180deg,rgba(123,92,245,.42),rgba(79,70,229,.2));border:1px solid rgba(123,92,245,.32)"></div>
        <div class="w-[28px] rounded-t-sm relative" style="height:172px;background:linear-gradient(180deg,rgba(123,92,245,.65),rgba(79,70,229,.3));border:1px solid rgba(123,92,245,.46)"><span class="absolute -top-2.5 left-1/2 -translate-x-1/2 w-2 h-2 rounded-full bg-[#7B5CF5] ap block" style="box-shadow:0 0 10px rgba(123,92,245,.9)"></span></div>
        <div class="w-[28px] rounded-t-sm relative" style="height:238px;background:linear-gradient(180deg,rgba(123,92,245,.88),rgba(79,70,229,.42));border:1px solid rgba(123,92,245,.58)"><span class="absolute -top-3 left-1/2 -translate-x-1/2 w-2.5 h-2.5 rounded-full ap2 block" style="background:#9B7DFF;box-shadow:0 0 14px rgba(123,92,245,1)"></span></div>
        <div class="w-[28px] rounded-t-sm" style="height:152px;background:linear-gradient(180deg,rgba(123,92,245,.52),rgba(79,70,229,.26));border:1px solid rgba(123,92,245,.38)"></div>
        <div class="w-[28px] rounded-t-sm" style="height:192px;background:linear-gradient(180deg,rgba(123,92,245,.72),rgba(79,70,229,.36));border:1px solid rgba(123,92,245,.48)"></div>
        <div class="w-[28px] rounded-t-sm" style="height:128px;background:linear-gradient(180deg,rgba(123,92,245,.42),rgba(79,70,229,.2));border:1px solid rgba(123,92,245,.3)"></div>
      </div>
      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 w-[300px] h-5 rounded-full" style="background:rgba(123,92,245,.28);filter:blur(16px)"></div>
      <div class="absolute top-2 right-12 w-[46px] h-[46px] rounded-[10px] flex items-center justify-center af" style="background:linear-gradient(135deg,rgba(123,92,245,.42),rgba(79,70,229,.22));border:1px solid rgba(123,92,245,.5);box-shadow:0 0 20px rgba(123,92,245,.42)">
        <x-senflux.logo width="20" height="20" color="white" gradient-id="insightsHero" />
      </div>
    </div>
  </div>
  <div class="max-w-[1180px] mx-auto px-6 relative z-10">
    <span class="pill mb-5 inline-block">Market Insights</span>
    <h1 class="font-syne font-extrabold text-[clamp(1.9rem,4.5vw,3.2rem)] max-w-[480px] leading-[1.1] mb-4">Intelligence. Not Noise.<br/><span class="tg">Insights</span> That Reveal<br/>What's Next.</h1>
    <p class="text-[14px] text-[#7a7a9a] max-w-[360px] mb-7 leading-[1.75]">In-depth analysis on market formation, participation trends, and emerging opportunities across ecosystems.</p>
    <button class="btn-o" style="padding:10px 22px;font-size:13.5px">Request Access →</button>
  </div>
</section>

{{-- ═══ FEATURED INSIGHTS ═══ --}}
<section class="py-16">
  <div class="max-w-[1280px] mx-auto px-6">
    <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)] text-center mb-8">Featured <span class="tg">Insights</span></h2>
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-4 rv">
      <div class="flex flex-col gap-3.5">
        <p class="text-[11px] text-[#9B7DFF] uppercase tracking-widest font-semibold">Featured Report</p>
        <div class="rounded-2xl overflow-hidden grid grid-cols-1 sm:grid-cols-2" style="background:rgba(8,8,18,.92);border:1px solid rgba(255,255,255,.07)">
          <div class="p-7 flex flex-col justify-between">
            <div>
              <h3 class="font-syne text-[17px] leading-[1.3] mb-3 text-white">How Participation Formed Before the SOL Ecosystem Expansion</h3>
              <p class="text-[13px] text-[#7a7a9a] leading-[1.6]">A deep dive into how wallet activity, liquidity rotation, and participation density signaled the next major expansion—hours before the market noticed.</p>
            </div>
            <a href="#" class="inline-flex items-center gap-1.5 text-[13px] text-[#9B7DFF] no-underline font-semibold mt-5 hover:text-white transition-colors">Read Full Report →</a>
          </div>
          <div class="flex items-center justify-center min-h-[200px]" style="background:radial-gradient(circle at 40% 50%,rgba(123,92,245,.32),rgba(8,8,18,.85))">
            <div class="relative w-[130px] h-[130px]">
              <div class="absolute inset-0 rounded-full border as" style="border-color:rgba(123,92,245,.25)"></div>
              <div class="absolute inset-4 rounded-full flex items-center justify-center af" style="background:radial-gradient(circle,rgba(123,92,245,.52),rgba(79,70,229,.3));box-shadow:0 0 40px rgba(123,92,245,.55)">
                <div class="w-[44px] h-[44px] rounded-[10px] flex items-center justify-center" style="background:linear-gradient(135deg,rgba(123,92,245,.75),rgba(79,70,229,.55));border:1px solid rgba(155,125,255,.5);box-shadow:0 0 20px rgba(123,92,245,.65)">
                  <x-senflux.logo width="20" height="20" color="white" gradient-id="featuredReport" />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
          <div class="card p-5 cursor-pointer hover:border-[rgba(123,92,245,.3)] transition-colors"><p class="text-[10px] text-[#9B7DFF] uppercase tracking-widest font-semibold mb-2">Analysis</p><h3 class="font-syne text-[13.5px] leading-[1.35] mb-2 text-white">Meme Market Rotation: Data Shows</h3><p class="text-[12px] text-[#7a7a9a] leading-[1.5]">Tracking how capital moves between narratives and what drives the next wave.</p><div class="flex justify-between items-center mt-3.5"><span class="text-[11px] text-[#4a4a6a]">APR 30, 2026</span><span class="text-[13px] text-[#9B7DFF]">↗</span></div></div>
          <div class="card p-5 cursor-pointer hover:border-[rgba(123,92,245,.3)] transition-colors"><p class="text-[10px] text-[#06B6D4] uppercase tracking-widest font-semibold mb-2">Insight</p><h3 class="font-syne text-[13.5px] leading-[1.35] mb-2 text-white">Wallet Cohesion: The Hidden Edge</h3><p class="text-[12px] text-[#7a7a9a] leading-[1.5]">Why wallet alignment is a stronger signal than volume alone.</p><div class="flex justify-between items-center mt-3.5"><span class="text-[11px] text-[#4a4a6a]">APR 30, 2026</span><span class="text-[13px] text-[#9B7DFF]">↗</span></div></div>
          <div class="card p-5 cursor-pointer hover:border-[rgba(123,92,245,.3)] transition-colors"><p class="text-[10px] text-[#10B981] uppercase tracking-widest font-semibold mb-2">Report</p><h3 class="font-syne text-[13.5px] leading-[1.35] mb-2 text-white">Participation vs Short Term Hype</h3><p class="text-[12px] text-[#7a7a9a] leading-[1.5]">How persistence reveals early formation before market moves.</p><div class="flex justify-between items-center mt-3.5"><span class="text-[11px] text-[#4a4a6a]">APR 30, 2026</span><span class="text-[13px] text-[#9B7DFF]">↗</span></div></div>
        </div>
      </div>
      {{-- Sidebar --}}
      <div class="flex flex-col gap-2.5">
        <div class="flex items-center gap-2 mb-1"><span class="w-2 h-2 rounded-full bg-[#10B981] ap block"></span><span class="text-[11px] text-[#7a7a9a] uppercase tracking-widest font-semibold">Latest Insights</span></div>
        @php
        $latestInsights = [
            ['color'=>'#9B7DFF','bg'=>'rgba(123,92,245,.15)','border'=>'rgba(123,92,245,.22)','label'=>'Analysis','labelColor'=>'text-[#9B7DFF]','title'=>'AI Agent Tokens: Participation Trends You Can\'t Ignore','date'=>'APR 28, 17:04'],
            ['color'=>'#06B6D4','bg'=>'rgba(6,182,212,.1)','border'=>'rgba(6,182,212,.22)','label'=>'Insight','labelColor'=>'text-[#06B6D4]','title'=>'Liquidity Clusters: Where Smart Capital Accumulates Early','date'=>'APR 27, 11:34'],
            ['color'=>'#10B981','bg'=>'rgba(16,185,129,.1)','border'=>'rgba(16,185,129,.22)','label'=>'Report','labelColor'=>'text-[#10B981]','title'=>'Gaming Sector Formation Heating Up','date'=>'APR 26, 22:14'],
            ['color'=>'#F59E0B','bg'=>'rgba(245,158,11,.1)','border'=>'rgba(245,158,11,.22)','label'=>'Analysis','labelColor'=>'text-[#F59E0B]','title'=>'What On-Chain Data Reveals About Market Cycles','date'=>'APR 23, 09:12'],
            ['color'=>'#F43F5E','bg'=>'rgba(244,63,94,.1)','border'=>'rgba(244,63,94,.22)','label'=>'Insight','labelColor'=>'text-[#F43F5E]','title'=>'Early Signals That Precede Breakout Movements','date'=>'APR 24, 15:54'],
        ];
        @endphp
        @foreach($latestInsights as $item)
        <a href="#" class="ir">
          <div class="w-[52px] h-[52px] rounded-xl flex-shrink-0 flex items-center justify-center" style="background:{{ $item['bg'] }};border:1px solid {{ $item['border'] }}">
            <x-senflux.logo width="22" height="22" color="white" gradient-id="ins{{ $loop->index }}" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-[10px] uppercase tracking-widest font-semibold mb-0.5 {{ $item['labelColor'] }}">{{ $item['label'] }}</p>
            <p class="font-syne text-[13px] font-semibold text-white leading-[1.3] mb-0.5">{{ $item['title'] }}</p>
            <p class="text-[11px] text-[#4a4a6a]">{{ $item['date'] }}</p>
          </div>
          <span class="text-[#4a4a6a] text-[13px] flex-shrink-0 pt-0.5">↗</span>
        </a>
        @endforeach
      </div>
    </div>
  </div>
</section>

{{-- ═══ PILLARS ═══ --}}
<section class="py-16" style="background:rgba(8,8,18,.65)">
  <div class="max-w-[1280px] mx-auto px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 rv">
      <div class="flex gap-3.5 items-start"><div class="ib flex-shrink-0"><svg width="18" height="18" fill="none"><path d="M2 9L5 6L8 9L11 5L14 8L17 4" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div><div><h3 class="font-syne text-[14px] mb-1">Data-Driven</h3><p class="text-[13px] text-[#7a7a9a]">Every insight is backed by real on-chain data and observable participation behavior.</p></div></div>
      <div class="flex gap-3.5 items-start"><div class="ib flex-shrink-0"><svg width="18" height="18" fill="none"><path d="M3 9L7 13L15 5" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="9" cy="9" r="7" stroke="#9B7DFF" stroke-width="1.5"/></svg></div><div><h3 class="font-syne text-[14px] mb-1">Transparent</h3><p class="text-[13px] text-[#7a7a9a]">We show the why behind what we observe.</p></div></div>
      <div class="flex gap-3.5 items-start"><div class="ib flex-shrink-0"><svg width="18" height="18" fill="none"><circle cx="9" cy="9" r="7" stroke="#9B7DFF" stroke-width="1.5"/><path d="M9 5V9L12 11" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><div><h3 class="font-syne text-[14px] mb-1">Timely</h3><p class="text-[13px] text-[#7a7a9a]">Insights delivered while formation is happening, not after.</p></div></div>
      <div class="flex gap-3.5 items-start"><div class="ib flex-shrink-0"><svg width="18" height="18" fill="none"><circle cx="6" cy="6" r="2" stroke="#9B7DFF" stroke-width="1.5"/><circle cx="12" cy="6" r="2" stroke="#9B7DFF" stroke-width="1.5"/><circle cx="9" cy="12" r="2" stroke="#9B7DFF" stroke-width="1.5"/><path d="M8 6H10M7.5 8L9 10M10.5 8L9 10" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"/></svg></div><div><h3 class="font-syne text-[14px] mb-1">Actionable</h3><p class="text-[13px] text-[#7a7a9a]">Designed to give participants an edge in understanding what's next.</p></div></div>
    </div>
  </div>
</section>

</div>
