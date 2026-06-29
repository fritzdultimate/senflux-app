<div>
    @push('styles')
        @vite('resources/css/dc.css')
        @vite('resources/css/billing.css')
        @vite('resources/css/deposit.css')
    @endpush

    <div class="dc" wire:poll.10000ms="refreshIfPending">

        {{-- ── Pending gate — blocks new deposit if one is in flight ───────── --}}
        @if($this->pendingDeposit)
            <x-billing.pending-gate
                :record="$this->pendingDeposit"
                :can-cancel="$this->canCancelPending"
                plan-label="Wallet Deposit"
                :amount="$this->pendingDeposit->amount_usd"
                :error="$errorMessage"
            />
        @else

            @if($errorMessage)
                <div class="dc-alert" wire:key="error-{{ md5($errorMessage) }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $errorMessage }}
                </div>
            @endif

            <div class="dc-console-header">
                <div class="dc-console-header__label">
                    <span class="dc-console-header__eyebrow">Wallet Funding</span>
                    <h2>Add capital to your wallet</h2>
                </div>
            </div>

            <p class="dc-subtext">
                Deposited funds land in your wallet balance. Once you're ready, deploy them into a
                Senflux Pack to start earning.
            </p>

            <div class="dc-panel" wire:key="amount-panel">
                <div class="dc-panel__header">
                    <span class="dc-panel__index">01</span>
                    <span class="dc-panel__title">Amount</span>
                </div>

                <div class="dc-amount-row">
                    <div class="dc-amount-field">
                        <span class="dc-amount-field__prefix">$</span>
                        <input
                            type="number"
                            wire:model.live.debounce.350ms="amountUsd"
                            min="{{ $this->minDeposit }}"
                            step="0.01"
                            class="dc-amount-field__input"
                            placeholder="0.00"
                            autocomplete="off"
                        />
                    </div>
                    <div class="dc-amount-range">
                        <span>Min ${{ number_format($this->minDeposit, 0) }}</span>
                    </div>
                </div>

                @error('amountUsd')
                    <p class="dc-field-error">{{ $message }}</p>
                @enderror
            </div>

            @if($amountUsd >= $this->minDeposit)
                <div class="dc-panel" wire:key="crypto-panel">
                    <div class="dc-panel__header">
                        <span class="dc-panel__index">02</span>
                        <span class="dc-panel__title">Settlement currency</span>
                    </div>

                    <div class="dc-currency-rail">
                        @foreach([
                            ['code' => 'sol', 'label' => 'Solana', 'net' => 'SOL'],
                            ['code' => 'usdtsol', 'label' => 'USDT', 'net' => 'Solana'],
                            ['code' => 'usdttrc20', 'label' => 'USDT', 'net' => 'TRC-20'],
                            ['code' => 'usdterc20', 'label' => 'USDT', 'net' => 'ERC-20'],
                            ['code' => 'eth', 'label' => 'Ethereum', 'net' => 'ETH'],
                            ['code' => 'btc', 'label' => 'Bitcoin', 'net' => 'BTC'],
                            ['code' => 'bnb', 'label' => 'BNB', 'net' => 'BSC'],
                        ] as $c)
                            <button
                                type="button"
                                wire:click="selectCurrency('{{ $c['code'] }}')"
                                class="dc-currency {{ $cryptoCurrency === $c['code'] ? 'dc-currency--active' : '' }}"
                            >
                                <span class="dc-currency__code">{{ strtoupper($c['code']) }}</span>
                                <span class="dc-currency__label">{{ $c['label'] }} · {{ $c['net'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($amountUsd >= $this->minDeposit && $cryptoCurrency)
                <button
                    type="button"
                    wire:click="submit"
                    wire:loading.attr="disabled"
                    wire:target="submit"
                    class="dc-submit"
                >
                    <span wire:loading.remove wire:target="submit" class="dc-submit__label">
                        Create Deposit
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </span>
                    <span wire:loading wire:target="submit" class="dc-submit__label">
                        <svg class="dc-spin" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                        Opening Invoice…
                    </span>
                </button>
            @endif

        @endif

        {{-- ── Modal ───────────────────────────────────────────────────────── --}}
        @if($showModal && $deposit)
            <x-deposit.payment-modal
                :pay-address="$deposit->pay_address"
                :crypto-amount="$deposit->crypto_amount"
                :crypto-currency="$deposit->crypto_currency"
                :network="$deposit->network"
                :amount-usd="$deposit->amount_usd"
                :expires-at="$deposit->expires_at"
                cta-label="Track Live Payment"
                cta-action="goToTracker"
            />
        @endif

        {{-- ── History panel ───────────────────────────────────────────────── --}}
        <div class="history-panel">
            <div class="history-panel__header">
                <h3>Deposit History</h3>
                <span class="history-panel__count">{{ $this->history->total() }} total</span>
            </div>

            @if($this->history->isEmpty())
                <div class="history-empty">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <p>No deposits yet. Add capital above to fund your wallet.</p>
                </div>
            @else
                <div class="history-table">
                    <div class="history-row history-row--head">
                        <span>Amount</span>
                        <span>Status</span>
                        <span>Date</span>
                        <span></span>
                    </div>

                    @foreach($this->history as $dep)
                        <x-billing.history-row
                            plan-label="Wallet Deposit"
                            :amount="$dep->amount_usd"
                            status="{{ $dep->status->value }}"
                            :date="$dep->created_at"
                            meta="{{ strtoupper($dep->crypto_currency) }}"
                            :track-url="in_array($dep->status->value, ['pending', 'waiting', 'confirming']) ? route('dashboard.deposit.track', $dep) : null"
                        />
                    @endforeach
                </div>

                <div class="history-pagination">
                    {{ $this->history->links() }}
                </div>
            @endif
        </div>

    </div>
</div>