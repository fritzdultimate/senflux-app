<div wire:poll.10000="refresh">
    @push('styles')
        @vite('resources/css/terminal.css')
        @vite('resources/css/dashboard.css')
    @endpush

    <div class="dash">

        <div class="panel__head" style="margin-bottom: 20px">
            <div>
                <div class="panel__title" style="font-size: 1.1rem">Live Formation Feed</div>
                <div class="panel__sub">Senflux Intelligence Engine · updated {{ now()->format('g:i A') }}</div>
            </div>
        </div>

        <div class="terminal-grid">

            {{-- Formation cards ─────────────────────────────────── --}}
            <div class="terminal-feed">
                @forelse ($this->formations as $formation)
                    @include('components.formation-card', ['formation' => $formation])
                @empty
                    <div class="empty-state">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                        <p>No formations being monitored right now.</p>
                    </div>
                @endforelse
            </div>

            {{-- Activity ticker ──────────────────────────────────── --}}
            <div class="panel panel--ticker">
                <div class="panel__title" style="margin-bottom: 14px; font-size: .8rem">LIVE ACTIVITY</div>
                <div class="activity-list">
                    @forelse ($this->activityEvents as $event)
                        <div class="activity-row activity-row--event">
                            <span class="event-dot" style="background: {{ $event->formation->state->color() }}"></span>
                            <div class="activity-row__body">
                                <span class="activity-row__desc">{{ $event->message }}</span>
                                <span class="activity-row__time">{{ $event->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="panel__sub">No activity yet.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Formation detail modal ──────────────────────────────────── --}}
    @if ($this->selectedFormation)
        <div class="modal-overlay" wire:click.self="closeFormation">
            <div class="modal-panel">
                @include('components.formation-card', ['formation' => $this->selectedFormation, 'readonly' => true])

                @if ($this->selectedFormation->isVerifiable())
                    <p class="onchain-disclaimer">
                        Price, liquidity, and volume above are pulled live from {{ ucfirst($this->selectedFormation->dex) }}
                        and can be independently verified at the link. Formation Score and intelligence metrics are Senflux's
                        own analysis and are not on-chain data.
                    </p>
                @endif

                <div class="modal-section">
                    <div class="modal-section__title">TIMELINE</div>
                    <div class="timeline">
                        @foreach ($this->selectedTimeline as $event)
                            <div class="timeline-row">
                                <span class="activity-row__desc">{{ $event->message }}</span>
                                <span class="activity-row__time">{{ $event->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-section">
                    <div class="modal-section__title">DEPLOYMENT</div>
                    @php
                        $deployment = $this->selectedDeployment;
                        $summary = $this->selectedFormation->deploymentSummary();
                        $showThreshold = 3;
                        $deployedCount = $deployment['deployed_slots']->count();
                    @endphp

                    {{-- Platform-wide stat — always visible, regardless of viewer --}}
                    <div class="deploy-aggregate">
                        <span>{{ number_format($summary['total_slots']) }} slots deployed platform-wide</span>
                        <span>${{ number_format($summary['total_capital'], 0) }} total capital</span>
                    </div>

                    @if ($deployment['has_deployed'])
                        <div class="slot-summary-bar">
                            <span>You have <strong>{{ $deployedCount }}</strong> slot{{ $deployedCount === 1 ? '' : 's' }} here</span>
                            <span>${{ number_format($deployment['deployed_total_capital'], 2) }} deployed ·
                                <span class="slot-row__profit">+${{ number_format($deployment['deployed_total_profit'], 2) }}</span></span>
                        </div>

                        @if ($deployedCount > $showThreshold && !$showAllDeployedSlots)
                            <button wire:click="toggleDeployedSlots" class="slot-list-toggle">
                                View all {{ $deployedCount }} slots →
                            </button>
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
                                    <button wire:click="deploy({{ $slot->id }}, {{ $this->selectedFormation->id }})" class="btn-deploy">
                                        Deploy This Slot
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        @error('deployment')
                            <p class="deploy-error">{{ $message }}</p>
                        @enderror
                    @endif

                    @if (!$deployment['has_deployed'] && !$deployment['can_deploy'])
                        <p class="deploy-status">Waiting For Qualification — no funded slots available to deploy here.</p>
                    @endif
                </div>

                <div class="modal-footer">
                    <div class="share-control" x-data="{ open: false, copied: false }">
                        <button @click="open = !open" @click.outside="open = false" class="modal-footer__share">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></svg>
                            Share
                        </button>

                        <div x-show="open" x-transition x-cloak class="share-menu">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('formations.share', $this->selectedFormation)) }}&text={{ urlencode('$' . $this->selectedFormation->token_symbol . ' — ' . $this->selectedFormation->state->label() . ' formation, score ' . $this->selectedFormation->score . '/100 on Senflux') }}"
                            target="_blank" rel="noopener" class="share-menu__item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 2H22l-7.6 8.7L23.3 22h-7l-5.5-7.2L4.5 22H1.4l8.1-9.3L1 2h7.2l5 6.6L18.9 2Zm-1.2 18h1.7L7.4 3.9H5.6L17.7 20Z"/></svg>
                                Share on X
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode(route('formations.share', $this->selectedFormation)) }}&text={{ urlencode('$' . $this->selectedFormation->token_symbol . ' formation — ' . $this->selectedFormation->state->label() . ' on Senflux') }}"
                            target="_blank" rel="noopener" class="share-menu__item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M22 2 2 10.6l6.3 2.1L11 20l3.4-4.9L20 18 22 2ZM8.7 12.3 17.6 6l-7.3 7.6-.1 2.6-1.5-3.9Z"/></svg>
                                Share on Telegram
                            </a>
                            <button
                                @click="navigator.clipboard.writeText('{{ route('formations.share', $this->selectedFormation) }}'); copied = true; setTimeout(() => copied = false, 1800)"
                                class="share-menu__item"
                            >
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                <span x-text="copied ? 'Copied!' : 'Copy Link'"></span>
                            </button>
                        </div>
                    </div>

                    <button wire:click="closeFormation" class="modal-footer__close">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>