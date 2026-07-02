{{-- resources/views/dev/market-data-test.blade.php --}}
<!DOCTYPE html>
<html>
<head><title>Market Data Test</title></head>
<body style="background:#05050c; color:#fff; font-family: sans-serif; padding: 40px;">
    <h1>Live DexScreener Data</h1>
    @foreach ($data as $symbol => $token)
        <div style="background:#0d1120; border:1px solid #222; border-radius:12px; padding:20px; margin-bottom:16px; max-width:500px;">
            @if ($token)
                <h2>${{ $symbol }} — {{ $token['name'] }}</h2>
                <p>Price: ${{ $token['price_usd'] }}</p>
                <p>Liquidity: ${{ number_format($token['liquidity_usd']) }}</p>
                <p>24h Volume: ${{ number_format($token['volume_24h']) }}</p>
                <p>24h Txns: {{ $token['buys_24h'] }} buys / {{ $token['sells_24h'] }} sells</p>
                <p>DEX: {{ $token['dex'] }}</p>
                <p><a href="{{ $token['pair_url'] }}" target="_blank" style="color:#9B7DFF">View on DexScreener ↗</a></p>
                <p style="color:#666; font-size:.8rem">Fetched: {{ $token['fetched_at'] }}</p>
            @else
                <p>{{ $symbol }}: fetch failed</p>
            @endif
        </div>
    @endforeach
</body>
</html>