@props(['tier', 'accent', 'fundAmount'])

<div class="-mt-2 mx-6 mb-3 rounded-lg border border-white/10 bg-[#0B0D13] p-4">
    <div class="mb-2 flex items-baseline justify-between">
        <label class="text-[11px] font-semibold uppercase tracking-wide text-[#565B6E]">Capital amount</label>
        <span class="font-['IBM_Plex_Mono'] text-[11px] text-[#565B6E]">
            ${{ number_format($tier->min_capital_per_slot, 0) }}{{ $tier->max_capital_per_slot ? ' – $'.number_format($tier->max_capital_per_slot, 0) : '+' }}
        </span>
    </div>

    <div class="flex items-center gap-2">
        <div class="flex flex-1 items-center rounded-md border border-white/10 bg-[#07080C] transition-colors focus-within:border-white/25">
            <span class="pl-3 font-['IBM_Plex_Mono'] text-sm text-[#565B6E]">$</span>
            <input
                type="number"
                wire:model="fundAmount"
                min="{{ $tier->min_capital_per_slot }}"
                max="{{ $tier->max_capital_per_slot ?? '' }}"
                class="w-full border-0 bg-transparent py-2.5 pl-1.5 pr-3 font-['IBM_Plex_Mono'] text-sm tabular-nums text-[#F2F3F7] focus:outline-none focus:ring-0"
            >
        </div>
        <button type="button" wire:click="fundSlot"
            class="shrink-0 rounded-md px-4 py-2.5 text-sm font-semibold text-[#07080C] transition-opacity hover:opacity-85"
            style="background: {{ $accent }}">
            Confirm
        </button>
        <button type="button" wire:click="cancelFunding"
            class="shrink-0 rounded-md border border-white/10 px-3.5 py-2.5 text-sm text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
            Cancel
        </button>
    </div>
</div>