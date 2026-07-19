
@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
@endpush

@php
    $tierColors = ['#60a5fa', '#8B7CF6', '#F0A93D'];
    $accent = $tierColors[$this->subscription->packTier->sort_order ?? 0] ?? '#8B7CF6';

    $statusTone = match($this->subscription->status->value) {
        'active' => 'positive',
        'in_renewal_window' => 'warning',
        default => 'neutral',
    };

    $fundedSlots = $this->subscription->slots->where('status.value', 'funded');
    $capitalDeployed = $fundedSlots->sum('capital_amount');
    $totalEarned = $this->subscription->slots->sum('realized_profit');
@endphp

<div class="relative min-h-screen overflow-hidden bg-[#07080C] ">

    {{-- ── Ambient backdrop glow — the signature atmospheric touch ─────── --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[420px] overflow-hidden">
        <div class="absolute left-1/2 top-[-180px] h-[420px] w-[680px] -translate-x-1/2 rounded-full blur-3xl opacity-[0.12]" style="background: {{ $accent }}"></div>
    </div>
    {{-- faint dot-grid texture for terminal depth --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.4]" style="background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="relative mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:px-0">

        {{-- ── Breadcrumb ─────────────────────────────────────────────── --}}
        <a href="{{ route('dashboard.packs.index') }}" wire:navigate
           class="mb-8 inline-flex items-center gap-1.5 text-xs font-medium text-[#565B6E] transition-colors hover:text-[#888EA3]">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            My Packs
        </a>

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="mb-7 flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-packs.tier-glyph :color="$accent" size="lg" />
                <div>
                    <p class="font-['IBM_Plex_Mono'] text-[11px] tabular-nums tracking-wider text-[#565B6E]">PACK #{{ str_pad($this->subscription->id, 4, '0', STR_PAD_LEFT) }}</p>
                    <h1 class="font-['Sora'] text-2xl font-bold text-[#F2F3F7]">{{ $this->subscription->packTier->name }}</h1>
                </div>
            </div>
            <x-ui.status-pill
                :label="$this->subscription->status->value === 'in_renewal_window' ? 'Renewal Window' : $this->subscription->status->label()"
                :tone="$statusTone"
                :pulse="$statusTone === 'warning'"
            />
        </div>

        {{-- ── Alerts ─────────────────────────────────────────────────── --}}
        <div class="mb-6 space-y-2.5">
            @if($errorMessage)
                <x-ui.alert variant="error">{{ $errorMessage }}</x-ui.alert>
            @endif
            @if($successMessage)
                <x-ui.alert variant="success">{{ $successMessage }}</x-ui.alert>
            @endif
        </div>

        {{-- ── Hero stat strip ──────────────────────────────────────────── --}}
        <div class="mb-6 flex divide-x divide-white/[0.06] overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] shadow-[inset_0_1px_0_0_rgba(255,255,255,0.05)]">
            <x-ui.hero-stat icon="banknotes" label="Capital Deployed" :value="'$' . number_format($capitalDeployed, 2)" />
            <x-ui.hero-stat icon="trending" label="Total Earned" :value="'+$' . number_format($totalEarned, 2)" tone="positive" />
            <x-ui.hero-stat icon="grid" label="Slots Funded" :value="$fundedSlots->count() . '/' . $this->subscription->slots->count()" />
            <x-ui.hero-stat icon="calendar" label="Matures" :value="$this->subscription->matures_at->format('M j')" :suffix="$this->subscription->matures_at->format('Y')" />
        </div>

        {{-- ── Refund — only when actually eligible ────────────────────── --}}
        @if($this->subscription->isEligibleForRefund())
            <x-ui.panel class="mb-5">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-sm text-[#888EA3]">No slots funded yet — you're within the 3-day refund window.</p>
                    <button
                        type="button"
                        wire:click="requestRefund"
                        wire:confirm="Refund this pack purchase? This cannot be undone."
                        class="shrink-0 rounded-lg border border-white/10 px-4 py-2 text-xs font-semibold text-[#888EA3] transition-colors hover:border-[#F2545B]/40 hover:text-[#F2545B]"
                    >
                        Request refund
                    </button>
                </div>
            </x-ui.panel>
        @endif

        {{-- ── Renewal window decision ──────────────────────────────────── --}}
        @if($this->subscription->isInRenewalWindow())
            <x-ui.panel
                eyebrow="Action required"
                title="This pack has matured — choose what happens next"
                tone="warning"
                class="mb-5"
            >
                <div class="grid gap-3 sm:grid-cols-3">
                    <x-packs.renewal-option
                        title="Withdraw" icon="withdraw"
                        description="Return all capital to your wallet"
                        wire:click="withdraw"
                        wire:confirm="Withdraw all capital to your wallet?"
                    />
                    <x-packs.renewal-option
                        title="Continue" icon="continue"
                        description="Same tier, capital rolls into a new cycle"
                        wire:click="continueCycle"
                        wire:confirm="Continue capital into a new cycle?"
                    />
                    <x-packs.renewal-option
                        title="Auto-Compound" icon="compound"
                        description="Capital and profit both restake"
                        wire:click="autoCompound"
                        wire:confirm="Restake profit alongside capital into a new cycle?"
                    />
                </div>

                @if($this->upgradeOptions->isNotEmpty())
                    <div class="mt-6 border-t border-[#F0A93D]/10 pt-5">
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wide text-[#565B6E]">Or upgrade to a higher tier</p>

                        <div class="flex flex-wrap gap-2">
                            @foreach($this->upgradeOptions as $opt)
                                <button
                                    type="button"
                                    wire:click="startUpgrade({{ $opt->id }})"
                                    class="rounded-lg border px-4 py-2.5 text-xs font-semibold transition-all {{ $upgradingToTierId === $opt->id ? 'border-[#8B7CF6]/50 bg-[#8B7CF6]/10 text-[#8B7CF6]' : 'border-white/10 text-[#888EA3] hover:border-white/20 hover:text-[#F2F3F7]' }}"
                                >
                                    {{ $opt->name }} <span class="font-['IBM_Plex_Mono'] tabular-nums text-[#565B6E]">${{ number_format($opt->price, 0) }}</span>
                                </button>
                            @endforeach
                        </div>

                        @if($upgradingToTierId)
                            <div class="mt-4 rounded-xl border border-[#8B7CF6]/20 bg-[#8B7CF6]/[0.05] p-4">
                                <label class="flex items-center gap-2 text-sm text-[#888EA3]">
                                    <input type="checkbox" wire:model="upgradeCompound" class="rounded border-white/20 bg-transparent">
                                    Also compound profit into the upgrade
                                </label>
                                <div class="mt-3 flex gap-2">
                                    <button type="button" wire:click="confirmUpgrade"
                                        class="rounded-lg bg-[#7C6AEF] px-4 py-2.5 text-sm font-bold text-white shadow-[0_4px_14px_-2px_rgba(124,106,239,0.5)] transition-transform hover:scale-105">
                                        Confirm upgrade
                                    </button>
                                    <button type="button" wire:click="cancelUpgrade"
                                        class="rounded-lg border border-white/10 px-4 py-2.5 text-sm text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </x-ui.panel>
        @endif

        {{-- ── Slot ledger ───────────────────────────────────────────────── --}}
        <x-ui.panel eyebrow="Allocation" title="Slots">
            <x-slot:actions>
                <span class="font-['IBM_Plex_Mono'] text-xs tabular-nums text-[#565B6E]">
                    {{ $fundedSlots->count() }}/{{ $this->subscription->slots->count() }} funded
                </span>
            </x-slot:actions>

            <div class="-mx-6 -my-6 divide-y divide-white/[0.06]">
                @foreach($this->subscription->slots as $slot)
                    <x-packs.slot-row :pack-slot="$slot" :accent="$accent" :is-funding="$fundingSlotId === $slot->id" />

                    @if($fundingSlotId === $slot->id)
                        <x-packs.fund-panel :tier="$this->subscription->packTier" :accent="$accent" :fund-amount="$fundAmount" />
                    @endif

                    @if($slot->status->value === 'funded')
                        <div class="-mt-3 px-6 pb-4">
                            <button
                                type="button"
                                wire:click="earlyExit({{ $slot->id }})"
                                wire:confirm="Early exit forfeits 8% of capital as a fee. Continue?"
                                class="text-[11px] font-medium text-[#565B6E] underline decoration-dotted underline-offset-2 transition-colors hover:text-[#F2545B]"
                            >
                                Early exit (8% fee)
                            </button>
                        </div>
                    @endif
                @endforeach
            </div>
        </x-ui.panel>

    </div>
</div>
