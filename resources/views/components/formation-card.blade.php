@php
    $readonly = $readonly ?? false;
    $color = $formation->state->color();

    $radius = 54;
    $circumference = 2 * M_PI * $radius;
    $scoreOffset = $circumference * (1 - min(100, max(0, $formation->score)) / 100);

    // Strength meter: maps the confidence label to a lit-segment count out of 5.
    // Assumption: labels are Low / Moderate / High — adjust the match if your
    // actual confidence tiers differ.
    $confidenceLc = strtolower($formation->confidence ?? '');
    $litSegments = match (true) {
        str_contains($confidenceLc, 'high') => 5,
        str_contains($confidenceLc, 'moderate') => 3,
        str_contains($confidenceLc, 'low') => 2,
        default => 3,
    };

    $buys = $formation->buys_24h ?? 0;
    $sells = $formation->sells_24h ?? 0;
    $txTotal = $buys + $sells;
    $buyPct = $txTotal > 0 ? round(($buys / $txTotal) * 100) : 50;
@endphp

<div class="fc" style="--fc-accent: {{ $color }}">

    {{-- Head --}}
    <div class="fc-head">
        <div>
            <div class="fc-symbol">
                ${{ $formation->token_symbol }}
                <x-ui.info-tip text="Formation official symbol." />
            </div>
            <div class="fc-name">{{ $formation->token_name }}</div>
        </div>
        <span class="fc-state" style="background:{{ $color }}1a; color:{{ $color }}; border-color:{{ $color }}40">
            <span class="fc-state__dot" style="background:{{ $color }}"></span>
            {{ strtoupper($formation->state->label()) }}
        </span>
    </div>

    {{-- Instrument panel: the signature moment --}}
    <div class="fc-instrument">
        <div class="fc-gauge">
            <svg viewBox="0 0 130 130" width="112" height="112">
                <circle cx="65" cy="65" r="{{ $radius }}" fill="none" stroke="rgba(255,255,255,.06)" stroke-width="9"/>
                <circle cx="65" cy="65" r="{{ $radius }}" fill="none" stroke="{{ $color }}" stroke-width="9"
                        stroke-linecap="round" stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $scoreOffset }}"
                        transform="rotate(-90 65 65)" class="fc-gauge__arc"/>
            </svg>
            <div class="fc-gauge__readout">
                <span class="fc-gauge__value">{{ $formation->score }}</span>
                <span class="fc-gauge__max">/ 100</span>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:4px; justify-content:center; margin-top:4px;">
            <span style="font-size:10px; color:rgba(255,255,255,.4); text-transform:uppercase; letter-spacing:.04em;">
                Formation Score
            </span>
            <x-ui.info-tip 
                text="A 0-100 read on how healthy this formation looks right now, weighted from liquidity depth, trading volume relative to that liquidity, buy/sell pressure, and price momentum. It's Senflux's own heuristic, not an on-chain metric." 
            />
        </div>

        <div class="fc-instrument__divider"></div>

        <div class="fc-meter">
            <div class="fc-meter__bars">
                @for ($i = 1; $i <= 5; $i++)
                    <span class="fc-meter__bar {{ $i <= $litSegments ? 'is-lit' : '' }}"
                          style="height:{{ 8 + $i * 4 }}px; {{ $i <= $litSegments ? '--fc-bar-color:' . $color : '' }}"></span>
                @endfor
            </div>
            <div class="fc-meter__label">{{ $formation->confidence }}</div>
            <div class="fc-meter__sub">
                Formation Strength
                <x-ui.info-tip text="How reliable this formation's signals look overall — Low, Moderate, or High. This isn't the score itself, it's how much confidence Senflux has IN that score." />
            </div>
        </div>
    </div>

    <div class="fc-note">
        <p><strong>Score —</strong> {{ $formation->scoreReason() }}</p>
        <p><strong>Strength —</strong> {{ $formation->confidenceReason() }}</p>
    </div>
    <p class="fc-detected">{{ $formation->detectedAgo() }}</p>

    {{-- Metric rows --}}
    @php
        $metrics = [
            'capital_concentration' => 'New Capital Inflow',
            'liquidity_migration'   => 'Liquidity Flow',
            'participation_growth'  => 'Participation Growth',
            'wallet_quality'        => 'Wallet Quality',
        ];

        $metricTips = [
            'capital_concentration' => 'How much NEW capital is entering this formation relative to what was already there — a rising number means fresh money is coming in, not just existing holders moving positions around.',
            'liquidity_migration'   => 'Whether liquidity in this pool has been growing or draining over time — steady growth is a stronger signal than a single good day.',
            'participation_growth'  => 'How many distinct wallets are engaging with this token, not just how much is being traded — broad participation is harder to fake than volume alone.',
            'wallet_quality'        => 'A quality read on the wallets participating — down-weighted for patterns that look like bots or wash trading, so the score is harder to game.',
        ];
    @endphp

    <div class="fc-metrics">
        @foreach ($metrics as $key => $label)
            <div class="fc-metric">
                <div class="fc-metric__top">
                    <span class="fc-metric__label" style="display:inline-flex; align-items:center; gap:5px;">
                        {{ $label }}
                        <x-ui.info-tip :text="$metricTips[$key]" position="right" />
                    </span>
                    <span class="fc-metric__value">{{ $formation->{$key} }}<small>%</small></span>
                </div>
                <div class="fc-metric__track">
                    <div class="fc-metric__fill" style="width:{{ $formation->{$key} }}%"></div>
                </div>
                <p class="fc-metric__why">{{ $formation->metricReason($key) }}</p>
            </div>
        @endforeach
    </div>

    {{-- On-chain --}}
    @if ($formation->isVerifiable())
        <div class="fc-onchain">
            <div class="fc-onchain__head">
                <span class="fc-onchain__label">ON-CHAIN DATA</span>
                <span class="fc-onchain__verified">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                    Verified
                </span>
            </div>

            <div class="fc-stat-strip">
                <div><span>Price</span><strong>${{ number_format($formation->price_usd, $formation->price_usd < 0.01 ? 8 : 4) }}</strong></div>
                <div><span>Liquidity</span><strong>${{ number_format($formation->liquidity_usd) }}</strong></div>
                <div><span>FDV</span><strong>{{ $formation->fdv ? '$' . number_format($formation->fdv) : '—' }}</strong></div>
                <div><span>Mkt Cap</span><strong>{{ $formation->market_cap ? '$' . number_format($formation->market_cap) : '—' }}</strong></div>
            </div>

            <div class="fc-timeframes">
                @foreach ([['5M', $formation->price_change_5m], ['1H', $formation->price_change_1h], ['6H', $formation->price_change_6h], ['24H', $formation->price_change_24h]] as [$tfLabel, $change])
                    <div class="tf-pill {{ $change === null ? 'tf-pill--idle' : ($change >= 0 ? 'tf-pill--up' : 'tf-pill--down') }}">
                        <span>{{ $tfLabel }}</span>
                        <strong>{{ $change === null ? '—' : ($change >= 0 ? '+' : '') . number_format($change, 1) . '%' }}</strong>
                    </div>
                @endforeach
            </div>

            {{-- Buy/sell proportion — instant visual read instead of two raw counts --}}
            <div class="fc-flow">
                <div class="fc-flow__labels">
                    <span style="color:#10B981">{{ $buys }} buys</span>
                    <span>24h transactions ({{ number_format($txTotal) }})</span>
                    <span style="color:#EF4444">{{ $sells }} sells</span>
                </div>
                <div class="fc-flow__bar">
                    <div class="fc-flow__buy" style="width:{{ $buyPct }}%"></div>
                </div>
            </div>

            <div class="fc-onchain__stats">
                <div>
                    <span>Unique Wallets (24h)</span>
                    <strong>
                        @if ($formation->unique_wallets_24h !== null)
                            {{ number_format($formation->unique_wallets_24h) }}
                            @if ($formation->unique_wallets_24h_change_pct !== null)
                                <small style="color:{{ $formation->unique_wallets_24h_change_pct >= 0 ? '#10B981' : '#EF4444' }}">
                                    ({{ $formation->unique_wallets_24h_change_pct >= 0 ? '+' : '' }}{{ number_format($formation->unique_wallets_24h_change_pct, 0) }}%)
                                </small>
                            @endif
                        @else
                            <span class="fc-pending">Pending Birdeye</span>
                        @endif
                    </strong>
                </div>
                <div>
                    <span style="display:inline-flex; align-items:center; gap:4px;">
                        Buy / Sell Volume (24h)
                        <x-ui.info-tip text="Buy and sell count within 24 hours." position="right" />
                    </span>
                    <strong class="split">
                        @if ($formation->volume_buy_24h_usd !== null)
                            <span style="color:#10B981">${{ number_format($formation->volume_buy_24h_usd) }}</span> / <span style="color:#EF4444">${{ number_format($formation->volume_sell_24h_usd) }}</span>
                        @else
                            <span class="fc-pending">Pending Birdeye</span>
                        @endif
                    </strong>
                </div>
                <div><span>24h Volume</span><strong>${{ number_format($formation->volume_24h) }}</strong></div>
            </div>

            @if ($formation->market_data_synced_at)
                <p class="fc-synced">Synced {{ $formation->marketDataFreshness() }}</p>
            @endif
        </div>

        <p class="fc-disclaimer">
            Price, liquidity, and volume above are pulled live from {{ ucfirst($formation->dex) }} and can be independently
            verified on-chain. Formation Score, Formation Strength, and the reasoning shown throughout this card are
            Senflux's own analysis, generated from that market data — they are not on-chain figures themselves.
        </p>
    @endif

    @unless ($readonly)
        <button wire:click="openFormation({{ $formation->id }})" class="fc-cta">
            View Formation
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
    @endunless

    <div class="fc-watermark">
        <span class="fc-watermark__dot"></span>
        Senflux Intelligence Engine
    </div>
</div>