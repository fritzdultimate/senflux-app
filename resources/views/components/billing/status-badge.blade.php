{{-- resources/views/components/billing/status-badge.blade.php --}}
@props(['status'])

@php
    $map = [
        'pending'    => ['label' => 'Pending',    'class' => 'sb-pending'],
        'waiting'    => ['label' => 'Awaiting Payment', 'class' => 'sb-pending'],
        'confirming' => ['label' => 'Confirming', 'class' => 'sb-confirming'],
        'confirmed'  => ['label' => 'Confirmed',  'class' => 'sb-confirming'],
        'active'     => ['label' => 'Active',     'class' => 'sb-active'],
        'finished'   => ['label' => 'Finished',   'class' => 'sb-finished'],
        'expired'    => ['label' => 'Expired',    'class' => 'sb-expired'],
        'failed'     => ['label' => 'Failed',     'class' => 'sb-failed'],
        'cancelled'  => ['label' => 'Cancelled',  'class' => 'sb-failed'],
        'refunded'   => ['label' => 'Refunded',   'class' => 'sb-expired'],
    ];
    $conf = $map[$status] ?? ['label' => ucfirst($status), 'class' => 'sb-pending'];
@endphp

<span class="status-badge {{ $conf['class'] }}">
    <span class="status-badge__dot"></span>
    {{ $conf['label'] }}
</span>
