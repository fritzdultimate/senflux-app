@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
@endpush

<div class="relative min-h-screen overflow-hidden bg-[#07080C]">

    {{-- ── Ambient backdrop glow ──────────────────────────────────────── --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[420px] overflow-hidden">
        <div class="absolute left-1/2 top-[-180px] h-[420px] w-[680px] -translate-x-1/2 rounded-full blur-3xl opacity-[0.12]" style="background: #60a5fa"></div>
    </div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.4]" style="background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="relative mx-auto w-full max-w-2xl px-4 py-10 sm:px-6 lg:px-0">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="mb-7 flex items-start gap-4">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#60a5fa]/25 bg-[#60a5fa]/10">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#60a5fa" stroke-width="1.8"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <div>
                <p class="font-['IBM_Plex_Mono'] text-[11px] tabular-nums tracking-wider text-[#565B6E]">SECURITY</p>
                <h1 class="font-['Sora'] text-2xl font-bold text-[#F2F3F7]">Two-Factor Authentication</h1>
                <p class="mt-1 max-w-lg text-sm text-[#888EA3]">Add a second layer of protection using an authenticator app like Google Authenticator or Authy.</p>
            </div>
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

        {{-- ── Recovery codes (shown once) ──────────────────────────────── --}}
        @if(!empty($recoveryCodes) && !$recoveryCodesAcknowledged)
            <x-ui.panel eyebrow="Save these now" title="Your recovery codes" tone="warning" class="mb-5">
                <p class="mb-4 text-sm text-[#888EA3]">Each code can be used once to sign in if you lose access to your authenticator app. Store them somewhere safe — this is the only time they'll be shown.</p>

                <div class="grid grid-cols-2 gap-2 rounded-xl border border-white/10 bg-black/30 p-4 sm:grid-cols-2">
                    @foreach($recoveryCodes as $code)
                        <span class="font-['IBM_Plex_Mono'] text-sm tracking-wider text-[#F2F3F7]">{{ $code }}</span>
                    @endforeach
                </div>

                <label class="mt-4 flex items-start gap-2.5 text-xs text-[#888EA3]">
                    <input type="checkbox" wire:model.live="recoveryCodesAcknowledged" class="mt-0.5 rounded border-white/20 bg-transparent">
                    I've saved these recovery codes somewhere safe.
                </label>

                <button type="button" wire:click="acknowledgeRecoveryCodes"
                    @disabled(!$recoveryCodesAcknowledged)
                    class="mt-4 rounded-lg bg-[#F0A93D] px-5 py-2.5 text-xs font-bold text-black transition-transform enabled:hover:scale-105 disabled:opacity-40 disabled:cursor-not-allowed">
                    Continue
                </button>
            </x-ui.panel>
        @else

            {{-- ── Current state panel ──────────────────────────────────── --}}
            <x-ui.panel eyebrow="Authenticator App" title="{{ $this->isEnabled ? 'Enabled' : 'Not Enabled' }}">
                <x-slot:actions>
                    <x-ui.status-pill :label="$this->isEnabled ? 'Active' : 'Inactive'" :tone="$this->isEnabled ? 'positive' : 'neutral'" />
                </x-slot:actions>

                @if($this->isEnabled)
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-[#F2F3F7]">Your account is protected with an authenticator app.</p>
                            <p class="mt-1 text-xs text-[#565B6E]">{{ $this->remainingRecoveryCodes }} recovery {{ Str::plural('code', $this->remainingRecoveryCodes) }} remaining.</p>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <button type="button" wire:click="regenerateRecoveryCodes"
                                class="rounded-lg border border-white/10 px-4 py-2.5 text-xs font-semibold text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
                                New codes
                            </button>
                            <button type="button" wire:click="requestDisable"
                                class="rounded-lg border border-[#F2545B]/30 px-4 py-2.5 text-xs font-semibold text-[#F2545B] transition-colors hover:bg-[#F2545B]/10">
                                Disable
                            </button>
                        </div>
                    </div>

                    {{-- ── Step-up prompt for regenerate/disable ────────────── --}}
                    @if($stepUpRequired)
                        <div class="mt-4 rounded-xl border border-white/10 bg-white/[0.03] p-4">
                            <p class="mb-1 text-xs font-bold uppercase tracking-wide text-[#a3a3b8]">Security check</p>
                            <p class="mb-3 text-xs text-[#565B6E]">Enter your current 6-digit authenticator code to continue.</p>
                            <input type="text" wire:model="stepUpCode" inputmode="numeric" autocomplete="one-time-code" placeholder="000000" maxlength="10"
                                class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-4 py-3 text-center font-mono text-lg tracking-[0.3em] text-[#F2F3F7] outline-none focus:border-white/30">
                            @if($stepUpError)
                                <p class="mt-2 text-xs text-[#F2545B]">{{ $stepUpError }}</p>
                            @endif
                            <div class="mt-3 flex gap-2">
                                <button wire:click="cancelStepUp" type="button" class="flex-1 rounded-lg border border-white/10 px-4 py-2.5 text-xs font-semibold text-[#a3a3b8] hover:text-[#F2F3F7]">Cancel</button>
                                <button wire:click="verifyStepUp" wire:loading.attr="disabled" type="button" class="flex-1 rounded-lg bg-white px-4 py-2.5 text-xs font-bold text-black hover:scale-[1.02] transition-transform">Verify</button>
                            </div>
                        </div>
                    @endif

                    {{-- ── Disable confirmation ─────────────────────────────── --}}
                    @if($confirmingDisable)
                        <div class="mt-4 rounded-xl border border-[#F2545B]/20 bg-[#F2545B]/[0.05] p-4">
                            <p class="mb-3 text-xs text-[#F2545B]">Disabling 2FA reduces your account's security. Enter your password to confirm.</p>
                            <input type="password" wire:model="disablePassword" placeholder="Current password"
                                class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-4 py-2.5 text-sm text-[#F2F3F7] outline-none focus:border-white/30">
                            @error('disablePassword') <p class="mt-1 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                            <div class="mt-3 flex gap-2">
                                <button wire:click="cancelDisable" type="button" class="flex-1 rounded-lg border border-white/10 px-4 py-2.5 text-xs font-semibold text-[#a3a3b8] hover:text-[#F2F3F7]">Cancel</button>
                                <button wire:click="disable" wire:loading.attr="disabled" type="button" class="flex-1 rounded-lg bg-[#F2545B] px-4 py-2.5 text-xs font-bold text-white hover:scale-[1.02] transition-transform">Confirm disable</button>
                            </div>
                        </div>
                    @endif

                @elseif(!$pendingSecret)
                    <div>
                        <p class="text-sm text-[#888EA3]">Two-factor authentication is currently off. Enable it to require a code from your phone every time you sign in or move funds.</p>
                        <button type="button" wire:click="beginEnrollment"
                            class="mt-4 rounded-lg bg-gradient-to-br from-[#60a5fa] to-[#3b82f6] px-5 py-2.5 text-xs font-bold text-white transition-transform hover:scale-105">
                            Enable two-factor authentication
                        </button>
                    </div>
                @else
                    {{-- ── Enrollment: scan QR + confirm ────────────────────── --}}
                    <div class="grid gap-6 sm:grid-cols-[auto,1fr]">
                        <div wire:key="qr-{{ $pendingSecret }}"
                             x-data
                             x-init="$el.innerHTML=''; new QRCode($el, { text: @js($this->pendingOtpAuthUrl), width: 168, height: 168, colorDark:'#000000', colorLight:'#ffffff' })"
                             class="mx-auto h-[168px] w-[168px] shrink-0 overflow-hidden rounded-xl bg-white p-2 sm:mx-0">
                        </div>

                        <div>
                            <p class="text-sm text-[#F2F3F7]">1. Scan this QR code with your authenticator app.</p>
                            <p class="mt-2 text-xs text-[#565B6E]">Can't scan it? Enter this key manually:</p>
                            <p class="mt-1 select-all break-all rounded-lg border border-white/10 bg-black/30 px-3 py-2 font-['IBM_Plex_Mono'] text-xs text-[#F2F3F7]">{{ $pendingSecret }}</p>

                            <p class="mt-4 text-sm text-[#F2F3F7]">2. Enter the 6-digit code it generates.</p>
                            <div class="mt-2 flex flex-col gap-2 sm:flex-row">
                                <input type="text" wire:model="confirmCode" inputmode="numeric" autocomplete="one-time-code" placeholder="000000" maxlength="6"
                                    class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-4 py-2.5 text-center font-mono text-lg tracking-[0.3em] text-[#F2F3F7] outline-none focus:border-white/30 sm:max-w-[160px]">
                                <div class="flex gap-2">
                                    <button type="button" wire:click="confirmEnrollment" wire:loading.attr="disabled"
                                        class="rounded-lg bg-gradient-to-br from-[#60a5fa] to-[#3b82f6] px-5 py-2.5 text-xs font-bold text-white transition-transform hover:scale-105 disabled:opacity-60">
                                        <span wire:loading.remove wire:target="confirmEnrollment">Verify &amp; Enable</span>
                                        <span wire:loading wire:target="confirmEnrollment">Verifying…</span>
                                    </button>
                                    <button type="button" wire:click="cancelEnrollment" class="rounded-lg border border-white/10 px-4 py-2.5 text-xs font-semibold text-[#888EA3] hover:text-[#F2F3F7]">Cancel</button>
                                </div>
                            </div>
                            @error('confirmCode') <p class="mt-2 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endif
            </x-ui.panel>
        @endif

    </div>
</div>
