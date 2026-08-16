{{--
    Auth Right Panel — Sign Up Form
    Usage: <x-auth.right />
--}}

<div class="auth-right flex items-center justify-center
            bg-[#0d1120] min-h-screen px-5 py-10 overflow-y-auto">

    <div class="auth-card w-full max-w-[440px]">

        {{-- ── Header ── --}}
        <div class="fade-up mb-6">
            {{-- Brand mark --}}
            <div class="flex items-center gap-2 mb-3">
                <svg width="22" height="22" viewBox="0 0 100 100" fill="none">
                    <defs>
                        <linearGradient id="slg2" x1="20" y1="10" x2="80" y2="90" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#9B7DFF"/>
                            <stop offset="1" stop-color="#4F46E5"/>
                        </linearGradient>
                    </defs>
                    <path d="M65 18C65 18 80 22 80 38C80 52 66 58 52 55C38 52 28 44 30 33C32 22 46 20 52 28C58 36 52 46 40 44C34 43 30 38 30 38M30 38C30 38 18 48 22 62C26 76 42 82 56 76C70 70 72 56 64 48C58 42 48 42 44 50C40 58 46 66 56 64"
                          stroke="url(#slg2)" stroke-width="9" stroke-linecap="round" fill="none"/>
                </svg>
                <span class="font-syne font-bold text-[13px] text-white tracking-[.07em]">SENFLUX</span>
            </div>

            {{-- Title + plan badge --}}
            <div class="flex items-center gap-2 mb-2">
                <h2 class="font-syne text-[22px] font-extrabold text-white">Create your account</h2>
                <span id="planBadge"
                      class="inline-flex items-center px-2 py-0.5 rounded-md text-[9.5px] font-bold tracking-wider uppercase
                             bg-[rgba(155,125,255,.15)] text-[#9B7DFF] border border-[rgba(155,125,255,.25)]">
                    FREE
                </span>
            </div>

            <p class="text-[13.5px] text-[#6b6b8a] hidden">Join 50,000+ participants tracking market formation</p>
        </div>

        {{-- ── Social buttons ── --}}
        <div class="fade-up-d1 grid grid-cols-2 gap-2.5 mb-1">
            <x-auth.social-btn provider="google" />
            <x-auth.social-btn provider="facebook" />
        </div>

        {{-- ── Divider ── --}}
        <div class="fade-up-d1 flex items-center gap-3 my-4">
            <div class="flex-1 h-px bg-[rgba(255,255,255,.06)]"></div>
            <span class="text-[11.5px] text-[#4a4a6a] shrink-0">or sign up with email</span>
            <div class="flex-1 h-px bg-[rgba(255,255,255,.06)]"></div>
        </div>

        {{-- ── Form ── --}}
        <form wire:submit="register" class="flex flex-col gap-4" novalidate>

            {{-- Name row --}}
            <div class="fade-up-d2 grid grid-cols-2 gap-2.5">
                <x-auth.form-input
                    id="fname"
                    label="First name"
                    error="firstname"
                    model="firstname"
                    placeholder="John"
                    autocomplete="given-name"
                />
                <x-auth.form-input
                    id="lname"
                    label="Last name"
                    error="lastname"
                    model="lastname"
                    placeholder="Doe"
                    autocomplete="family-name"
                />
            </div>

            {{-- Email --}}
            <div class="fade-up-d2">
                <x-auth.form-input
                    id="email"
                    label="Email address"
                    error="email"
                    model="email"
                    type="email"
                    placeholder="you@example.com"
                    autocomplete="email"
                >
                    <x-slot:icon>
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                            <rect x="1" y="3.5" width="14" height="10" rx="2"/>
                            <path d="M1 5.5L8 9.5L15 5.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </x-slot:icon>
                </x-auth.form-input>
            </div>

            {{-- Referral code --}}
            <div class="fade-up-d3">
                <x-auth.form-input
                    id="refcode"
                    label="Referral code"
                    labelNote="(optional)"
                    error="refcode"
                    model="refcode"
                    placeholder="Enter referral code"
                    autocomplete="off"
                    class="uppercase"
                >
                    <x-slot:icon>
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                            <circle cx="5" cy="5" r="2.5"/>
                            <circle cx="11" cy="5" r="2.5"/>
                            <circle cx="8" cy="11" r="2.5"/>
                            <path d="M7 5H9M6.5 7.5L7.5 9.5M9.5 7.5L8.5 9.5" stroke-linecap="round"/>
                        </svg>
                    </x-slot:icon>
                </x-auth.form-input>
            </div>

            {{-- Password --}}
            <div class="fade-up-d3">
                <x-auth.form-input
                    id="password"
                    label="Password"
                    error="password"
                    model="password"
                    type="password"
                    placeholder="Create a strong password"
                    autocomplete="new-password"
                    oninput="checkStrength()"
                >
                    <x-slot:icon>
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                            <rect x="3" y="7" width="10" height="8" rx="1.5"/>
                            <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round"/>
                            <circle cx="8" cy="11" r="1.2" fill="currentColor"/>
                        </svg>
                    </x-slot:icon>
                    <x-slot:suffix>
                        <span onclick="togglePwd('password','eye1')">
                            <svg id="eye1" width="16" height="16" fill="none" viewBox="0 0 16 16"
                                 stroke="currentColor" stroke-width="1.3">
                                <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/>
                                <circle cx="8" cy="8" r="2.2"/>
                            </svg>
                        </span>
                    </x-slot:suffix>
                    <x-slot:below>
                        <x-auth.strength-meter />
                    </x-slot:below>
                </x-auth.form-input>
            </div>

            {{-- Confirm password --}}
            <div class="fade-up-d4">
                <x-auth.form-input
                    id="password2"
                    label="Confirm password"
                    error="password_confirmation"
                    model="password_confirmation"
                    type="password"
                    placeholder="Repeat your password"
                    autocomplete="new-password"
                >
                    <x-slot:icon>
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16" stroke="currentColor" stroke-width="1.3">
                            <rect x="3" y="7" width="10" height="8" rx="1.5"/>
                            <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke-linecap="round"/>
                            <path d="M6 11.5L7.5 13L10 10.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </x-slot:icon>
                    <x-slot:suffix>
                        <span onclick="togglePwd('password2','eye2')">
                            <svg id="eye2" width="16" height="16" fill="none" viewBox="0 0 16 16"
                                 stroke="currentColor" stroke-width="1.3">
                                <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z"/>
                                <circle cx="8" cy="8" r="2.2"/>
                            </svg>
                        </span>
                    </x-slot:suffix>
                </x-auth.form-input>
            </div>

            {{-- Checkboxes --}}
            <div class="fade-up-d4 flex flex-col gap-3">
                <x-auth.checkbox-row
                    id="terms"
                    wrapId="termsWrap"
                    iconId="termsIcon"
                    wire:model="terms"
                    error="terms"
                >
                    I agree to the
                    <a href="{{ route('terms') }}" target="_black" class="text-[#9B7DFF] hover:text-white transition-colors">Terms of Service</a>
                    and
                    <a href="{{ route('privacy') }}" target="_black" class="text-[#9B7DFF] hover:text-white transition-colors">Privacy Policy</a>
                </x-auth.checkbox-row>

            </div>

            {{-- Submit --}}
            <div class="fade-up-d5">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2
                           bg-gradient-to-r from-[#9B7DFF] to-[#4F46E5]
                           hover:from-[#b09aff] hover:to-[#6056f5]
                           active:scale-[.98] text-white font-syne font-bold text-sm
                           py-3 rounded-xl tracking-wide
                           shadow-[0_4px_20px_rgba(155,125,255,.25)]
                           transition-all duration-200 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    {{-- Spinner --}}
                    <span wire:loading wire:target="register">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor"
                                  d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z"/>
                        </svg>
                    </span>

                    <span wire:loading.remove wire:target="register">
                        <svg width="16" height="16" fill="none" viewBox="0 0 16 16"
                            stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zm0 4v4l3 1.5"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="register" id="btnLabel">
                        Create Free Account
                    </span>
                    <span wire:loading wire:target="register">Creating account…</span>
                </button>
            </div>
        </form>

        {{-- ── Sign in link ── --}}
        <div class="fade-up-d6 text-center mt-5">
            <span class="text-[13.5px] text-[#6b6b8a]">Already have an account? </span>
            <a href="{{ route('login') }}"
               class="text-[13.5px] text-[#9B7DFF] font-semibold no-underline
                      hover:text-white transition-colors duration-200">
                Sign in →
            </a>
        </div>

        {{-- ── Flash message ── --}}
        <div id="msg"
             class="hidden mt-3.5 px-3.5 py-2.5 rounded-xl text-[13px] text-center">
        </div>

        {{-- ── Trust badges ── --}}
        <x-auth.trust-badges />

    </div>
</div>