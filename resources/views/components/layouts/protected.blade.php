{{-- resources/views/components/layouts/protected.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>{{ $title ?? 'Senflux' }}</title>
    @stack('styles')
    @vite([
        'resources/css/dashboard.css',
        'resources/css/dashboard-shared.css',
        'resources/css/app.css',
        'resources/css/live-trades.css',
        'resources/css/formation-detail.css',
        'resources/css/formation-card.css',
        'resources/css/terminal.css',
        'resources/css/bot-activity.css',

        'resources/css/market-insights.css',

        'resources/css/affiliate.css',

        'resources/css/alerts.css',

        'resources/css/portfolio.css',

        'resources/css/rank-rewards.css',

        'resources/css/settings.css',

        'resources/css/signals.css',

        'resources/css/wallet.css',

        'resources/css/withdraw.css',

        'resources/css/trading-bots.css',

        'resources/css/my-bots.css',


        'resources/js/nav.js',
    ])

    @vite('resources/css/dc.css')
    @vite('resources/css/billing.css')
    @vite('resources/css/deposit.css')

    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        [wire\:id] {
            animation: fade-in .15s ease-out;
        }

        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>

<body class="gbg">

    {{-- Sidebar backdrop (mobile tap-to-close) --}}
    <div class="sb-backdrop" id="sbBackdrop" onclick="closeSidebar()"></div>

    <main class="shell">
        {{-- Sidebar (desktop) / drawer (mobile) --}}
        @include('layouts.protected.aside')

        <div class="main">
            {{-- Top bar --}}
            @include('layouts.protected.header')

            {{-- Page content --}}
            <div class="content">
                {{ $slot }}
            </div>
        </div>
    </main>

    {{-- Bottom nav (mobile only, hidden on desktop via CSS) --}}
    @include('layouts.protected.mobile-nav')

    @livewireScripts
    @stack('scripts')

</body>
</html>