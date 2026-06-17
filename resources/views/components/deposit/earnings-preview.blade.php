{{-- resources/views/components/deposit/earnings-preview.blade.php --}}
@props(['daily' => 0, 'monthly' => 0])

<div class="earnings-preview">
    <div class="earnings-preview__row">
        <span>Est. daily</span>
        <strong>${{ number_format($daily, 2) }}</strong>
    </div>
    <div class="earnings-preview__row">
        <span>Est. monthly</span>
        <strong>${{ number_format($monthly, 2) }}</strong>
    </div>
</div>
