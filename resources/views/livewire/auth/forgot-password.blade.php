{{-- resources/views/livewire/auth/forgot-password.blade.php --}}

<div class="flex min-h-screen items-center justify-center bg-[#05050c] px-5 py-10"
     style="background-image:linear-gradient(rgba(123,92,245,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(123,92,245,.025) 1px,transparent 1px);background-size:44px 44px">

    <div class="w-full max-w-[420px]">

        {{-- Logo --}}
        <div class="fade-up flex items-center gap-2 mb-8">
            <x-senflux.logo width="22" height="22" gradient-id="fp-logo" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.07em]">SENFLUX</span>
        </div>

        @if($sent)
            {{-- ── Success state ── --}}
            <div class="fade-up text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 border border-emerald-500/20
                            flex items-center justify-center mx-auto mb-5">
                    <svg width="28" height="28" fill="none" viewBox="0 0 28 28" stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 14L11 21L24 8"/>
                    </svg>
                </div>
                <h2 class="font-syne text-xl font-extrabold text-white mb-2">Check your email</h2>
                <p class="text-[13.5px] text-[#7a7a9a] leading-relaxed mb-6">
                    If an account exists for <span class="text-[#c8c8e0]">{{ $email }}</span>,
                    a reset link is on its way. Check your spam folder too.
                </p>
                <a href="{{ route('login') }}"
                   wire:navigate
                   class="text-[13.5px] text-[#9B7DFF] font-semibold hover:text-white transition-colors">
                    ← Back to sign in
                </a>
            </div>

        @else
            {{-- ── Request form ── --}}
            <div class="fade-up mb-7">
                <h1 class="font-syne text-[22px] font-extrabold text-white mb-1.5">
                    Forgot password?
                </h1>
                <p class="text-[13.5px] text-[#7a7a9a]">
                    Enter your email and we'll send a reset link.
                </p>
            </div>

            <form wire:submit="sendLink" novalidate class="flex flex-col gap-4">

                <div class="fade-up-d1 flex flex-col gap-1.5">
                    <label class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
                        Email address
                    </label>
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#4a4a6a]">
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                                <rect x="1" y="3.5" width="14" height="10" rx="2"/>
                                <path d="M1 5.5L8 9.5L15 5.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <input wire:model.blur="email"
                               type="email" placeholder="you@example.com" autocomplete="email"
                               class="w-full bg-white/[.04] border rounded-xl text-sm text-white placeholder-[#4a4a6a]
                                      py-2.5 pl-10 pr-4
                                      focus:outline-none transition-all duration-200
                                      {{ $errors->has('email')
                                          ? 'border-red-500/60 bg-red-500/5'
                                          : 'border-white/[.07] focus:border-[rgba(123,92,245,.5)] focus:bg-[rgba(123,92,245,.06)]' }}" />
                    </div>
                    @error('email')
                        <p class="text-[11.5px] text-red-400 flex items-center gap-1">
                            <svg width="11" height="11" fill="none" viewBox="0 0 11 11" stroke="currentColor" stroke-width="1.5">
                                <circle cx="5.5" cy="5.5" r="4.5"/>
                                <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

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
                        <span wire:loading wire:target="sendLink">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="sendLink">Send reset link</span>
                        <span wire:loading wire:target="sendLink">Sending…</span>
                    </button>
                </div>

            </form>

            <div class="fade-up-d3 text-center mt-6">
                <a href="{{ route('login') }}"
                   wire:navigate
                   class="text-[13.5px] text-[#9B7DFF] font-semibold hover:text-white transition-colors">
                    ← Back to sign in
                </a>
            </div>
        @endif

    </div>
</div>