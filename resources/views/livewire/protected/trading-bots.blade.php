{{-- resources/views/livewire/protected/trading-bots.blade.php --}}
@vite('resources/css/trading-bots.css')

<div class="tb">

    <div class="tb-intro">
        <h2 class="tb-intro__title">Trading Bots</h2>
        <p class="tb-intro__desc">
            Every Senflux plan runs as an automated bot against your deposit.
            Choose the bot that matches your strategy — your deposit's daily rate
            is locked in the moment your bot deploys.
        </p>
    </div>

    @if($this->userHasActiveBot)
        <a href="{{ route('dashboard.bots.mine') }}" wire:navigate class="tb-active-banner">
            <span class="tb-active-banner__dot"></span>
            You have an active bot running —
            <strong>view your bots →</strong>
        </a>
    @endif

    <div class="tb-grid">
        @foreach($this->bots as $bot)
            @php
                $planColors = [
                    'core' => '#60a5fa',
                    'pro'  => '#9B7DFF',
                    'apex' => '#fbbf24',
                ];
                $color = $planColors[$bot['plan']->value] ?? '#6b7280';
            @endphp
            <div class="tb-card {{ $bot['is_user_plan'] ? 'tb-card--owned' : '' }}" style="--accent: {{ $color }}">
                @if($bot['is_user_plan'])
                    <span class="tb-card__badge">Your Plan</span>
                @endif

                <div class="tb-card__icon" style="background: {{ $color }}1a; border-color: {{ $color }}44">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="1.5">
                        <rect x="3" y="6" width="18" height="14" rx="2"/>
                        <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        <circle cx="9" cy="13" r="1.5"/>
                        <circle cx="15" cy="13" r="1.5"/>
                    </svg>
                </div>

                <h3 class="tb-card__name">{{ $bot['name'] }}</h3>
                <p class="tb-card__rate" style="color: {{ $color }}">
                    up to {{ number_format($bot['daily_rate_max'] * 100, 2) }}%<span>/day</span>
                </p>

                <div class="tb-card__range">
                    ${{ number_format($bot['min_deposit'], 0) }} – ${{ number_format($bot['max_deposit'], 0) }} deposit range
                </div>

                @if(!empty($bot['features']))
                    <ul class="tb-card__features">
                        @foreach($bot['features'] as $feature)
                            <li>
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <a
                    href="{{ route('dashboard.deposit.create') }}"
                    wire:navigate
                    class="tb-card__cta"
                    style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc)"
                >
                    Deploy {{ $bot['name'] }}
                </a>
            </div>
        @endforeach
    </div>

    <div class="tb-note">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>
        <p>Bot rates are scaled by current market formation state. See your active bot's real performance on the
            <a href="{{ route('dashboard.bots.mine') }}" wire:navigate>My Bots</a> page.</p>
    </div>

</div>
