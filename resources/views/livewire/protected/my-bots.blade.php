{{-- resources/views/livewire/protected/my-bots.blade.php --}}
@vite('resources/css/my-bots.css')

<div class="myb" wire:poll.30000ms="refresh">

    {{-- ── Stat strip ───────────────────────────────────────────────────── --}}
    <div class="myb-stats">
        <div class="myb-stat">
            <p class="myb-stat__label">Active Bots</p>
            <p class="myb-stat__value">{{ $this->activeBotCount }}</p>
        </div>
        <div class="myb-stat">
            <p class="myb-stat__label">Total Earned (Active)</p>
            <p class="myb-stat__value myb-stat__value--green">+${{ number_format($this->totalEarningRunning, 2) }}</p>
        </div>
        <div class="myb-stat myb-stat--cta">
            <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="myb-deploy-link">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Deploy New Bot
            </a>
        </div>
    </div>

    {{-- ── Bot list ─────────────────────────────────────────────────────── --}}
    @if($this->bots->isEmpty())
        <div class="myb-empty">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <rect x="3" y="4" width="18" height="16" rx="2"/>
                <path d="M9 4V3M15 4V3M3 9h3M18 9h3M9 14v1M15 14v1"/>
            </svg>
            <p>No bots deployed yet.</p>
            <p class="myb-empty__sub">Every active deposit runs as a bot. Deposit to deploy your first one.</p>
            <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="myb-empty__cta">Deploy a bot →</a>
        </div>
    @else
        <div class="myb-grid">
            @foreach($this->bots as $bot)
                @php
                    $planColors = [
                        'core' => '#60a5fa',
                        'pro'  => '#9B7DFF',
                        'apex' => '#fbbf24',
                    ];
                    $color = $planColors[$bot['plan']->value] ?? '#6b7280';
                @endphp
                <div class="myb-card {{ $bot['is_running'] ? 'myb-card--running' : '' }}">
                    <div class="myb-card__head">
                        <div class="myb-card__icon" style="background: {{ $color }}1a; border-color: {{ $color }}44">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="1.6">
                                <rect x="3" y="6" width="18" height="14" rx="2"/>
                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                <circle cx="9" cy="13" r="1.5"/>
                                <circle cx="15" cy="13" r="1.5"/>
                            </svg>
                        </div>
                        <div class="myb-card__title-wrap">
                            <span class="myb-card__title">{{ $bot['bot_name'] }}</span>
                            <span class="myb-card__status" style="color: {{ $bot['is_running'] ? '#22c55e' : '#6b7280' }}">
                                <span class="myb-card__dot" style="background: {{ $bot['is_running'] ? '#22c55e' : '#6b7280' }}"></span>
                                {{ $bot['is_running'] ? 'Running' : 'Finished' }}
                            </span>
                        </div>
                    </div>

                    <div class="myb-card__stats">
                        <div class="myb-card__stat">
                            <span>Principal</span>
                            <strong>${{ number_format($bot['principal'], 2) }}</strong>
                        </div>
                        <div class="myb-card__stat">
                            <span>Earned</span>
                            <strong class="myb-card__stat--green">+${{ number_format($bot['earned'], 2) }}</strong>
                        </div>
                        <div class="myb-card__stat">
                            <span>Rate</span>
                            <strong>{{ number_format($bot['daily_rate'] * 100, 2) }}%/day</strong>
                        </div>
                    </div>

                    <div class="myb-card__footer">
                        <span>Deployed {{ $bot['deployed_at']?->format('M j, Y') }}</span>
                        <span>{{ $bot['days_running'] }} days running</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
