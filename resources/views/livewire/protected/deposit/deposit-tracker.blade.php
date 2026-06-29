
<div class="tracker-page" wire:poll.8000ms="poll">
    @push('styles')
        @vite(['resources/css/deposit.css'])
    @endpush

    @php
        $deposit = $this->deposit;
        $status  = \App\Enums\DepositStatus::from($deposit->status->value);
    @endphp

    {{-- ── Background particle effect (pure CSS) ───────────────────────── --}}
    <div class="tracker-bg" aria-hidden="true">
        <div class="tracker-bg__grid"></div>
        @for($i = 0; $i < 6; $i++)
            <div class="tracker-bg__orb tracker-bg__orb--{{ $i }}"></div>
        @endfor
    </div>

    <div class="tracker-inner">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="tracker-header">
            <a href="{{ route('dashboard') }}" wire:navigate class="tracker-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Dashboard
            </a>
            <div class="tracker-header__meta">
                <span class="tracker-deposit-id">#{{ str_pad($deposit->id, 6, '0', STR_PAD_LEFT) }}</span>
                <span class="tracker-network-badge">{{ strtoupper($deposit->network ?? $deposit->crypto_currency) }}</span>
            </div>
        </div>

        {{-- ── Main status card ────────────────────────────────────────── --}}
        <div class="tracker-card {{ $confirmed ? 'tracker-card--confirmed' : ($failed || $expired ? 'tracker-card--failed' : '') }}">

            {{-- Status icon --}}
            <div class="tracker-status-icon">
                @if($confirmed)
                    <div class="status-icon status-icon--success">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                @elseif($failed)
                    <div class="status-icon status-icon--error">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </div>
                @elseif($expired)
                    <div class="status-icon status-icon--warning">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                @else
                    <div class="status-icon status-icon--loading">
                        <svg class="spin-slow" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    </div>
                @endif
            </div>

            {{-- Title & description --}}
            <div class="tracker-status-text">
                @if($confirmed)
                    <h1 class="tracker-status-title tracker-status-title--success">Payment Confirmed</h1>
                    <p class="tracker-status-desc">Your wallet has been credited. Deploy this capital into a Senflux Pack whenever you're ready.</p>
                @elseif($failed)
                    <h1 class="tracker-status-title tracker-status-title--error">Payment Failed</h1>
                    <p class="tracker-status-desc">The payment could not be processed. Please try a new deposit or contact support.</p>
                @elseif($expired)
                    <h1 class="tracker-status-title tracker-status-title--warning">Invoice Expired</h1>
                    <p class="tracker-status-desc">The payment window has closed. Create a new deposit to try again.</p>
                @else
                    <h1 class="tracker-status-title">Monitoring Payment</h1>
                    <p class="tracker-status-desc">Watching the blockchain for your transaction. This page updates automatically.</p>
                @endif
            </div>

            {{-- Amount display --}}
            <div class="tracker-amount-badge">
                <span class="tracker-amount-crypto">{{ number_format((float)$deposit->crypto_amount, 8) }} {{ strtoupper($deposit->crypto_currency) }}</span>
                <span class="tracker-amount-sep">·</span>
                <span class="tracker-amount-usd">${{ number_format($deposit->amount_usd, 2) }}</span>
            </div>

            {{-- ── Progress steps ──────────────────────────────────────── --}}
            @if(!$failed && !$expired)
            <div class="tracker-steps">
                @foreach($this->statusSteps as $i => $step)
                    <div class="tracker-step {{ $step['completed'] ? 'tracker-step--completed' : '' }} {{ $step['active'] ? 'tracker-step--active' : '' }} {{ $step['pending'] ? 'tracker-step--pending' : '' }}">
                        <div class="tracker-step__node">
                            @if($step['completed'])
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            @elseif($step['active'])
                                <div class="tracker-step__pulse"></div>
                            @else
                                <div class="tracker-step__dot"></div>
                            @endif
                        </div>
                        @if($i < count($this->statusSteps) - 1)
                            <div class="tracker-step__line {{ $step['completed'] ? 'tracker-step__line--filled' : '' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Step labels --}}
            <div class="tracker-step-labels">
                @foreach($this->statusSteps as $step)
                    <span class="tracker-step-label {{ $step['active'] ? 'tracker-step-label--active' : '' }} {{ $step['completed'] ? 'tracker-step-label--completed' : '' }}">
                        {{ $step['label'] }}
                    </span>
                @endforeach
            </div>
            @endif

            {{-- Confirmation progress bar (shows during confirming state) --}}
            @if($deposit->status === 'confirming' && $deposit->required_confirmations > 0)
            <div class="tracker-confirmations">
                <div class="tracker-conf-header">
                    <span>On-chain confirmations</span>
                    <span class="tracker-conf-count">{{ $deposit->confirmations }} / {{ $deposit->required_confirmations }}</span>
                </div>
                <div class="tracker-conf-bar">
                    <div class="tracker-conf-fill" style="width: {{ $this->confirmationProgress }}%"></div>
                </div>
            </div>
            @endif

        </div>{{-- /.tracker-card --}}

        {{-- ── Payment details panel ───────────────────────────────────── --}}
        @if(!$confirmed && !$failed && !$expired)
        <div class="tracker-details-panel">

            {{-- Pay address with copy --}}
            <div class="tracker-detail-row" x-data="{ copied: false }">
                <span class="tracker-detail-label">Pay Address</span>
                <div class="tracker-detail-value-wrap">
                    <span class="tracker-detail-mono">{{ Str::limit($deposit->pay_address, 22, '…') }}</span>
                    <button
                        class="tracker-copy-btn"
                        x-on:click="
                            navigator.clipboard.writeText('{{ $deposit->pay_address }}');
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        "
                        :title="copied ? 'Copied!' : 'Copy address'"
                    >
                        <svg x-show="!copied" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        <svg x-show="copied" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="tracker-detail-row">
                <span class="tracker-detail-label">Amount Due</span>
                <span class="tracker-detail-value">{{ number_format((float)$deposit->crypto_amount, 8) }} {{ strtoupper($deposit->crypto_currency) }}</span>
            </div>

            <div class="tracker-detail-row">
                <span class="tracker-detail-label">Network</span>
                <span class="tracker-detail-value">{{ strtoupper($deposit->network ?? $deposit->crypto_currency) }}</span>
            </div>

            @if($deposit->expires_at)
            <div
                class="tracker-detail-row"
                x-data="{
                    secondsLeft: {{ now()->diffInSeconds($deposit->expires_at, false) }},
                    get label() {
                        if (this.secondsLeft <= 0) return 'Expired';
                        const h = Math.floor(this.secondsLeft / 3600);
                        const m = Math.floor((this.secondsLeft % 3600) / 60);
                        const s = this.secondsLeft % 60;
                        return (h > 0 ? h + 'h ' : '') + m + 'm ' + s + 's';
                    }
                }"
                x-init="setInterval(() => { if (secondsLeft > 0) secondsLeft--; }, 1000)"
            >
                <span class="tracker-detail-label">Invoice Expires</span>
                <span class="tracker-detail-value tracker-detail-value--timer" x-text="label"></span>
            </div>
            @endif

            {{-- Live status indicator --}}
            <div class="tracker-live-indicator">
                <div class="tracker-live-dot"></div>
                <span>Live monitoring active</span>
                <span class="tracker-poll-info">· updates every 8s</span>
            </div>

        </div>
        @endif

        {{-- ── CTA buttons ─────────────────────────────────────────────── --}}
        <div class="tracker-actions">
            @if($confirmed)
                <button wire:click="goToDashboard" class="tracker-btn tracker-btn--primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    View Dashboard
                </button>
            @elseif($failed || $expired)
                <a href="{{ route('dashboard.deposit.create') }}" wire:navigate class="tracker-btn tracker-btn--primary">
                    Try Again
                </a>
                <a href="mailto:{{ config('app.support_email', 'support@senflux.ai') }}" class="tracker-btn tracker-btn--ghost">
                    Contact Support
                </a>
            @else
                <a href="{{ route('dashboard') }}" wire:navigate class="tracker-btn tracker-btn--ghost">
                    Go to Dashboard
                </a>
            @endif
        </div>

        {{-- ── Security note ───────────────────────────────────────────── --}}
        <p class="tracker-security-note">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Your deposit is being monitored by on-chain verification. Do not close this tab until payment is confirmed.
        </p>

    </div>{{-- /.tracker-inner --}}
</div>
