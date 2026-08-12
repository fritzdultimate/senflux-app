<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? 'Senflux' }} – {{ config('app.name', 'Senflux') }}</title>
    <meta name="description" content="{{ $description ?? 'Markets Expand After Participation Concentrates.' }}" />

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Vite: Tailwind + our CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Per-page extra styles --}}
    <style>
        /* page extras */
        .orb-ring{position:absolute;inset:0;border-radius:50%;border:1px solid rgba(123,92,245,.2)}
        .orb-ring2{position:absolute;inset:18px;border-radius:50%;border:1px solid rgba(123,92,245,.1)}
        .pipeline-step{display:flex;gap:14px;padding:14px 16px;border-radius:10px;cursor:pointer;transition:background .2s;border:1px solid transparent}
        .pipeline-step.active,.pipeline-step:hover{background:rgba(123,92,245,.08);border-color:rgba(123,92,245,.16)}
        .insight-row{display:flex;gap:12px;align-items:flex-start;padding:14px;border-radius:12px;cursor:pointer;transition:all .2s;text-decoration:none;background:rgba(8,8,18,.9);border:1px solid rgba(255,255,255,.07)}
        .insight-row:hover{border-color:rgba(123,92,245,.3);background:rgba(123,92,245,.04)}
        .hx{border-radius:10px;padding:11px 9px;text-align:center;cursor:pointer}
        .hx-vs{background:rgba(16,185,129,.2);border:1px solid rgba(16,185,129,.32)}
        .hx-s{background:rgba(123,92,245,.2);border:1px solid rgba(123,92,245,.32)}
        .hx-m{background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.26)}
        .hx-e{background:rgba(6,182,212,.15);border:1px solid rgba(6,182,212,.26)}
        .hx-w{background:rgba(244,63,94,.14);border:1px solid rgba(244,63,94,.22)}
        .hx-i{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07)}
        .fs{border-radius:8px;padding:10px 8px;text-align:center}
        .fs-i{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07)}
        .fs-e{background:rgba(6,182,212,.08);border:1px solid rgba(6,182,212,.2)}
        .fs-b{background:rgba(123,92,245,.1);border:1px solid rgba(123,92,245,.24)}
        .fs-a{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.24)}
        .fs-w{background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2)}
    </style>

    <style>
        html {
            scroll-behavior: smooth;
        }

        .card {
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.07);
            background: rgba(17,17,32,.58);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .card-brand {
            border-color: rgba(123,92,245,.16);
            box-shadow:
                0 20px 70px rgba(0,0,0,.18),
                inset 0 1px 0 rgba(255,255,255,.025);
        }

        .pill {
            padding: 6px 11px;
            border-radius: 999px;
            background: rgba(123,92,245,.08);
            border: 1px solid rgba(123,92,245,.18);
            color: #9B7DFF;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .tg {
            background: linear-gradient(
                100deg,
                #9B7DFF 0%,
                #7B5CF5 45%,
                #B49CFF 100%
            );
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .btn-p,
        .btn-o {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 17px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 600;
            transition:
                transform .2s ease,
                box-shadow .2s ease,
                border-color .2s ease,
                background .2s ease;
        }

        .btn-p {
            color: white;
            background: linear-gradient(
                135deg,
                #7B5CF5,
                #5946d8
            );
            border: 1px solid rgba(155,125,255,.4);
            box-shadow: 0 10px 30px rgba(123,92,245,.18);
        }

        .btn-p:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 36px rgba(123,92,245,.28);
        }

        .btn-o {
            color: #aaaac0;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.025);
        }

        .btn-o:hover {
            color: white;
            border-color: rgba(123,92,245,.3);
            background: rgba(123,92,245,.05);
        }

    </style>
    @stack('styles')

    {{-- Livewire styles --}}
    @livewireStyles
</head>
<body class="gbg" style="background:#05050c;color:#c8c8e0;font-family:'DM Sans',sans-serif">

    {{-- ═══ NAV ═══ --}}
    <x-senflux.nav :current="$currentPage ?? ''" />

    {{-- ═══ PAGE CONTENT ═══ --}}
    <main>
        @yield('content')
    </main>

    {{-- ═══ FOOTER ═══ --}}
    <x-senflux.footer />

    {{-- Livewire scripts --}}
    @livewireScripts

    {{-- Per-page scripts --}}
    @stack('scripts')

    {{-- Scroll reveal init --}}
    <script>
    (function () {
        const obs = new IntersectionObserver(
            entries => entries.forEach(el => { if (el.isIntersecting) el.target.classList.add('in'); }),
            { threshold: 0.08 }
        );
        document.querySelectorAll('.rv').forEach(el => obs.observe(el));

        // Responsive step-connectors
        function slineVis() {
            document.querySelectorAll('.sline').forEach(l => {
                l.style.display = window.innerWidth < 640 ? 'none' : 'block';
            });
        }
        slineVis();
        window.addEventListener('resize', slineVis);
    })();
    </script>
</body>
</html>
