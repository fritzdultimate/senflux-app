<div>
    @vite('resources/css/dc.css')

    <div class="dc">
        <div class="dc-console-header">
            <div class="dc-console-header__label">
                <span class="dc-console-header__eyebrow">{{ $this->subscription->packTier->name }}</span>
                <h2>Pack #{{ $this->subscription->id }} — {{ $this->subscription->status->label() }}</h2>
            </div>
        </div>

        @if($errorMessage)
            <div class="dc-alert">{{ $errorMessage }}</div>
        @endif
        @if($successMessage)
            <div class="dc-alert" style="border-color:#22c55e;color:#22c55e;">{{ $successMessage }}</div>
        @endif

        <div class="dc-readout">
            <div class="dc-readout__item">
                <span class="dc-readout__label">Purchased</span>
                <span class="dc-readout__value">{{ $this->subscription->purchased_at->format('M j, Y') }}</span>
            </div>
            <div class="dc-readout__divider"></div>
            <div class="dc-readout__item">
                <span class="dc-readout__label">Matures</span>
                <span class="dc-readout__value">{{ $this->subscription->matures_at->format('M j, Y') }}</span>
            </div>
            @if($this->subscription->renewal_window_ends_at)
                <div class="dc-readout__divider"></div>
                <div class="dc-readout__item">
                    <span class="dc-readout__label">Renewal window ends</span>
                    <span class="dc-readout__value dc-readout__value--accent">{{ $this->subscription->renewal_window_ends_at->format('M j, Y') }}</span>
                </div>
            @endif
        </div>

        {{-- Refund (only shows if actually eligible) --}}
        @if($this->subscription->isEligibleForRefund())
            <div class="dc-panel">
                <p>No slots funded yet — you're within the 3-day refund window.</p>
                <button type="button" wire:click="requestRefund" wire:confirm="Refund this pack purchase?" class="dc-submit dc-submit--ghost">
                    Request Refund
                </button>
            </div>
        @endif

        {{-- Renewal window actions --}}
        @if($this->subscription->isInRenewalWindow())
            <div class="dc-panel">
                <div class="dc-panel__header">
                    <span class="dc-panel__title">This pack has matured — choose what happens next</span>
                </div>

                <div class="dc-currency-rail">
                    <button type="button" wire:click="withdraw" wire:confirm="Withdraw all capital to your wallet?" class="dc-currency">
                        <span class="dc-currency__code">Withdraw</span>
                        <span class="dc-currency__label">Return capital to wallet</span>
                    </button>
                    <button type="button" wire:click="continueCycle" wire:confirm="Continue capital into a new cycle?" class="dc-currency">
                        <span class="dc-currency__code">Continue</span>
                        <span class="dc-currency__label">Same tier, new cycle</span>
                    </button>
                    <button type="button" wire:click="autoCompound" wire:confirm="Restake profit alongside capital into a new cycle?" class="dc-currency">
                        <span class="dc-currency__code">Auto-Compound</span>
                        <span class="dc-currency__label">Capital + profit, new cycle</span>
                    </button>
                </div>

                @if($this->upgradeOptions->isNotEmpty())
                    <p class="dc-subtext">Or upgrade to a higher tier:</p>
                    <div class="dc-currency-rail">
                        @foreach($this->upgradeOptions as $opt)
                            <button type="button" wire:click="startUpgrade({{ $opt->id }})" class="dc-currency">
                                <span class="dc-currency__code">{{ $opt->name }}</span>
                                <span class="dc-currency__label">${{ number_format($opt->price, 0) }}</span>
                            </button>
                        @endforeach
                    </div>

                    @if($upgradingToTierId)
                        <label class="dc-subtext">
                            <input type="checkbox" wire:model="upgradeCompound"> Also compound profit into the upgrade
                        </label>
                        <button type="button" wire:click="confirmUpgrade" class="dc-submit">Confirm Upgrade</button>
                    @endif
                @endif
            </div>
        @endif

        {{-- Slots --}}
        <div class="dc-panel">
            <div class="dc-panel__header">
                <span class="dc-panel__title">Slots</span>
            </div>

            <div class="dc-active-list">
                @foreach($this->subscription->slots as $slot)
                    <div class="dc-active-row" wire:key="slot-{{ $slot->id }}">
                        <div>
                            <span class="dc-active-row__plan">Slot #{{ $slot->slot_number }}</span>
                            <span class="dc-active-row__since">{{ $slot->status->label() }}</span>
                        </div>

                        @if($slot->status->value === 'empty')
                            @if($fundingSlotId === $slot->id)
                                <div style="display:flex; gap:8px; align-items:center;">
                                    <input type="number" wire:model="fundAmount"
                                        min="{{ $this->subscription->packTier->min_capital_per_slot }}"
                                        max="{{ $this->subscription->packTier->max_capital_per_slot ?? '' }}"
                                        class="dc-amount-field__input" style="width:120px;">
                                    <button type="button" wire:click="fundSlot" class="dc-submit">Fund</button>
                                    <button type="button" wire:click="cancelFunding" class="dc-submit dc-submit--ghost">Cancel</button>
                                </div>
                            @else
                                <button type="button" wire:click="startFunding({{ $slot->id }})" class="dc-submit">
                                    Fund Slot
                                </button>
                            @endif
                        @elseif($slot->status->value === 'funded')
                            <div class="dc-active-row__amounts">
                                <span class="dc-active-row__principal">${{ number_format($slot->capital_amount, 2) }}</span>
                                <span class="dc-active-row__earned">+${{ number_format($slot->realized_profit, 2) }} earned</span>
                            </div>
                            <button type="button" wire:click="earlyExit({{ $slot->id }})"
                                wire:confirm="Early exit forfeits 8% of capital as a fee. Continue?"
                                class="dc-submit dc-submit--ghost">
                                Early Exit
                            </button>
                        @else
                            <div class="dc-active-row__amounts">
                                <span class="dc-active-row__principal">${{ number_format($slot->capital_amount, 2) }} returned</span>
                                @if($slot->was_early_exit)
                                    <span class="dc-active-row__earned">-${{ number_format($slot->early_exit_fee_charged, 2) }} fee</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <a href="{{ route('dashboard.packs.index') }}" wire:navigate class="dc-subtext">&larr; Back to My Packs</a>
    </div>
</div>
