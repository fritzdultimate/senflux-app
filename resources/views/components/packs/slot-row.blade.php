@props(['packSlot', 'accent', 'isFunding' => false])

@php
    $statusValue = $packSlot->status->value;
    $barColor = match($statusValue) {
        'funded' => $accent,
        'closed' => '#3A3F4D',
        default => 'transparent',
    };
    $statusBadge = match($statusValue) {
        'funded' => ['bg' => $accent . '14', 'text' => $accent, 'label' => 'Funded'],
        'closed' => ['bg' => 'rgba(86,91,110,0.12)', 'text' => '#888EA3', 'label' => 'Closed'],
        default  => ['bg' => 'rgba(255,255,255,0.04)', 'text' => '#565B6E', 'label' => 'Empty'],
    };
@endphp

<div class="group relative flex items-center gap-4 py-5 pl-5 pr-6 transition-colors hover:bg-white/[0.015]" wire:key="slot-row-{{ $packSlot->id }}">
    <span class="absolute inset-y-3 left-0 w-[2px] rounded-full" style="background: {{ $barColor }}"></span>

    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md border border-white/10 bg-white/[0.02] font-['IBM_Plex_Mono'] text-xs font-medium text-[#888EA3]">
        {{ str_pad($packSlot->slot_number, 2, '0', STR_PAD_LEFT) }}
    </span>

    <div class="min-w-0 flex-1">
        <p class="text-sm font-medium text-[#F2F3F7]">Slot {{ $packSlot->slot_number }}</p>
        <span class="mt-1 inline-flex items-center rounded px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide" style="background: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['text'] }}">
            {{ $statusBadge['label'] }}
        </span>
    </div>

    @if($statusValue === 'empty' && !$isFunding)
        <button type="button" wire:click="startFunding({{ $packSlot->id }})"
            class="shrink-0 rounded-md border px-3.5 py-1.5 text-xs font-semibold transition-colors"
            style="border-color: {{ $accent }}40; color: {{ $accent }}">
            Fund slot
        </button>
    @elseif($statusValue === 'funded')
        <div class="shrink-0 text-right">
            <x-ui.money :amount="$packSlot->capital_amount" size="lg" />
            <x-ui.money :amount="$packSlot->realized_profit" sign tone="positive" size="sm" class="mt-0.5 block" />
        </div>
    @elseif($statusValue === 'closed')
        <div class="shrink-0 text-right">
            <x-ui.money :amount="$packSlot->capital_amount" tone="muted" size="base" />
            @if($packSlot->was_early_exit)
                <span class="mt-0.5 block font-['IBM_Plex_Mono'] text-xs tabular-nums text-[#F2545B]">-${{ number_format($packSlot->early_exit_fee_charged, 2) }} fee</span>
            @endif
        </div>
    @endif
</div>