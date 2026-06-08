{{-- Stats Grid --}}
<div class="grid grid-cols-2 gap-2 w-full mb-3 lg:grid-cols-5">

    {{-- Wallet Balance --}}
    <div class="rounded-2xl border border-[rgba(123,92,245,0.22)] bg-[rgba(255,255,255,0.033)] p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10.5px] font-bold tracking-widest uppercase text-[#4a4a6a]">Wallet Balance</span>
            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-[rgba(123,92,245,0.12)] shrink-0">
                <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#9B7DFF" stroke-width="1.3">
                    <rect x="1" y="4" width="12" height="9" rx="1.8" />
                    <path d="M1 7h12" stroke-linecap="round" />
                    <path d="M10 10.5h1.5" stroke-linecap="round" />
                </svg>
            </div>
        </div>
        <div class="font-syne font-extrabold text-[clamp(1rem,3.5vw,1.65rem)] text-white leading-none break-all">$4,826.50</div>
        <div class="flex items-center gap-1 mt-1.5 text-[11.5px] text-[#7a7a9a]">
            <span class="text-[#10B981]">↑ 2.4%</span>
            <span class="text-[#4a4a6a]">today</span>
        </div>
    </div>

    {{-- Staking --}}
    <div class="rounded-2xl border border-[rgba(16,185,129,0.22)] bg-[rgba(255,255,255,0.033)] p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10.5px] font-bold tracking-widest uppercase text-[#4a4a6a]">Staking</span>
            <div class="w-7 h-7 rounded-lg flex items-center justify-center bg-[rgba(16,185,129,0.1)] shrink-0">
                <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#10B981" stroke-width="1.3" stroke-linejoin="round">
                    <path d="M7 1L12.5 3.5V7C12.5 10.1 10 12.6 7 13.5C4 12.6 1.5 10.1 1.5 7V3.5z" />
                </svg>
            </div>
        </div>
        <div class="font-syne font-extrabold text-[clamp(1rem,3.5vw,1.65rem)] text-[#10B981] leading-none break-all">$2,700.00</div>
        <div class="flex items-center gap-1 mt-1.5">
            <span class="text-[9.5px] font-semibold px-1.5 py-0.5 rounded-md bg-[rgba(16,185,129,0.12)] text-[#10B981] border border-[rgba(16,185,129,0.22)]">18% APY</span>
            <span class="text-[11.5px] text-[#4a4a6a]">Pro</span>
        </div>
    </div>

    {{-- Total Earned --}}
    <div class="rounded-2xl border border-[rgba(255,255,255,0.07)] bg-[rgba(255,255,255,0.033)] p-4">
        <div class="mb-2">
            <span class="text-[10.5px] font-bold tracking-widest uppercase text-[#4a4a6a]">Total Earned</span>
        </div>
        <div class="font-syne font-extrabold text-[clamp(1rem,3.5vw,1.65rem)] leading-none break-all bg-gradient-to-br from-[#9B7DFF] via-[#7B5CF5] to-[#4F46E5] bg-clip-text text-transparent">$324.18</div>
        <div class="mt-1.5 text-[11.5px] text-[#4a4a6a]">All-time rewards</div>
    </div>

    {{-- Bot P&L Today --}}
    <div class="rounded-2xl border border-[rgba(16,185,129,0.22)] bg-[rgba(255,255,255,0.033)] p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10.5px] font-bold tracking-widest uppercase text-[#4a4a6a]">Bot P&L Today</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] shrink-0 animate-pulse"></span>
        </div>
        <div class="font-syne font-extrabold text-[clamp(1rem,3.5vw,1.65rem)] text-[#10B981] leading-none break-all">+$48.14</div>
        <div class="flex items-center gap-1.5 mt-1.5">
            <span class="text-[9.5px] font-semibold px-1.5 py-0.5 rounded-md bg-[rgba(16,185,129,0.12)] text-[#10B981] border border-[rgba(16,185,129,0.22)]">Running</span>
            <a href="#" class="text-[11.5px] text-[#9B7DFF] underline underline-offset-2">Pause</a>
        </div>
    </div>

    {{-- Active Signals — spans full width on mobile when alone in row --}}
    <div class="rounded-2xl border border-[rgba(123,92,245,0.22)] bg-[rgba(255,255,255,0.033)] p-4 col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10.5px] font-bold tracking-widest uppercase text-[#4a4a6a]">Active Signals</span>
            <span class="w-1.5 h-1.5 rounded-full bg-[#9B7DFF] shrink-0 animate-pulse"></span>
        </div>
        <div class="font-syne font-extrabold text-[clamp(1rem,3.5vw,1.65rem)] leading-none bg-gradient-to-br from-[#9B7DFF] via-[#7B5CF5] to-[#4F46E5] bg-clip-text text-transparent">7</div>
        <div class="mt-1.5">
            <a href="#" class="text-[11.5px] text-[#9B7DFF] no-underline">View all →</a>
        </div>
    </div>

</div>