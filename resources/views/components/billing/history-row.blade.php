{{-- resources/views/components/billing/history-row.blade.php --}}
@props([
    'planLabel',
    'amount',
    'status',
    'date',
    'meta'      => null,   // small extra text e.g. "Monthly" or "0.42 SOL"
    'trackUrl'  => null,   // if pending/waiting, link to live tracker
])

<div class="history-row">
    <div class="history-row__main">
        <span class="history-row__plan">{{ $planLabel }}</span>
        @if($meta)
            <span class="history-row__meta">{{ $meta }}</span>
        @endif
    </div>

    <div class="history-row__amount">${{ number_format($amount, 2) }}</div>

    <div class="history-row__status">
        <x-billing.status-badge :status="$status" />
    </div>

    <div class="history-row__date">{{ $date->format('M j, Y') }}</div>

    <div class="history-row__action">
        @if($trackUrl)
            <a href="{{ $trackUrl }}" wire:navigate class="history-row__link">Track →</a>
        @endif
    </div>
</div>
