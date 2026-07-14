<div wire:poll.10000="refresh">
    @push('styles')
        @vite('resources/css/terminal.css')
        @vite('resources/css/formation-detail.css')
    @endpush

    @php $f = $this->fresh; @endphp

    <div class="fd-page">

        {{-- Back bar --}}
        <div class="fd-topbar">
            <a href="{{ route('terminal') }}" wire:navigate class="fd-back">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                Terminal
            </a>
            <span class="fd-topbar__sep">/</span>
            <span class="fd-topbar__current">${{ $f->token_symbol }}</span>
            <span class="fd-topbar__spacer"></span>
            <span class="fd-topbar__live"><span class="fd-live-dot"></span> Live · updated {{ now()->format('g:i:s A') }}</span>
        </div>

        <div class="fd-grid">

            {{-- Main column --}}
            <div class="fd-main">
                @include('components.formation-card', ['formation' => $f, 'readonly' => true])

                @if ($f->isVerifiable())
                    <p class="onchain-disclaimer">
                        Price, liquidity, and volume above are pulled live from {{ ucfirst($f->dex) }}
                        and can be independently verified at the link. Formation Score and intelligence
                        metrics are Senflux's own analysis and are not on-chain data.
                    </p>
                @endif

                {{-- Trade preview → dedicated page --}}
                <div class="fd-panel hidden">
                    <div class="fd-panel__head">
                        <span class="fd-panel__title">ON-CHAIN TRADE VERIFICATION</span>
                        <a href="{{ route('dashboard.trades.live', ['formation' => $f->id]) }}" wire:navigate class="fd-panel__link">
                            View all trades →
                        </a>
                    </div>
                    <p class="onchain-disclaimer" style="margin-bottom: 12px;">
                        Real, publicly verifiable Solana transactions on {{ $f->token_symbol }}'s liquidity pool —
                        general market activity, not trades executed by Senflux.
                    </p>
                    @forelse ($this->recentTrades as $trade)
                        <a href="{{ $trade->explorerUrl() }}" target="_blank" rel="noopener" class="trade-row">
                            <span class="trade-row__sig">{{ Str::limit($trade->tx_signature, 20) }}</span>
                            <span class="trade-row__time">{{ $trade->block_time?->diffForHumans() ?? '—' }}</span>
                            <span class="trade-row__verify">
                                Verify
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17L17 7"/><path d="M8 7h9v9"/></svg>
                            </span>
                        </a>
                    @empty
                        <p class="fd-empty">No verified trade activity yet.</p>
                    @endforelse
                </div>

                {{-- Timeline --}}
                <div class="fd-panel">
                    <div class="fd-panel__head">
                        <span class="fd-panel__title">TIMELINE</span>
                        <span class="fd-panel__count">{{ number_format($this->eventsTotal) }} total events</span>
                    </div>

                    <div class="fd-timeline">
                        @forelse ($this->timelineGroups as $group)
                            <div class="fd-timeline-day">
                                <div class="fd-timeline-day__label">{{ $group['label'] }}</div>
                                @foreach ($group['items'] as $cluster)
                                    @php
                                        $icons = [
                                            'detected' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3"/>',
                                            'state_change' => '<path d="M21 12a9 9 0 1 1-3-6.7M21 3v6h-6"/>',
                                            'signal' => '<path d="M13 2 3 14h8l-1 8 10-12h-8z"/>',
                                            'deployment' => '<circle cx="12" cy="12" r="9"/><path d="m9 12 2 2 4-4"/>',
                                        ];
                                        $icon = $icons[$cluster['type']] ?? $icons['detected'];
                                    @endphp
                                    <div class="fd-tl-row" x-data="{ open: false }">
                                        <span class="fd-tl-row__icon fd-tl-row__icon--{{ $cluster['type'] }}">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $icon !!}</svg>
                                        </span>
                                        <div class="fd-tl-row__body">
                                            <div class="fd-tl-row__top">
                                                <span class="fd-tl-row__msg">{{ $cluster['primary']->message ?? $cluster['primary']->type->defaultMessage() }}</span>
                                                <span class="fd-tl-row__time">{{ $cluster['primary']->created_at->format('g:i A') }}</span>
                                            </div>
                                            @if ($cluster['count'] > 1)
                                                <button type="button" @click="open = !open" class="fd-tl-row__more">
                                                    +{{ $cluster['count'] - 1 }} similar event{{ $cluster['count'] - 1 === 1 ? '' : 's' }}
                                                    <span x-text="open ? '▲' : '▼'"></span>
                                                </button>
                                                <div x-show="open" x-transition x-cloak class="fd-tl-row__sub">
                                                    @foreach ($cluster['others'] as $sub)
                                                        <div class="fd-tl-row__sub-item">
                                                            <span>{{ $sub->message ?? $sub->type->defaultMessage() }}</span>
                                                            <span>{{ $sub->created_at->format('g:i A') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <p class="fd-empty">No activity recorded yet.</p>
                        @endforelse
                    </div>

                    @if ($this->timelineHasMore)
                        <button wire:click="loadMoreTimeline" wire:loading.attr="disabled" class="fd-load-more">
                            <span wire:loading.remove wire:target="loadMoreTimeline">Load more history</span>
                            <span wire:loading wire:target="loadMoreTimeline">Loading…</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="fd-side">
                <div class="fd-panel">
                    <div class="fd-panel__head"><span class="fd-panel__title">DEPLOYMENT</span></div>
                    @php
                        $deployment = $this->deployment;
                        $summary = $f->deploymentSummary();
                        $showThreshold = 3;
                        $deployedCount = $deployment['deployed_slots']->count();
                    @endphp

                    <div class="deploy-aggregate">
                        <span>{{ number_format($summary['total_slots']) }} slots deployed platform-wide</span>
                        <span>${{ number_format($summary['total_capital'], 0) }} total capital</span>
                    </div>

                    @if ($deployment['has_deployed'])
                        <div class="slot-summary-bar">
                            <span>You have <strong>{{ $deployedCount }}</strong> slot{{ $deployedCount === 1 ? '' : 's' }} here</span>
                            <span>${{ number_format($deployment['deployed_total_capital'], 2) }} ·
                                <span class="slot-row__profit">+${{ number_format($deployment['deployed_total_profit'], 2) }}</span></span>
                        </div>

                        @if ($deployedCount > $showThreshold && !$showAllDeployedSlots)
                            <button wire:click="toggleDeployedSlots" class="slot-list-toggle">View all {{ $deployedCount }} slots →</button>
                        @else
                            <div class="slot-list">
                                @foreach ($deployment['deployed_slots'] as $slot)
                                    <div class="slot-row slot-row--active">
                                        <div class="slot-row__head">
                                            <span class="slot-row__number">Slot #{{ $slot->slot_number }}</span>
                                            <span class="slot-row__tier">{{ $slot->subscription->packTier->name }}</span>
                                        </div>
                                        <div class="slot-row__stats">
                                            <div><span>Capital</span><strong>${{ number_format($slot->capital_amount, 2) }}</strong></div>
                                            <div><span>Deployed</span><strong>{{ $slot->deployed_at->diffForHumans() }}</strong></div>
                                            <div><span>Profit</span><strong class="slot-row__profit">+${{ number_format($slot->realized_profit, 2) }}</strong></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if ($deployedCount > $showThreshold)
                                <button wire:click="toggleDeployedSlots" class="slot-list-toggle">Show less</button>
                            @endif
                        @endif
                    @endif

                    @if ($deployment['can_deploy'])
                        <div class="slot-list" style="margin-top: {{ $deployment['has_deployed'] ? '14px' : '0' }}">
                            @foreach ($deployment['eligible_slots'] as $slot)
                                <div class="slot-row slot-row--eligible">
                                    <div class="slot-row__head">
                                        <span class="slot-row__number">Slot #{{ $slot->slot_number }}</span>
                                        <span class="slot-row__tier">{{ $slot->subscription->packTier->name }}</span>
                                    </div>
                                    <div class="slot-row__stats">
                                        <div><span>Capital</span><strong>${{ number_format($slot->capital_amount, 2) }}</strong></div>
                                        <div><span>Funded</span><strong>{{ $slot->funded_at->diffForHumans() }}</strong></div>
                                    </div>
                                    <button wire:click="deploy({{ $slot->id }})" class="btn-deploy">Deploy This Slot</button>
                                </div>
                            @endforeach
                        </div>
                        @error('deployment') <p class="deploy-error">{{ $message }}</p> @enderror
                    @endif

                    @if (!$deployment['has_deployed'] && !$deployment['can_deploy'])
                        <p class="deploy-status">Waiting For Qualification — no funded slots available to deploy here.</p>
                    @endif
                </div>

                {{-- Serious-company footer details --}}
                <div class="fd-panel fd-meta">
                    <div class="fd-meta__row"><span>Formation ID</span><strong>#{{ $f->id }}</strong></div>
                    <div class="fd-meta__row"><span>Sector</span><strong>{{ ucfirst(str_replace('_', ' ', $f->sector ?? '—')) }}</strong></div>
                    <div class="fd-meta__row"><span>DEX</span><strong>{{ ucfirst($f->dex ?? '—') }}</strong></div>
                    <div class="fd-meta__row"><span>First Detected</span><strong>{{ $f->created_at->format('M j, Y g:i A') }}</strong></div>
                    @if ($f->market_data_synced_at)
                        <div class="fd-meta__row"><span>Data Last Synced</span><strong>{{ $f->marketDataFreshness() }}</strong></div>
                    @endif
                    <div class="fd-meta__row"><span>Managed</span><strong>{{ $f->auto_managed ? 'Auto (Engine)' : 'Curated' }}</strong></div>
                </div>

                <div class="fd-panel">
                    <div class="share-control" x-data="{ open: false, copied: false }">
                        <button @click="open = !open" @click.outside="open = false" class="modal-footer__share" style="width:100%; justify-content:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg>
                            Share
                        </button>
                        <div x-show="open" x-transition x-cloak class="share-menu">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('formations.share', $f)) }}&text={{ urlencode('$' . $f->token_symbol . ' — ' . $f->state->label() . ' formation, score ' . $f->score . '/100 on Senflux') }}" target="_blank" rel="noopener" class="share-menu__item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.6 8.7L23.3 22h-7l-5.5-7.2L4.5 22H1.4l8.1-9.3L1 2h7.2l5 6.6L18.9 2Zm-1.2 18h1.7L7.4 3.9H5.6L17.7 20Z"/></svg>
                                Share on X
                            </a>
                            <button @click="navigator.clipboard.writeText('{{ route('formations.share', $f) }}'); copied = true; setTimeout(() => copied = false, 1800)" class="share-menu__item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                <span x-text="copied ? 'Copied!' : 'Copy Link'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>