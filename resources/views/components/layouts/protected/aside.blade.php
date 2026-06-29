{{-- resources/views/layouts/protected/aside.blade.php --}}
<aside class="sidebar" id="sidebar">

    {{-- Logo --}}
    <div class="flex items-center justify-center gap-2.5 mt-5">
        <x-senflux.logo width="32" height="32" gradient-id="wlc-logo" />
        <span class="font-syne font-bold text-[15px] text-white tracking-[.1em]">SENFLUX</span>
    </div>

    {{-- ── OVERVIEW ─────────────────────────────────────────────────── --}}
    <div class="sb-sect">OVERVIEW</div>

    <a href="{{ route('dashboard') }}" wire:navigate
       class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 1h5.5v5.5H1zm8.5 0H15v5.5H9.5zM1 9.5h5.5V15H1zm8.5 0H15V15H9.5z" />
        </svg>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('dashboard.markets') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 11L5 7.5L7.5 9.5L10.5 5.5L13 7.5" />
        </svg>
        <span>Markets</span>
    </a>

    <a href="{{ route('dashboard.live-trades') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 11.5L5 8L7.5 10L11 5L13.5 7M11.5 5h2v2" />
        </svg>
        <span>Live Trades</span>
        <span class="sb-badge sb-badge-live">LIVE</span>
    </a>

    <a href="{{ route('dashboard.signals') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 8.5h11M7.5 4L11 8.5L7.5 13M2 4v9" />
        </svg>
        <span>Signals</span>
        <span class="sb-badge sb-badge-pro">PRO</span>
    </a>

    <a href="{{ route('dashboard.terminal') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 3h11v9H2zM4 6l2 2-2 2M7.5 10h3" />
        </svg>
        <span>Terminal</span>
        <span class="sb-badge sb-badge-new">NEW</span>
    </a>

    {{-- ── CAPITAL ──────────────────────────────────────────────────── --}}
    <div class="sb-sect">CAPITAL</div>

    <a href="{{ route('dashboard.packs.index') }}" wire:navigate
       class="sb-item {{ request()->routeIs('dashboard.subscribe', 'dashboard.subscription.*') ? 'active' : '' }}">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.5 1L13 4V8c0 3.1-2.7 5.8-5.5 6.5C4.2 13.8 2 11.1 2 8V4z" />
        </svg>
        <span>My Packs</span>
    </a>

    <a href="{{ route('dashboard.deposit.create') }}" wire:navigate
       class="sb-item {{ request()->routeIs('dashboard.deposit.*') ? 'active' : '' }}">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.5 1v9M4 7l3.5 3.5L11 7M2 13.5h11" />
        </svg>
        <span>Deposit</span>
    </a>

    <a href="{{ route('dashboard.withdraw') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.5 14V8M4 10.5l3.5 3.5 3.5-3.5M2 5h11M2 2h11" />
        </svg>
        <span>Withdraw</span>
    </a>

    <a href="{{ route('dashboard.wallet') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <rect x="1" y="3" width="13" height="9" rx="1.5" />
            <path d="M1 6h13M4 9.5h2" />
        </svg>
        <span>Wallet</span>
    </a>

    {{-- ── BOTS ─────────────────────────────────────────────────────── --}}
    <div class="sb-sect">PACKS</div>

    <a href="{{ route('dashboard.packs.browse') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="9" height="8" rx="1.5" />
            <path d="M5.5 4V3M9.5 4V3M1 8h2M12 8h2M5.5 8.5v.5M9.5 8.5v.5" />
        </svg>
        <span>Formation Packs</span>
    </a>

    <a href="{{ route('dashboard.bots.mine') }}" wire:navigate class="sb-item hidden">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 5h11v8.5H2zM5 5V3.5C5 2.1 10 2.1 10 3.5V5M7.5 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
        </svg>
        <span>My Bots</span>
    </a>

    {{-- ── NETWORK ──────────────────────────────────────────────────── --}}
    <div class="sb-sect">NETWORK</div>

    <a href="{{ route('dashboard.affiliate') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4.5 2.3a2.2 2.2 0 1 0 0 4.4 2.2 2.2 0 0 0 0-4.4zm6 0a2.2 2.2 0 1 0 0 4.4 2.2 2.2 0 0 0 0-4.4zM7.5 9a2.2 2.2 0 1 0 0 4.4A2.2 2.2 0 0 0 7.5 9z" />
        </svg>
        <span>Affiliate</span>
    </a>

    <a href="{{ route('dashboard.rank-rewards') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01z" transform="scale(0.65) translate(0.5, 0.5)" />
            <path d="M7.5 1l1.4 4.3H14l-3.7 2.7 1.4 4.3-3.7-2.7-3.7 2.7 1.4-4.3L2 5.3h5.1z" />
        </svg>
        <span>Rank & Rewards</span>
    </a>

    {{-- ── ANALYTICS ────────────────────────────────────────────────── --}}
    <div class="sb-sect">ANALYTICS</div>

    <a href="{{ route('dashboard.portfolio') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 12L5 8l2.5 2L11 5l2 2M1 14h13" />
        </svg>
        <span>Portfolio</span>
    </a>

    <a href="{{ route('dashboard.market-insights') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 8a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0zM7.5 5v3l2 1.5" />
        </svg>
        <span>Market Insights</span>
    </a>

    <a href="{{ route('dashboard.alerts') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.5 1.5c-2.5 0-4.5 2-4.5 4.5v3.5l-1 1.5h11l-1-1.5V6c0-2.5-2-4.5-4.5-4.5zM6 11.5c0 .8.7 1.5 1.5 1.5s1.5-.7 1.5-1.5" />
        </svg>
        <span>Alerts</span>
    </a>

    {{-- ── SYSTEM ───────────────────────────────────────────────────── --}}
    <div class="sb-sect">SYSTEM</div>

    <a href="{{ route('dashboard.settings') }}" wire:navigate class="sb-item">
        <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.5 1L9 4l3-.5L13 6l-2 2 1.5 3-2.5 1L8 10H7l-1.5 2-2.5-1L4.5 8 2 6l1.5-2.5L6.5 4zM7.5 9a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z" />
        </svg>
        <span>Settings</span>
    </a>

    {{-- ── Bottom ───────────────────────────────────────────────────── --}}
    <div class="sb-bottom">
        <a href="{{ route('logout') }}" class="sb-item" style="color:#F43F5E; margin-top:2px"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <svg width="15" height="15" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35">
                <path d="M6 2H3C2.4 2 2 2.4 2 3V12C2 12.6 2.4 13 3 13H6" stroke-linecap="round" />
                <path d="M10 5L13 7.5L10 10M13 7.5H6" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
        <div class="sb-av-wrap">
            <div class="sb-av">
                {{ strtoupper(substr(auth()->user()->name ?? 'JD', 0, 2)) }}
                <div class="ldot"></div>
            </div>
            <div>
                <div class="sb-name">{{ auth()->user()->name ?? 'John Doe' }}</div>
                <div class="sb-role">{{ ucfirst(auth()->user()->subscription_plan ?? 'Member') }}</div>
            </div>
        </div>
    </div>

</aside>