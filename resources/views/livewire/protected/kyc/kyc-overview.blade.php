@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
@endpush

@php
    $tiers = [
        ['enum' => \App\Enums\KycTier::BASIC, 'submission' => $this->latestBasic, 'accent' => '#60a5fa'],
        ['enum' => \App\Enums\KycTier::ENHANCED, 'submission' => $this->latestEnhanced, 'accent' => '#8B7CF6'],
    ];

    $basicApproved = $this->user->hasApprovedTier(\App\Enums\KycTier::BASIC);
@endphp

<div
    class="relative min-h-screen overflow-hidden bg-[#07080C]"
    x-data="{ basicOpen: false, enhancedOpen: false }"
>
    {{-- ── Ambient backdrop glow ──────────────────────────────────────── --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[420px] overflow-hidden">
        <div class="absolute left-1/2 top-[-180px] h-[420px] w-[680px] -translate-x-1/2 rounded-full blur-3xl opacity-[0.12]" style="background: #8B7CF6"></div>
    </div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.4]" style="background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="relative mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:px-0">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="mb-7 flex items-start gap-4">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-[#8B7CF6]/25 bg-[#8B7CF6]/10">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#8B7CF6" stroke-width="1.8"><path d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </span>
            <div>
                <p class="font-['IBM_Plex_Mono'] text-[11px] tabular-nums tracking-wider text-[#565B6E]">COMPLIANCE</p>
                <h1 class="font-['Sora'] text-2xl font-bold text-[#F2F3F7]">Identity Verification</h1>
                <p class="mt-1 max-w-lg text-sm text-[#888EA3]">Verify your identity to unlock withdrawals. Basic verification covers standard limits — Enhanced verification unlocks the highest limits.</p>
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

        <div class="space-y-5">
            @foreach($tiers as $row)
                @php
                    /** @var \App\Enums\KycTier $tier */
                    $tier = $row['enum'];
                    /** @var \App\Models\KycSubmission|null $submission */
                    $submission = $row['submission'];
                    $accent = $row['accent'];
                    $status = $submission?->status ?? \App\Enums\KycStatus::UNSUBMITTED;
                    $tone = match($status) {
                        \App\Enums\KycStatus::APPROVED => 'positive',
                        \App\Enums\KycStatus::PENDING => 'warning',
                        \App\Enums\KycStatus::REJECTED => 'negative',
                        default => 'neutral',
                    };
                    $locked = $tier === \App\Enums\KycTier::ENHANCED && !$basicApproved;
                @endphp

                <x-ui.panel eyebrow="{{ $tier->label() }} Verification" title="{{ $tier === \App\Enums\KycTier::BASIC ? 'Government ID + Selfie' : 'Proof of Address' }}">
                    <x-slot:actions>
                        <x-ui.status-pill :label="$status->label()" :tone="$tone" :pulse="$tone === 'warning'" />
                    </x-slot:actions>

                    <p class="text-sm text-[#888EA3]">{{ $tier->description() }}</p>

                    @if($locked)
                        <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/[0.02] px-4 py-3">
                            <svg class="h-4 w-4 shrink-0 text-[#565B6E]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            <p class="text-xs text-[#565B6E]">Complete Basic verification first to unlock Enhanced verification.</p>
                        </div>
                    @elseif($status === \App\Enums\KycStatus::APPROVED)
                        <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-[#2DD4A7]/20 bg-[#2DD4A7]/[0.05] px-4 py-3">
                            <svg class="h-4 w-4 shrink-0 text-[#2DD4A7]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-xs text-[#2DD4A7]">Verified on {{ $submission->reviewed_at?->format('M j, Y') }}</p>
                        </div>
                    @elseif($status === \App\Enums\KycStatus::PENDING)
                        <div class="mt-4 flex items-center gap-2.5 rounded-xl border border-[#F0A93D]/20 bg-[#F0A93D]/[0.05] px-4 py-3">
                            <svg class="h-4 w-4 shrink-0 text-[#F0A93D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M12 8v4l2.5 2.5"/><circle cx="12" cy="12" r="9"/></svg>
                            <p class="text-xs text-[#F0A93D]">Submitted {{ $submission->submitted_at?->diffForHumans() }} — under review. This usually takes 1-2 business days.</p>
                        </div>
                    @else
                        @if($status === \App\Enums\KycStatus::REJECTED)
                            <div class="mt-4 rounded-xl border border-[#F2545B]/20 bg-[#F2545B]/[0.05] px-4 py-3">
                                <p class="text-[11px] font-bold uppercase tracking-wide text-[#F2545B]">Rejected — reason</p>
                                <p class="mt-1 text-xs text-[#F2545B]/90">{{ $submission->rejection_reason }}</p>
                            </div>
                        @endif

                        {{-- ── Toggle button ─────────────────────────────────── --}}
                        @if($tier === \App\Enums\KycTier::BASIC)
                            <div x-show="!basicOpen" x-cloak class="mt-4">
                                <button type="button" @click="basicOpen = true"
                                    class="rounded-lg px-5 py-2.5 text-xs font-bold text-white transition-transform hover:scale-105"
                                    style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));">
                                    {{ $status === \App\Enums\KycStatus::REJECTED ? 'Resubmit documents' : 'Start verification' }}
                                </button>
                            </div>

                            {{-- ── Basic upload form ────────────────────────────── --}}
                            <div x-show="basicOpen" x-cloak x-transition.opacity.duration.150ms class="mt-5 border-t border-white/[0.06] pt-5">
                                <form wire:submit.prevent="submitBasic" class="space-y-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-[#565B6E]">Document type</label>
                                            <select wire:model="idDocumentType" class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-[#F2F3F7] outline-none focus:border-white/30">
                                                <option value="passport">Passport</option>
                                                <option value="national_id">National ID</option>
                                                <option value="drivers_license">Driver's License</option>
                                            </select>
                                            @error('idDocumentType') <p class="mt-1 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-[#565B6E]">Document number</label>
                                            <input type="text" wire:model="idDocumentNumber" placeholder="e.g. P1234567"
                                                class="w-full rounded-lg border border-white/10 bg-white/[0.03] px-3.5 py-2.5 text-sm text-[#F2F3F7] outline-none focus:border-white/30">
                                            @error('idDocumentNumber') <p class="mt-1 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-3">
                                        <div>
                                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-[#565B6E]">ID — Front</label>
                                            <input type="file" wire:model="idFront" accept="image/*,.pdf"
                                                class="block w-full text-xs text-[#888EA3] file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-[#F2F3F7]">
                                            <div wire:loading wire:target="idFront" class="mt-1 text-[10px] text-[#565B6E]">Uploading…</div>
                                            @error('idFront') <p class="mt-1 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-[#565B6E]">ID — Back <span class="normal-case text-[#565B6E]/70">(optional)</span></label>
                                            <input type="file" wire:model="idBack" accept="image/*,.pdf"
                                                class="block w-full text-xs text-[#888EA3] file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-[#F2F3F7]">
                                            <div wire:loading wire:target="idBack" class="mt-1 text-[10px] text-[#565B6E]">Uploading…</div>
                                            @error('idBack') <p class="mt-1 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-[#565B6E]">Selfie</label>
                                            <input type="file" wire:model="selfie" accept="image/*"
                                                class="block w-full text-xs text-[#888EA3] file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-[#F2F3F7]">
                                            <div wire:loading wire:target="selfie" class="mt-1 text-[10px] text-[#565B6E]">Uploading…</div>
                                            @error('selfie') <p class="mt-1 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <p class="text-[11px] text-[#565B6E]">Accepted formats: JPG, PNG, PDF. Max 8MB per file. Your documents are stored privately and only visible to you and our compliance team.</p>

                                    <div class="flex gap-2 pt-1">
                                        <button type="submit" wire:loading.attr="disabled" wire:target="submitBasic"
                                            class="rounded-lg px-5 py-2.5 text-xs font-bold text-white transition-transform hover:scale-105 disabled:opacity-60"
                                            style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));">
                                            <span wire:loading.remove wire:target="submitBasic">Submit for review</span>
                                            <span wire:loading wire:target="submitBasic">Submitting…</span>
                                        </button>
                                        <button type="button" @click="basicOpen = false"
                                            class="rounded-lg border border-white/10 px-5 py-2.5 text-xs font-semibold text-[#888EA3] hover:text-[#F2F3F7]">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div x-show="!enhancedOpen" x-cloak class="mt-4">
                                <button type="button" @click="enhancedOpen = true"
                                    class="rounded-lg px-5 py-2.5 text-xs font-bold text-white transition-transform hover:scale-105"
                                    style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));">
                                    {{ $status === \App\Enums\KycStatus::REJECTED ? 'Resubmit document' : 'Start verification' }}
                                </button>
                            </div>

                            {{-- ── Enhanced upload form ─────────────────────────── --}}
                            <div x-show="enhancedOpen" x-cloak x-transition.opacity.duration.150ms class="mt-5 border-t border-white/[0.06] pt-5">
                                <form wire:submit.prevent="submitEnhanced" class="space-y-4">
                                    <div>
                                        <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-wide text-[#565B6E]">Proof of address <span class="normal-case text-[#565B6E]/70">(utility bill or bank statement, within 90 days)</span></label>
                                        <input type="file" wire:model="proofOfAddress" accept="image/*,.pdf"
                                            class="block w-full text-xs text-[#888EA3] file:mr-3 file:rounded-lg file:border-0 file:bg-white/10 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-[#F2F3F7]">
                                        <div wire:loading wire:target="proofOfAddress" class="mt-1 text-[10px] text-[#565B6E]">Uploading…</div>
                                        @error('proofOfAddress') <p class="mt-1 text-[11px] text-[#F2545B]">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="flex gap-2 pt-1">
                                        <button type="submit" wire:loading.attr="disabled" wire:target="submitEnhanced"
                                            class="rounded-lg px-5 py-2.5 text-xs font-bold text-white transition-transform hover:scale-105 disabled:opacity-60"
                                            style="background: linear-gradient(135deg, {{ $accent }}, color-mix(in srgb, {{ $accent }} 70%, black));">
                                            <span wire:loading.remove wire:target="submitEnhanced">Submit for review</span>
                                            <span wire:loading wire:target="submitEnhanced">Submitting…</span>
                                        </button>
                                        <button type="button" @click="enhancedOpen = false"
                                            class="rounded-lg border border-white/10 px-5 py-2.5 text-xs font-semibold text-[#888EA3] hover:text-[#F2F3F7]">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endif

                    {{-- ── Submitted documents (view only) ──────────────────── --}}
                    @if($submission && $submission->documentFields())
                        <div class="mt-5 flex flex-wrap gap-2 border-t border-white/[0.06] pt-4">
                            @foreach($submission->documentFields() as $field => $label)
                                <a href="{{ URL::temporarySignedRoute('kyc.documents.show', now()->addMinutes(5), ['submission' => $submission->id, 'field' => $field]) }}"
                                   target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.02] px-3 py-1.5 text-[11px] font-medium text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
                                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </x-ui.panel>
            @endforeach
        </div>

    </div>
</div>
