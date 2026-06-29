{{-- resources/views/livewire/protected/alerts.blade.php --}}
<div>
    @push('styles')
        @vite('resources/css/alerts.css')
    @endpush

    <div class="alt">

        @if($savedFlash)
            <div class="alt-flash" wire:poll.2500ms="clearFlash">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $savedFlash }}
            </div>
        @endif

        <p class="alt-intro">Choose which events send you an email notification. Security alerts are strongly recommended to stay enabled.</p>

        {{-- ── Capital ──────────────────────────────────────────────────────── --}}
        <div class="alt-panel">
            <p class="alt-panel__title">Capital & Deposits</p>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Deposit Confirmed</span>
                    <span class="alt-row__desc">When your crypto payment is confirmed on-chain</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="deposit_confirmed">
                    <span class="alt-switch__track"></span>
                </label>
            </div>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Deposit Activated</span>
                    <span class="alt-row__desc">When your deposit starts earning daily ROI</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="deposit_activated">
                    <span class="alt-switch__track"></span>
                </label>
            </div>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Daily Earning Summary</span>
                    <span class="alt-row__desc">A daily digest of earnings across all active deposits</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="daily_earning_summary">
                    <span class="alt-switch__track"></span>
                </label>
            </div>
        </div>

        {{-- ── Withdrawals ──────────────────────────────────────────────────── --}}
        <div class="alt-panel">
            <p class="alt-panel__title">Withdrawals</p>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Withdrawal Approved</span>
                    <span class="alt-row__desc">When admin approves your withdrawal request</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="withdrawal_approved">
                    <span class="alt-switch__track"></span>
                </label>
            </div>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Withdrawal Paid</span>
                    <span class="alt-row__desc">When funds are sent to your wallet address</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="withdrawal_paid">
                    <span class="alt-switch__track"></span>
                </label>
            </div>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Withdrawal Rejected</span>
                    <span class="alt-row__desc">If a withdrawal request is declined</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="withdrawal_rejected">
                    <span class="alt-switch__track"></span>
                </label>
            </div>
        </div>

        {{-- ── Network & Rank ───────────────────────────────────────────────── --}}
        <div class="alt-panel">
            <p class="alt-panel__title">Network & Rank</p>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Referral Bonus Earned</span>
                    <span class="alt-row__desc">Each time you earn a commission from your network</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="referral_bonus">
                    <span class="alt-switch__track"></span>
                </label>
            </div>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Rank Achieved</span>
                    <span class="alt-row__desc">When you advance to a new rank</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="rank_achieved">
                    <span class="alt-switch__track"></span>
                </label>
            </div>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Leadership Match Bonus</span>
                    <span class="alt-row__desc">When a downline member's rank-up earns you a match bonus</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="leadership_match">
                    <span class="alt-switch__track"></span>
                </label>
            </div>
        </div>

        {{-- ── Account ───────────────────────────────────────────────────────── --}}
        <div class="alt-panel">
            <p class="alt-panel__title">Account</p>

            <div class="alt-row">
                <div class="alt-row__body">
                    <span class="alt-row__name">Subscription Expiring</span>
                    <span class="alt-row__desc">3 days before your plan subscription expires</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="subscription_expiring">
                    <span class="alt-switch__track"></span>
                </label>
            </div>

            <div class="alt-row alt-row--locked">
                <div class="alt-row__body">
                    <span class="alt-row__name">
                        Security Alerts
                        <span class="alt-row__badge">Recommended</span>
                    </span>
                    <span class="alt-row__desc">New device login, password changes, 2FA changes</span>
                </div>
                <label class="alt-switch">
                    <input type="checkbox" wire:model="security_alerts">
                    <span class="alt-switch__track"></span>
                </label>
            </div>
        </div>

        {{-- ── Save ──────────────────────────────────────────────────────────── --}}
        <button wire:click="save" wire:loading.attr="disabled" type="button" class="alt-save-btn">
            <span wire:loading.remove wire:target="save">Save Preferences</span>
            <span wire:loading wire:target="save">Saving…</span>
        </button>

    </div>
</div>
