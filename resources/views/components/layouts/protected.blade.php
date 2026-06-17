{{-- resources/views/components/layouts/protected.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
    <title>{{ $title ?? 'Senflux' }}</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    @vite(['resources/css/dashboard-shared.css'])
    @vite(['resources/js/nav.js'])
    @stack('styles')
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