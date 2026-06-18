{{-- resources/views/layouts/protected/mobile-nav.blade.php --}}
{{-- Visible on mobile only — hidden via CSS on desktop (min-width: 1024px) --}}
<nav class="mob-nav">

    <a href="{{ route('dashboard') }}" wire:navigate
       class="mob-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 1h5.5v5.5H1zm8.5 0H15v5.5H9.5zM1 9.5h5.5V15H1zm8.5 0H15V15H9.5z" />
        </svg>
        Home
    </a>

    <a href="{{ route('dashboard.deposit.create') }}" wire:navigate
       class="mob-nav-item {{ request()->routeIs('dashboard.deposit.*') ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.5 1v9M4 7l3.5 3.5L11 7M2 13.5h11" />
        </svg>
        Deposit
    </a>

    <a href="#"
       class="mob-nav-item {{ request()->routeIs('dashboard.withdraw*') ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M7.5 14V5M4 7.5l3.5-3.5L11 7.5M2 1.5h11" />
        </svg>
        Withdraw
    </a>

    <a href="#"
       class="mob-nav-item {{ request()->routeIs('dashboard.affiliate*', 'dashboard.rank*') ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4.5 2.3a2.2 2.2 0 1 0 0 4.4 2.2 2.2 0 0 0 0-4.4zm6 0a2.2 2.2 0 1 0 0 4.4 2.2 2.2 0 0 0 0-4.4zM7.5 9a2.2 2.2 0 1 0 0 4.4A2.2 2.2 0 0 0 7.5 9z" />
        </svg>
        Network
    </a>

    <a href="#" class="mob-nav-item" onclick="openSidebar(); return false;">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round">
            <path d="M2 4h11M2 7.5h11M2 11h11" />
        </svg>
        More
    </a>

</nav>