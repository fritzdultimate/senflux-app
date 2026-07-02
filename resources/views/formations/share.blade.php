{{-- resources/views/formations/share.blade.php --}}
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>{{ $formation->token_symbol }} — {{ $formation->state->label() }} Formation | Senflux</title>

        <meta property="og:title" content="{{ $formation->token_symbol }} — {{ $formation->state->label() }} Formation">
        <meta property="og:description" content="Formation Score {{ $formation->score }}/100 · Confidence {{ $formation->confidence }} · Detected {{ $formation->detectedAgo() }}">
        <meta property="og:image" content="{{ route('formations.share.og', $formation) }}">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">

        @vite('resources/css/app.css')
        @vite('resources/css/terminal.css')
        @vite('resources/css/dashboard.css')
    </head>
    <body class="bg-[#05050c] text-white min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-lg">
            <x-formation-card :formation="$formation" :readonly="true" />

            <p class="text-center text-sm text-gray-500 mt-6">
                Tracked live by Senflux Intelligence Engine ·
                <a href="{{ route('register') }}" class="text-[#9B7DFF]">senflux.com</a>
            </p>
        </div>
    </body>
</html>