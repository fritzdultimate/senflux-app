{{-- resources/views/components/formation-card.blade.php --}}
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

    <div class="formation-card__meta">
        <span class="formation-card__score">{{ $formation->score }}<small>/100</small></span>
        <span class="formation-card__confidence">Confidence: {{ $formation->confidence }}</span>
        <span class="formation-card__time">{{ $formation->detectedAgo() }}</span>
    </div>

    @php
        $metrics = [
            'Capital Concentration' => $formation->capital_concentration,
            'Liquidity Migration' => $formation->liquidity_migration,
            'Participation Growth' => $formation->participation_growth,
            'Wallet Quality' => $formation->wallet_quality,
        ];
    @endphp

    <div class="formation-card__metrics">
        @foreach ($metrics as $label => $value)
            <div class="metric-row">
                <div class="metric-row__labels">
                    <span>{{ $label }}</span>
                    <span>{{ $value }}%</span>
                </div>
                <div class="metric-row__track">
                    <div class="metric-row__fill" style="width: {{ $value }}%"></div>
                </div>
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
            <div class="onchain-block__stats">
                <div>
                    <span>Price</span>
                    <strong>${{ number_format($formation->price_usd, $formation->price_usd < 0.01 ? 8 : 4) }}</strong>
                </div>
                <div>
                    <span>Liquidity</span>
                    <strong>${{ number_format($formation->liquidity_usd) }}</strong>
                </div>
                <div>
                    <span>24h Volume</span>
                    <strong>${{ number_format($formation->volume_24h) }}</strong>
                </div>
            </div>
            <a href="{{ $formation->pair_url }}" target="_blank" rel="noopener" class="onchain-block__link">
                Verify on {{ ucfirst($formation->dex) }} ↗
            </a>
            @if ($formation->market_data_synced_at)
                <p class="onchain-block__synced">Synced {{ $formation->marketDataFreshness() }}</p>
            @endif
        </div>
    @endif

    @unless ($readonly)
        <button wire:click="openFormation({{ $formation->id }})" class="formation-card__cta">View Formation →</button>
    @endunless

    <div class="formation-card__watermark">
        <span class="formation-card__watermark-dot"></span>
        Senflux Intelligence Engine
    </div>
</div>