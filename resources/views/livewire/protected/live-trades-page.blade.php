<div wire:poll.8000="refresh">
    @push('styles')
        @vite('resources/css/live-trades.css')
        @vite('resources/css/formation-detail.css')
        @vite('resources/css/terminal.css')
    @endpush

    <div class="lt-page">
        <div class="fd-topbar">
            <a href="{{ route('terminal') }}" wire:navigate class="fd-back">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                Terminal
            </a>
            <span class="fd-topbar__sep">/</span>
            <span class="fd-topbar__current">Live Trades{{ $this->formation ? ' · ' . $this->formation->token_symbol : '' }}</span>
            <span class="fd-topbar__spacer"></span>
            <span class="fd-topbar__live"><span class="fd-live-dot"></span> Live tape</span>
        </div>

        @php $stats = $this->stats; @endphp
        <div class="lt-stats">
            <div class="lt-stat"><span>24H Trades</span><strong>{{ number_format($stats['trades_24h']) }}</strong></div>
            <div class="lt-stat">
                <span>Buy / Sell</span>
                <strong class="split"><span style="color:#10B981">{{ $stats['buys_24h'] }}</span> / <span style="color:#EF4444">{{ $stats['sells_24h'] }}</span></strong>
            </div>
            <div class="lt-stat"><span>Failed 24H</span><strong>{{ number_format($stats['failed_24h']) }}</strong></div>
            <div class="lt-stat"><span>Active Formations</span><strong>{{ $stats['active_formations'] }}</strong></div>
        </div>

        <div class="lt-filters">
            @if ($this->formation)
                <span class="lt-filter-pill lt-filter-pill--active">
                    ${{ $this->formation->token_symbol }}
                    <button wire:click="filterByFormation(null)">✕</button>
                </span>
            @endif
            <button wire:click="filterBySource(null)" class="lt-filter-pill {{ !$source ? 'lt-filter-pill--active' : '' }}">All Sources</button>
            <!-- <button wire:click="filterBySource('market_pool')" class="lt-filter-pill {{ $source === 'market_pool' ? 'lt-filter-pill--active' : '' }}">Market Pool</button> -->
            <!-- <button wire:click="filterBySource('senflux')" class="lt-filter-pill {{ $source === 'senflux' ? 'lt-filter-pill--active' : '' }}">Senflux</button> -->

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
    </div>
</div>