<div>
    @vite('resources/css/dc.css')

    <div class="dc">
        <div class="dc-console-header">
            <div class="dc-console-header__label">
                <span class="dc-console-header__eyebrow">Senflux Packs</span>
                <h2>Choose your access tier</h2>
            </div>
            <div class="dc-console-header__pulse">
                Wallet: ${{ number_format($this->walletBalance, 2) }}
            </div>
        </div>

        @if($errorMessage)
            <div class="dc-alert">{{ $errorMessage }}</div>
        @endif

        <div class="dc-tier-rail">
            @foreach($this->tiers as $tier)
                <div class="dc-tier" wire:key="tier-{{ $tier->id }}">
                    <div class="dc-tier__top">
                        <span class="dc-tier__name">{{ $tier->name }}</span>
                    </div>

                    <div class="dc-tier__rate">
                        <span class="dc-tier__rate-value">${{ number_format($tier->price, 0) }}</span>
                        <span class="dc-tier__rate-label">access fee</span>
                    </div>

                    <div class="dc-tier__meta">
                        <span>{{ $tier->duration_days }} days · {{ $tier->slot_count }} slots</span>
                    </div>

                    <div class="dc-tier__meta">
                        <span>
                            ${{ number_format($tier->min_capital_per_slot, 0) }}{{ $tier->max_capital_per_slot ? ' – $'.number_format($tier->max_capital_per_slot, 0) : '+' }} / slot
                        </span>
                    </div>

                    @if($tier->historical_outcome_min)
                        <div class="dc-tier__meta">
                            <span>{{ $tier->historical_outcome_min }}% – {{ $tier->historical_outcome_max }}% historical</span>
                        </div>
                    @endif

                    @if($tier->features)
                        <ul class="dc-tier__features">
                            @foreach($tier->features as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if($confirmingTierId === $tier->id)
                        <div class="dc-tier__confirm">
                            <p>Buy {{ $tier->name }} for ${{ number_format($tier->price, 0) }}?</p>
                            <button type="button" wire:click="buy" class="dc-submit">Confirm Purchase</button>
                            <button type="button" wire:click="cancelConfirm" class="dc-submit dc-submit--ghost">Cancel</button>
                        </div>
                    @else
                        <button type="button" wire:click="confirmBuy({{ $tier->id }})" class="dc-submit">
                            Buy {{ $tier->name }}
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <p class="dc-subtext">
            Already have a pack? <a href="{{ route('dashboard.packs.index') }}" wire:navigate>View your packs</a>.
        </p>
    </div>
</div>
