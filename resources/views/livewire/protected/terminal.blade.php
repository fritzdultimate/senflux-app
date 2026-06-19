{{-- resources/views/livewire/protected/terminal.blade.php --}}
@vite('resources/css/terminal.css')

<div class="term" wire:poll.6000ms="refresh">

    {{-- ── Header strip ─────────────────────────────────────────────────── --}}
    @php
        $f = $this->formation;
        $stateColors = [
            'idle' => '#6b7280', 'early' => '#06b6d4', 'building' => '#f59e0b',
            'active' => '#22c55e', 'weakening' => '#ef4444',
        ];
        $color = $stateColors[$f?->state ?? 'idle'] ?? '#6b7280';
    @endphp

    <div class="term-header">
        <div class="term-header__item">
            <span class="term-live-dot"></span>
            <span class="term-header__label">LIVE FEED</span>
        </div>
        <div class="term-header__item">
            <span class="term-header__label">Formation</span>
            <span class="term-header__val" style="color: {{ $color }}">{{ ucfirst($f?->state ?? 'idle') }}</span>
        </div>
        <div class="term-header__item">
            <span class="term-header__label">Open Trades</span>
            <span class="term-header__val">{{ $this->openTradeCount }}</span>
        </div>
        <div class="term-header__item">
            <span class="term-header__label">Active Signals</span>
            <span class="term-header__val">{{ $this->activeSignalCount }}</span>
        </div>
    </div>

    {{-- ── Feed ──────────────────────────────────────────────────────────── --}}
    <div class="term-feed">
        @if($this->feed->isEmpty())
            <div class="term-empty">
                <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/></svg>
                <p>No activity yet. Trades and signals will appear here as they happen.</p>
            </div>
        @else
            @foreach($this->feed as $item)
                @if($item['kind'] === 'trade')
                    @php
                        $trade = $item['data'];
                        $tColor = $trade->type->color();
                        $isClosed = $trade->status->value === 'closed';
                        $pnlPositive = (float) $trade->pnl_amount >= 0;
                    @endphp
                    <div class="term-row">
                        <div class="term-row__icon" style="background: {{ $tColor }}1a; border-color: {{ $tColor }}44">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="{{ $tColor }}" stroke-width="2">
                                @if($trade->type->value === 'long')
                                    <path d="M12 19V5M5 12l7-7 7 7"/>
                                @else
                                    <path d="M12 5v14M5 12l7 7 7-7"/>
                                @endif
                            </svg>
                        </div>
                        <div class="term-row__body">
                            <span class="term-row__text">
                                <strong>{{ $trade->trackedAsset->symbol }}</strong>
                                {{ $trade->type->label() }} position {{ $isClosed ? 'closed' : 'opened' }}
                                @if($trade->pnl_percent !== null)
                                    <span class="{{ $pnlPositive ? 'term-pnl--pos' : 'term-pnl--neg' }}">
                                        ({{ $pnlPositive ? '+' : '' }}{{ number_format($trade->pnl_percent, 2) }}%)
                                    </span>
                                @endif
                            </span>
                            <span class="term-row__time">{{ $item['timestamp']->diffForHumans() }}</span>
                        </div>
                        <span class="term-row__tag" style="color: {{ $tColor }}">{{ $isClosed ? 'CLOSED' : 'OPEN' }}</span>
                    </div>
                @else
                    @php $signal = $item['data']; $sColor = $signal->signal_type->color(); @endphp
                    <div class="term-row">
                        <div class="term-row__icon" style="background: {{ $sColor }}1a; border-color: {{ $sColor }}44">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="{{ $sColor }}" stroke-width="2"><path d="M2 12h6l3 8 4-16 3 8h4"/></svg>
                        </div>
                        <div class="term-row__body">
                            <span class="term-row__text">
                                <strong>{{ $signal->trackedAsset->symbol }}</strong>
                                {{ $signal->signal_type->label() }} signal
                                <span class="term-row__confidence">{{ number_format($signal->confidence_score, 0) }}/100 confidence</span>
                            </span>
                            <span class="term-row__time">{{ $item['timestamp']->diffForHumans() }}</span>
                        </div>
                        <span class="term-row__tag" style="color: {{ $sColor }}">SIGNAL</span>
                    </div>
                @endif
            @endforeach
        @endif
    </div>

</div>
