<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Senflux — Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { syne: ['Syne', 'sans-serif'], dm: ['DM Sans', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] } } } }</script>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    @stack('styles')
</head>

<body class="gbg" style="background:#05050c;color:#c8c8e0;font-family:'DM Sans',sans-serif">


    {{-- ═══ PAGE CONTENT ═══ --}}
    <main>
        {{ $slot }}
    </main>


    {{-- Livewire scripts --}}
    @livewireScripts

    {{-- Per-page scripts --}}
    @stack('scripts')
</body>

</html>