{{-- resources/views/livewire/auth/collect-email.blade.php --}}

<div class="flex min-h-screen items-center justify-center bg-[#05050c] px-5 py-10"
     style="background-image:linear-gradient(rgba(123,92,245,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(123,92,245,.025) 1px,transparent 1px);background-size:44px 44px">

    <div class="w-full max-w-[420px]">

        {{-- Logo --}}
        <div class="fade-up flex items-center gap-2 mb-8">
            <x-senflux.logo width="22" height="22" gradient-id="ce-logo" />
            <span class="font-syne font-bold text-[13px] text-white tracking-[.07em]">SENFLUX</span>
        </div>

        {{-- Icon --}}
        <div class="fade-up w-14 h-14 rounded-2xl mb-6 flex items-center justify-center
                    bg-[rgba(123,92,245,.1)] border border-[rgba(123,92,245,.2)]">
            <svg width="26" height="26" fill="none" viewBox="0 0 26 26"
                 stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="6" width="22" height="16" rx="3"/>
                <path d="M2 10l11 7 11-7"/>
            </svg>
        </div>

        {{-- Header --}}
        <div class="fade-up mb-7">
            <h1 class="font-syne text-[22px] font-extrabold text-white mb-2">
                One last step
            </h1>
            <p class="text-[13.5px] text-[#7a7a9a] leading-relaxed">
                Your {{ ucfirst(Auth::user()->provider ?? 'social') }} account didn't include an email address.
                We need one to send you important account notifications.
            </p>
        </div>

        {{-- Form --}}
        <form wire:submit="save" novalidate class="flex flex-col gap-4">

            <div class="fade-up-d1 flex flex-col gap-1.5">
                <label class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
                    Email address
                </label>
                <div class="relative flex items-center">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#4a4a6a]">
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"
                             stroke="currentColor" stroke-width="1.3">
                            <rect x="1" y="3.5" width="14" height="10" rx="2"/>
                            <path d="M1 5.5L8 9.5L15 5.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <input wire:model.blur="email"
                           type="email"
                           placeholder="you@example.com"
                           autocomplete="email"
                           class="w-full bg-white/[.04] border rounded-xl text-sm text-white placeholder-[#4a4a6a]
                                  py-2.5 pl-10 pr-4 focus:outline-none transition-all duration-200
                                  {{ $errors->has('email')
                                      ? 'border-red-500/60 bg-red-500/5'
                                      : 'border-white/[.07] focus:border-[rgba(123,92,245,.5)] focus:bg-[rgba(123,92,245,.06)]' }}" />
                </div>
                @error('email')
                    <p class="text-[11.5px] text-red-400 flex items-center gap-1">
                        <svg width="11" height="11" fill="none" viewBox="0 0 11 11"
                             stroke="currentColor" stroke-width="1.5">
                            <circle cx="5.5" cy="5.5" r="4.5"/>
                            <path d="M5.5 3.5v2.5M5.5 7.5v.2" stroke-linecap="round"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Info note --}}
            <div class="fade-up-d2 flex items-start gap-2.5 px-3.5 py-3 rounded-xl
                        bg-[rgba(123,92,245,.06)] border border-[rgba(123,92,245,.15)]">
                <svg width="15" height="15" fill="none" viewBox="0 0 15 15"
                     stroke="#9B7DFF" stroke-width="1.4" stroke-linecap="round"
                     class="shrink-0 mt-0.5">
                    <circle cx="7.5" cy="7.5" r="6.5"/>
                    <path d="M7.5 5v.5M7.5 7.5v3"/>
                </svg>
                <p class="text-[12.5px] text-[#7a7a9a] leading-relaxed">
                    We'll send a verification link to this address. Your account will be fully
                    activated once verified.
                </p>
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
                    <span wire:loading wire:target="save">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="save">Continue to verification</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
            </div>

        </form>

        {{-- Skip / logout --}}
        <div class="fade-up-d4 text-center mt-5">
            <button wire:click="$dispatch('logout')"
                    onclick="document.getElementById('logout-form').submit()"
                    class="text-[13px] text-[#4a4a6a] hover:text-[#7a7a9a] transition-colors cursor-pointer">
                Sign out instead
            </button>
        </div>

        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
            @csrf
        </form>

    </div>
</div>