{{-- resources/views/livewire/protected/dashboard.blade.php --}}
<div>

    {{-- resources/views/livewire/protected/dashboard.blade.php --}}
   @push('styles')
        @vite('resources/css/dashboard.css')
   @endpush

    <div class="dash" wire:poll.30000ms="refresh">

        <livewire:onboarding.checklist />

        {{-- ── Pending deposit alert banner ────────────────────────────────── --}}
        @if($this->pendingDeposit)
            <a href="{{ route('dashboard.deposit.track', $this->pendingDeposit) }}" wire:navigate class="dash-alert">
                <span class="dash-alert__dot"></span>
                <span>Payment in progress —
                    <strong>{{ $this->pendingDeposit->planConfig->label }} · ${{ number_format($this->pendingDeposit->amount_usd, 2) }}</strong>
                    · {{ $this->pendingDeposit->status->label() }}</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @endif

        @include('dashboard.stats-grid')

        {{-- ── ROW 2: Chart + Formation + Actions ─────────────────────────── --}}
        <div class="dash-row-2">

            {{-- Earnings chart ──────────────────────────────────── --}}
            <div class="panel panel--chart">
                <div class="panel__head">
                    <div>
                        <div class="panel__title">Earnings</div>
                        <div class="panel__sub">Last 7 days</div>
                    </div>
                    @php
                        $totals  = array_column($this->earningsChart, 'total');
                        $chartMax = max(max($totals), 0.01);
                        $chartSum = array_sum($totals);
                    @endphp
                    <span class="badge b-g">+${{ number_format($chartSum, 2) }}</span>
                </div>

                <div class="chart-bars">
                    @foreach($this->earningsChart as $bar)
                        @php $heightPct = $chartMax > 0 ? round(($bar['total'] / $chartMax) * 100) : 4; @endphp
                        <div class="chart-bar-col">
                            @if($bar['total'] > 0)
                                <span class="chart-bar-val">${{ number_format($bar['total'], 2) }}</span>
                            @endif
                            <div class="chart-bar {{ $loop->last ? 'chart-bar--active' : '' }}" style="height: {{ max($heightPct, 4) }}%"></div>
                            <span class="chart-bar-day">{{ $bar['day'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Formation state ──────────────────────────────────── --}}
            <div class="panel panel--formation">
                @php
                    $formation = $this->formationState;
                    $stateColors = [
                        'idle'      => '#6b7280',
                        'early'     => '#06b6d4',
                        'building'  => '#f59e0b',
                        'active'    => '#22c55e',
                        'weakening' => '#ef4444',
                    ];
                    $stateColor = $stateColors[$formation?->state ?? 'idle'] ?? '#6b7280';
                    $multiplierPct = $formation ? round($formation->earnings_multiplier * 100) : 50;
                @endphp
                <div class="panel__head">
                    <div>
                        <div class="panel__title">Market Formation</div>
                        <div class="panel__sub">{{ $formation?->ecosystem ?? 'Solana' }} ecosystem</div>
                    </div>
                    <div class="formation-dot-wrap">
                        <span class="formation-dot" style="background: {{ $stateColor }}"></span>
                        <span class="formation-dot-label" style="color: {{ $stateColor }}">
                            {{ ucfirst($formation?->state ?? 'Unknown') }}
                        </span>
                    </div>
                </div>

                <div class="formation-meter">
                    <div class="formation-meter__track">
                        <div class="formation-meter__fill" style="width: {{ $multiplierPct }}%; background: {{ $stateColor }}"></div>
                    </div>
                    <div class="formation-meter__labels">
                        <span>Payout multiplier</span>
                        <strong>{{ $multiplierPct }}%</strong>
                    </div>
                </div>

                <div class="formation-stats">
                    <div class="formation-stat">
                        <span>Bot Status</span>
                        <strong>{{ ucfirst($formation?->bot_status ?? 'Standby') }}</strong>
                    </div>
                    <div class="formation-stat">
                        <span>Active Wallets</span>
                        <strong>{{ $formation?->active_wallets ? number_format($formation->active_wallets) : '—' }}</strong>
                    </div>
                    <div class="formation-stat">
                        <span>Formation Score</span>
                        <strong>{{ $formation?->formation_score ? $formation->formation_score.'/100' : '—' }}</strong>
                    </div>
                </div>
            </div>

            {{-- Quick actions ────────────────────────────────────── --}}
            <div class="panel panel--actions">
                <div class="panel__title" style="margin-bottom: 10px">Quick Actions</div>
                <div class="qa-grid">
                    <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="qa qa--primary">
                        <div class="qa__icon">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 2v16M6 10l6 8 6-8"/><path d="M2 20h20"/></svg>
                        </div>
                        <span>Deposit</span>
                    </a>
                    <a href="#" class="qa">
                        <div class="qa__icon">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 22V6M6 14l6-8 6 8"/><path d="M2 4h20"/></svg>
                        </div>
                        <span>Withdraw</span>
                    </a>
                    <a href="{{ route('dashboard.subscribe') }}" wire:navigate class="qa {{ $this->activeSubscription ? 'qa--muted' : '' }}">
                        <div class="qa__icon">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <span>{{ $this->activeSubscription ? 'Subscribed' : 'Subscribe' }}</span>
                    </a>
                    <a href="#" class="qa">
                        <div class="qa__icon">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/></svg>
                        </div>
                        <span>Referral</span>
                    </a>
                </div>

                {{-- Rank progress ──────────────── --}}
                <div class="rank-block">
                    <div class="rank-block__head">
                        <span style="color: {{ $this->rankProgress['color'] ?? '#6b7280' }}; font-weight: 700; font-size: .82rem">
                            {{ $this->rankProgress['label'] }}
                        </span>
                        @if($this->rankProgress['next'])
                            <span class="rank-block__next">→ {{ $this->rankProgress['next'] }}</span>
                        @endif
                    </div>
                    <div class="rank-bar"> 
                        <div class="rank-bar__fill" style="width: {{ $this->rankProgress['pct'] }}%; background: {{ $this->rankProgress['color'] ?? '#6b7280' }}"></div>
                    </div>
                    @if(isset($this->rankProgress['tv']))
                        <div class="rank-block__sub">
                            ${{ number_format($this->rankProgress['tv'], 0) }} / ${{ number_format($this->rankProgress['tv_req'], 0) }} team volume
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ── ROW 3: Active deposits + Activity ───────────────────────────── --}}
        <div class="dash-row-3">

            {{-- Active deposits ──────────────────────────────────── --}}
            <div class="panel">
                <div class="panel__head">
                    <div>
                        <div class="panel__title">Active Deposits</div>
                        <div class="panel__sub">Currently earning</div>
                    </div>
                    <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="panel__link">New +</a>
                </div>

                @if($this->activeDeposits->isEmpty())
                    <div class="empty-state">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2M12 12v3M10 14h4"/></svg>
                        <p>No active deposits yet.</p>
                        @unless($this->activeSubscription)
                            <a href="{{ route('dashboard.subscribe') }}" wire:navigate class="empty-state__cta">Subscribe to start →</a>
                        @else
                            <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="empty-state__cta">Deploy capital →</a>
                        @endunless
                    </div>
                @else
                    <div class="deposit-list">
                        @foreach($this->activeDeposits as $deposit)
                            <div class="deposit-row">
                                <div class="deposit-row__plan">
                                    <span class="deposit-row__name">{{ $deposit->planConfig->label }}</span>
                                    <span class="deposit-row__since">since {{ $deposit->activated_at->format('M j, Y') }}</span>
                                </div>
                                <div class="deposit-row__stats">
                                    <div class="deposit-row__principal">
                                        ${{ number_format($deposit->actually_paid_usd ?? $deposit->amount_usd, 2) }}
                                    </div>
                                    <div class="deposit-row__rate">
                                        {{ number_format($deposit->daily_rate * 100, 2) }}%/day
                                    </div>
                                </div>
                                <div class="deposit-row__earned">
                                    <span class="deposit-row__earned-val">+${{ number_format($deposit->total_earnings, 2) }}</span>
                                    <span class="deposit-row__earned-lbl">earned</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Subscription + Balances ──────────────────────────── --}}
            <div class="panel">
                <div class="panel__title" style="margin-bottom: 12px">Subscription</div>
                @if($this->activeSubscription)
                    <div class="sub-block sub-block--active">
                        <div class="sub-block__plan">{{ $this->activeSubscription->planConfig->label }} Plan</div>
                        <div class="sub-block__expires">Expires {{ $this->activeSubscription->expires_at->diffForHumans() }}</div>
                        <div class="sub-block__bar">
                            @php
                                $subTotal = $this->activeSubscription->starts_at->diffInDays($this->activeSubscription->expires_at);
                                $subLeft  = round(now()->diffInDays($this->activeSubscription->expires_at));
                                $subPct   = $subTotal > 0 ? min(100, round(($subLeft / $subTotal) * 100)) : 0;
                            @endphp
                            <div class="sub-block__bar-fill" style="width: {{ $subPct }}%"></div>
                        </div>
                        <div class="sub-block__days">{{ $subLeft }} days remaining</div>
                    </div>
                @else
                    <div class="sub-block sub-block--inactive">
                        <p>No active subscription.</p>
                        <a href="{{ route('dashboard.subscribe') }}" wire:navigate class="sub-block__cta">Subscribe now →</a>
                    </div>
                @endif

                <div class="dv"></div>

                <div class="panel__title" style="margin-bottom: 10px; font-size: .8rem">Wallet Balances</div>
                <div class="wallet-rows">
                    <div class="wallet-row">
                        <span>Main Wallet</span>
                        <strong>${{ number_format($this->mainBalance, 2) }}</strong>
                    </div>
                    <div class="wallet-row">
                        <span>Referral Wallet</span>
                        <strong>${{ number_format($this->referralBalance, 2) }}</strong>
                    </div>
                    <div class="wallet-row">
                        <span>Rank Wallet</span>
                        <strong>${{ number_format($this->rankBalance, 2) }}</strong>
                    </div>
                </div>
            </div>

            {{-- Recent Activity ──────────────────────────────────── --}}
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__title">Recent Activity</div>
                </div>

                @if($this->recentActivity->isEmpty())
                    <div class="empty-state">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        <p>No activity yet.</p>
                    </div>
                @else
                    <div class="activity-list">
                        @foreach($this->recentActivity as $tx)
                            @php
                                $isCredit = in_array($tx->type, [
                                    \App\Enums\TransactionType::DAILY_EARNING->value,
                                    \App\Enums\TransactionType::REFERRAL_BONUS->value,
                                    \App\Enums\TransactionType::RANK_BONUS->value,
                                    \App\Enums\TransactionType::DEPOSIT->value,
                                ]);
                                $icons = [
                                    \App\Enums\TransactionType::DAILY_EARNING->value   => 'earning',
                                    \App\Enums\TransactionType::REFERRAL_BONUS->value  => 'referral',
                                    \App\Enums\TransactionType::RANK_BONUS->value      => 'rank',
                                    \App\Enums\TransactionType::DEPOSIT->value         => 'deposit',
                                    \App\Enums\TransactionType::WITHDRAWAL->value      => 'withdraw',
                                ];
                                $icon = $icons[$tx->type] ?? 'earning';
                            @endphp
                            <div class="activity-row">
                                <div class="activity-row__icon activity-row__icon--{{ $icon }}">
                                    @if($icon === 'earning')
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M12 2v8m0 12V14M8 6l4-4 4 4M8 18l4 4 4-4"/></svg>
                                    @elseif($icon === 'referral')
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="3"/><path d="M3 20v-1a5 5 0 0 1 10 0v1M16 11a3 3 0 0 1 0 6M21 20v-.5a4.5 4.5 0 0 0-4.5-4.5"/></svg>
                                    @elseif($icon === 'rank')
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    @elseif($icon === 'deposit')
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M12 2v16M6 10l6 8 6-8"/></svg>
                                    @else
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M12 22V6M6 14l6-8 6 8"/></svg>
                                    @endif
                                </div>
                                <div class="activity-row__body">
                                    <span class="activity-row__desc">{{ $tx->description ?? \App\Enums\TransactionType::from($tx->type)->label() }}</span>
                                    <span class="activity-row__time">{{ $tx->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="activity-row__amount {{ $isCredit ? 'activity-row__amount--credit' : 'activity-row__amount--debit' }}">
                                    {{ $isCredit ? '+' : '-' }}${{ number_format(abs($tx->amount), 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>