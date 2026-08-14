<div wire:poll.8000="refresh">
    <div class="lt-page">
        <div class="fd-topbar">
            <a href="{{ route('dashboard.terminal') }}" wire:navigate class="fd-back">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                Terminal
            </a>
            <span class="fd-topbar__sep">/</span>
            <span class="fd-topbar__current">Bot Activity{{ $this->formation ? ' · ' . $this->formation->token_symbol : '' }}</span>
            <span class="fd-topbar__spacer"></span>
        </div>

        {{-- HERO --}}
        <div class="ba-hero">

            <div class="panel__head" style="margin-bottom: 20px">
                <div>
                    <div class="panel__title" style="font-size: 1.1rem">Bot Activity</div>
                    <div class="panel__sub">
                        Your Senflux system is continuously monitoring, validating, and deploying across qualifying formations.
                    </div>
                </div>
            </div>

            <div class="ba-status">
                <span class="ba-status__dot"></span>
                <div class="ba-status__text">
                    <strong>Bot Active</strong>
                    <span>Last activity: {{ $this->botStatus['last_activity']?->diffForHumans() ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- OVERVIEW --}}
        @php $overview = $this->overview; @endphp
        <div class="ba-overview">
            <div class="ba-overview__head">
                <span class="ba-overview__badge"><span class="ba-status__dot ba-status__dot--sm"></span> Monitoring</span>
                <span class="ba-overview__count">{{ number_format($overview['active_formations']) }} Active Formations</span>
            </div>
            <div class="ba-overview__grid">
                <div class="ba-metric">
                    <span>Today's Activity</span>
                    <strong>{{ number_format($overview['actions_today']) }}</strong>
                </div>
                <div class="ba-metric ba-metric--green">
                    <span>Successful</span>
                    <strong>{{ number_format($overview['successful_today']) }}</strong>
                </div>
                <div class="ba-metric {{ $overview['failed_today'] > 0 ? 'ba-metric--red' : '' }}">
                    <span>Failed</span>
                    <strong>{{ number_format($overview['failed_today']) }}</strong>
                </div>
                <div class="ba-metric">
                    <span>Capital Deployed</span>
                    <strong>${{ number_format($overview['capital_deployed'], 2) }}</strong>
                </div>
                <div class="ba-metric">
                    <span>Active Deployments</span>
                    <strong>{{ number_format($overview['active_deployments']) }}</strong>
                </div>
            </div>
        </div>

        {{-- PERFORMANCE --}}
        @php $perf = $this->performance; $intel = $this->intelligence; @endphp
        <div class="ba-card ba-card--perf">
            <p class="ba-card__title">Deployment Performance</p>
            <div class="ba-card__grid">
                <div><span>Active Capital</span><strong>${{ number_format($perf['active_capital'], 2) }}</strong></div>
                <div><span>Realized Profit</span><strong class="ba-pos">${{ number_format($perf['realized_profit'], 2) }}</strong></div>
                <div><span>Unrealized P/L</span><strong>${{ number_format($perf['unrealized_pl'], 2) }}</strong></div>
                <div><span>24H Performance</span><strong class="{{ $perf['change_24h_pct'] > 0 ? 'ba-pos' : ($perf['change_24h_pct'] < 0 ? 'ba-neg' : '') }}">{{ $perf['change_24h_pct'] >= 0 ? '+' : '' }}{{ number_format($perf['change_24h_pct'], 2) }}%</strong></div>
                <div><span>Total Actions</span><strong>{{ number_format($perf['total_actions']) }}</strong></div>
            </div>
        </div>

        {{--
            Single Alpine root spans the tab buttons AND both panels below,
            so `tab` is shared client-side state — no Livewire round trip
            on click, switching is instant.
        --}}
        <div x-data="{ tab: @js($tab) }">

        {{-- TABS — pure client-side switch --}}
        <div class="ba-tabs">
            <button @click="tab = 'activity'" :class="{ 'ba-tab--active': tab === 'activity' }" class="ba-tab">Bot Activity</button>
            <button @click="tab = 'history'" :class="{ 'ba-tab--active': tab === 'history' }" class="ba-tab">Trade History</button>
            @if ($this->formation)
                <span class="ba-tab-filter">
                    ${{ $this->formation->token_symbol }}
                    <button wire:click="filterByFormation(null)">✕</button>
                </span>
            @endif
        </div>

        {{--
            Both panels render on every request and are toggled purely with
            CSS (x-show). That's what makes switching instant. Trade-off
            worth knowing: since the History table's query isn't behind a
            Blade @if anymore, it now runs on every 8s poll even while
            hidden, not just while the tab is open. At 30-row pagination
            that's cheap, but if it ever gets heavy, the fix is to lazy-load
            history behind a one-time $wire.call() the first time the tab
            is opened rather than reverting to server-side tab switching.
        --}}
        <div x-show="tab === 'activity'" x-cloak>

            @php
                $feed = $this->activityFeed;
                $pulseFormations = $feed->unique(fn ($i) => $i['trade']->formation_id)->take(8);
                $ring = $intel['ring'];
            @endphp

            <div class="ba-activity-grid">

                {{-- SIGNAL TIMELINE --}}
                <div class="ba-signal-col">

                    <div class="ba-disclaimer">
                        <strong>Your Senflux Activity</strong>
                        <p>Senflux continuously monitors qualifying formations and manages deployed capital automatically when conditions change.</p>
                    </div>

                    <div class="ba-signal">
                        @forelse ($feed as $index => $item)
                            @php $trade = $item['trade']; @endphp
                            <div
                                class="ba-signal__node {{ $index === 0 ? 'ba-signal__node--latest' : '' }}"
                                wire:key="feed-{{ $trade->id }}"
                                style="animation-delay: {{ min($index, 8) * 45 }}ms"
                            >
                                <div class="ba-signal__marker">
                                    <span class="ba-signal__icon ba-signal__icon--{{ $trade->type }}">
                                        @if ($trade->type === 'buy')
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                                        @else
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                                        @endif
                                    </span>
                                    @if ($index === 0)
                                        <span class="ba-signal__ping"></span>
                                    @endif
                                </div>

                                <div class="ba-signal__card">
                                    <div class="ba-signal__top">
                                        <div class="ba-signal__heading">
                                            <span class="ba-signal__title">{{ $item['title'] }}</span>
                                            @if ($index === 0)
                                                <span class="ba-signal__new">Latest</span>
                                            @endif
                                        </div>
                                        <span class="ba-signal__time">{{ $trade->block_time?->format('g:i A') ?? '—' }}</span>
                                    </div>

                                    <div class="ba-signal__meta">
                                        <a href="{{ route('dashboard.formations.detail', $trade->formation) }}" wire:navigate class="ba-signal__formation">
                                            ${{ $trade->formation->token_symbol }}
                                        </a>
                                        @isset($trade->formation->state)
                                            <span class="ba-signal__state ba-signal__state--{{ $trade->formation->state->value }}">
                                                {{ ucfirst($trade->formation->state->value) }}
                                            </span>
                                        @endisset
                                        <span class="ba-signal__amount">{{ number_format($trade->token_amount, 2) }} tokens</span>
                                    </div>

                                    <p class="ba-signal__reason">{{ $item['reason'] }}</p>

                                    <div class="ba-signal__footer">
                                        <div class="ba-signal__impact" title="Relative size vs. recent actions">
                                            <span class="ba-signal__impact-fill ba-signal__impact-fill--{{ $trade->type }}" style="width: {{ $item['impact_pct'] }}%"></span>
                                        </div>
                                        <span class="ba-signal__status">✓ Executed</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="ba-signal__empty">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                                <p>No bot activity yet</p>
                                <span>Senflux will log actions here the moment a formation qualifies for deployment.</span>
                            </div>
                        @endforelse
                    </div>

                </div>

                {{-- STICKY RAIL --}}
                <div class="ba-rail">

                    @if ($pulseFormations->isNotEmpty())
                        <div class="ba-rail__panel">
                            <p class="ba-rail__title">Live Pulse</p>
                            <div class="ba-pulse">
                                @foreach ($pulseFormations as $p)
                                    @php $f = $p['trade']->formation; @endphp
                                    <a href="{{ route('dashboard.formations.detail', $f) }}" wire:navigate class="ba-pulse__chip">
                                        <span class="ba-pulse__dot ba-pulse__dot--{{ $f->state?->value ?? 'idle' }}"></span>
                                        ${{ $f->token_symbol }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="ba-rail__panel">
                        <p class="ba-rail__title">Deployment Radar</p>
                        <p class="ba-rail__sub">{{ number_format($intel['total']) }} formations monitored</p>

                        <div class="ba-radar">
                            <div
                                class="ba-radar__ring"
                                style="background: conic-gradient(
                                    #22c55e 0deg {{ $ring['strengthening_deg'] }}deg,
                                    #9B7DFF {{ $ring['strengthening_deg'] }}deg {{ $ring['building_deg'] }}deg,
                                    #06b6d4 {{ $ring['building_deg'] }}deg {{ $ring['stable_deg'] }}deg,
                                    #ef4444 {{ $ring['stable_deg'] }}deg 360deg
                                )"
                            >
                                <div class="ba-radar__center">
                                    <strong>{{ number_format($intel['total']) }}</strong>
                                    <span>total</span>
                                </div>
                            </div>

                            <div class="ba-radar__legend">
                                <div class="ba-radar__row">
                                    <span class="ba-dot ba-dot--up"></span>
                                    <span class="ba-radar__label">Strengthening</span>
                                    <strong>{{ number_format($intel['strengthening']) }}</strong>
                                </div>
                                <div class="ba-radar__row">
                                    <span class="ba-dot ba-dot--purple"></span>
                                    <span class="ba-radar__label">Building</span>
                                    <strong>{{ number_format($intel['building']) }}</strong>
                                </div>
                                <div class="ba-radar__row">
                                    <span class="ba-dot ba-dot--cyan"></span>
                                    <span class="ba-radar__label">Stable</span>
                                    <strong>{{ number_format($intel['stable']) }}</strong>
                                </div>
                                <div class="ba-radar__row">
                                    <span class="ba-dot ba-dot--red"></span>
                                    <span class="ba-radar__label">Weakening</span>
                                    <strong>{{ number_format($intel['weakening']) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="ba-rail__panel ba-scan"
                        x-data="{ pct: 0 }"
                        x-init="setInterval(() => { pct = (pct + 100/80) % 100 }, 100)"
                    >
                        <p class="ba-rail__title">Next Scan</p>
                        <p class="ba-rail__sub">Formations are re-evaluated continuously</p>
                        <div class="ba-scan__track">
                            <div class="ba-scan__fill" :style="`width: ${pct}%`"></div>
                        </div>
                    </div>

                </div>

            </div>

            </div>

            <div x-show="tab === 'history'" x-cloak>

            <div class="lt-filters">
                <button wire:click="filterBySource(null)" class="lt-filter-pill {{ !$source ? 'lt-filter-pill--active' : '' }}">All Sources</button>

                <span style="width:1px;background:rgba(255,255,255,.08);align-self:stretch;"></span>

                <button wire:click="filterByType(null)" class="lt-filter-pill {{ !$type ? 'lt-filter-pill--active' : '' }}">All Types</button>
                <button wire:click="filterByType('buy')" class="lt-filter-pill {{ $type === 'buy' ? 'lt-filter-pill--active' : '' }}">Buys</button>
                <button wire:click="filterByType('sell')" class="lt-filter-pill {{ $type === 'sell' ? 'lt-filter-pill--active' : '' }}">Sells</button>

                <button wire:click="toggleFailed" class="lt-filter-pill {{ $includeFailed ? 'lt-filter-pill--active' : '' }}">
                    {{ $includeFailed ? 'Hide Failed' : 'Show Failed' }}
                </button>
            </div>

            <div class="lt-disclaimer">
                "Senflux" trades are actions taken by the platform on behalf of deployed capital.
            </div>

            <div class="lt-table-wrap">
                <table class="ftbl lt-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Formation</th>
                            <th>Type</th>
                            <th>Token Amount</th>
                            <th class="hidden">Trader Wallet</th>
                            <th>Source</th>
                            <th>Status</th>
                            <th>Verify</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->trades as $trade)
                            <tr wire:key="trade-{{ $trade->id }}" class="lt-row {{ $trade->failed ? 'lt-row--failed' : '' }}">
                                <td class="mono">{{ $trade->block_time?->format('g:i:s A') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('dashboard.formations.detail', $trade->formation) }}" wire:navigate class="lt-formation-link">
                                        ${{ $trade->formation->token_symbol }}
                                    </a>
                                </td>
                                <td>
                                    <span class="lt-side lt-side--{{ $trade->type }}">{{ strtoupper($trade->type) }}</span>
                                </td>
                                <td class="mono">{{ number_format($trade->token_amount, 4) }}</td>
                                <td class="mono hidden">{{ Str::limit($trade->trader_wallet, 12) }}</td>
                                <td>
                                    <span class="lt-source lt-source--{{ $trade->source->value }}">
                                        {{ $trade->source === \App\Enums\TradeActivitySource::MARKET_POOL ? 'Market Pool' : 'Senflux' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($trade->failed)
                                        <span class="lt-status lt-status--failed">Failed</span>
                                    @else
                                        <span class="lt-status lt-status--success">Success</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $trade->explorerUrl() }}" target="_blank" rel="noopener" class="trade-row__verify">
                                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17L17 7"/><path d="M8 7h9v9"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-8" style="color:#4a4a6a">No trade activity matches these filters yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($this->trades->hasPages())
                <div class="ff-pagination">
                    {{ $this->trades->links('vendor.pagination.senflux') }}
                </div>
            @endif

            </div>

        </div>
    </div>
</div>