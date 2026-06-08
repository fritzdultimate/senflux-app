{{-- resources/views/layouts/protected/mobile-nav.blade.php --}}
{{-- Visible on mobile only — hidden via CSS on desktop (min-width: 1024px) --}}
<nav class="mob-nav">

    <a href="{{ route('dashboard') }}" class="mob-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 1h5.5v5.5H1zm8.5 0H15v5.5H9.5zM1 9.5h5.5V15H1zm8.5 0H15V15H9.5z" />
        </svg>
        Home
    </a>

    <a href="#" class="mob-nav-item {{ request()->routeIs('markets*') ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 11L5 7.5L7.5 9.5L10.5 5.5L13 7.5" />
        </svg>
        Markets
    </a>

    <a href="#" class="mob-nav-item mob-nav-more {{ request()->routeIs('trades*') ? 'active' : '' }}">
        <div class="mob-nav-badge"></div>
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 11.5L5 8L7.5 10L11 5L13.5 7M11.5 5h2v2" />
        </svg>
        Trades
    </a>

    <a href="#" class="mob-nav-item {{ request()->routeIs('signals*') ? 'active' : '' }}">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round" stroke-linejoin="round">
            <path d="M2 8.5h11M7.5 4L11 8.5L7.5 13M2 4v9" />
        </svg>
        Signals
    </a>

    <a href="#" class="mob-nav-item" onclick="openSidebar(); return false;">
        <svg width="18" height="18" fill="none" viewBox="0 0 15 15" stroke="currentColor" stroke-width="1.35" stroke-linecap="round">
            <path d="M2 4h11M2 7.5h11M2 11h11" />
        </svg>
        More
    </a>

</nav>