{{-- resources/views/livewire/protected/portfolio.blade.php --}}

<div>
    @push('styles')
        @vite('resources/css/portfolio.css')
    @endpush

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
        @php
            $chart = $this->cumulativeChart;
            $maxCum = max(array_column($chart, 'cumulative')) ?: 1;
            $currentTotal = end($chart)['cumulative'] ?? 0;

            $points = collect($chart)->values();
            $n = max($points->count() - 1, 1);

            $coords = $points->map(function ($p, $i) use ($n, $maxCum) {
                $x = round(($i / $n) * 600, 1);
                $y = round(160 - (($p['cumulative'] / $maxCum) * 150) - 5, 1);
                return ['x' => $x, 'y' => $y, 'label' => $p['label'], 'date' => $p['date'], 'cumulative' => $p['cumulative']];
            });

            $path = $coords->map(fn ($c) => "{$c['x']},{$c['y']}")->implode(' ');
            $fillPath = "0,160 $path 600,160";

            // Distinct label indices only — prevents duplicate labels on short ranges.
            $labelIndices = collect([0, intdiv($points->count(), 2), $points->count() - 1])->unique()->values();
        @endphp

        <div class="pf-chart-value">${{ number_format($currentTotal, 2) }} <span>total earned in range</span></div>

        <div class="pf-chart">
            <span class="pf-chart__axis pf-chart__axis--top">${{ number_format($maxCum, 2) }}</span>
            <span class="pf-chart__axis pf-chart__axis--bottom">$0</span>

            <svg viewBox="0 0 600 160" preserveAspectRatio="none" class="pf-chart__svg">
                <polyline points="{{ $fillPath }}" fill="rgba(155,125,255,.08)" stroke="none" />
                <polyline points="{{ $path }}" fill="none" stroke="#9B7DFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />

                @foreach($coords as $c)
                    <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="3" fill="#9B7DFF" class="pf-chart__dot">
                        <title>{{ \Carbon\Carbon::parse($c['date'])->format('M j, Y') }} — ${{ number_format($c['cumulative'], 2) }}</title>
                    </circle>
                @endforeach
            </svg>
        </div>

        <div class="pf-chart-labels">
            @foreach($labelIndices as $i)
                <span style="left: {{ $points->count() > 1 ? round(($i / ($points->count() - 1)) * 100, 1) : 0 }}%">{{ $chart[$i]['label'] }}</span>
            @endforeach
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
                <div class="pf-deposit-list">
                    @foreach($this->packBreakdown as $sub)
                        <div class="pf-deposit-row" wire:click="selectSubscription({{ $sub['id'] }})">
                            <div class="pf-deposit-row__main">
                                <div class="pf-deposit-row__head">
                                    <span class="pf-deposit-row__plan">{{ $sub['tier'] }}</span>
                                    <span class="pf-status-badge pf-status-badge--{{ $sub['status'] }}">
                                        {{ $sub['status_label'] }}
                                    </span>
                                </div>
                                <span class="pf-deposit-row__meta">
                                    {{ number_format($sub['daily_rate'] * 100, 2) }}%/day · {{ round($sub['days_active']) }} days active · {{ $sub['slots_funded'] }}/{{ $sub['slots_total'] }} slots funded
                                </span>
                            </div>
                            <div class="pf-deposit-row__stats">
                                <div class="pf-deposit-row__col">
                                    <span class="pf-deposit-row__col-label">Principal</span>
                                    <span class="pf-deposit-row__col-val">${{ number_format($sub['principal'], 2) }}</span>
                                </div>
                                <div class="pf-deposit-row__col">
                                    <span class="pf-deposit-row__col-label">Earned</span>
                                    <span class="pf-deposit-row__col-val pf-deposit-row__col-val--green">+${{ number_format($sub['earned'], 2) }}</span>
                                </div>
                                <div class="pf-deposit-row__col">
                                    <span class="pf-deposit-row__col-label">ROI</span>
                                    <span class="pf-deposit-row__col-val pf-deposit-row__col-val--green">+{{ $sub['roi_pct'] }}%</span>
                                </div>
                            </div>
                            <svg class="pf-deposit-row__chevron {{ $selectedSubscriptionId === $sub['id'] ? 'pf-deposit-row__chevron--open' : '' }}" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>

                        @if($selectedSubscriptionId === $sub['id'])
                            <div class="pf-deposit-detail">

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
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>