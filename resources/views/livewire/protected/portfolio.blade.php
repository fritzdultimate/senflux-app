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
            @endphp

            <div class="pf-chart">
                <svg viewBox="0 0 600 160" preserveAspectRatio="none" class="pf-chart__svg">
                    @php
                        $points = collect($chart)->values();
                        $n = max($points->count() - 1, 1);
                        $path = $points->map(function ($p, $i) use ($n, $maxCum) {
                            $x = round(($i / $n) * 600, 1);
                            $y = round(160 - (($p['cumulative'] / $maxCum) * 150) - 5, 1);
                            return "$x,$y";
                        })->implode(' ');
                        $fillPath = "0,160 $path 600,160";
                    @endphp
                    <polyline points="{{ $fillPath }}" fill="rgba(155,125,255,.08)" stroke="none" />
                    <polyline points="{{ $path }}" fill="none" stroke="#9B7DFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <div class="pf-chart-labels">
                @foreach($chart as $i => $point)
                    @if($i === 0 || $i === count($chart) - 1 || $i === intdiv(count($chart), 2))
                        <span style="left: {{ count($chart) > 1 ? round(($i / (count($chart) - 1)) * 100, 1) : 0 }}%">{{ $point['label'] }}</span>
                    @endif
                @endforeach
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
                                    {{ number_format($sub['daily_rate'] * 100, 2) }}%/day · {{ $sub['days_active'] }} days active · {{ $sub['slots_funded'] }}/{{ $sub['slots_total'] }} slots funded
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
                                <div class="pf-slots-grid" style="display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:.9rem;">
                                    @foreach($sub['slots'] as $slot)
                                        <div class="pf-slot-chip pf-slot-chip--{{ $slot['status'] }}" style="padding:.4rem .7rem; border-radius:8px; font-size:.75rem;">
                                            <strong>Slot {{ $slot['slot_number'] }}</strong> · {{ $slot['status_label'] }}
                                            @if($slot['formation_symbol'])
                                                · {{ $slot['formation_symbol'] }}
                                            @endif
                                            @if($slot['status'] !== 'empty')
                                                <br>${{ number_format($slot['capital_amount'], 2) }}
                                                @if($slot['realized_profit'] != 0)
                                                    (+${{ number_format($slot['realized_profit'], 2) }})
                                                @endif
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