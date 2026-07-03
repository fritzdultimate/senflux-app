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

        {{-- Feed + Heatmap --}}
        <div class="grid grid-cols-1 xl:grid-cols-[1fr_358px] gap-3.5 mb-3.5">
            <div class="rounded-2xl overflow-hidden" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
                <div class="flex items-center justify-between px-5 py-3.5 border-b" style="border-color:rgba(255,255,255,.07)">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#10B981] ap block"></span>
                        <span class="text-[12px] font-semibold text-[#c8c8e0]">LIVE FORMATION FEED</span>
                    </div>
                    <span class="text-[11px] text-[#4a4a6a]">Last updated: {{ now()->diffForHumans() }}</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="ftbl">
                        <thead>
                            <tr><th>Asset</th><th>Formation State</th><th>Participation (VS 24H)</th><th>Persistence Score</th><th>Velocity</th><th>Trend</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($this->formations as $formation)
                                <tr wire:click="openFormation({{ $formation->id }})" style="cursor:pointer">
                                    <td>
                                        <span class="font-syne font-bold text-white text-[13px]">{{ $formation->token_symbol }}</span><br>
                                        <span class="text-[11px] text-[#4a4a6a]">{{ $formation->token_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:{{ $formation->state->color() }}22;color:{{ $formation->state->color() }};border:1px solid {{ $formation->state->color() }}55">
                                            {{ strtoupper($formation->state->label()) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="color:{{ $formation->state->color() }}" class="font-semibold">
                                            {{ $formation->price_change_24h >= 0 ? '+' : '' }}{{ number_format($formation->price_change_24h ?? 0, 0) }}%
                                        </span><br>
                                        <span class="text-[11px] text-[#4a4a6a]">{{ $formation->participationLevel() }}</span>
                                    </td>
                                    <td>
                                        <span class="text-white font-bold">{{ $formation->score }}</span><span class="text-[#4a4a6a] text-[12px]">/100</span><br>
                                        <span class="text-[11px] font-semibold" style="color:{{ $formation->state->color() }}">{{ $formation->persistenceLevel() }}</span>
                                    </td>
                                    <td>
                                        <div class="spark">
                                            @foreach ($formation->sparklineHeights() as $i => $h)
                                                <span style="height:{{ $h }}px; {{ $loop->last ? 'opacity:1;background:'.$formation->state->color() : '' }}"></span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td style="color:{{ $formation->state->color() }}" class="text-base">{{ $formation->trendArrow() }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-8 text-[#4a4a6a]">No formations being monitored right now.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Heatmap --}}
            <div class="rounded-2xl overflow-hidden" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
                <div class="flex items-center justify-between px-4 py-3.5 border-b" style="border-color:rgba(255,255,255,.07)">
                    <span class="text-[12px] font-semibold text-[#c8c8e0]">PARTICIPATION HEATMAP</span>
                    <span class="text-[11px] text-[#9B7DFF] rounded-md px-2 py-1" style="background:rgba(123,92,245,.1);border:1px solid rgba(123,92,245,.2)">Solana Ecosystem</span>
                </div>
                <div class="grid grid-cols-3 gap-1.5 p-3.5">
                    @foreach ($this->sectorHeatmap as $sector)
                        <div class="hx hx-{{ $sector['strength'] }}">
                            <p class="text-[10px] font-bold text-white leading-tight">{{ $sector['label'] }}</p>
                            <p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">{{ $sector['strengthLabel'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="grid grid-cols-4 border-t" style="border-color:rgba(255,255,255,.07)">
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        @php $totalWallets = \App\Models\Formation::active()->sum('active_wallets'); @endphp
                        <p class="font-syne font-bold text-[14px] text-white">
                            {{ $totalWallets > 0 ? number_format($totalWallets) : '—' }}
                        </p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Active Wallets</p>
                        @if ($totalWallets === 0)
                            <p class="text-[8px] text-[#4a4a6a] mt-0.5">Pending Birdeye</p>
                        @endif
                    </div>
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        <p class="font-syne font-bold text-[14px] text-white">{{ number_format($this->platformStats['active_participants']) }}</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Active Participants</p>
                    </div>
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        <p class="font-syne font-bold text-[14px] text-white">{{ number_format($this->platformStats['new_deployments_24h']) }}</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">New Deployments 24H</p>
                    </div>
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        <p class="font-syne font-bold text-[14px] text-white">${{ number_format($this->platformStats['capital_deployed']) }}</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Capital Deployed</p>
                    </div>
                    <div class="p-3 text-center">
                        <p class="font-syne font-bold text-[14px] text-white">{{ $this->platformStats['avg_formation_score'] }}/100</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Avg Formation Score</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formation States Explained — reuses your real enum, not a copy of the guest mock --}}
        <div class="rounded-2xl p-5" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
            <p class="text-[12px] font-semibold text-[#c8c8e0] uppercase tracking-wider mb-4">Formation States Explained</p>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
                @foreach (\App\Enums\FormationState::cases() as $state)
                    <div class="fs" style="background:{{ $state->color() }}14;border:1px solid {{ $state->color() }}33">
                        <p class="text-[10px] font-bold uppercase tracking-wide leading-tight" style="color:{{ $state->color() }}">{{ $state->label() }}</p>
                        <p class="text-[10px] text-[rgba(255,255,255,.4)] mt-1 leading-tight">{{ $state->description() }}</p>
                    </div>
                @endforeach
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

                @php $trades = $this->selectedFormation->recentTradeActivities(); @endphp
                @if ($trades->isNotEmpty())
                    <div class="modal-section">
                        <div class="modal-section__title">ON-CHAIN TRADE VERIFICATION</div>
                        <p class="onchain-disclaimer" style="margin-bottom: 12px;">
                            Below are real, publicly verifiable Solana transactions on {{ $this->selectedFormation->token_symbol }}'s
                            liquidity pool — anyone can confirm these directly on Solscan. These are general market activity, not
                            trades executed by Senflux.
                        </p>
                        <div class="trade-list">
                            @foreach ($trades as $trade)
                                <a href="{{ $trade->explorerUrl() }}" target="_blank" rel="noopener" class="trade-row">
                                    <span class="trade-row__sig">{{ Str::limit($trade->tx_signature, 20) }}</span>
                                    <span class="trade-row__time">{{ $trade->block_time?->diffForHumans() ?? '—' }}</span>
                                    <span class="trade-row__verify">Verify ↗</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
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