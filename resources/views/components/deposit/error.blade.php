{{-- resources/views/components/deposit/error.blade.php --}}
@props(['message'])

<div class="deposit-error" role="alert">
    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/>
        <line x1="12" y1="8" x2="12" y2="12"/>
        <line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    {{ $message }}
</div>
