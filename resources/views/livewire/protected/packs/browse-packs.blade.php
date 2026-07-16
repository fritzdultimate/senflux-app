{{-- resources/views/livewire/protected/packs/browse-packs.blade.php --}}
@push('styles')
    @vite('resources/css/trading-bots.css')
@endpush

<div class="tb">

    <div class="tb-intro">
        <h2 class="tb-intro__title">Senflux Packs</h2>
        <p class="tb-intro__desc">
            Every pack unlocks a set of capital allocation slots. Fund a slot and the Senflux
            Bot deploys it into qualifying formations — your historical outcome range is set
            the moment you buy, by the tier you choose.
        </p>
    </div>

    @php
        $current = $this->activeSubscription;
        $isRenewalWindow = $current && $current->isInRenewalWindow();
    @endphp

    @if($current)
        <a href="{{ route('dashboard.packs.index') }}" wire:navigate class="tb-active-banner">
            <span class="tb-active-banner__dot"></span>
            @if($isRenewalWindow)
                Your {{ $current->packTier->name }} pack has matured —
                <strong>decide what's next in My Packs &rarr;</strong>
            @else
                You're on {{ $current->packTier->name }} —
                <strong>view your pack &rarr;</strong>
            @endif
        </a>
    @endif

    @if($errorMessage)
        <p style="color:#f87171; font-size:14px; margin: 0 0 16px;">{{ $errorMessage }}</p>
    @endif

    <div class="tb-grid">
        @php
            $tierColors = ['#60a5fa', '#9B7DFF', '#fbbf24'];
        @endphp
        @foreach($this->tiers as $i => $tier)
            @php
                $color = $tierColors[$i] ?? '#6b7280';

                $isCurrentOrLower = $current && $tier->price <= $current->packTier->price;
                $isUpgradeTarget = $current && !$isRenewalWindow && !$isCurrentOrLower;
                $upgradeCost = $isUpgradeTarget ? $current->estimateUpgradeCost($tier) : null;
                $isSameTier = $current && $tier->id === $current->pack_tier_id;

            @endphp
            <div class="tb-card {{ ($isCurrentOrLower || $isRenewalWindow) ? 'tb-card--disabled' : '' }}" style="--accent: {{ $color }}" wire:key="tier-{{ $tier->id }}">

                <div class="tb-card__icon" style="background: {{ $color }}1a; border-color: {{ $color }}44">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="1.5">
                        <path d="M21 8l-9-5-9 5 9 5 9-5z"/>
                        <path d="M3 8v8l9 5 9-5V8"/>
                        <path d="M12 13v8"/>
                    </svg>
                </div>

                @if($isSameTier)
                    <span class="tb-card__current-badge">Your current tier</span>
                @endif

                <h3 class="tb-card__name">{{ $tier->name }}</h3>

                <p class="tb-card__rate" style="color: {{ $color }}">
                    ${{ number_format($tier->price, 0) }} access fee
                </p>

                <div class="tb-card__range">
                    @if($tier->historical_outcome_min)
                        {{ $tier->historical_outcome_min }}–{{ $tier->historical_outcome_max }}%<span>historical</span>
                    @endif
                     · {{ $tier->duration_days }} days · {{ $tier->slot_count }} slots
                </div>

                <div class="tb-card__range">
                    ${{ number_format($tier->min_capital_per_slot, 0) }}{{ $tier->max_capital_per_slot ? ' – $'.number_format($tier->max_capital_per_slot, 0) : '+' }} per slot
                </div>

                @if(!empty($tier->features))
                    <ul class="tb-card__features">
                        @foreach($tier->features as $feature)
                            <li>
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($isRenewalWindow)
                    <button
                        type="button"
                        disabled
                        title="Your pack has matured — manage the renewal decision from My Packs first"
                        class="tb-card__cta tb-card__cta--disabled"
                    >
                        Manage in My Packs
                    </button>
                @elseif($isCurrentOrLower)
                    <button
                        type="button"
                        disabled
                        title="{{ $isSameTier ? 'This is your current tier' : 'Lower than your current tier — upgrade instead of downgrading' }}"
                        class="tb-card__cta tb-card__cta--disabled"
                    >
                        {{ $isSameTier ? 'Current tier' : 'Not available' }}
                    </button>
                @elseif($isUpgradeTarget)
                    <button
                        type="button"
                        wire:click="upgradeNow({{ $tier->id }})"
                        wire:confirm="Upgrade to {{ $tier->name }} now for ${{ number_format($upgradeCost, 2) }} (credit applied for your {{ $current->remainingDays() }} unused days on {{ $current->packTier->name }})? Your pack restarts a fresh {{ $tier->duration_days }}-day cycle at the new tier, effective today. Your existing slots keep earning uninterrupted."
                        class="tb-card__cta"
                        style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc); border: none; cursor: pointer; width: 100%;"
                    >
                        Upgrade to {{ $tier->name }} — ${{ number_format($upgradeCost, 2) }}
                    </button>
                @else
                    <button
                        type="button"
                        wire:click="buy({{ $tier->id }})"
                        wire:confirm="Buy {{ $tier->name }} for ${{ number_format($tier->price, 0) }}?"
                        class="tb-card__cta"
                        style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc); border: none; cursor: pointer; width: 100%;"
                    >
                        Buy {{ $tier->name }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <div class="tb-note">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        <p>A slot only earns once it's deployed into a qualifying formation. See exactly what's
            happening with your capital on the <a href="{{ route('dashboard.packs.index') }}" wire:navigate>My Packs</a> page.</p>
    </div>

</div>