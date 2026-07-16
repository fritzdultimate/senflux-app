<!-- @once -->
    @push('styles')
        <style>
            [x-cloak] { display: none !important; }

            .ui-tip {
                position: relative;
                display: inline-flex;
                align-items: center;
                vertical-align: middle;
                line-height: 0;
            }

            .ui-tip__trigger {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 15px;
                height: 15px;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.16);
                color: rgba(255, 255, 255, 0.45);
                background: rgba(255, 255, 255, 0.04);
                cursor: help;
                transition: color 0.15s ease, border-color 0.15s ease, background 0.15s ease;
                flex-shrink: 0;
            }

            .ui-tip__trigger:hover,
            .ui-tip.is-open .ui-tip__trigger {
                color: rgba(255, 255, 255, 0.85);
                border-color: rgba(255, 255, 255, 0.32);
                background: rgba(255, 255, 255, 0.08);
            }

            .ui-tip__bubble {
                position: absolute;
                z-index: 60;
                width: max-content;
                max-width: 240px;
                padding: 9px 12px;
                font-size: 12px;
                line-height: 1.5;
                font-weight: 400;
                color: rgba(255, 255, 255, 0.88);
                background: #14161f;
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                box-shadow: 0 8px 24px -6px rgba(0, 0, 0, 0.5), 0 2px 8px rgba(0, 0, 0, 0.3);
                pointer-events: none;
            }

            .ui-tip__bubble--top    { bottom: calc(100% + 9px); left: 50%; transform: translateX(-50%); }
            .ui-tip__bubble--bottom { top: calc(100% + 9px); left: 50%; transform: translateX(-50%); }
            .ui-tip__bubble--left   { right: calc(100% + 9px); top: 50%; transform: translateY(-50%); }
            .ui-tip__bubble--right  { left: calc(100% + 9px); top: 50%; transform: translateY(-50%); }

            .ui-tip__arrow {
                position: absolute;
                width: 8px;
                height: 8px;
                background: #14161f;
                border: 1px solid rgba(255, 255, 255, 0.1);
                transform: rotate(45deg);
            }

            .ui-tip__bubble--top .ui-tip__arrow {
                bottom: -4.5px; left: 50%; margin-left: -4px;
                border-top: none; border-left: none;
            }
            .ui-tip__bubble--bottom .ui-tip__arrow {
                top: -4.5px; left: 50%; margin-left: -4px;
                border-bottom: none; border-right: none;
            }
            .ui-tip__bubble--left .ui-tip__arrow {
                right: -4.5px; top: 50%; margin-top: -4px;
                border-top: none; border-left: none;
            }
            .ui-tip__bubble--right .ui-tip__arrow {
                left: -4.5px; top: 50%; margin-top: -4px;
                border-bottom: none; border-right: none;
            }

            @media (max-width: 480px) {
                .ui-tip__bubble { max-width: 200px; }
            }
        </style>
    @endpush
<!-- @endonce -->

@props([
    'text' => null,
    'position' => 'top',
])

<span
    x-data="{ open: false }"
    x-init="$el.addEventListener('mouseenter', () => open = true); $el.addEventListener('mouseleave', () => open = false)"
    @click.stop="open = !open"
    @click.outside="open = false"
    class="ui-tip"
    :class="{ 'is-open': open }"
>
    <button type="button" class="ui-tip__trigger" @click.prevent tabindex="0" aria-label="More information">
        <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.5 9a2.5 2.5 0 0 1 4.9.8c0 1.6-2.4 1.8-2.4 3.4"/>
            <circle cx="12" cy="17.3" r="0.9" fill="currentColor" stroke="none"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="ui-tip__bubble ui-tip__bubble--{{ $position }}"
        role="tooltip"
    >
        {{ $text ?? $slot }}
        <span class="ui-tip__arrow"></span>
    </div>
</span>