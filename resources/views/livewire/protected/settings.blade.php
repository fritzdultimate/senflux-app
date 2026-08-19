{{-- resources/views/livewire/protected/settings.blade.php --}}
<div>

    <div class="set" wire:poll.3000ms="clearFlashes">

        {{-- ── Profile ──────────────────────────────────────────────────────── --}}
        <div class="set-panel">
            <p class="set-panel__title">Profile</p>

            @if($profileFlash)
                <div class="set-flash set-flash--success">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $profileFlash }}
                </div>
            @endif

            <div class="set-grid">
                <div class="set-field">
                    <label class="set-label">Full Name</label>
                    <input type="text" wire:model="name" class="set-input">
                    @error('name') <span class="set-error">{{ $message }}</span> @enderror
                </div>

                <div class="set-field">
                    <label class="set-label">Email Address</label>
                    <input type="email" wire:model="email" class="set-input">
                    @error('email') <span class="set-error">{{ $message }}</span> @enderror
                </div>

                <div class="set-field">
                    <label class="set-label">Phone Number</label>
                    <input type="text" wire:model="phone" class="set-input" placeholder="Optional">
                    @error('phone') <span class="set-error">{{ $message }}</span> @enderror
                </div>

                <div class="set-field">
                    <label class="set-label">Country</label>
                    <input type="text" wire:model="country" class="set-input" placeholder="Optional">
                    @error('country') <span class="set-error">{{ $message }}</span> @enderror
                </div>

                <div class="set-field set-field--full">
                    <label class="set-label">Timezone</label>
                    <select wire:model="timezone" class="set-input set-select">
                        @foreach($timezones as $tz)
                            <option value="{{ $tz }}">{{ $tz }}</option>
                        @endforeach
                    </select>
                    @error('timezone') <span class="set-error">{{ $message }}</span> @enderror
                </div>
            </div>

            <button wire:click="saveProfile" wire:loading.attr="disabled" type="button" class="set-btn-primary">
                <span wire:loading.remove wire:target="saveProfile">Save Profile</span>
                <span wire:loading wire:target="saveProfile">Saving…</span>
            </button>
        </div>

        {{-- ── KYC status ───────────────────────────────────────────────────── --}}
        <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-5 shadow-[inset_0_1px_0_0_rgba(255,255,255,0.05)] sm:p-6">
            <p class="font-['Sora'] text-sm font-semibold text-[#F2F3F7]">Identity Verification</p>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                @if(auth()->user()->kyc_tier)
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-[#2DD4A7]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path d="M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm text-[#F2F3F7]">{{ \App\Enums\KycTier::from(auth()->user()->kyc_tier)->label() }} Verified</p>
                            <p class="text-xs text-[#565B6E]">Verified on {{ auth()->user()->kyc_verified_at?->format('M j, Y') }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-[#F0A93D]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                        <div>
                            <p class="text-sm text-[#F2F3F7]">Not Verified</p>
                            <p class="text-xs text-[#565B6E]">Identity verification is required before you can withdraw.</p>
                        </div>
                    </div>
                @endif
                <a href="{{ route('dashboard.kyc') }}" wire:navigate
                   class="shrink-0 rounded-lg border border-white/10 px-4 py-2.5 text-center text-xs font-semibold text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
                    Manage verification
                </a>
            </div>
        </div>

        {{-- ── Password ─────────────────────────────────────────────────────── --}}
        <div class="set-panel">
            <p class="set-panel__title">Change Password</p>

            @if($passwordFlash)
                <div class="set-flash set-flash--success">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $passwordFlash }}
                </div>
            @endif

            @if($passwordError)
                <div class="set-flash set-flash--error">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $passwordError }}
                </div>
            @endif

            <div class="set-grid">
                <div class="set-field set-field--full">
                    <label class="set-label">Current Password</label>
                    <input type="password" wire:model="current_password" class="set-input" autocomplete="current-password">
                    @error('current_password') <span class="set-error">{{ $message }}</span> @enderror
                </div>

                <div class="set-field">
                    <label class="set-label">New Password</label>
                    <input type="password" wire:model="new_password" class="set-input" autocomplete="new-password">
                    @error('new_password') <span class="set-error">{{ $message }}</span> @enderror
                </div>

                <div class="set-field">
                    <label class="set-label">Confirm New Password</label>
                    <input type="password" wire:model="new_password_confirmation" class="set-input" autocomplete="new-password">
                </div>
            </div>

            <button wire:click="updatePassword" wire:loading.attr="disabled" type="button" class="set-btn-primary">
                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                <span wire:loading wire:target="updatePassword">Updating…</span>
            </button>
        </div>

        {{-- ── 2FA ───────────────────────────────────────────────────────────── --}}
        <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-5 shadow-[inset_0_1px_0_0_rgba(255,255,255,0.05)] sm:p-6">
            <p class="font-['Sora'] text-sm font-semibold text-[#F2F3F7]">Two-Factor Authentication</p>

            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="text-sm {{ $two_factor_enabled ? 'text-[#2DD4A7]' : 'text-[#888EA3]' }}">
                        {{ $two_factor_enabled ? 'Enabled' : 'Disabled' }}
                    </span>
                    <p class="mt-0.5 text-xs text-[#565B6E]">
                        Adds an extra layer of security to your account using an authenticator app.
                    </p>
                </div>
                <a href="{{ route('dashboard.security') }}" wire:navigate
                   class="shrink-0 rounded-lg border border-white/10 px-4 py-2.5 text-center text-xs font-semibold text-[#888EA3] transition-colors hover:text-[#F2F3F7]">
                    Manage 2FA
                </a>
            </div>
        </div>

    </div>

</div>
