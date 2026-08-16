{{-- resources/views/livewire/auth/login.blade.php --}}
 
<div class="flex min-h-screen bg-[#05050c]"
     style="background-image:linear-gradient(rgba(123,92,245,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(123,92,245,.025) 1px,transparent 1px);background-size:44px 44px">
 
    {{-- ═══ LEFT PANEL ═══ --}}
    <x-auth.login.auth-left :activeWallets="$this->activeWallets" :percentageIncrease="$this->percentageIncrease" />
 
    {{-- ═══ RIGHT PANEL ═══ --}}
    <main class="flex-1 flex flex-col items-center justify-center
                 px-6 py-10 overflow-y-auto bg-[#05050c]/60">
        <div class="w-full max-w-[420px]">
 
            {{-- Header --}}
            <div class="fade-up mb-7">
                <div class="flex items-center gap-2 mb-3">
                    <x-senflux.logo width="22" height="22" gradient-id="login-rg" />
                    <span class="font-syne font-bold text-[13px] text-white tracking-[.07em]">SENFLUX</span>
                </div>
                <h1 class="font-syne text-[22px] font-extrabold text-white mb-1.5">Welcome back</h1>
                <p class="text-[13.5px] text-[#7a7a9a]">Sign in to your account to continue</p>
 
                {{-- Reset success toast --}}
                @if(request('reset'))
                    <div class="mt-3 flex items-center gap-2 px-3.5 py-2.5 rounded-xl
                                bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[13px]">
                        <svg width="14" height="14" fill="none" viewBox="0 0 14 14"
                             stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 7.5L5.5 11L12 4"/>
                        </svg>
                        Password updated. Sign in with your new password.
                    </div>
                @endif
            </div>
 
            {{-- Social --}}
            <div class="fade-up-d1 grid grid-cols-2 gap-2.5 mb-1">
                <x-auth.social-btn provider="google" />
                <x-auth.social-btn provider="facebook" />
            </div>
 
            {{-- Divider --}}
            <div class="fade-up-d1 flex items-center gap-3 my-5">
                <div class="flex-1 h-px bg-white/[.07]"></div>
                <span class="text-[11.5px] text-[#4a4a6a] shrink-0">or sign in with email</span>
                <div class="flex-1 h-px bg-white/[.07]"></div>
            </div>
 
            {{-- ── Form ── --}}
            <form wire:submit="login" novalidate class="flex flex-col gap-4">
 
                {{-- Email --}}
                <div class="fade-up-d2 flex flex-col gap-1.5">
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
 
                {{-- Password --}}
                <div class="fade-up-d3 flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-[11.5px] font-semibold tracking-[.07em] uppercase text-[#6b6b8a]">
                            Password
                        </label>
                        <a href="{{ route('password.request') }}"
                           wire:navigate
                           class="text-[12px] text-[#9B7DFF] hover:text-white transition-colors">
                            Forgot password?
                        </a>
                    </div>
                    <div x-data="{show: false}" class="relative flex items-center">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-[#4a4a6a]">
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                                <rect x="3" y="7" width="10" height="8" rx="1.5"/>
                                <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round"/>
                                <circle cx="8" cy="11" r="1.2" fill="currentColor"/>
                            </svg>
                        </div>
                        <input wire:model.blur="password"
                               :type="show ? 'text' : 'password'"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               class="w-full bg-white/[.04] border rounded-xl text-sm text-white placeholder-[#4a4a6a]
                                      py-2.5 pl-10 pr-10
                                      focus:outline-none transition-all duration-200
                                      {{ $errors->has('password')
                                          ? 'border-red-500/60 bg-red-500/5'
                                          : 'border-white/[.07] focus:border-[rgba(123,92,245,.5)] focus:bg-[rgba(123,92,245,.06)]' }}" />
                        <button type="button"
                                @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2
                                       text-[#4a4a6a] hover:text-[#c8c8e0] transition-colors cursor-pointer">
                            
                            <svg x-show="show" width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                                <path d="M2 2L14 14M6.5 6.7A2.2 2.2 0 0 0 9.3 9.5M4 4.5C2.7 5.6 1.8 6.8 1 8c1.8 2.8 4.2 5 7 5 1.1 0 2.2-.3 3.2-.8M10.5 5.8C11.5 6.5 12.3 7.2 13 8c-.5.8-1.2 1.6-2 2.3" stroke-linecap="round"/>
                                <circle cx="8" cy="8" r="2.2"/>
                            </svg>
                            <svg x-show="!show" width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                                <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/>
                                <circle cx="8" cy="8" r="2.2"/>
                            </svg>
                        </button>
                    </div>
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
 
                {{-- Remember me --}}
                <div class="fade-up-d3">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" wire:model="remember" id="remember" class="sr-only peer"/>
                        <div class="w-[17px] h-[17px] shrink-0 rounded-[5px] border
                                    flex items-center justify-center transition-all duration-150
                                    border-white/[.15] bg-transparent
                                    peer-checked:bg-[#7B5CF5] peer-checked:border-[#7B5CF5]
                                    group-hover:border-[rgba(123,92,245,.4)]">
                            <svg width="9" height="9" fill="none" viewBox="0 0 9 9"
                                 stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                 class="opacity-0 peer-checked:opacity-100 transition-opacity duration-150">
                                <path d="M1 4.5L3.5 7L8 2"/>
                            </svg>
                        </div>
                        <span class="text-[13px] text-[#7a7a9a] group-hover:text-[#c8c8e0] transition-colors">
                            Remember me for 30 days
                        </span>
                    </label>
                </div>
 
                {{-- Submit --}}
                <div class="fade-up-d4">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2
                                   bg-gradient-to-r from-[#7B5CF5] to-[#4F46E5]
                                   hover:from-[#9B7DFF] hover:to-[#6056f5]
                                   disabled:opacity-60 disabled:cursor-not-allowed
                                   active:scale-[.98] text-white font-syne font-bold text-sm
                                   py-3 rounded-xl tracking-wide cursor-pointer
                                   shadow-[0_4px_18px_rgba(123,92,245,.45)]
                                   hover:shadow-[0_6px_28px_rgba(123,92,245,.65)]
                                   transition-all duration-200">
 
                        <span wire:loading wire:target="login">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                            </svg>
                        </span>
 
                        <span wire:loading.remove wire:target="login">
                            <svg width="16" height="16" fill="none" viewBox="0 0 16 16"
                                 stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2H3C2.4 2 2 2.4 2 3V13C2 13.6 2.4 14 3 14H13C13.6 14 14 13.6 14 13V10"/>
                                <path d="M9 2H14V7M14 2L8 8"/>
                            </svg>
                        </span>
 
                        <span wire:loading.remove wire:target="login">Sign In to Senflux</span>
                        <span wire:loading wire:target="login">Signing in…</span>
                    </button>
                </div>
 
            </form>
 
            {{-- Sign up link --}}
            <div class="fade-up-d5 text-center mt-6">
                <span class="text-[13.5px] text-[#7a7a9a]">Don't have an account? </span>
                <a href="{{ route('register') }}"
                   wire:navigate
                   class="text-[13.5px] text-[#9B7DFF] font-semibold hover:text-white transition-colors">
                    Create one free →
                </a>
            </div>
 
            {{-- Terms footer --}}
            <div class="fade-up-d5 mt-7 pt-5 border-t border-white/[.06] text-center">
                <p class="text-[11.5px] text-[#4a4a6a] leading-relaxed">
                    By signing in you agree to our
                    <a href="{{ route('terms') }}" target="_black" class="text-[#7a7a9a] hover:text-white transition-colors">Terms of Service</a>
                    and
                    <a href="{{ route('privacy') }}" target="_black" class="text-[#7a7a9a] hover:text-white transition-colors">Privacy Policy</a>.
                </p>
            </div>
 
        </div>
    </main>
 
</div>
 
@push('scripts')
<script>
    (function () {
        const s = document.getElementById('ticker');
        if (!s) return;
        let d = 1;
        setInterval(() => {
            s.scrollLeft += d;
            if (s.scrollLeft >= s.scrollWidth - s.clientWidth) d = -1;
            if (s.scrollLeft <= 0) d = 1;
        }, 30);
    })();
</script>
@endpush