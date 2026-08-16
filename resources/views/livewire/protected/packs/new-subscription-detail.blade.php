@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
@endpush

@php
    $tierColors = ['#60a5fa', '#8B7CF6', '#F0A93D'];
    $accent = $tierColors[$this->subscription->packTier->sort_order ?? 0] ?? '#8B7CF6';

    $slot = $this->slot;
    $isMatured = $this->isMatured;

    // Status pill reflects real maturity even if the backend `status`
    // enum hasn't flipped to IN_RENEWAL_WINDOW yet — matures_at wins.
    $statusLabel = $isMatured
        ? 'Matured'
        : ($this->subscription->status->value === 'in_renewal_window' ? 'Renewal Window' : $this->subscription->status->label());
    $statusTone = $isMatured ? 'warning' : match($this->subscription->status->value) {
        'active' => 'positive',
        'in_renewal_window' => 'warning',
        default => 'neutral',
    };

    $capitalDeployed = $slot->capital_amount ?? 0;
    $totalEarned = $slot->realized_profit ?? 0;
    $growthPct = $capitalDeployed > 0 ? round($totalEarned / $capitalDeployed * 100, 2) : 0;

    // Cycle progress — pinned to 100% once matured rather than continuing
    // to compute a live fraction that can quietly sit at "0 days left" with
    // no visual change.
    $cycleStart = $this->subscription->created_at;
    $cycleEnd = $this->subscription->matures_at;
    $cycleTotalSeconds = max(1, $cycleStart->diffInSeconds($cycleEnd));
    $cycleElapsedSeconds = min($cycleTotalSeconds, $cycleStart->diffInSeconds(now()));
    $cycleProgressPct = $isMatured ? 100 : round($cycleElapsedSeconds / $cycleTotalSeconds * 100, 1);
    $daysToMaturity = $isMatured ? 0 : (int) floor(now()->diffInDays($cycleEnd));

    // Whether the subscription's status even permits capital actions at
    // all — refunded/closed/anything-not-active-or-renewal short-circuits
    // the Position/Deploy panels entirely, regardless of slot state.
    $isActionable = $this->isActionable;
    $isFunded = $slot && $slot->isFunded();

    // ASSUMPTION: REFUNDED is the only terminal status I've confirmed exists
    // (from PackPurchaseService::refund()). If there are other terminal
    // states (withdrawn/closed/etc.) add them here so a matured-but-already-
    // closed pack doesn't show the renewal panel again.
    $showRenewalPanel = $isFunded && $isMatured && $isActionable;

    // Early-exit breakdown — computed here (not in the modal) so the number
    // shown in the confirmation is exactly what the backend will charge,
    // not a client-side guess.
    $earlyExitFee = round($capitalDeployed * 0.08, 2);
    $earlyExitNet = $capitalDeployed - $earlyExitFee;

    // Contribution timeline — deploy + every top-up, newest first.
    $contributions = collect();
    if ($slot) {
        try {
            $contributions = $slot->contributions()->latest()->get();
        } catch (\Throwable $e) {
            $contributions = collect([
                (object) [
                    'type' => 'deploy',
                    'amount' => $capitalDeployed,
                    'created_at' => $slot->created_at,
                ],
            ]);
        }
    }
@endphp

<div
    class="relative min-h-screen overflow-hidden bg-[#07080C]"
    x-data="{
        deployOpen: false,
        topupOpen: false,
        confirm: { open: false, type: null },
        openConfirm(type) { this.confirm = { open: true, type: type }; },
        closeConfirm() { this.confirm = { open: false, type: null }; },
    }"
    @pack-action-completed.window="deployOpen = false; topupOpen = false; closeConfirm()"
