{{-- resources/views/components/billing/pending-gate.blade.php --}}
@props([
    'record',           // Subscription or Deposit model
    'canCancel',
    'cancelAction'  => 'cancelPending',
    'resumeAction'  => 'resumeTracking',
    'planLabel',
    'amount',
    'error'         => null,   // pass $errorMessage here for inline display
])

<div class="pending-gate">
    <div class="pending-gate__row">
        <div class="pending-gate__icon">
            <span class="pending-gate__pulse"></span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>

        <div class="pending-gate__body">
            <strong>You have a payment in progress</strong>
            <p>{{ $planLabel }} — ${{ number_format($amount, 2) }} · started {{ $record->created_at->diffForHumans() }}</p>
        </div>

        <div class="pending-gate__actions">
            <button type="button" wire:click="{{ $resumeAction }}" class="pending-gate__btn pending-gate__btn--primary">
                Resume Tracking
            </button>
            @if($canCancel)
                <button
                    type="button"
                    wire:click="{{ $cancelAction }}"
                    wire:confirm="Cancel this pending payment?"
                    class="pending-gate__btn pending-gate__btn--ghost"
                >
                    Cancel
                </button>
            @endif
        </div>
    </div>

    @if($error)
        <div class="pending-gate__error" wire:key="pending-error-{{ md5($error) }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $error }}
        </div>
    @endif
</div>
