{{-- resources/views/livewire/protected/market-insights.blade.php --}}
@vite('resources/css/market-insights.css')

<div class="mi">

    {{-- ── Asset picker ─────────────────────────────────────────────────── --}}
    @if($this->assets->isEmpty())
        <div class="mi-empty">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M1 8a6.5 6.5 0 1 0 13 0 6.5 6.5 0 0 0-13 0zM7.5 5v3l2 1.5"/></svg>
            <p>No tracked assets configured yet.</p>
        </div>
    @else
        <div class="mi-asset-tabs">
            @foreach($this->assets as $asset)
                <button
                    wire:click="selectAsset({{ $asset->id }})"
                    type="button"
                    class="mi-asset-tab {{ $this->selectedAsset?->id === $asset->id ? 'mi-asset-tab--active' : '' }}"
                >
                    {{ $asset->symbol }}
                </button>
            @endforeach
        </div>

        @if($this->selectedAsset)
            {{-- ── Header ───────────────────────────────────────────────── --}}
            <div class="mi-header">
                <div>
                    <h2 class="mi-header__symbol">{{ $this->selectedAsset->symbol }}</h2>
                    <p class="mi-header__name">{{ $this->selectedAsset->name }} · {{ $this->selectedAsset->network }}</p>
                </div>
                <div class="mi-header__price-wrap">
                    <p class="mi-header__price">
                        @if($this->selectedAsset->current_price)
                            ${{ number_format($this->selectedAsset->current_price, 6) }}
                        @else
                            <span class="mi-header__no-price">No price data</span>
                        @endif
                    </p>
                    @if($this->priceChangeInRange !== null)
                        <span class="{{ $this->priceChangeInRange >= 0 ? 'mi-change--pos' : 'mi-change--neg' }}">
                            {{ $this->priceChangeInRange >= 0 ? '+' : '' }}{{ $this->priceChangeInRange }}% ({{ $range }}d)
                        </span>
                    @endif
                </div>
            </div>

            {{-- ── Chart panel ──────────────────────────────────────────── --}}
            <div class="mi-panel">
                <div class="mi-panel__head">
                    <p class="mi-panel__title">Price History</p>
                    <div class="mi-range-toggle">
                        @foreach(['1' => '24H', '7' => '7D', '30' => '30D'] as $val => $lbl)
                            <button
                                wire:click="setRange('{{ $val }}')"
                                type="button"
                                class="mi-range-btn {{ $range === $val ? 'mi-range-btn--active' : '' }}"
                            >{{ $lbl }}</button>
                        @endforeach
                    </div>
                </div>

                @if(empty($this->chartPoints))
                    <div class="mi-chart-empty">
                        <p>No price history recorded yet for this range.</p>
                        <p class="mi-chart-empty__sub">History builds up as the price sync job runs.</p>
                    </div>
                @else
                    @php
                        $points = collect($this->chartPoints);
                        $prices = $points->pluck('price');
                        $min = $prices->min();
                        $max = $prices->max();
                        $span = max($max - $min, 0.0000001);
                        $n = max($points->count() - 1, 1);
                        $path = $points->values()->map(function ($p, $i) use ($n, $min, $span) {
                            $x = round(($i / $n) * 600, 1);
                            $y = round(150 - ((($p['price'] - $min) / $span) * 140) - 5, 1);
                            return "$x,$y";
                        })->implode(' ');
                    @endphp
                    <div class="mi-chart">
                        <svg viewBox="0 0 600 150" preserveAspectRatio="none" class="mi-chart__svg">
                            <polyline points="0,150 {{ $path }} 600,150" fill="rgba(155,125,255,.08)" stroke="none" />
                            <polyline points="{{ $path }}" fill="none" stroke="#9B7DFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="mi-chart-range">
                        <span>${{ number_format($min, 6) }}</span>
                        <span>${{ number_format($max, 6) }}</span>
                    </div>
                @endif
            </div>

            {{-- ── Formation trend ──────────────────────────────────────── --}}
            <div class="mi-panel">
                <p class="mi-panel__title" style="margin-bottom: .9rem">Formation State Trend</p>
                @if($this->formationTrend->isEmpty())
                    <div class="mi-chart-empty">
                        <p>No formation history yet.</p>
                    </div>
                @else
                    <div class="mi-formation-strip">
                        @foreach($this->formationTrend as $f)
                            @php
                                $stateColors = ['idle' => '#6b7280', 'early' => '#06b6d4', 'building' => '#f59e0b', 'active' => '#22c55e', 'weakening' => '#ef4444'];
                                $fColor = $stateColors[$f->state] ?? '#6b7280';
                            @endphp
                            <div class="mi-formation-bar" style="background: {{ $fColor }}; height: {{ round($f->earnings_multiplier * 100) }}%" title="{{ ucfirst($f->state) }} — {{ round($f->earnings_multiplier * 100) }}%"></div>
                        @endforeach
                    </div>
                    <div class="mi-formation-labels">
                        <span>{{ \Carbon\Carbon::parse($this->formationTrend->first()->created_at)->format('M j') }}</span>
                        <span>{{ \Carbon\Carbon::parse($this->formationTrend->last()->created_at)->format('M j') }}</span>
                    </div>
                @endif
            </div>
        @endif
    @endif

</div>
