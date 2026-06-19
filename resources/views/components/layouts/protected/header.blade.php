{{-- resources/views/layouts/protected/header.blade.php --}}
<header class="topbar">

    {{-- Hamburger — opens sidebar on mobile --}}
    <button class="tb-hamburger" onclick="openSidebar()" aria-label="Open menu">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="#c8c8e0" stroke-width="1.4" stroke-linecap="round">
            <path d="M2 4h11M2 7.5h11M2 11h11" />
        </svg>
    </button>

    {{-- Logo — visible on mobile only (topbar), hidden on desktop (sidebar has it) --}}
    <a href="{{ route('dashboard') }}" class="tb-logo-mob">
        <div class="flex items-center justify-center gap-2.5">
            <x-senflux.logo width="20" height="20" gradient-id="wlc-logo" />
            <span class="font-syne font-semibold text-[13px] text-white tracking-[.1em]">SENFLUX</span>
        </div>
    </a>

    {{-- Page title — desktop only --}}
    <div class="tb-title">Dashboard</div>

    {{-- Ticker — hidden on mobile, shown on tablet+ --}}
    <div class="ticker">
        <div class="tk"><span class="tk-s">BTC</span><span class="tk-p" data-price="BTC">$69,174</span><span class="up" style="font-size:10.5px">+2.4%</span></div>
        <div class="tk"><span class="tk-s">ETH</span><span class="tk-p" data-price="ETH">$3,482</span><span class="up" style="font-size:10.5px">+1.8%</span></div>
        <div class="tk"><span class="tk-s">SOL</span><span class="tk-p" data-price="SOL">$187.32</span><span class="dn" style="font-size:10.5px">-0.6%</span></div>
        <div class="tk"><span class="tk-s">BNB</span><span class="tk-p" data-price="BNB">$608.68</span><span class="up" style="font-size:10.5px">+1.0%</span></div>
        <div class="tk"><span class="tk-s">XRP</span><span class="tk-p" data-price="XRP">$0.6423</span><span class="dn" style="font-size:10.5px">-1.5%</span></div>
        <div class="tk"><span class="tk-s">ADA</span><span class="tk-p">$0.4871</span><span class="up" style="font-size:10.5px">+3.1%</span></div>
        <div class="tk"><span class="tk-s">WIF</span><span class="tk-p">$2.84</span><span class="up" style="font-size:10.5px">+14.2%</span></div>
        <div class="tk"><span class="tk-s">BONK</span><span class="tk-p">$0.000028</span><span class="up" style="font-size:10.5px">+8.6%</span></div>
    </div>

    {{-- Right actions --}}
    <div class="tb-right">
        <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="btn-dep">
            <svg width="12" height="12" fill="none" viewBox="0 0 12 12" stroke="white" stroke-width="1.5">
                <path d="M6 1V8M3 5l3 3 3-3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 10.5h10" stroke-linecap="round"/>
            </svg>
            <span class="btn-text">Deposit</span>
        </a>
        <a href="{{ route('dashboard.withdraw') }}" wire:navigate class="btn-wd">
            <svg width="12" height="12" fill="none" viewBox="0 0 12 12" stroke="currentColor" stroke-width="1.5">
                <path d="M6 8V1M3 4l3-3 3 3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 10.5h10" stroke-linecap="round"/>
            </svg>
            <span class="btn-text">Withdraw</span>
        </a>
        <div class="tb-ico">
            <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#c8c8e0" stroke-width="1.3">
                <path d="M7 1.5C4.5 1.5 2.5 3.5 2.5 6v3.5l-1 1.5h11l-1-1.5V6C11.5 3.5 9.5 1.5 7 1.5z"/>
                <path d="M5.5 11.5c0 .8.7 1.5 1.5 1.5s1.5-.7 1.5-1.5"/>
            </svg>
            <div class="ndot"></div>
        </div>
        <div class="tb-av">{{ strtoupper(substr(auth()->user()->name ?? 'JD', 0, 2)) }}</div>
    </div>

</header>