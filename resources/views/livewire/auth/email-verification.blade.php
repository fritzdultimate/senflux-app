{{-- resources/views/livewire/auth/email-verification.blade.php --}}

<div class="flex min-h-screen items-center justify-center bg-[#05050c] px-5 py-10"
     style="background-image:linear-gradient(rgba(123,92,245,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(123,92,245,.025) 1px,transparent 1px);background-size:44px 44px">

    <div class="w-full max-w-[420px] text-center">

        {{-- Icon --}}
        <div class="fade-up w-20 h-20 rounded-2xl mx-auto mb-6 flex items-center justify-center
                    bg-[rgba(123,92,245,.1)] border border-[rgba(123,92,245,.2)]">
            <svg width="36" height="36" fill="none" viewBox="0 0 36 36" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="8" width="30" height="22" rx="4"/>
                <path d="M3 13L18 22L33 13"/>
            </svg>
        </div>

        {{-- Logo --}}
        <div class="fade-up flex items-center justify-center gap-2 mb-5">
            <x-senflux.logo width="20" height="20" gradient-id="ev-logo" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.07em]">SENFLUX</span>
        </div>

        <div class="fade-up mb-6">
            <h1 class="font-syne text-[22px] font-extrabold text-white mb-2">Verify your email</h1>
            <p class="text-[13.5px] text-[#7a7a9a] leading-relaxed">
                We sent a verification link to
                <span class="text-[#c8c8e0]">{{ Auth::user()?->email }}</span>.
                Click the link in that email to activate your account.
            </p>
        </div>

        {{-- Resent success --}}
        @if($resent)
            <div class="fade-up mb-4 flex items-center justify-center gap-2 px-4 py-3 rounded-xl
                        bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[13px]">
                <svg width="14" height="14" fill="none" viewBox="0 0 14 14"
                     stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 7.5L5.5 11L12 4"/>
                </svg>
                New verification email sent.
            </div>
        @endif

        @error('resend')
            <p class="text-[11.5px] text-red-400 flex items-center justify-center gap-1 mb-4">
                <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="currentColor" stroke-width="1.5">
                    <circle cx="5.5" cy="5.5" r="4.5"/>
                    <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
                </svg>
                {{ $message }}
            </p>
        @enderror

        {{-- Actions --}}
        <div class="fade-up-d1 flex flex-col gap-3">
            <button wire:click="resend"
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2
                           bg-gradient-to-r from-[#7B5CF5] to-[#4F46E5]
                           hover:from-[#9B7DFF] hover:to-[#6056f5]
                           disabled:opacity-60 disabled:cursor-not-allowed
                           active:scale-[.98] text-white font-syne font-bold text-sm
                           py-3 rounded-xl tracking-wide cursor-pointer
                           shadow-[0_4px_18px_rgba(123,92,245,.45)]
                           transition-all duration-200">
                <span wire:loading wire:target="resend">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="resend">Resend verification email</span>
                <span wire:loading wire:target="resend">Sending…</span>
            </button>

            <button wire:click="logout"
                    class="w-full py-2.5 rounded-xl text-sm text-[#7a7a9a] hover:text-white
                           border border-white/[.07] hover:border-white/[.15]
                           transition-all duration-200 cursor-pointer">
                Sign out
            </button>
        </div>

        <p class="fade-up-d2 mt-6 text-[11.5px] text-[#4a4a6a] leading-relaxed">
            Didn't get the email? Check spam, or make sure
            <span class="text-[#7a7a9a]">{{ Auth::user()?->email }}</span> is correct.
        </p>

    </div>
</div>