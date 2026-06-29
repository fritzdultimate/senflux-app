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
                <p class="pf-stat__sub">Principal + earnings</p>
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
                <p class="pf-stat__label">Active Deposits</p>
                <p class="pf-stat__value">{{ $this->activeCount }}</p>
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

        {{-- ── Deposit breakdown ────────────────────────────────────────────── --}}
        <div class="pf-panel">
            <p class="pf-panel__title" style="margin-bottom: .9rem">Deposit Breakdown</p>

            @if(empty($this->depositBreakdown))
                <div class="pf-empty">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2M12 12v3M10 14h4"/></svg>
                    <p>No deposits yet.</p>
                    <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="pf-empty__cta">Deploy capital →</a>
                </div>
            @else
                <div class="pf-deposit-list">
                    @foreach($this->depositBreakdown as $d)
                        <div class="pf-deposit-row" wire:click="selectDeposit({{ $d['id'] }})">
                            <div class="pf-deposit-row__main">
                                <div class="pf-deposit-row__head">
                                    <span class="pf-deposit-row__plan">{{ $d['plan'] }}</span>
                                    <span class="pf-status-badge pf-status-badge--{{ $d['status'] }}">
                                        {{ ucfirst($d['status']) }}
                                    </span>
                                </div>
                                <span class="pf-deposit-row__meta">
                                    {{ number_format($d['daily_rate'] * 100, 2) }}%/day · {{ $d['days_active'] }} days active
                                </span>
                            </div>
                            <div class="pf-deposit-row__stats">
                                <div class="pf-deposit-row__col">
                                    <span class="pf-deposit-row__col-label">Principal</span>
                                    <span class="pf-deposit-row__col-val">${{ number_format($d['principal'], 2) }}</span>
                                </div>
                                <div class="pf-deposit-row__col">
                                    <span class="pf-deposit-row__col-label">Earned</span>
                                    <span class="pf-deposit-row__col-val pf-deposit-row__col-val--green">+${{ number_format($d['earned'], 2) }}</span>
                                </div>
                                <div class="pf-deposit-row__col">
                                    <span class="pf-deposit-row__col-label">ROI</span>
                                    <span class="pf-deposit-row__col-val pf-deposit-row__col-val--green">+{{ $d['roi_pct'] }}%</span>
                                </div>
                            </div>
                            <svg class="pf-deposit-row__chevron {{ $selectedDepositId === $d['id'] ? 'pf-deposit-row__chevron--open' : '' }}" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>

                        @if($selectedDepositId === $d['id'])
                            <div class="pf-deposit-detail">
                                @if($this->selectedDepositEarnings->isEmpty())
                                    <p class="pf-deposit-detail__empty">No earnings recorded yet for this deposit.</p>
                                @else
                                    <table class="pf-detail-table">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Rate</th>
                                                <th class="pf-table__right">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($this->selectedDepositEarnings as $e)
                                                <tr>
                                                    <td>{{ $e->earned_date->format('M j, Y') }}</td>
                                                    <td class="pf-table__muted">{{ number_format($e->rate_applied * 100, 2) }}%</td>
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
