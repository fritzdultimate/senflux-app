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

    @if($this->userHasActivePack)
        <a href="{{ route('dashboard.packs.index') }}" wire:navigate class="tb-active-banner">
            <span class="tb-active-banner__dot"></span>
            You have an active pack running —
            <strong>view your packs &rarr;</strong>
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
            @php $color = $tierColors[$i] ?? '#6b7280'; @endphp
            <div class="tb-card" style="--accent: {{ $color }}" wire:key="tier-{{ $tier->id }}">

                <div class="tb-card__icon" style="background: {{ $color }}1a; border-color: {{ $color }}44">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="1.5">
                        <path d="M21 8l-9-5-9 5 9 5 9-5z"/>
                        <path d="M3 8v8l9 5 9-5V8"/>
                        <path d="M12 13v8"/>
                    </svg>
                </div>

                <h3 class="tb-card__name">{{ $tier->name }}</h3>

                <p class="tb-card__rate" style="color: {{ $color }}">
                    @if($tier->historical_outcome_min)
                        {{ $tier->historical_outcome_min }}–{{ $tier->historical_outcome_max }}%<span>historical</span>
                    @else
                        ${{ number_format($tier->price, 0) }}<span>access fee</span>
                    @endif
                </p>

                <div class="tb-card__range">
                    ${{ number_format($tier->price, 0) }} access fee · {{ $tier->duration_days }} days · {{ $tier->slot_count }} slots
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

                <button
                    type="button"
                    wire:click="buy({{ $tier->id }})"
                    wire:confirm="Buy {{ $tier->name }} for ${{ number_format($tier->price, 0) }}?"
                    class="tb-card__cta"
                    style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc); border: none; cursor: pointer; width: 100%;"
                >
                    Buy {{ $tier->name }}
                </button>
            </div>
        @endforeach
    </div>

    <div class="tb-note">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        <p>A slot only earns once it's deployed into a qualifying formation. See exactly what's
            happening with your capital on the <a href="{{ route('dashboard.packs.index') }}" wire:navigate>My Packs</a> page.</p>
    </div>

</div>