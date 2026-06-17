<div>
    
    @vite('resources/css/dc.css')
    @vite('resources/css/billing.css')
    
    <div class="dc" wire:poll.10000ms="refreshIfPending">

        @unless(auth()->user()->has_active_subscription)
            <div class="dc-gate">
                <div class="dc-gate__signal">
                    <span class="dc-gate__signal-dot"></span>
                    <span class="dc-gate__signal-dot"></span>
                    <span class="dc-gate__signal-dot"></span>
                </div>
                <h3>No Active Formation Channel</h3>
                <p>A plan subscription opens your formation channel — the link between your capital and the intelligence layer.</p>
                <a href="{{ route('dashboard.subscribe') }}" wire:navigate class="dc-gate__btn">
                    Open a Channel
                </a>
            </div>
        @else

            {{-- ── Pending gate — blocks new deposit if one is in flight ───────── --}}
            @if($this->pendingDeposit)
                <x-billing.pending-gate
                    :record="$this->pendingDeposit"
                    :can-cancel="$this->canCancelPending"
                    plan-label="{{ $this->pendingDeposit->planConfig->label }} Plan"
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
                        <span class="dc-console-header__eyebrow">Capital Deployment</span>
                        <h2>Choose where your capital is positioned</h2>
                    </div>
                    <div class="dc-console-header__pulse">
                        <span class="dc-pulse-dot"></span>
                        Formation feed live
                    </div>
                </div>

                <div class="dc-tier-rail">
                    @foreach($plans as $plan)
                        <button
                            type="button"
                            wire:click="selectPlan({{ $plan->id }})"
                            wire:loading.attr="disabled"
                            wire:target="selectPlan({{ $plan->id }})"
                            class="dc-tier {{ $planId === $plan->id ? 'dc-tier--active' : '' }}"
                            wire:key="plan-{{ $plan->id }}"
                        >
                            <div class="dc-tier__top">
                                <span class="dc-tier__name">{{ $plan->label }}</span>
                                @if($plan->is_popular)
                                    <span class="dc-tier__tag">Most deployed</span>
                                @endif
                            </div>

                            <div class="dc-tier__rate">
                                <span class="dc-tier__rate-value">{{ number_format($plan->daily_rate_max * 100, 1) }}%</span>
                                <span class="dc-tier__rate-label">daily ceiling</span>
                            </div>

                            <div class="dc-tier__bar-wrap">
                                <div class="dc-tier__bar" style="width: {{ min(100, $plan->daily_rate_max * 100 / 1.3 * 100) }}%"></div>
                            </div>

                            <div class="dc-tier__meta">
                                <span>${{ number_format($plan->min_deposit, 0) }}–{{ $plan->max_deposit >= 999999 ? '∞' : '$'.number_format($plan->max_deposit / 1000, 0).'k' }}</span>
                                <span class="dc-tier__price">${{ number_format($plan->monthly_price, 0) }}/mo</span>
                            </div>

                            @if($planId === $plan->id)
                                <div class="dc-tier__active-mark">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>

                @if($planId && $this->selectedPlan)
                    @php $plan = $this->selectedPlan; @endphp
                    <div class="dc-panel" wire:key="amount-panel-{{ $planId }}">
                        <div class="dc-panel__header">
                            <span class="dc-panel__index">02</span>
                            <span class="dc-panel__title">Capital amount</span>
                        </div>

                        <div class="dc-amount-row">
                            <div class="dc-amount-field">
                                <span class="dc-amount-field__prefix">$</span>
                                <input
                                    type="number"
                                    wire:model.live.debounce.350ms="amountUsd"
                                    min="{{ $plan->min_deposit }}"
                                    max="{{ $plan->max_deposit }}"
                                    step="0.01"
                                    class="dc-amount-field__input"
                                    placeholder="0.00"
                                    autocomplete="off"
                                />
                            </div>
                            <div class="dc-amount-range">
                                <span>Min ${{ number_format($plan->min_deposit, 0) }}</span>
                                @if($plan->max_deposit < 999999)
                                    <span>Max ${{ number_format($plan->max_deposit, 0) }}</span>
                                @endif
                            </div>
                        </div>

                        @error('amountUsd')
                            <p class="dc-field-error">{{ $message }}</p>
                        @enderror

                        @if($amountUsd > 0)
                            <div class="dc-readout">
                                <div class="dc-readout__item">
                                    <span class="dc-readout__label">Est. daily</span>
                                    <span class="dc-readout__value">${{ number_format($this->estimatedDaily, 2) }}</span>
                                </div>
                                <div class="dc-readout__divider"></div>
                                <div class="dc-readout__item">
                                    <span class="dc-readout__label">Est. monthly</span>
                                    <span class="dc-readout__value dc-readout__value--accent">${{ number_format($this->estimatedMonthly, 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if($planId && $amountUsd >= ($this->selectedPlan->min_deposit ?? 0))
                    <div class="dc-panel" wire:key="crypto-panel-{{ $planId }}">
                        <div class="dc-panel__header">
                            <span class="dc-panel__index">03</span>
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

                @if($planId && $amountUsd >= ($this->selectedPlan->min_deposit ?? 0) && $cryptoCurrency)
                    <button
                        type="button"
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        wire:target="submit"
                        class="dc-submit"
                    >
                        <span wire:loading.remove wire:target="submit" class="dc-submit__label">
                            Deploy Capital
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

            {{-- ── Active deposits — currently earning ─────────────────────────── --}}
            @if($this->activeDeposits->isNotEmpty())
                <div class="dc-panel" wire:key="active-deposits-panel">
                    <div class="dc-panel__header">
                        <span class="dc-panel__index"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></span>
                        <span class="dc-panel__title">Currently deployed</span>
                    </div>

                    <div class="dc-active-list">
                        @foreach($this->activeDeposits as $ad)
                            <div class="dc-active-row">
                                <div>
                                    <span class="dc-active-row__plan">{{ $ad->planConfig->label }}</span>
                                    <span class="dc-active-row__since">since {{ $ad->activated_at->format('M j, Y') }}</span>
                                </div>
                                <div class="dc-active-row__amounts">
                                    <span class="dc-active-row__principal">${{ number_format($ad->amount_usd, 2) }}</span>
                                    <span class="dc-active-row__earned">+${{ number_format($ad->total_earnings, 2) }} earned</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
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
                        <p>No deposits yet. Deploy capital above to start earning.</p>
                    </div>
                @else
                    <div class="history-table">
                        <div class="history-row history-row--head">
                            <span>Plan</span>
                            <span>Amount</span>
                            <span>Status</span>
                            <span>Date</span>
                            <span></span>
                        </div>

                        @foreach($this->history as $dep)
                            <x-billing.history-row
                                plan-label="{{ $dep->planConfig->label }}"
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

        @endunless
    </div>
</div>
