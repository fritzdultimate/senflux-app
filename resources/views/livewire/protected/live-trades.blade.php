{{-- resources/views/livewire/protected/live-trades.blade.php --}}
<div>
    @push('styles')
        @vite('resources/css/live-trades.css')
    @endpush

    <div class="lt" wire:poll.8000ms="refresh">

        {{-- ── Stat strip ───────────────────────────────────────────────────── --}}
        <div class="lt-stats">
            <div class="lt-stat">
                <span class="lt-live-dot"></span>
                <div>
                    <p class="lt-stat__label">Open Positions</p>
                    <p class="lt-stat__value">{{ $this->openCount }}</p>
                </div>
            </div>
            <div class="lt-stat">
                <p class="lt-stat__label">Unrealized P&L</p>
                <p class="lt-stat__value {{ $this->totalOpenPnl >= 0 ? 'lt-stat__value--green' : 'lt-stat__value--red' }}">
                    {{ $this->totalOpenPnl >= 0 ? '+' : '' }}${{ number_format($this->totalOpenPnl, 2) }}
                </p>
            </div>
            <div class="lt-stat">
                <p class="lt-stat__label">Win Rate (Closed)</p>
                <p class="lt-stat__value">{{ $this->winRate }}%</p>
            </div>
        </div>

        {{-- ── Open trades ──────────────────────────────────────────────────── --}}
        <div class="lt-panel">
            <div class="lt-panel__head">
                <p class="lt-panel__title">Open Positions</p>
                <span class="lt-live-badge">
                    <span class="lt-live-badge__dot"></span> LIVE
                </span>
            </div>

            @if($this->openTrades->isEmpty())
                <div class="lt-empty">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M3 17l6-6 4 4 8-8"/></svg>
                    <p>No open positions right now.</p>
                </div>
            @else
                <div class="lt-table-scroll">
                    <table class="lt-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th class="lt-table__right">Entry</th>
                                <th class="lt-table__right">Current</th>
                                <th class="lt-table__right">P&L</th>
                                <th class="lt-table__right">Opened</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->openTrades as $trade)
                                @php
                                    $pnlPositive = (float) $trade->pnl_amount >= 0;
                                    $typeColor = $trade->type->color();
                                @endphp
                                <tr>
                                    <td>
                                        <b class="lt-table__asset">{{ $trade->trackedAsset->symbol }}</b>
                                        <span class="lt-table__network">{{ $trade->trackedAsset->network }}</span>
                                    </td>
                                    <td>
                                        <span class="lt-type-badge" style="color: {{ $typeColor }}; background: {{ $typeColor }}1a; border-color: {{ $typeColor }}44">
                                            {{ $trade->type->label() }}
                                        </span>
                                    </td>
                                    <td class="lt-table__right lt-table__mono">${{ number_format($trade->entry_price, 4) }}</td>
                                    <td class="lt-table__right lt-table__mono">
                                        @if($trade->current_price)
                                            ${{ number_format($trade->current_price, 4) }}
                                        @else
                                            <span class="lt-table__muted">—</span>
                                        @endif
                                    </td>
                                    <td class="lt-table__right">
                                        @if($trade->pnl_percent !== null)
                                            <span class="{{ $pnlPositive ? 'lt-pnl--pos' : 'lt-pnl--neg' }}">
                                                {{ $pnlPositive ? '+' : '' }}{{ number_format($trade->pnl_percent, 2) }}%
                                            </span>
                                        @else
                                            <span class="lt-table__muted">Pending feed</span>
                                        @endif
                                    </td>
                                    <td class="lt-table__right lt-table__muted">{{ $trade->opened_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ── Closed trades ────────────────────────────────────────────────── --}}
        <div class="lt-panel">
            <p class="lt-panel__title" style="margin-bottom: .9rem">Recent Closed Trades</p>

            @if($this->closedTrades->isEmpty())
                <div class="lt-empty">
                    <p>No closed trades yet.</p>
                </div>
            @else
                <div class="lt-table-scroll">
                    <table class="lt-table">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Type</th>
                                <th class="lt-table__right">Entry</th>
                                <th class="lt-table__right">Exit</th>
                                <th class="lt-table__right">P&L</th>
                                <th class="lt-table__right">Closed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->closedTrades as $trade)
                                @php $pnlPositive = (float) $trade->pnl_amount >= 0; @endphp
                                <tr>
                                    <td><b class="lt-table__asset">{{ $trade->trackedAsset->symbol }}</b></td>
                                    <td>
                                        <span class="lt-type-badge" style="color: {{ $trade->type->color() }}; background: {{ $trade->type->color() }}1a; border-color: {{ $trade->type->color() }}44">
                                            {{ $trade->type->label() }}
                                        </span>
                                    </td>
                                    <td class="lt-table__right lt-table__mono">${{ number_format($trade->entry_price, 4) }}</td>
                                    <td class="lt-table__right lt-table__mono">${{ number_format($trade->exit_price, 4) }}</td>
                                    <td class="lt-table__right">
                                        <span class="{{ $pnlPositive ? 'lt-pnl--pos' : 'lt-pnl--neg' }}">
                                            {{ $pnlPositive ? '+' : '' }}{{ number_format($trade->pnl_percent, 2) }}%
                                        </span>
                                    </td>
                                    <td class="lt-table__right lt-table__muted">{{ $trade->closed_at->diffForHumans() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>
