{{-- resources/views/components/deposit/amount-input.blade.php --}}
@props([
    'min'             => 100,
    'max'             => 999999,
    'estimatedDaily'  => 0,
    'estimatedMonthly'=> 0,
])

<div class="amount-input-wrap">
    <span class="amount-prefix">$</span>
    <input
        type="number"
        wire:model.live.debounce.400ms="amountUsd"
        class="amount-input"
        placeholder="0.00"
        min="{{ $min }}"
        max="{{ $max }}"
        step="0.01"
        autocomplete="off"
    />
    <span class="amount-suffix">USD</span>
</div>

@error('amountUsd')
    <p class="field-error">{{ $message }}</p>
@enderror

<p class="amount-hint">
    Min: ${{ number_format($min, 0) }}
    @if($max < 999999) · Max: ${{ number_format($max, 0) }} @endif
</p>

@if($estimatedDaily > 0)
    <x-deposit.earnings-preview
        :daily="$estimatedDaily"
        :monthly="$estimatedMonthly"
    />
@endif
