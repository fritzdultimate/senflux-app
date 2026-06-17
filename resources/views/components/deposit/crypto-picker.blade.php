{{-- resources/views/components/deposit/crypto-picker.blade.php --}}
@props(['currencies', 'selected'])

<div class="crypto-grid">
    @foreach($currencies as $currency)
        <button
            wire:click="$set('cryptoCurrency', '{{ $currency['code'] }}')"
            type="button"
            class="crypto-option {{ $selected === $currency['code'] ? 'crypto-option--active' : '' }}"
        >
            <span class="crypto-option__code">{{ strtoupper($currency['code']) }}</span>
            <div class="crypto-option__meta">
                <span class="crypto-option__label">{{ $currency['label'] }}</span>
                <span class="crypto-option__network">{{ $currency['network'] }}</span>
            </div>
        </button>
    @endforeach
</div>
