{{-- resources/views/livewire/auth/reset-password.blade.php --}}

<div class="flex min-h-screen items-center justify-center bg-[#05050c] px-5 py-10"
     style="background-image:linear-gradient(rgba(123,92,245,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(123,92,245,.025) 1px,transparent 1px);background-size:44px 44px">

    <div class="w-full max-w-[420px]">

        {{-- Logo --}}
        <div class="fade-up flex items-center gap-2 mb-8">
            <x-senflux.logo width="22" height="22" gradient-id="rp-logo" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.07em]">SENFLUX</span>
        </div>

        <div class="fade-up mb-7">
            <h1 class="font-syne text-[22px] font-extrabold text-white mb-1.5">Set new password</h1>
            <p class="text-[13.5px] text-[#7a7a9a]">Must be at least 8 characters with a number and symbol.</p>
        </div>

        <form wire:submit="resetPassword" novalidate class="flex flex-col gap-4">

            {{-- Hidden email (pre-filled from URL) --}}
            <input type="hidden" wire:model="email" />

            {{-- Password --}}
            <div class="fade-up-d1 flex flex-col gap-1.5">
                <label class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
                    New password
                </label>
                <div class="relative flex items-center">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#4a4a6a]">
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                            <rect x="3" y="7" width="10" height="8" rx="1.5"/>
                            <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round"/>
                            <circle cx="8" cy="11" r="1.2" fill="currentColor"/>
                        </svg>
                    </div>
                    <input wire:model.live="password"
                           type="{{ $showPassword ? 'text' : 'password' }}"
                           placeholder="Create a strong password"
                           autocomplete="new-password"
                           class="w-full bg-white/[.04] border rounded-xl text-sm text-white placeholder-[#4a4a6a]
                                  py-2.5 pl-10 pr-10
                                  focus:outline-none transition-all duration-200
                                  {{ $errors->has('password')
                                      ? 'border-red-500/60 bg-red-500/5'
                                      : 'border-white/[.07] focus:border-[rgba(123,92,245,.5)] focus:bg-[rgba(123,92,245,.06)]' }}" />
                    <button type="button"
                            wire:click="$toggle('showPassword')"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                   text-[#4a4a6a] hover:text-[#c8c8e0] transition-colors cursor-pointer">
                        @if($showPassword)
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                                <path d="M2 2L14 14M6.5 6.7A2.2 2.2 0 0 0 9.3 9.5M4 4.5C2.7 5.6 1.8 6.8 1 8c1.8 2.8 4.2 5 7 5 1.1 0 2.2-.3 3.2-.8M10.5 5.8C11.5 6.5 12.3 7.2 13 8c-.5.8-1.2 1.6-2 2.3" stroke-linecap="round"/>
                                <circle cx="8" cy="8" r="2.2"/>
                            </svg>
                        @else
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                                <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/>
                                <circle cx="8" cy="8" r="2.2"/>
                            </svg>
                        @endif
                    </button>
                </div>
                {{-- Inline strength meter --}}
                @include('livewire.auth.partials._strength-meter')
                @error('password')
                    <p class="text-[11.5px] text-red-400 flex items-center gap-1">
                        <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="currentColor" stroke-width="1.5">
                            <circle cx="5.5" cy="5.5" r="4.5"/>
                            <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Confirm password --}}
            <div class="fade-up-d2 flex flex-col gap-1.5">
                <label class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
                    Confirm password
                </label>
                <div class="relative flex items-center">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#4a4a6a]">
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                            <rect x="3" y="7" width="10" height="8" rx="1.5"/>
                            <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round"/>
                            <path d="M6 11.5L7.5 13L10 10.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <input wire:model.blur="password_confirmation"
                           type="password"
                           placeholder="Repeat your password"
                           autocomplete="new-password"
                           class="w-full bg-white/[.04] border rounded-xl text-sm text-white placeholder-[#4a4a6a]
                                  py-2.5 pl-10 pr-4
                                  focus:outline-none transition-all duration-200
                                  border-white/[.07] focus:border-[rgba(123,92,245,.5)] focus:bg-[rgba(123,92,245,.06)]" />
                </div>
            </div>

            {{-- Submit --}}
            <div class="fade-up-d3">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full flex items-center justify-center gap-2
                               bg-gradient-to-r from-[#7B5CF5] to-[#4F46E5]
                               hover:from-[#9B7DFF] hover:to-[#6056f5]
                               disabled:opacity-60 disabled:cursor-not-allowed
                               active:scale-[.98] text-white font-syne font-bold text-sm
                               py-3 rounded-xl tracking-wide cursor-pointer
                               shadow-[0_4px_18px_rgba(123,92,245,.45)]
                               transition-all duration-200">
                    <span wire:loading wire:target="resetPassword">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="resetPassword">Update password</span>
                    <span wire:loading wire:target="resetPassword">Updating…</span>
                </button>
            </div>

        </form>

        <div class="fade-up-d4 text-center mt-6">
            <a href="{{ route('login') }}"
               wire:navigate
               class="text-[13.5px] text-[#9B7DFF] font-semibold hover:text-white transition-colors">
                ← Back to sign in
            </a>
        </div>

    </div>
</div>