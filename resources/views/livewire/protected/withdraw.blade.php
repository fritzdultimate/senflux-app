{{-- resources/views/livewire/protected/withdraw.blade.php --}}
<div>
    @vite('resources/css/withdraw.css')

    <div class="wd">

        {{-- ── Alerts ───────────────────────────────────────────────────────── --}}
        @if($successMessage)
            <div class="wd-alert wd-alert--success">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $successMessage }}
            </div>
        @endif

        @if($errorMessage)
            <div class="wd-alert wd-alert--error">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $errorMessage }}
            </div>
        @endif

        <div class="wd-layout">

            {{-- ── LEFT: Form ──────────────────────────────────────────────── --}}
            <div class="wd-form-col">

                {{-- Wallet selection ──────────────────────────────────────── --}}
                <div class="wd-section">
                    <p class="wd-label">01 — SELECT WALLET</p>
                    <div class="wd-wallet-grid">
                        @foreach($this->walletOptions as $opt)
                            <button
                                wire:click="$set('walletType', '{{ $opt['value'] }}')"
                                type="button"
                                class="wd-wallet-card {{ $walletType === $opt['value'] ? 'wd-wallet-card--active' : '' }}"
                            >
                                <span class="wd-wallet-card__label">{{ $opt['label'] }}</span>
                                <span class="wd-wallet-card__balance">${{ number_format($opt['balance'], 2) }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Amount ───────────────────────────────────────────────── --}}
                <div class="wd-section">
                    <p class="wd-label">02 — AMOUNT</p>
                    <div class="wd-amount-wrap">
                        <span class="wd-amount-prefix">$</span>
                        <input
                            type="number"
                            wire:model.live.debounce.300ms="amount"
                            class="wd-amount-input"
                            placeholder="0.00"
                            step="0.01"
                            min="{{ $this->settings->min_amount }}"
                            max="{{ $this->availableBalance }}"
                        />
                        <button wire:click="setMax" type="button" class="wd-max-btn">MAX</button>
                    </div>
                    @error('amount')
                        <p class="wd-field-error">{{ $message }}</p>
                    @enderror
                    <div class="wd-balance-hint">
                        Available: <strong>${{ number_format($this->availableBalance, 2) }}</strong>
                        &nbsp;·&nbsp; Min: ${{ number_format($this->settings->min_amount, 2) }}
                    </div>

                    @if($amount > 0)
                        <div class="wd-fee-preview">
                            <div class="wd-fee-row">
                                <span>Amount</span>
                                <span>${{ number_format($amount, 2) }}</span>
                            </div>
                            @if($this->estimatedFee > 0)
                                <div class="wd-fee-row wd-fee-row--fee">
                                    <span>Fee</span>
                                    <span>-${{ number_format($this->estimatedFee, 2) }}</span>
                                </div>
                            @endif
                            <div class="wd-fee-row wd-fee-row--net">
                                <span>You receive</span>
                                <strong>${{ number_format($this->netAmount, 2) }}</strong>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Network ──────────────────────────────────────────────── --}}
                <div class="wd-section">
                    <p class="wd-label">03 — NETWORK</p>
                    <div class="wd-network-grid">
                        @foreach($networks as $net)
                            <button
                                wire:click="selectNetwork('{{ $net['code'] }}')"
                                type="button"
                                class="wd-network-btn {{ $network === $net['code'] ? 'wd-network-btn--active' : '' }}"
                            >
                                <span class="wd-network-btn__currency">{{ $net['currency'] }}</span>
                                <span class="wd-network-btn__label">{{ $net['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Wallet address ────────────────────────────────────────── --}}
                <div class="wd-section">
                    <p class="wd-label">04 — WALLET ADDRESS</p>
                    <div class="wd-address-wrap">
                        <input
                            type="text"
                            wire:model="walletAddress"
                            class="wd-address-input"
                            placeholder="Enter your {{ strtoupper($network) }} wallet address"
                            autocomplete="off"
                            spellcheck="false"
                        />
                    </div>
                    @error('walletAddress')
                        <p class="wd-field-error">{{ $message }}</p>
                    @enderror
                    <p class="wd-address-warning">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        Only send to a {{ strtoupper($network) }} address. Wrong network = permanent loss.
                    </p>
                </div>

                {{-- Submit ────────────────────────────────────────────────── --}}
                @if($amount >= $this->settings->min_amount && $walletAddress && !$showConfirm)
                    <button
                        wire:click="requestConfirm"
                        wire:loading.attr="disabled"
                        type="button"
                        class="wd-submit-btn"
                    >
                        <span wire:loading.remove wire:target="requestConfirm">
                            Request Withdrawal
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                        <span wire:loading wire:target="requestConfirm">Validating…</span>
                    </button>
                @endif

                {{-- Confirm modal ────────────────────────────────────────── --}}
                @if($showConfirm)
                    <div class="wd-confirm">
                        <div class="wd-confirm__title">Confirm Withdrawal</div>
                        <div class="wd-confirm__rows">
                            <div class="wd-confirm__row">
                                <span>Amount</span>
                                <strong>${{ number_format($amount, 2) }}</strong>
                            </div>
                            <div class="wd-confirm__row">
                                <span>Network</span>
                                <strong>{{ strtoupper($network) }}</strong>
                            </div>
                            <div class="wd-confirm__row">
                                <span>Address</span>
                                <span class="wd-confirm__addr">{{ $walletAddress }}</span>
                            </div>
                            @if($this->estimatedFee > 0)
                                <div class="wd-confirm__row">
                                    <span>Fee</span>
                                    <span>-${{ number_format($this->estimatedFee, 2) }}</span>
                                </div>
                            @endif
                            <div class="wd-confirm__row wd-confirm__row--net">
                                <span>You receive</span>
                                <strong>${{ number_format($this->netAmount, 2) }}</strong>
                            </div>
                        </div>
                        <p class="wd-confirm__warning">
                            This action cannot be undone. Funds will be sent to the address above.
                        </p>
                        <div class="wd-confirm__actions">
                            <button wire:click="cancelConfirm" type="button" class="wd-btn-ghost">Cancel</button>
                            <button
                                wire:click="submit"
                                wire:loading.attr="disabled"
                                type="button"
                                class="wd-btn-primary"
                            >
                                <span wire:loading.remove wire:target="submit">Confirm</span>
                                <span wire:loading wire:target="submit">Processing…</span>
                            </button>
                        </div>
                    </div>
                @endif

            </div>{{-- /.wd-form-col --}}

            {{-- ── RIGHT: History ──────────────────────────────────────────── --}}
            <div class="wd-history-col">
                <p class="wd-history-title">Withdrawal History</p>

                @if($this->history->isEmpty())
                    <div class="wd-history-empty">
                        <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M12 22V6M6 14l6-8 6 8"/><path d="M2 4h20"/></svg>
                        <p>No withdrawals yet.</p>
                    </div>
                @else
                    <div class="wd-history-list">
                        @foreach($this->history as $w)
                            @php
                                $statusColors = [
                                    'pending' => '#f59e0b',
                                    'approved' => '#60a5fa',
                                    'rejected' => '#ef4444',
                                    'paid' => '#22c55e',
                                ];
                                $color = $statusColors[$w->status->value] ?? '#6b7280';
                            @endphp
                            <div class="wd-history-row">
                                <div class="wd-history-row__left">
                                    <div class="wd-history-row__amount">${{ number_format($w->amount, 2) }}</div>
                                    <div class="wd-history-row__meta">
                                        {{ strtoupper($w->network) }} · {{ strtoupper($w->crypto_currency) }}
                                        · {{ $w->created_at->format('M j, Y') }}
                                    </div>
                                    <div class="wd-history-row__addr">{{ Illuminate\Support\Str::limit($w->wallet_address, 18) }}</div>
                                </div>
                                <div class="wd-history-row__right">
                                    <span class="wd-status-badge" style="color: {{ $color }}; background: {{ $color }}22; border-color: {{ $color }}44">
                                        {{ ucfirst($w->status->value) }}
                                    </span>
                                    @if($w->status->value === 'pending' && $w->created_at->diffInMinutes(now()) <= 30)
                                        <button
                                            wire:click="cancelWithdrawal({{ $w->id }})"
                                            wire:confirm="Cancel this withdrawal request?"
                                            type="button"
                                            class="wd-cancel-btn"
                                        >Cancel</button>
                                    @endif
                                    @if($w->tx_hash)
                                        <div class="wd-history-row__hash">TX: {{ Illuminate\Support\Str::limit($w->tx_hash, 16) }}</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>{{-- /.wd-history-col --}}

        </div>{{-- /.wd-layout --}}
    </div>
</div>