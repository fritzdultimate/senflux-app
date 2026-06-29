{{-- resources/views/livewire/protected/wallet.blade.php --}}
@push('styles')
    @vite('resources/css/wallet.css')
@endpush

<div class="wal">

    {{-- ── Wallet cards ─────────────────────────────────────────────────── --}}
    <div class="wal-cards">
        @foreach($this->wallets as $w)
            <div class="wal-card">
                <p class="wal-card__label">{{ $w['label'] }}</p>
                <p class="wal-card__balance">${{ number_format($w['balance'], 2) }}</p>
                @if($w['locked'] > 0)
                    <p class="wal-card__locked">${{ number_format($w['locked'], 2) }} locked</p>
                @endif
            </div>
        @endforeach

        <div class="wal-card wal-card--total">
            <p class="wal-card__label">Total Available</p>
            <p class="wal-card__balance">${{ number_format($this->totalAvailable, 2) }}</p>
            @if($this->totalLocked > 0)
                <p class="wal-card__locked">${{ number_format($this->totalLocked, 2) }} locked</p>
            @endif
        </div>
    </div>

    {{-- ── Month summary ────────────────────────────────────────────────── --}}
    <div class="wal-month">
        <div class="wal-month__item">
            <span>This Month In</span>
            <strong class="wal-month__credit">+${{ number_format($this->thisMonthCredits, 2) }}</strong>
        </div>
        <div class="wal-month__divider"></div>
        <div class="wal-month__item">
            <span>This Month Out</span>
            <strong class="wal-month__debit">-${{ number_format($this->thisMonthDebits, 2) }}</strong>
        </div>
    </div>

    {{-- ── Filters ──────────────────────────────────────────────────────── --}}
    <div class="wal-filters">
        <select wire:model.live="walletFilter" class="wal-select">
            <option value="all">All Wallets</option>
            @foreach($this->wallets as $w)
                <option value="{{ $w['type'] }}">{{ $w['label'] }}</option>
            @endforeach
        </select>

        <select wire:model.live="typeFilter" class="wal-select">
            <option value="all">All Types</option>
            @foreach($this->typeOptions as $t)
                <option value="{{ $t['value'] }}">{{ $t['label'] }}</option>
            @endforeach
        </select>

        <input type="date" wire:model.live="dateFrom" class="wal-date" placeholder="From">
        <input type="date" wire:model.live="dateTo" class="wal-date" placeholder="To">

        @if($walletFilter !== 'all' || $typeFilter !== 'all' || $dateFrom || $dateTo)
            <button wire:click="resetFilters" type="button" class="wal-reset-btn">Clear</button>
        @endif
    </div>

    {{-- ── Transaction ledger ──────────────────────────────────────────── --}}
    <div class="wal-panel">
        @if($this->transactions->isEmpty())
            <div class="wal-empty">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-4 0v2M12 12v3M10 14h4"/></svg>
                <p>No transactions match your filters.</p>
            </div>
        @else
            <div class="wal-table-scroll">
                <table class="wal-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Wallet</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th class="wal-table__right">Amount</th>
                            <th class="wal-table__right">Balance After</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->transactions as $tx)
                            @php
                                $typeColors = [
                                    'deposit'          => '#60a5fa',
                                    'withdrawal'       => '#ef4444',
                                    'daily_earning'    => '#22c55e',
                                    'referral_bonus'   => '#9B7DFF',
                                    'rank_bonus'       => '#fbbf24',
                                    'leadership_match' => '#fbbf24',
                                    'fee'              => '#ef4444',
                                    'adjustment'       => '#6b7280',
                                ];
                                $color = $typeColors[$tx->type->value] ?? '#6b7280';
                            @endphp
                            <tr>
                                <td>
                                    <span class="wal-type-badge" style="color: {{ $color }}; background: {{ $color }}1a; border-color: {{ $color }}44">
                                        {{ $tx->type->label() }}
                                    </span>
                                </td>
                                <td class="wal-table__muted">{{ $tx->wallet->type->label() }}</td>
                                <td class="wal-table__desc">{{ $tx->description ?? '—' }}</td>
                                <td class="wal-table__muted">{{ $tx->created_at->format('M j, Y g:ia') }}</td>
                                <td class="wal-table__right">
                                    <span class="{{ $tx->is_debit ? 'wal-amt--debit' : 'wal-amt--credit' }}">
                                        {{ $tx->signed_amount }}
                                    </span>
                                </td>
                                <td class="wal-table__right wal-table__muted">
                                    ${{ number_format($tx->balance_after, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="wal-pagination">
                {{ $this->transactions->links() }}
            </div>
        @endif
    </div>

</div>