>

    {{-- ── Ambient backdrop glow ──────────────────────────────────────── --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[420px] overflow-hidden">
        <div class="absolute left-1/2 top-[-180px] h-[420px] w-[680px] -translate-x-1/2 rounded-full blur-3xl opacity-[0.12]" style="background: {{ $isMatured ? '#F0A93D' : $accent }}"></div>
    </div>
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
            <x-ui.status-pill :label="$statusLabel" :tone="$statusTone" :pulse="$statusTone === 'warning'" />
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

        {{-- ── Hero stat strip ───────────────────────────────────────────── --}}
        <div class="mb-6 grid grid-cols-2 divide-x divide-y divide-white/[0.06] overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] shadow-[inset_0_1px_0_0_rgba(255,255,255,0.05)] sm:grid-cols-4 sm:divide-y-0">
            <x-ui.hero-stat icon="banknotes" label="Capital Deployed" :value="'$' . number_format($capitalDeployed, 2)" />
            <x-ui.hero-stat icon="trending" label="Total Earned" :value="'+$' . number_format($totalEarned, 2)" tone="positive" />
            <x-ui.hero-stat icon="grid" label="Growth" :value="($growthPct >= 0 ? '+' : '') . number_format($growthPct, 2) . '%'" :tone="$growthPct >= 0 ? 'positive' : 'negative'" />
            <x-ui.hero-stat icon="calendar" :label="$isMatured ? 'Matured' : 'Matures'" :value="$cycleEnd->format('M j')" :suffix="$cycleEnd->format('Y')" />
        </div>

        {{-- ── Refund — only when actually eligible ────────────────────── --}}
        @if($this->subscription->isEligibleForRefund())
            <x-ui.panel class="mb-5">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-sm text-[#888EA3]">No capital deployed yet — you're within the 3-day refund window.</p>
                    <button
                        type="button"
                        @click="openConfirm('refund')"
                        class="shrink-0 rounded-lg border border-white/10 px-4 py-2 text-xs font-semibold text-[#888EA3] transition-colors hover:border-[#F2545B]/40 hover:text-[#F2545B]"
                    >
                        Request refund
                    </button>
                </div>
            </x-ui.panel>
        @endif

        {{-- ── Renewal window decision — forced open by real maturity ──── --}}
        @if($showRenewalPanel)
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
                        @click="openConfirm('withdraw')"
                    />
                    <x-packs.renewal-option
                        title="Continue" icon="continue"
                        description="Same tier, capital rolls into a new cycle"
                        @click="openConfirm('continue')"
                    />
                    <x-packs.renewal-option
                        title="Auto-Compound" icon="compound"
                        description="Capital and profit both restake"
                        @click="openConfirm('compound')"
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
                                    <button type="button" @click="openConfirm('upgrade')"
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

        @if($isFunded)

            {{-- ═══════════════════════════════════════════════════════════
                 POSITION
                 ═══════════════════════════════════════════════════════════ --}}

            <div class="mb-5 overflow-hidden rounded-2xl border {{ $isMatured ? 'border-[#F0A93D]/25' : 'border-white/10' }} bg-white/[0.02] shadow-[inset_0_1px_0_0_rgba(255,255,255,0.05)]">

                {{-- Capital + progress --}}
                <div class="relative overflow-hidden p-6 sm:p-7">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full blur-3xl opacity-[0.08]" style="background: {{ $isMatured ? '#F0A93D' : $accent }}"></div>

                    <div class="relative flex items-start justify-between gap-6">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-[#565B6E]">Your Position</p>
                                @if($isMatured)
                                    <span class="inline-flex items-center gap-1 rounded-full border border-[#F0A93D]/30 bg-[#F0A93D]/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[#F0A93D]">
                                        <svg width="9" height="9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                                        Matured
                                    </span>
                                @endif
                            </div>
                            <div class="mt-2 flex items-baseline gap-3">
                                <span class="font-['Sora'] text-[2.35rem] font-bold leading-none text-[#F2F3F7] tabular-nums">${{ number_format($capitalDeployed, 2) }}</span>
                                @if($totalEarned != 0)
                                    <span class="font-['IBM_Plex_Mono'] text-sm font-semibold tabular-nums {{ $totalEarned >= 0 ? 'text-[#3ECF8E]' : 'text-[#F2545B]' }}">
                                        {{ $totalEarned >= 0 ? '+' : '' }}${{ number_format($totalEarned, 2) }}
                                    </span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-[#565B6E]">
                                @if($isMatured)
                                    Cycle complete — choose a renewal option above to continue.
                                @else
                                    Deployed capital · continuously trading since {{ $slot->created_at->format('M j, Y') }}
                                @endif
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="topupOpen = !topupOpen"
                            @disabled($isMatured || !$isActionable)
                            @if($isMatured) title="Matured positions can't receive new capital — choose a renewal option above"
                            @elseif(!$isActionable) title="This pack is {{ strtolower($this->subscription->status->label()) }} — no further capital actions are available" @endif
                            class="shrink-0 rounded-xl px-5 py-3 text-sm font-bold text-white transition-transform {{ ($isMatured || !$isActionable) ? 'cursor-not-allowed bg-white/[0.05] text-[#565B6E]' : 'shadow-[0_4px_18px_-2px_rgba(0,0,0,0.4)] hover:scale-105' }}"
                            @unless($isMatured || !$isActionable) style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));" @endunless
                        >
                            + Add Capital
                        </button>
                    </div>

                    {{-- Cycle progress bar --}}
                    <div class="relative mt-6">
                        <div class="mb-1.5 flex items-center justify-between text-[11px]">
                            <span class="font-medium text-[#565B6E]">Cycle progress</span>
                            <span class="font-['IBM_Plex_Mono'] tabular-nums {{ $isMatured ? 'text-[#F0A93D]' : 'text-[#888EA3]' }}">
                                {{ $isMatured ? 'Complete' : $daysToMaturity . ' ' . Str::plural('day', $daysToMaturity) . ' to maturity' }}
                            </span>
                        </div>
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-white/[0.06]">
                            <div class="h-full rounded-full transition-all" style="width: {{ $cycleProgressPct }}%; background: {{ $isMatured ? 'linear-gradient(90deg, #F0A93D, #d99424)' : "linear-gradient(90deg, {$accent}, color-mix(in srgb, {$accent} 60%, white))" }};"></div>
                        </div>
                    </div>
                </div>

                {{-- Top-up panel — instant client-side toggle, no server round trip to open --}}
                <div x-show="topupOpen" x-cloak x-transition.opacity.duration.150ms>
                    <div class="border-t border-white/[0.06] bg-white/[0.015] p-6 sm:p-7">
                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wide text-[#565B6E]">Add capital to this position</p>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="relative flex-1">
                                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 font-['IBM_Plex_Mono'] text-sm text-[#565B6E]">$</span>
                                <input
                                    type="number" step="0.01" min="0"
                                    wire:model="topUpAmount"
                                    placeholder="0.00"
                                    inputmode="decimal"
                                    autocomplete="off"
                                    @wheel.prevent
                                    class="w-full rounded-xl border border-white/10 bg-white/[0.03] py-3 pl-8 pr-4 font-['IBM_Plex_Mono'] text-lg font-semibold tabular-nums text-[#F2F3F7] outline-none transition-colors focus:border-[color:var(--accent)] [appearance:textfield] [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0"
                                    style="--accent: {{ $accent }}"
                                >
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="openConfirm('topup')"
                                    class="rounded-xl px-5 py-3 text-sm font-bold text-white transition-transform hover:scale-105"
                                    style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));">
                                    Continue
                                </button>
                                <button type="button" @click="topupOpen = false"
                                    class="rounded-xl border border-white/10 px-5 py-3 text-sm text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
                                    Cancel
                                </button>
                            </div>
                        </div>
                        <p class="mt-2.5 text-[11px] text-[#565B6E]">Top-ups join your existing position immediately — your maturity date doesn't change.</p>
                    </div>
                </div>

                {{-- Contribution timeline --}}
                <div class="border-t border-white/[0.06] p-6 sm:p-7">
                    <p class="mb-4 text-[11px] font-bold uppercase tracking-wide text-[#565B6E]">Deployment history</p>

                    <div class="space-y-0">
                        @foreach($contributions as $c)
                            <div class="flex items-center gap-3.5 {{ !$loop->last ? 'pb-4' : '' }} {{ !$loop->first ? 'pt-4 border-t border-white/[0.05]' : '' }}">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border" style="border-color: color-mix(in srgb, {{ $accent }} 35%, transparent); background: color-mix(in srgb, {{ $accent }} 10%, transparent);">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="{{ $accent }}" stroke-width="2.4"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-[#F2F3F7]">{{ ($c->type ?? 'deploy') === 'topup' ? 'Capital Top-Up' : 'Initial Deployment' }}</p>
                                    <p class="text-[11px] text-[#565B6E]">{{ \Illuminate\Support\Carbon::parse($c->created_at)->format('M j, Y · g:i A') }}</p>
                                </div>
                                <span class="font-['IBM_Plex_Mono'] text-sm font-semibold tabular-nums text-[#F2F3F7]">+${{ number_format($c->amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Early exit — only meaningful before maturity, and only for an actionable subscription --}}
                @if(!$isMatured && $isActionable)
                    <div class="border-t border-white/[0.06] px-6 py-4 sm:px-7">
                        <button
                            type="button"
                            @click="openConfirm('earlyExit')"
                            class="text-[11px] font-medium text-[#565B6E] underline decoration-dotted underline-offset-2 transition-colors hover:text-[#F2545B]"
                        >
                            Early exit (8% fee)
                        </button>
                    </div>
                @endif

            </div>

        @elseif(!$isActionable)

            {{-- Refunded / closed / any status other than active or in_renewal_window.
                 No deploy, no top-up, no early exit — nothing here is actionable. --}}
            <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-8 text-center">
                <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-white/[0.05] text-[#565B6E]">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M12 8v4l2.5 2.5"/><circle cx="12" cy="12" r="9"/></svg>
                </div>
                <p class="mt-4 font-['Sora'] text-lg font-bold text-[#F2F3F7]">This pack is {{ strtolower($this->subscription->status->label()) }}</p>
                <p class="mx-auto mt-2 max-w-sm text-sm text-[#888EA3]">No further capital actions are available on this pack. Head back to My Packs to see your other subscriptions.</p>
            </div>

        @elseif($isMatured)

            {{-- Cycle ended without ever deploying capital --}}
            <div class="rounded-2xl border border-[#F0A93D]/25 bg-white/[0.02] p-8 text-center">
                <p class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">This pack's cycle ended without capital being deployed</p>
                <p class="mx-auto mt-2 max-w-sm text-sm text-[#888EA3]">No position was ever opened, so there's nothing to trade or renew. Contact support if this doesn't look right.</p>
            </div>

        @else

            {{-- ═══════════════════════════════════════════════════════════
                 DEPLOY — no capital committed yet. One clear CTA.
                 ═══════════════════════════════════════════════════════════ --}}

            <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02] p-8 text-center shadow-[inset_0_1px_0_0_rgba(255,255,255,0.05)] sm:p-10">
                <div class="pointer-events-none absolute left-1/2 top-0 h-40 w-72 -translate-x-1/2 -translate-y-1/2 rounded-full blur-3xl opacity-[0.1]" style="background: {{ $accent }}"></div>

                <div class="relative">
                    <x-packs.tier-glyph :color="$accent" size="lg" class="mx-auto" />
                    <h2 class="mt-5 font-['Sora'] text-xl font-bold text-[#F2F3F7]">Deploy capital to activate this position</h2>
                    <p class="mx-auto mt-2 max-w-sm text-sm text-[#888EA3]">
                        Deploy the minimum to get started — you can add more capital to this same position anytime while it trades.
                    </p>

                    <div x-show="!deployOpen" x-cloak>
                        <button
                            type="button"
                            @click="deployOpen = true"
                            class="mt-6 rounded-xl px-7 py-3.5 text-sm font-bold text-white shadow-[0_4px_18px_-2px_rgba(0,0,0,0.4)] transition-transform hover:scale-105"
                            style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));"
                        >
                            Deploy ${{ number_format($this->minCapital, 2) }} minimum
                        </button>
                    </div>

                    <div x-show="deployOpen" x-cloak x-transition.opacity.duration.150ms>
                        <div class="mx-auto mt-6 max-w-sm text-left">
                            <div class="relative">
                                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 font-['IBM_Plex_Mono'] text-sm text-[#565B6E]">$</span>
                                <input
                                    type="number" step="0.01" min="{{ $this->minCapital }}"
                                    wire:model="deployAmount"
                                    inputmode="decimal"
                                    autocomplete="off"
                                    @wheel.prevent
                                    class="w-full rounded-xl border border-white/10 bg-white/[0.03] py-3 pl-8 pr-4 text-center font-['IBM_Plex_Mono'] text-lg font-semibold tabular-nums text-[#F2F3F7] outline-none transition-colors focus:border-[color:var(--accent)] [appearance:textfield] [-moz-appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-inner-spin-button]:m-0"
                                    style="--accent: {{ $accent }}"
                                >
                            </div>
                            <p class="mt-2 text-center text-[11px] text-[#565B6E]">Minimum ${{ number_format($this->minCapital, 2) }}</p>
                            <div class="mt-4 flex justify-center gap-2">
                                <button type="button" @click="openConfirm('deploy')"
                                    class="rounded-xl px-6 py-3 text-sm font-bold text-white transition-transform hover:scale-105"
                                    style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));">
                                    Continue
                                </button>
                                <button type="button" @click="deployOpen = false"
                                    class="rounded-xl border border-white/10 px-6 py-3 text-sm text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════
         SHARED CONFIRMATION MODAL — replaces every wire:confirm in this
         page with one bank-grade breakdown. `confirm.type` picks which
         body renders; the actual Livewire call only fires when the person
         presses the modal's Confirm button, never on the trigger click.
         ═══════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="confirm.open"
        x-cloak
        x-transition.opacity.duration.150ms
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm"
        style="backdrop-filter: blur(6px);"
        @keydown.escape.window="closeConfirm()"
    >
        <div
            @click.outside="closeConfirm()"
            x-show="confirm.open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="w-full max-w-sm rounded-2xl border border-white/10 bg-[#0B0C12] p-6 shadow-2xl"
        >
            {{-- Icon --}}
            <div
                class="mx-auto flex h-11 w-11 items-center justify-center rounded-full"
                :class="{
                    'bg-[#F2545B]/10 text-[#F2545B]': confirm.type === 'earlyExit' || confirm.type === 'withdraw',
                    'bg-[#3ECF8E]/10 text-[#3ECF8E]': confirm.type === 'continue' || confirm.type === 'compound',
                }"
                :style="!(confirm.type === 'earlyExit' || confirm.type === 'withdraw' || confirm.type === 'continue' || confirm.type === 'compound') ? `background: color-mix(in srgb, {{ $accent }} 12%, transparent); color: {{ $accent }};` : ''"
            >
                <svg x-show="confirm.type === 'earlyExit' || confirm.type === 'withdraw'" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                <svg x-show="!(confirm.type === 'earlyExit' || confirm.type === 'withdraw')" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
            </div>

            {{-- Title + description per type --}}
            <div class="mt-4 text-center">
                <template x-if="confirm.type === 'deploy'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Deployment</h3></template>
                <template x-if="confirm.type === 'topup'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Top-Up</h3></template>
                <template x-if="confirm.type === 'earlyExit'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Early Exit</h3></template>
                <template x-if="confirm.type === 'withdraw'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Withdrawal</h3></template>
                <template x-if="confirm.type === 'continue'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Continue</h3></template>
                <template x-if="confirm.type === 'compound'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Auto-Compound</h3></template>
                <template x-if="confirm.type === 'upgrade'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Upgrade</h3></template>
                <template x-if="confirm.type === 'refund'"><h3 class="font-['Sora'] text-lg font-bold text-[#F2F3F7]">Confirm Refund</h3></template>
            </div>

            {{-- Breakdown rows — receipt style --}}
            <div class="mt-5 space-y-2 rounded-xl border border-white/[0.06] bg-white/[0.02] p-4 text-sm">

                <template x-if="confirm.type === 'deploy'">
                    <div class="flex items-center justify-between">
                        <span class="text-[#888EA3]">Deploy amount</span>
                        <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]" x-text="'$' + Number($wire.deployAmount || 0).toFixed(2)"></span>
                    </div>
                </template>

                <template x-if="confirm.type === 'topup'">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Top-up amount</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]" x-text="'$' + Number($wire.topUpAmount || 0).toFixed(2)"></span>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-white/[0.06] pt-2">
                            <span class="text-[#888EA3]">New position total</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]" x-text="'$' + (Number($wire.topUpAmount || 0) + {{ $capitalDeployed }}).toFixed(2)"></span>
                        </div>
                    </div>
                </template>

                <template x-if="confirm.type === 'earlyExit'">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Capital</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]">${{ number_format($capitalDeployed, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Early exit fee (8%)</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2545B]">−${{ number_format($earlyExitFee, 2) }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-white/[0.06] pt-2">
                            <span class="font-semibold text-[#F2F3F7]">You'll receive</span>
                            <span class="font-['IBM_Plex_Mono'] font-bold text-[#F2F3F7]">${{ number_format($earlyExitNet, 2) }}</span>
                        </div>
                    </div>
                </template>

                <template x-if="confirm.type === 'withdraw'">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Capital</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]">${{ number_format($capitalDeployed, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Earned profit</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#3ECF8E]">+${{ number_format($totalEarned, 2) }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-white/[0.06] pt-2">
                            <span class="font-semibold text-[#F2F3F7]">Total to wallet</span>
                            <span class="font-['IBM_Plex_Mono'] font-bold text-[#F2F3F7]">${{ number_format($capitalDeployed + $totalEarned, 2) }}</span>
                        </div>
                    </div>
                </template>

                <template x-if="confirm.type === 'continue'">
                    <div class="flex items-center justify-between">
                        <span class="text-[#888EA3]">Capital rolling into new cycle</span>
                        <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]">${{ number_format($capitalDeployed, 2) }}</span>
                    </div>
                </template>

                <template x-if="confirm.type === 'compound'">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Capital</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]">${{ number_format($capitalDeployed, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Profit restaked</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#3ECF8E]">+${{ number_format($totalEarned, 2) }}</span>
                        </div>
                        <div class="mt-2 flex items-center justify-between border-t border-white/[0.06] pt-2">
                            <span class="font-semibold text-[#F2F3F7]">New position total</span>
                            <span class="font-['IBM_Plex_Mono'] font-bold text-[#F2F3F7]">${{ number_format($capitalDeployed + $totalEarned, 2) }}</span>
                        </div>
                    </div>
                </template>

                <template x-if="confirm.type === 'upgrade'">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Upgrading to</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]">
                                {{ optional($this->upgradeOptions->firstWhere('id', $upgradingToTierId))->name ?? '—' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[#888EA3]">Compound profit too</span>
                            <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]" x-text="$wire.upgradeCompound ? 'Yes' : 'No'"></span>
                        </div>
                    </div>
                </template>

                <template x-if="confirm.type === 'refund'">
                    <div class="flex items-center justify-between">
                        <span class="text-[#888EA3]">Refund amount</span>
                        <span class="font-['IBM_Plex_Mono'] font-semibold text-[#F2F3F7]">${{ number_format($this->subscription->price_paid, 2) }}</span>
                    </div>
                </template>

            </div>

            <p class="mt-3 text-center text-[11px] text-[#565B6E]" x-show="confirm.type === 'earlyExit' || confirm.type === 'withdraw'">
                This action is final and can't be undone.
            </p>

            {{-- Actions --}}
            <div class="mt-5 flex gap-2">
                <button
                    type="button"
                    @click="closeConfirm()"
                    class="flex-1 rounded-xl border border-white/10 px-4 py-3 text-sm font-semibold text-[#888EA3] transition-colors hover:text-[#F2F3F7]"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-60 cursor-wait"
                    wire:target="deploy,topUp,earlyExit,withdraw,continueCycle,autoCompound,confirmUpgrade,requestRefund"
                    @click="
                        if (confirm.type === 'deploy') $wire.deploy();
                        if (confirm.type === 'topup') $wire.topUp();
                        if (confirm.type === 'earlyExit') $wire.earlyExit();
                        if (confirm.type === 'withdraw') $wire.withdraw();
                        if (confirm.type === 'continue') $wire.continueCycle();
                        if (confirm.type === 'compound') $wire.autoCompound();
                        if (confirm.type === 'upgrade') $wire.confirmUpgrade();
                        if (confirm.type === 'refund') $wire.requestRefund();
                    "
                    class="relative flex-1 rounded-xl px-4 py-3 text-sm font-bold text-white transition-transform hover:scale-105"
                    :class="{
                        'bg-[#F2545B]': confirm.type === 'earlyExit' || confirm.type === 'withdraw',
                        'bg-[#3ECF8E] text-[#07080C]': confirm.type === 'continue' || confirm.type === 'compound',
                    }"
                    :style="!(confirm.type === 'earlyExit' || confirm.type === 'withdraw' || confirm.type === 'continue' || confirm.type === 'compound') ? `background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));` : ''"
                >
                    <span wire:loading.remove wire:target="deploy,topUp,earlyExit,withdraw,continueCycle,autoCompound,confirmUpgrade,requestRefund">Confirm</span>
                    <span wire:loading wire:target="deploy,topUp,earlyExit,withdraw,continueCycle,autoCompound,confirmUpgrade,requestRefund" class="inline-flex items-center gap-1.5">
                        <svg class="animate-spin" width="14" height="14" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.25"/><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>
                        Processing…
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>