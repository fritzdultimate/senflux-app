<div>

    @vite('resources/css/deposit.css')
    @vite('resources/css/deposit-additions.css')
    @vite('resources/css/billing.css')

    <div
        wire:poll.10000ms="refreshIfPending"
        x-data="{
            intervals: [
                { value: 'monthly',   label: 'Monthly',   save: ''         },
                { value: 'quarterly', label: 'Quarterly', save: 'Save 15%' },
                { value: 'yearly',    label: 'Yearly',    save: 'Save 40%' },
            ],
        }"
        class="deposit-create"
    >

        {{-- ── Active subscription notice ──────────────────────────────────── --}}
        @if($this->currentSubscription)
            <div class="sub-active-notice">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
                <div>
                    <strong>{{ $this->currentSubscription->planConfig->label }} Plan Active</strong>
                    <span>Expires {{ $this->currentSubscription->expires_at->diffForHumans() }}</span>
                </div>
                <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="sub-active-notice__cta">
                    Deposit Capital →
                </a>
            </div>
        @endif

        {{-- ── Pending gate — blocks new subscription if one is in flight ────── --}}
        @if($this->pendingSubscription)
            <x-billing.pending-gate
                :record="$this->pendingSubscription"
                :can-cancel="$this->canCancelPending"
                plan-label="{{ $this->pendingSubscription->planConfig->label }} Plan"
                :amount="$this->pendingSubscription->amount_paid"
                :error="$errorMessage"
            />
        @else

            @if($errorMessage)
                <x-deposit.error :message="$errorMessage" wire:key="error-{{ md5($errorMessage) }}" />
            @endif

            {{-- Step 1: Plan --}}
            <div class="deposit-section">
                <p class="deposit-section-label">01 — Choose Plan</p>
                <div class="plan-grid">
                    @foreach($plans as $plan)
                        <button
                            type="button"
                            wire:click="selectPlan({{ $plan['id'] }})"
                            wire:loading.attr="disabled"
                            wire:target="selectPlan({{ $plan['id'] }})"
                            class="plan-card {{ $planId === $plan['id'] ? 'plan-card--active' : '' }}"
                        >
                            @if($plan['is_popular'])
                                <span class="plan-badge">Most Popular</span>
                            @endif

                            <div class="plan-card__header">
                                <span class="plan-card__name">{{ $plan['label'] }}</span>
                                <span class="plan-card__rate">{{ $plan['rate_pct'] }}% <small>/ day</small></span>
                            </div>

                            <div class="plan-card__price">
                                <span class="plan-card__amount">
                                    ${{ number_format($plan[$interval . '_price'], 2) }}
                                </span>
                                <span class="plan-card__interval">/ {{ $interval }}</span>
                            </div>

                            <div class="plan-card__range">
                                ${{ number_format($plan['min_deposit'], 0) }} –
                                {{ $plan['max_deposit'] >= 999999 ? '∞' : '$'.number_format($plan['max_deposit'], 0) }}
                                capital
                            </div>

                            @if($planId === $plan['id'])
                                <div class="plan-card__check">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Step 2: Billing interval --}}
            @if($planId)
                <div class="deposit-section" wire:key="interval-section-{{ $planId }}">
                    <p class="deposit-section-label">02 — Billing Period</p>

                    <div class="interval-tabs">
                        <template x-for="opt in intervals" :key="opt.value">
                            <button
                                type="button"
                                x-on:click="$wire.selectInterval(opt.value)"
                                class="interval-tab"
                                :class="{ 'interval-tab--active': @js($interval) === opt.value }"
                            >
                                <span x-text="opt.label"></span>
                                <span class="interval-save" x-show="opt.save" x-text="opt.save"></span>
                            </button>
                        </template>
                    </div>

                    <div class="sub-price-display">
                        <span class="sub-price-amount">${{ number_format($selectedPlanPrice, 2) }}</span>
                        <span class="sub-price-interval">/ {{ $interval }}</span>
                    </div>
                </div>
            @endif

            {{-- Submit --}}
            @if($planId)
                <button
                    type="button"
                    class="deposit-submit-btn"
                    wire:click="subscribe"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50"
                    wire:target="subscribe"
                >
                    <span wire:loading.remove wire:target="subscribe">
                        Subscribe & Pay
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="subscribe" class="btn-loading">
                        <svg class="spin" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                        </svg>
                        Processing…
                    </span>
                </button>
            @endif

        @endif

        {{-- ── Modal ───────────────────────────────────────────────────────── --}}
        @if($showModal && $payAddress)
            <x-deposit.payment-modal
                :pay-address="$payAddress"
                :crypto-amount="$cryptoAmount"
                :crypto-currency="$cryptoCurrency"
                :network="$cryptoCurrency"
                :amount-usd="$invoiceAmountUsd"
                :expires-at="$expiresAt ? \Carbon\Carbon::parse($expiresAt) : null"
                cta-label="Track Subscription Payment"
                cta-action="goToTracker"
            />
        @endif

        {{-- ── History panel ───────────────────────────────────────────────── --}}
        <div class="history-panel">
            <div class="history-panel__header">
                <h3>Subscription History</h3>
                <span class="history-panel__count">{{ $this->history->total() }} total</span>
            </div>

            @if($this->history->isEmpty())
                <div class="history-empty">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M9 11l3 3L22 4M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <p>No subscriptions yet. Choose a plan above to get started.</p>
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

                    @foreach($this->history as $sub)
                        <x-billing.history-row
                            plan-label="{{ $sub->planConfig->label }}"
                            :amount="$sub->amount_paid"
                            status="{{ $sub->status }}"
                            :date="$sub->created_at"
                            meta="{{ ucfirst($sub->interval->value) }}"
                            :track-url="in_array($sub->status, ['pending', 'waiting']) ? route('dashboard.subscription.track', $sub) : null"
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
