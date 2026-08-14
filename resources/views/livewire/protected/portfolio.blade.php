{{-- resources/views/livewire/protected/portfolio.blade.php --}}

<div>

    <div class="pf">

        {{-- ── Stat strip ───────────────────────────────────────────────────── --}}
        <div class="pf-stats">
            <div class="pf-stat pf-stat--main">
                <p class="pf-stat__label">Portfolio Value</p>
                <p class="pf-stat__value">${{ number_format($this->portfolioValue, 2) }}</p>
                <p class="pf-stat__sub">Deployed capital + earnings</p>
            </div>
            <div class="pf-stat">
                <p class="pf-stat__label">Total Earned</p>
                <p class="pf-stat__value pf-stat__value--green">+${{ number_format($this->totalEarned, 2) }}</p>
            </div>
            <div class="pf-stat">
                <p class="pf-stat__label">ROI</p>
                <p class="pf-stat__value pf-stat__value--green">+{{ number_format($this->roiPercent, 2) }}%</p>
            </div>
            <div class="pf-stat">
                <p class="pf-stat__label">Capital Deployed</p>
                <p class="pf-stat__value">${{ number_format($this->totalPrincipal, 2) }}</p>
            </div>
            <div class="pf-stat">
                <p class="pf-stat__label">Active Slots</p>
                <p class="pf-stat__value">{{ $this->activeSlotsCount }}</p>
            </div>
        </div>

        {{-- ── Earnings chart ───────────────────────────────────────────────── --}}
        <div class="pf-panel">
            <div class="pf-panel__head">
                <div>
                    <p class="pf-panel__title">Earnings Over Time</p>
                    <p class="pf-panel__sub">Cumulative growth</p>
                </div>
                <div class="pf-range-toggle">
                    @foreach(['7' => '7D', '30' => '30D', '90' => '90D', 'all' => 'All'] as $val => $lbl)
                        <button
                            wire:click="setRange('{{ $val }}')"
                            type="button"
                            class="pf-range-btn {{ $range === $val ? 'pf-range-btn--active' : '' }}"
                        >{{ $lbl }}</button>
                    @endforeach
                </div>
            </div>

            @php
                $chart = $this->cumulativeChart;
                $maxCum = max(array_column($chart, 'cumulative')) ?: 1;
                $currentTotal = end($chart)['cumulative'] ?? 0;

                $points = collect($chart)->values();
                $n = max($points->count() - 1, 1);

                $coords = $points->map(function ($p, $i) use ($n, $maxCum) {
                    $x = round(($i / $n) * 600, 1);
                    $y = round(150 - (($p['cumulative'] / $maxCum) * 140), 1);
                    return ['x' => $x, 'y' => $y, 'label' => $p['label'], 'date' => $p['date'], 'cumulative' => $p['cumulative']];
                });

                $path = $coords->map(fn ($c) => "{$c['x']},{$c['y']}")->implode(' ');
                $fillPath = "0,150 $path 600,150";

                $labelIndices = collect([0, intdiv($points->count(), 2), $points->count() - 1])->unique()->values();
            @endphp

            <div class="pf-chart-value">${{ number_format($currentTotal, 2) }} <span>total earned in range</span></div>

            <div class="pf-chart-wrap">
                <div class="pf-chart-yaxis">
                    <span>${{ number_format($maxCum, 2) }}</span>
                    <span>${{ number_format($maxCum / 2, 2) }}</span>
                    <span>$0.00</span>
                </div>

                <div class="pf-chart">
                    <svg viewBox="0 0 600 150" preserveAspectRatio="none" class="pf-chart__svg">
                        <line x1="0" y1="0" x2="600" y2="0" class="pf-chart__gridline" />
                        <line x1="0" y1="75" x2="600" y2="75" class="pf-chart__gridline" />
                        <line x1="0" y1="150" x2="600" y2="150" class="pf-chart__gridline" />

                        <polyline points="{{ $fillPath }}" fill="rgba(155,125,255,.08)" stroke="none" />
                        <polyline points="{{ $path }}" fill="none" stroke="#9B7DFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                        @foreach($coords as $c)
                            <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="3" fill="#9B7DFF" class="pf-chart__dot">
                                <title>{{ \Carbon\Carbon::parse($c['date'])->format('M j, Y') }} — ${{ number_format($c['cumulative'], 2) }}</title>
                            </circle>
                        @endforeach
                    </svg>

                    <div class="pf-chart-labels">
                        @foreach($labelIndices as $i)
                            <span style="left: {{ $points->count() > 1 ? round(($i / ($points->count() - 1)) * 100, 1) : 0 }}%">{{ $chart[$i]['label'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Pack breakdown ───────────────────────────────────────────────── --}}
        <div class="pf-panel">
            <p class="pf-panel__title" style="margin-bottom: .9rem">Pack Breakdown</p>

            @if(empty($this->packBreakdown))
                <div class="pf-empty">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2M12 12v3M10 14h4"/></svg>
                    <p>No packs yet.</p>
                    <a href="{{ route('dashboard.packs.browse') }}" wire:navigate class="pf-empty__cta">Browse packs →</a>
                </div>
            @else
                <div class="pf-pack-list">
                    @foreach($this->packBreakdown as $sub)
                        <div class="pf-pack-card">
                            <button type="button" class="pf-pack-card__summary" wire:click="selectSubscription({{ $sub['id'] }})">
                                <div class="pf-pack-card__identity">
                                    <span class="pf-pack-card__tier">{{ $sub['tier'] }}</span>
                                    <span class="pf-status-badge pf-status-badge--{{ $sub['status'] }}">{{ $sub['status_label'] }}</span>
                                </div>
                                <p class="pf-pack-card__meta">
                                    {{ number_format($sub['daily_rate'] * 100, 2) }}%/day
                                    <span class="pf-pack-card__dot">·</span>
                                    {{ round($sub['days_active']) }} days active
                                    <span class="pf-pack-card__dot">·</span>
                                    {{ $sub['slots_funded'] }}/{{ $sub['slots_total'] }} slots funded
                                </p>

                                <div class="pf-pack-card__figures">
                                    <div class="pf-figure">
                                        <span class="pf-figure__label">Principal</span>
                                        <span class="pf-figure__value">${{ number_format($sub['principal'], 2) }}</span>
                                    </div>
                                    <div class="pf-figure">
                                        <span class="pf-figure__label">Earned</span>
                                        <span class="pf-figure__value pf-figure__value--green">+${{ number_format($sub['earned'], 2) }}</span>
                                    </div>
                                    <div class="pf-figure">
                                        <span class="pf-figure__label">ROI</span>
                                        <span class="pf-figure__value pf-figure__value--green">+{{ $sub['roi_pct'] }}%</span>
                                    </div>
                                </div>

                                <svg class="pf-pack-card__chevron {{ $selectedSubscriptionId === $sub['id'] ? 'pf-pack-card__chevron--open' : '' }}" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>

                            @if($selectedSubscriptionId === $sub['id'])
                                <div class="pf-pack-card__detail">

                                    {{-- Slots grid --}}
                                    <div class="pf-slots-grid">
                                        @foreach($sub['slots'] as $slot)
                                            <div class="pf-slot-chip pf-slot-chip--{{ $slot['status'] }}">
                                                <div class="pf-slot-chip__head">
                                                    <span class="pf-slot-chip__num">Slot {{ $slot['slot_number'] }}</span>
                                                    <span class="pf-slot-chip__status">{{ $slot['status_label'] }}</span>
                                                </div>
                                                @if($slot['status'] !== 'empty')
                                                    <div class="pf-slot-chip__body">
                                                        <span class="pf-slot-chip__amount">${{ number_format($slot['capital_amount'], 2) }}</span>
                                                        @if($slot['realized_profit'] != 0)
                                                            <span class="pf-slot-chip__profit">+${{ number_format($slot['realized_profit'], 2) }}</span>
                                                        @endif
                                                        @if($slot['formation_symbol'])
                                                            <span class="pf-slot-chip__formation">{{ $slot['formation_symbol'] }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="pf-slot-chip__body pf-slot-chip__body--empty">Not yet deployed</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Earnings table --}}
                                    @if($this->selectedSubscriptionEarnings->isEmpty())
                                        <p class="pf-deposit-detail__empty">No earnings recorded yet for this pack.</p>
                                    @else
                                        <div class="pf-table-scroll">
                                            <table class="pf-detail-table">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Rate</th>
                                                        <th>Formation</th>
                                                        <th class="pf-table__right">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($this->selectedSubscriptionEarnings as $e)
                                                        <tr>
                                                            <td>{{ $e->earned_date->format('M j, Y') }}</td>
                                                            <td class="pf-table__muted">{{ number_format($e->base_rate_applied * 100, 2) }}%</td>
                                                            <td class="pf-table__muted">{{ $e->formation?->token_symbol ?? '—' }}</td>
                                                            <td class="pf-table__right pf-table__green">+${{ number_format($e->amount, 4) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>