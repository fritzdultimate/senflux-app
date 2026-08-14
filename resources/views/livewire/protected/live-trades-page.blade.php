<div wire:poll.8000="refresh">
    @push('styles')
        @vite('resources/css/live-trades.css')
        @vite('resources/css/formation-detail.css')
        @vite('resources/css/terminal.css')
        @vite('resources/css/bot-activity.css')
    @endpush

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
            <div class="ba-hero__text">
                <h1 class="ba-hero__title">Bot Activity</h1>
                <p class="ba-hero__sub">Your Senflux system is continuously monitoring, validating, and deploying across qualifying formations.</p>
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

        {{-- PERFORMANCE + INTELLIGENCE --}}
        @php $perf = $this->performance; $intel = $this->intelligence; @endphp
        <div class="ba-cards">
            <div class="ba-card">
                <p class="ba-card__title">Deployment Performance</p>
                <div class="ba-card__grid">
                    <div><span>Active Capital</span><strong>${{ number_format($perf['active_capital'], 2) }}</strong></div>
                    <div><span>Realized Profit</span><strong class="ba-pos">${{ number_format($perf['realized_profit'], 2) }}</strong></div>
                    <div><span>Unrealized P/L</span><strong>${{ number_format($perf['unrealized_pl'], 2) }}</strong></div>
                    <div><span>24H Performance</span><strong class="{{ $perf['change_24h_pct'] > 0 ? 'ba-pos' : ($perf['change_24h_pct'] < 0 ? 'ba-neg' : '') }}">{{ $perf['change_24h_pct'] >= 0 ? '+' : '' }}{{ number_format($perf['change_24h_pct'], 2) }}%</strong></div>
                    <div><span>Total Actions</span><strong>{{ number_format($perf['total_actions']) }}</strong></div>
                </div>
            </div>

            <div class="ba-card">
                <p class="ba-card__title">Current Intelligence</p>
                <p class="ba-card__sub">{{ number_format($intel['total']) }} formations currently monitored</p>
                <div class="ba-intel">
                    <div class="ba-intel__row">
                        <span class="ba-dot ba-dot--up"></span>
                        <span class="ba-intel__label">Strengthening</span>
                        <strong>{{ number_format($intel['strengthening']) }}</strong>
                    </div>
                    <div class="ba-intel__row">
                        <span class="ba-dot ba-dot--purple"></span>
                        <span class="ba-intel__label">Building</span>
                        <strong>{{ number_format($intel['building']) }}</strong>
                    </div>
                    <div class="ba-intel__row">
                        <span class="ba-dot ba-dot--cyan"></span>
                        <span class="ba-intel__label">Stable</span>
                        <strong>{{ number_format($intel['stable']) }}</strong>
                    </div>
                    <div class="ba-intel__row">
                        <span class="ba-dot ba-dot--red"></span>
                        <span class="ba-intel__label">Weakening</span>
                        <strong>{{ number_format($intel['weakening']) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABS --}}
        <div class="ba-tabs">
            <button wire:click="switchTab('activity')" class="ba-tab {{ $tab === 'activity' ? 'ba-tab--active' : '' }}">Bot Activity</button>
            <button wire:click="switchTab('history')" class="ba-tab {{ $tab === 'history' ? 'ba-tab--active' : '' }}">Trade History</button>
            @if ($this->formation)
                <span class="ba-tab-filter">
                    ${{ $this->formation->token_symbol }}
                    <button wire:click="filterByFormation(null)">✕</button>
                </span>
            @endif
        </div>

        @if ($tab === 'activity')

            <div class="ba-disclaimer">
                <strong>Your Senflux Activity</strong>
                <p>Senflux continuously monitors qualifying formations and manages deployed capital automatically when conditions change.</p>
            </div>

            <div class="ba-feed">
                @forelse ($this->activityFeed as $item)
                    @php $trade = $item['trade']; @endphp
                    <div class="ba-feed__row" wire:key="feed-{{ $trade->id }}">
                        <div class="ba-feed__icon ba-feed__icon--{{ $trade->type }}">
                            @if ($trade->type === 'buy')
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                            @else
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                            @endif
                        </div>
                        <div class="ba-feed__body">
                            <div class="ba-feed__top">
                                <span class="ba-feed__title">{{ $item['title'] }}</span>
                                <span class="ba-feed__time">{{ $trade->block_time?->format('g:i A') ?? '—' }}</span>
                            </div>
                            <div class="ba-feed__meta">
                                <a href="{{ route('dashboard.formations.detail', $trade->formation) }}" wire:navigate class="ba-feed__formation">
                                    ${{ $trade->formation->token_symbol }}
                                </a>
                                @isset($trade->formation->state)
                                    <span class="ba-feed__state ba-feed__state--{{ $trade->formation->state->value }}">
                                        {{ ucfirst($trade->formation->state->value) }}
                                    </span>
                                @endisset
                            </div>
                            <p class="ba-feed__reason">{{ $item['reason'] }}</p>
                            <span class="ba-feed__status">✓ Executed</span>
                        </div>
                    </div>
                @empty
                    <div class="ba-feed__empty">No bot activity yet — Senflux will log actions here as formations qualify for deployment.</div>
                @endforelse
            </div>

        @else

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
                            <th>Trader Wallet</th>
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
                                <td class="mono">{{ Str::limit($trade->trader_wallet, 12) }}</td>
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

        @endif
    </div>
</div>