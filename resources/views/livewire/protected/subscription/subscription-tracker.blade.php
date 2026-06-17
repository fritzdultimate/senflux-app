{{-- resources/views/livewire/protected/subscription/subscription-tracker.blade.php --}}
@vite('resources/css/deposit.css')

<div class="tracker-page" wire:poll.8000ms="poll">

    @php
        $subscription = $this->subscription;
    @endphp

    <div class="tracker-bg" aria-hidden="true">
        <div class="tracker-bg__grid"></div>
        @for($i = 0; $i < 6; $i++)
            <div class="tracker-bg__orb tracker-bg__orb--{{ $i }}"></div>
        @endfor
    </div>

    <div class="tracker-inner">

        <div class="tracker-header">
            <a href="{{ route('dashboard') }}" wire:navigate class="tracker-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Dashboard
            </a>
            <span class="tracker-network-badge">{{ $subscription->planConfig->label }} Plan</span>
        </div>

        <div class="tracker-card {{ $confirmed ? 'tracker-card--confirmed' : ($failed ? 'tracker-card--failed' : '') }}">

            <div class="tracker-status-icon">
                @if($confirmed)
                    <div class="status-icon status-icon--success">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                @elseif($failed)
                    <div class="status-icon status-icon--error">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </div>
                @else
                    <div class="status-icon status-icon--loading">
                        <svg class="spin-slow" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    </div>
                @endif
            </div>

            <div class="tracker-status-text">
                @if($confirmed)
                    <h1 class="tracker-status-title tracker-status-title--success">Subscription Active</h1>
                    <p class="tracker-status-desc">Your {{ $subscription->planConfig->label }} plan is now active. You can deposit capital and start earning.</p>
                @elseif($failed)
                    <h1 class="tracker-status-title tracker-status-title--error">Payment Failed</h1>
                    <p class="tracker-status-desc">The subscription payment could not be processed. Please try again.</p>
                @else
                    <h1 class="tracker-status-title">Confirming Subscription Payment</h1>
                    <p class="tracker-status-desc">Watching the blockchain for your payment. This updates automatically.</p>
                @endif
            </div>

            <div class="tracker-amount-badge">
                <span class="tracker-amount-usd">${{ number_format($subscription->amount_paid, 2) }} — {{ ucfirst($subscription->interval->value) }}</span>
            </div>

        </div>

        <div class="tracker-actions">
            @if($confirmed)
                <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="tracker-btn tracker-btn--primary">
                    Deposit Capital
                </a>
            @elseif($failed)
                <a href="{{ route('dashboard.subscribe') }}" wire:navigate class="tracker-btn tracker-btn--primary">
                    Try Again
                </a>
            @else
                <a href="{{ route('dashboard') }}" wire:navigate class="tracker-btn tracker-btn--ghost">
                    Go to Dashboard
                </a>
            @endif
        </div>

        <p class="tracker-security-note">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Do not close this tab until the payment is confirmed.
        </p>

    </div>
</div>