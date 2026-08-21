{{-- resources/views/components/deposit/payment-modal.blade.php --}}
{{--
    Props:
    - payAddress     : string
    - cryptoAmount   : string|float
    - cryptoCurrency : string
    - network        : string
    - amountUsd      : float
    - expiresAt      : Carbon|null
    - ctaLabel       : string
    - ctaAction      : string  (Livewire method name)
--}}
@props([
    'payAddress',
    'cryptoAmount',
    'cryptoCurrency',
    'network'      => null,
    'amountUsd'    => 0,
    'expiresAt'    => null,
    'ctaLabel'     => 'Track Payment',
    'ctaAction'    => 'goToTracker',
])

@php
    $secondsLeft = $expiresAt ? now()->diffInSeconds($expiresAt, false) : 86400;
    $networkLabel = strtoupper($network ?? $cryptoCurrency);
@endphp

<div
    class="modal-backdrop"
    wire:key="payment-modal-{{ $payAddress }}"
    x-data="{
        open: true,
        copied: false,
        secondsLeft: {{ max(0, $secondsLeft) }},
        get timeDisplay() {
            if (this.secondsLeft <= 0) return 'Expired';
            const total = Math.floor(this.secondsLeft);
            const h = Math.floor(total / 3600);
            const m = Math.floor((total % 3600) / 60);
            const s = total % 60;
            if (h > 0) return h + 'h ' + String(m).padStart(2,'0') + 'm';
            return String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
        },
        init() {
            const t = setInterval(() => {
                if (this.secondsLeft > 0) this.secondsLeft--;
                else clearInterval(t);
            }, 1000);
        }
    }"
    x-show="open"
    x-transition.opacity
    style="display: none"
>
    <div class="modal-box" @click.outside="open = false">

        {{-- Header --}}
        <div class="modal-header">
            <div class="modal-header__left">
                <span class="modal-live-dot"></span>
                <span class="modal-title">Invoice Ready</span>
            </div>
            <div class="modal-timer" :class="{ 'modal-timer--urgent': secondsLeft < 300 }">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <span x-text="timeDisplay"></span>
            </div>
        </div>

        {{-- Amount --}}
        <div class="modal-amount-section">
            <p class="modal-amount-label">Send exactly</p>
            <div class="modal-amount-display">
                <span class="modal-amount-value">{{ number_format((float) $cryptoAmount, 8) }}</span>
                <span class="modal-amount-currency">{{ strtoupper($cryptoCurrency) }}</span>
            </div>
            @if($amountUsd > 0)
                <p class="modal-amount-usd">≈ ${{ number_format((float) $amountUsd, 2) }} USD</p>
            @endif
        </div>

        {{-- QR Code --}}
        <div class="modal-qr-wrap">
            <div
                class="modal-qr-inner"
                wire:ignore
                x-data
                x-init="
                    new QRCode($el, {
                        text: '{{ $payAddress }}',
                        width: 148,
                        height: 148,
                        colorDark: '#ffffff',
                        colorLight: '#0d1120',
                        correctLevel: QRCode.CorrectLevel.M
                    });
                "
            ></div>
        </div>

        {{-- Address --}}
        <div class="modal-address-wrap">
            <p class="modal-address-label">{{ $networkLabel }} Address</p>
            <div class="modal-address-box">
                <span class="modal-address-text">{{ $payAddress }}</span>
                <button
                    type="button"
                    class="modal-copy-btn"
                    x-on:click="
                        navigator.clipboard.writeText('{{ $payAddress }}');
                        copied = true;
                        setTimeout(() => copied = false, 2000);
                    "
                >
                    <svg x-show="!copied" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="9" y="9" width="13" height="13" rx="2"/>
                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                    </svg>
                    <svg x-show="copied" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Network warning --}}
        <div class="modal-warning">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Send only <strong>{{ strtoupper($cryptoCurrency) }}</strong> on the
            <strong>{{ $networkLabel }}</strong> network. Wrong network = permanent loss.
        </div>

        {{-- CTA --}}
        <button
            wire:click="{{ $ctaAction }}"
            type="button"
            class="modal-track-btn"
        >
            {{ $ctaLabel }}
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
        </button>

    </div>
</div>
