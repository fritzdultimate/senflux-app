{{-- resources/views/livewire/protected/markets.blade.php --}}
<div>
    @push('styles')
        @vite('resources/css/markets.css')
    @endpush

    <div class="mkt" wire:poll.30000ms="refresh">

        {{-- ── Formation state hero ────────────────────────────────────────── --}}
        @php
            $f = $this->formation;
            $stateColors = [
                'idle'      => '#6b7280',
                'early'     => '#06b6d4',
                'building'  => '#f59e0b',
                'active'    => '#22c55e',
                'weakening' => '#ef4444',
            ];
            $stateDescriptions = [
                'idle'      => 'Minimal Meaningful Participation',
                'early'     => 'Initial Participation Beginning To Emerge',
                'building'  => 'Participation Density Increasing Consistently',
                'active'    => 'Sustained Formation Confirmed',
                'weakening' => 'Participation Beginning To Fade',
            ];
            $state = $f?->state->value ?? 'idle';
            $color = $stateColors[$state] ?? '#6b7280';
            $multiplierPct = $f ? round($f->state->earningsMultiplier() * 100) : 50;
        @endphp

        <div class="mkt-hero" style="border-color: {{ $color }}33; background: linear-gradient(155deg, {{ $color }}14, transparent)">
            <div class="mkt-hero__top">
                <div>
                    <p class="mkt-hero__label">{{ ucfirst($f?->ecosystem ?? 'Solana') }} Ecosystem</p>
                    <div class="mkt-hero__state-wrap">
                        <span class="mkt-hero__dot" style="background: {{ $color }}"></span>
                        <h2 class="mkt-hero__state" style="color: {{ $color }}">{{ ucfirst($state) }}</h2>
                    </div>
                    <p class="mkt-hero__desc">{{ $stateDescriptions[$state] ?? '' }}</p>
                </div>
                <div class="mkt-hero__multiplier">
                    <p class="mkt-hero__label">Payout Multiplier</p>
                    <p class="mkt-hero__multiplier-val" style="color: {{ $color }}">{{ $multiplierPct }}%</p>
                </div>
            </div>

            <div class="mkt-meter">
                <div class="mkt-meter__track">
                    <div class="mkt-meter__fill" style="width: {{ $multiplierPct }}%; background: {{ $color }}"></div>
                </div>
            </div>
        </div>

        {{-- ── Formation metrics ───────────────────────────────────────────── --}}
        <div class="mkt-stats">
            <div class="mkt-stat">
                <p class="mkt-stat__label">Bot Status</p>
                <p class="mkt-stat__value">{{ $f?->bot_status ? ucfirst($f->bot_status) : '—' }}</p>
            </div>
            <div class="mkt-stat">
                <p class="mkt-stat__label">Active Wallets</p>
                <p class="mkt-stat__value">{{ $f?->active_wallets ? number_format($f->active_wallets) : '—' }}</p>
            </div>
            <div class="mkt-stat">
                <p class="mkt-stat__label">Formation Score</p>
                <p class="mkt-stat__value">{{ $f?->formation_score ? $f->formation_score.'/100' : '—' }}</p>
            </div>
            <div class="mkt-stat">
                <p class="mkt-stat__label">Liquidity Score</p>
                <p class="mkt-stat__value">{{ $f?->liquidity_score ? $f->liquidity_score.'/100' : '—' }}</p>
            </div>
        </div>

        @if($f?->notes)
            <div class="mkt-notes">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
                <p>{{ $f->notes }}</p>
            </div>
        @endif

        <div class="mkt-layout">

            {{-- ── Platform stats ──────────────────────────────────────────── --}}
            <div class="mkt-panel">
                <p class="mkt-panel__title">Platform Overview</p>
                <div class="mkt-platform-stats">
                    <div class="mkt-platform-stat">
                        <span>Total Capital Deployed</span>
                        <strong>${{ number_format($this->platformStats['total_deposited'], 0) }}</strong>
                    </div>
                    <div class="mkt-platform-stat">
                        <span>Active Deposits</span>
                        <strong>{{ number_format($this->platformStats['active_deposits']) }}</strong>
                    </div>
                    <div class="mkt-platform-stat">
                        <span>Total Members</span>
                        <strong>{{ number_format($this->platformStats['total_users']) }}</strong>
                    </div>
                    <div class="mkt-platform-stat">
                        <span>Total Paid Out</span>
                        <strong>${{ number_format($this->platformStats['total_paid_out'], 0) }}</strong>
                    </div>
                </div>
            </div>

            {{-- ── Plan rates ──────────────────────────────────────────────── --}}
            <div class="mkt-panel">
                <p class="mkt-panel__title">Plan Rates</p>
                <div class="mkt-plans">
                    @foreach($this->planConfigs as $plan)
                        <div class="mkt-plan-row">
                            <span class="mkt-plan-row__name">{{ $plan->label }}</span>
                            <span class="mkt-plan-row__rate">
                                up to {{ number_format($plan->daily_rate_max * 100, 2) }}%/day
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- ── Formation history ───────────────────────────────────────────── --}}
        <div class="mkt-panel">
            <p class="mkt-panel__title" style="margin-bottom: .9rem">Formation History</p>

            @if($this->formationHistory->isEmpty())
                <div class="mkt-empty">
                    <p>No formation history recorded yet.</p>
                </div>
            @else
                <div class="mkt-history-list">
                    @foreach($this->formationHistory as $h)
                        @php $hColor = $stateColors[$h->state->value] ?? '#6b7280'; @endphp
                        <div class="mkt-history-row">
                            <span class="mkt-history-dot" style="background: {{ $hColor }}"></span>
                            <span class="mkt-history-state" style="color: {{ $hColor }}">{{ ucfirst($h->state) }}</span>
                            <span class="mkt-history-meta">{{ round($h->earnings_multiplier * 100) }}% multiplier</span>
                            <span class="mkt-history-time">{{ \Carbon\Carbon::parse($h->created_at)->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>
