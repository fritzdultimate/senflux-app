
@props([
    'plan',
    'selected' => false,
    'mode'     => 'deposit', // 'deposit' or 'subscribe'
    'interval' => 'monthly',
])

<button
    wire:click="{{ $mode === 'subscribe' ? 'selectPlan' : 'selectPlan' }}({{ $plan->id }})"
    type="button"
    class="plan-card {{ $selected ? 'plan-card--active' : '' }}"
>
    @if($plan->is_popular)
        <span class="plan-badge">Most Popular</span>
    @endif

    {{-- Name + daily rate --}}
    <div class="plan-card__header">
        <span class="plan-card__name">{{ $plan->label }}</span>
        <span class="plan-card__rate">
            {{ number_format($plan->daily_rate_max * 100, 1) }}%
            <small>/ day</small>
        </span>
    </div>

    {{-- Price --}}
    <div class="plan-card__price">
        @if($mode === 'subscribe')
            <span class="plan-card__amount">${{ number_format($plan->getPriceForInterval($interval), 0) }}</span>
            <span class="plan-card__interval">/ {{ $interval }}</span>
        @else
            <span class="plan-card__amount">${{ number_format($plan->monthly_price, 0) }}</span>
            <span class="plan-card__interval">/ mo</span>
        @endif
    </div>

    {{-- Participation range --}}
    <div class="plan-card__range">
        ${{ number_format($plan->min_deposit, 0) }} –
        {{ $plan->max_deposit >= 999999 ? '∞' : '$'.number_format($plan->max_deposit, 0) }}
        capital
    </div>

    @if($selected)
        <div class="plan-card__check">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
    @endif
</button>
