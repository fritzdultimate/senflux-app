@php $readonly = $readonly ?? false; @endphp
<div class="formation-card">
    <div class="formation-card__head">
        <div>
            <div class="formation-card__symbol">${{ $formation->token_symbol }}</div>
            <div class="formation-card__name">{{ $formation->token_name }}</div>
        </div>
        <span class="formation-card__state" style="background: {{ $formation->state->color() }}22; color: {{ $formation->state->color() }}">
            {{ strtoupper($formation->state->label()) }}
        </span>
    </div>

    {{-- Formation Score + Strength --}}
    <div class="fc-score-block">
        <div class="fc-score-block__main">
            <span class="fc-score-block__value">{{ $formation->score }}<small>/100</small></span>
            <div>
                <p class="fc-score-block__label">Formation Score</p>
                <p class="fc-score-block__reason">{{ $formation->scoreReason() }}</p>
            </div>
        </div>
        <div class="fc-score-block__main" style="margin-top: 10px">
            <span class="fc-score-block__strength" style="color: {{ $formation->state->color() }}">{{ $formation->confidence }}</span>
            <div>
                <p class="fc-score-block__label">Formation Strength</p>
                <p class="fc-score-block__reason">{{ $formation->confidenceReason() }}</p>
            </div>
        </div>
        <p class="fc-score-block__time">{{ $formation->detectedAgo() }}</p>
    </div>

    {{-- Renamed metrics, each with its own "why" --}}
    @php
        $metrics = [
            'capital_concentration' => 'New Capital Inflow',
            'liquidity_migration'   => 'Liquidity Flow',
            'participation_growth'  => 'Participation Growth',
            'wallet_quality'        => 'Wallet Quality',
        ];
    @endphp

    <div class="formation-card__metrics">
        @foreach ($metrics as $key => $label)
            <div class="metric-row">
                <div class="metric-row__labels">
                    <span>{{ $label }}</span>
                    <span>{{ $formation->{$key} }}%</span>
                </div>
                <div class="metric-row__track">
                    <div class="metric-row__fill" style="width: {{ $formation->{$key} }}%"></div>
                </div>
                <p class="metric-row__why">{{ $formation->metricReason($key) }}</p>
            </div>
        @endforeach
    </div>

    @if ($formation->isVerifiable())
        <div class="onchain-block">
            <div class="onchain-block__head">
                <span class="onchain-block__label">ON-CHAIN DATA</span>
                <span class="onchain-block__verified">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                    Verified
                </span>
            </div>

            <div class="onchain-block__price-row">
                <div><span>Price</span><strong>${{ number_format($formation->price_usd, $formation->price_usd < 0.01 ? 8 : 4) }}</strong></div>
                <div><span>Liquidity</span><strong>${{ number_format($formation->liquidity_usd) }}</strong></div>
                <div><span>FDV</span><strong>{{ $formation->fdv ? '$' . number_format($formation->fdv) : '—' }}</strong></div>
                <div><span>Mkt Cap</span><strong>{{ $formation->market_cap ? '$' . number_format($formation->market_cap) : '—' }}</strong></div>
            </div>

            <div class="onchain-block__timeframes">
                @foreach ([['5M', $formation->price_change_5m], ['1H', $formation->price_change_1h], ['6H', $formation->price_change_6h], ['24H', $formation->price_change_24h]] as [$label, $change])
                    <div class="tf-pill {{ $change === null ? 'tf-pill--idle' : ($change >= 0 ? 'tf-pill--up' : 'tf-pill--down') }}">
                        <span>{{ $label }}</span>
                        <strong>{{ $change === null ? '—' : ($change >= 0 ? '+' : '') . number_format($change, 1) . '%' }}</strong>
                    </div>
                @endforeach
            </div>

            <div class="onchain-block__stats">
                <div><span>24h Volume</span><strong>${{ number_format($formation->volume_24h) }}</strong></div>
                <div><span>24h Txns</span><strong>{{ number_format(($formation->buys_24h ?? 0) + ($formation->sells_24h ?? 0)) }}</strong></div>
                <div><span>Buys / Sells</span><strong class="split"><span style="color:#10B981">{{ $formation->buys_24h ?? 0 }}</span> / <span style="color:#EF4444">{{ $formation->sells_24h ?? 0 }}</span></strong></div>

                <!-- remove later -->
                <!-- <div class="hidden">
                    <span>Buyers / Sellers</span>
                    <strong class="split">
                        @if ($formation->unique_buyers_24h !== null)
                            <span style="color:#10B981">{{ $formation->unique_buyers_24h }}</span> / <span style="color:#EF4444">{{ $formation->unique_sellers_24h }}</span>
                        @else
                            <span style="opacity:.5">Pending Birdeye</span>
                        @endif
                    </strong>
                </div> -->

                <div>
                    <span>Unique Wallets (24h)</span>
                    <strong>
                        @if ($formation->unique_wallets_24h !== null)
                            {{ number_format($formation->unique_wallets_24h) }}
                            @if ($formation->unique_wallets_24h_change_pct !== null)
                                <small style="color: {{ $formation->unique_wallets_24h_change_pct >= 0 ? '#10B981' : '#EF4444' }}">
                                    ({{ $formation->unique_wallets_24h_change_pct >= 0 ? '+' : '' }}{{ number_format($formation->unique_wallets_24h_change_pct, 0) }}%)
                                </small>
                            @endif
                        @else
                            <span style="opacity:.5">Pending Birdeye</span>
                        @endif
                    </strong>
                </div>
                <div>
                    <span>Buy / Sell Volume (24h)</span>
                    <strong class="split">
                        @if ($formation->volume_buy_24h_usd !== null)
                            <span style="color:#10B981">${{ number_format($formation->volume_buy_24h_usd) }}</span> / <span style="color:#EF4444">${{ number_format($formation->volume_sell_24h_usd) }}</span>
                        @else
                            <span style="opacity:.5">Pending Birdeye</span>
                        @endif
                    </strong>
                </div>
            </div>

            @if ($formation->market_data_synced_at)
                <p class="onchain-block__synced">Synced {{ $formation->marketDataFreshness() }}</p>
            @endif
        </div>

        <p class="onchain-disclaimer">
            Price, liquidity, and volume above are pulled live from {{ ucfirst($formation->dex) }} and can be independently
            verified on-chain. Formation Score, Formation Strength, and the reasoning shown throughout this card are
            Senflux's own analysis, generated from that market data — they are not on-chain figures themselves.
        </p>
    @endif

    @unless ($readonly)
        <button wire:click="openFormation({{ $formation->id }})" class="formation-card__cta">View Formation →</button>
    @endunless

    <div class="formation-card__watermark">
        <span class="formation-card__watermark-dot"></span>
        Senflux Intelligence Engine
    </div>
</div>