{{-- resources/views/livewire/auth/two-factor.blade.php --}}

<div class="flex min-h-screen items-center justify-center bg-[#05050c] px-5 py-10"
     style="background-image:linear-gradient(rgba(123,92,245,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(123,92,245,.025) 1px,transparent 1px);background-size:44px 44px">

    <div class="w-full max-w-[420px]">

        {{-- Logo --}}
        <div class="fade-up flex items-center gap-2 mb-8">
            <x-senflux.logo width="22" height="22" gradient-id="2fa-logo" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.07em]">SENFLUX</span>
        </div>

        {{-- Icon + header --}}
        <div class="fade-up mb-7">
            <div class="w-14 h-14 rounded-2xl mb-5 flex items-center justify-center
                        bg-[rgba(123,92,245,.1)] border border-[rgba(123,92,245,.2)]">
                <svg width="26" height="26" fill="none" viewBox="0 0 26 26" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="5" y="11" width="16" height="13" rx="2"/>
                    <path d="M9 11V8a4 4 0 0 1 8 0v3"/>
                    <circle cx="13" cy="17.5" r="1.5" fill="#9B7DFF"/>
                </svg>
            </div>
            <h1 class="font-syne text-[22px] font-extrabold text-white mb-1.5">
                Two-factor authentication
            </h1>
            <p class="text-[13.5px] text-[#7a7a9a]">
                @if($useRecovery)
                    Enter one of your recovery codes.
                @else
                    Open your authenticator app and enter the 6-digit code.
                @endif
            </p>
        </div>

        <form wire:submit="verify" novalidate class="flex flex-col gap-4">

            @if(! $useRecovery)
                {{-- TOTP code --}}
                <div class="fade-up-d1 flex flex-col gap-1.5">
                    <label class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
                        Authentication code
                    </label>
                    <input wire:model="code"
                           type="text"
                           inputmode="numeric"
                           maxlength="6"
                           placeholder="000000"
                           autocomplete="one-time-code"
                           class="w-full bg-white/[.04] border rounded-xl text-sm text-white placeholder-[#4a4a6a]
                                  py-2.5 px-4 text-center tracking-[.4em] text-lg font-mono
                                  focus:outline-none transition-all duration-200
                                  {{ $errors->has('code')
                                      ? 'border-red-500/60 bg-red-500/5'
                                      : 'border-white/[.07] focus:border-[rgba(123,92,245,.5)] focus:bg-[rgba(123,92,245,.06)]' }}" />
                    @error('code')
                        <p class="text-[11.5px] text-red-400 flex items-center gap-1">
                            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="currentColor" stroke-width="1.5">
                                <circle cx="5.5" cy="5.5" r="4.5"/>
                                <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            @else
                {{-- Recovery code --}}
                <div class="fade-up-d1 flex flex-col gap-1.5">
                    <label class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
                        Recovery code
                    </label>
                    <input wire:model="recoveryCode"
                           type="text"
                           placeholder="xxxx-xxxx-xxxx"
                           autocomplete="off"
                           class="w-full bg-white/[.04] border rounded-xl text-sm text-white placeholder-[#4a4a6a]
                                  py-2.5 px-4 font-mono
                                  focus:outline-none transition-all duration-200
                                  {{ $errors->has('recoveryCode')
                                      ? 'border-red-500/60 bg-red-500/5'
                                      : 'border-white/[.07] focus:border-[rgba(123,92,245,.5)] focus:bg-[rgba(123,92,245,.06)]' }}" />
                    @error('recoveryCode')
                        <p class="text-[11.5px] text-red-400 flex items-center gap-1">
                            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="currentColor" stroke-width="1.5">
                                <circle cx="5.5" cy="5.5" r="4.5"/>
                                <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            @endif

            {{-- Submit --}}
            <div class="fade-up-d2">
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
                    <span wire:loading wire:target="verify">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="verify">Verify and sign in</span>
                    <span wire:loading wire:target="verify">Verifying…</span>
                </button>
            </div>

        </form>

        {{-- Toggle TOTP / Recovery --}}
        <div class="fade-up-d3 text-center mt-5">
            <button wire:click="$toggle('useRecovery')"
                    class="text-[13px] text-[#9B7DFF] hover:text-white transition-colors cursor-pointer">
                @if($useRecovery)
                    Use authenticator app instead
                @else
                    Use a recovery code instead
                @endif
            </button>
        </div>

        <div class="fade-up-d4 text-center mt-3">
            <a href="{{ route('login') }}"
               wire:navigate
               class="text-[13px] text-[#4a4a6a] hover:text-[#7a7a9a] transition-colors">
                ← Back to sign in
            </a>
        </div>

    </div>
</div>