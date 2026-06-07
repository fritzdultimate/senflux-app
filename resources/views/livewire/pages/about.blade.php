{{-- resources/views/livewire/pages/about.blade.php --}}

<div>
    {{-- ═══ HERO ═══ --}}
    <section class="pt-[100px] min-h-[520px] relative overflow-hidden flex items-center">

        {{-- Background glow --}}
        <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(ellipse 55% 60% at 70% 50%,rgba(123,92,245,.18),transparent 65%)"></div>

        <div class="max-w-[1180px] mx-auto px-6 w-full relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                {{-- LEFT: Text --}}
                <div>
                    <span class="pill mb-5 inline-block">About Us</span>

                    <h1 class="font-syne font-bold text-[clamp(1.6rem,2.8vw,2.6rem)] leading-[1.15] mb-5 max-w-[480px]">
                        We Built Senflux Because 
                        <span class="tg">Markets</span>
                        Leave Clues Before They Move.
                    </h1>

                    <p class="text-[14px] text-[#7a7a9a] max-w-[400px] mb-8 leading-[1.75]">
                        Markets rarely move without warning.
                    </p>

                    <p class="text-[14px] text-[#7a7a9a] max-w-[400px] mb-8 leading-[1.75]">
                        Participation, liquidity, and capital activity often strengthen before broader market attention arrives.
                    </p>

                    <p class="text-[14px] text-[#7a7a9a] max-w-[400px] mb-8 leading-[1.75]">
                        Senflux combines real-time participation intelligence with structured deployment systems to help users identify, validate, and act on emerging market opportunities earlier.
                    </p>

                    <div class="fledx gap-3 flex-wrap hidden">
                        <a href="{{ route('terminal') }}" class="btn-p">Explore Intelligence →</a>
                        <a href="{{ route('home') }}" class="btn-o">View Capital Flows</a>
                    </div>
                </div>

                {{-- RIGHT: Globe visual --}}
                <div class="hidden lg:flex items-center justify-center">
                    <div class="relative w-[300px] h-[300px]">

                        {{-- Outer spinning ring --}}
                        <div class="absolute inset-0 rounded-full border"
                            style="border-color:rgba(123,92,245,.16);animation:spin 18s linear infinite"></div>

                        {{-- Inner glow orb --}}
                        <div class="absolute inset-7 rounded-full flex items-center justify-center"
                            style="background:radial-gradient(ellipse at 30% 30%,rgba(123,92,245,.32),rgba(79,70,229,.16) 55%,rgba(5,5,12,.85));box-shadow:0 0 80px rgba(123,92,245,.35),0 0 200px rgba(123,92,245,.1)">

                            {{-- Logo box --}}
                            <div class="w-[68px] h-[68px] rounded-[16px] flex items-center justify-center"
                                style="background:linear-gradient(135deg,rgba(123,92,245,.6),rgba(79,70,229,.4));border:1px solid rgba(155,125,255,.4);box-shadow:0 0 30px rgba(123,92,245,.6)">
                                <x-senflux.logo width="32" height="32" color="white" gradient-id="aboutHero" />
                            </div>
                        </div>

                        {{-- Orbit person icons --}}
                        <div class="absolute top-2 right-16 w-7 h-7 rounded-full flex items-center justify-center"
                            style="background:rgba(123,92,245,.15);border:1px solid rgba(123,92,245,.3)">
                            <svg width="13" height="13" fill="none">
                                <circle cx="6.5" cy="4" r="2.2" stroke="#9B7DFF" stroke-width="1.2" />
                                <path d="M1.5 12C1.5 9.5 3.7 8 6.5 8C9.3 8 11.5 9.5 11.5 12" stroke="#9B7DFF"
                                    stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="absolute top-16 right-2 w-7 h-7 rounded-full flex items-center justify-center"
                            style="background:rgba(123,92,245,.15);border:1px solid rgba(123,92,245,.3)">
                            <svg width="13" height="13" fill="none">
                                <circle cx="6.5" cy="4" r="2.2" stroke="#9B7DFF" stroke-width="1.2" />
                                <path d="M1.5 12C1.5 9.5 3.7 8 6.5 8C9.3 8 11.5 9.5 11.5 12" stroke="#9B7DFF"
                                    stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="absolute top-16 left-2 w-7 h-7 rounded-full flex items-center justify-center"
                            style="background:rgba(123,92,245,.15);border:1px solid rgba(123,92,245,.3)">
                            <svg width="13" height="13" fill="none">
                                <circle cx="6.5" cy="4" r="2.2" stroke="#9B7DFF" stroke-width="1.2" />
                                <path d="M1.5 12C1.5 9.5 3.7 8 6.5 8C9.3 8 11.5 9.5 11.5 12" stroke="#9B7DFF"
                                    stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-7 h-7 rounded-full flex items-center justify-center"
                            style="background:rgba(123,92,245,.15);border:1px solid rgba(123,92,245,.3)">
                            <svg width="13" height="13" fill="none">
                                <circle cx="6.5" cy="4" r="2.2" stroke="#9B7DFF" stroke-width="1.2" />
                                <path d="M1.5 12C1.5 9.5 3.7 8 6.5 8C9.3 8 11.5 9.5 11.5 12" stroke="#9B7DFF"
                                    stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                        </div>

                        {{-- Bottom glow shadow --}}
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-[180px] h-5 rounded-full"
                            style="background:rgba(123,92,245,.28);filter:blur(16px)"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══ MISSION ═══ --}}
    <section class="py-20 text-center" style="background:rgba(8,8,18,.65)">
        <div class="max-w-[1180px] mx-auto px-6">
            <span class="pill mb-3.5 inline-block">Our Mission</span>
            <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Make Early Market Opportunity <span
                    class="tg">Visible.</span>
            </h2>
            <p class="text-[14px] text-[#7a7a9a] max-w-[520px] mx-auto mt-3 mb-12 leading-[1.75]">
                Senflux transforms real-time on-chain activity into actionable participation intelligence, helping users identify strengthening market conditions and deploy with greater confidence.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 rv">
                <div class="card card-brand p-6 text-left">
                    <div class="ib mb-3.5"><svg width="18" height="18" fill="none">
                            <circle cx="9" cy="9" r="4" stroke="#9B7DFF" stroke-width="1.5" />
                            <path d="M9 1V3M9 15V17M1 9H3M15 9H17" stroke="#9B7DFF" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">Early Visibility</h3>
                    <p class="text-[13px] text-[#7a7a9a]">Detect participation before it becomes obvious.</p>
                </div>
                <div class="card card-brand p-6 text-left">
                    <div class="ib mb-3.5"><svg width="18" height="18" fill="none">
                            <path d="M3 9L7 13L15 5" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <rect x="1" y="1" width="16" height="16" rx="3" stroke="#9B7DFF" stroke-width="1.5" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">Structured Intelligence</h3>
                    <p class="text-[13px] text-[#7a7a9a]">Turn noise into signal through data, metrics and intent.</p>
                </div>
                <div class="card card-green p-6 text-left">
                    <div class="ib ib-g mb-3.5"><svg width="18" height="18" fill="none">
                            <circle cx="6" cy="5.5" r="2.5" stroke="#10B981" stroke-width="1.5" />
                            <circle cx="12" cy="5.5" r="2.5" stroke="#10B981" stroke-width="1.5" />
                            <path d="M2 16C2 13.2 3.8 11 6 11M16 16C16 13.2 14.2 11 12 11" stroke="#10B981"
                                stroke-width="1.5" stroke-linecap="round" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">Empower Participants</h3>
                    <p class="text-[13px] text-[#7a7a9a]">Give users the same edge large players rely on.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ HOW WE DO IT ═══ --}}
    <section class="py-20">
        <div class="max-w-[1180px] mx-auto px-6">
            <div class="text-center mb-12">
                <span class="pill mb-3.5 inline-block">How we do it</span>
                <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">How We <span class="tg">Do</span> it
                </h2>
                <p class="text-[14px] text-[#7a7a9a] mt-2.5">Senflux tracks capital behavior and acts only when movement
                    becomes real.</p>
            </div>
            <div class="flex items-start gap-0 rv">
                <div class="flex-1 text-center px-3">
                    <div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
                        style="border-color:rgba(123,92,245,.3);background:rgba(123,92,245,.08)"><svg width="20"
                            height="20" fill="none">
                            <rect x="3" y="3" width="14" height="14" rx="2" stroke="#9B7DFF" stroke-width="1.5" />
                            <path d="M7 10H13M10 7V13" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" />
                        </svg></div>
                    <p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">1. Collect</p>
                    <p class="text-[12px] text-[#7a7a9a]">We aggregate real-time on-chain data across wallets, tokens,
                        and liquidity flows.</p>
                </div>
                <div class="sline mt-6"></div>
                <div class="flex-1 text-center px-3">
                    <div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
                        style="border-color:rgba(123,92,245,.3);background:rgba(123,92,245,.08)"><svg width="20"
                            height="20" fill="none">
                            <path d="M3 14L7 10L10 13L14 7L17 9" stroke="#9B7DFF" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></div>
                    <p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">2. Analyze</p>
                    <p class="text-[12px] text-[#7a7a9a]">Our models evaluate market participation density, persistence,
                        velocity and wallet behavior.</p>
                </div>
                <div class="sline mt-6"></div>
                <div class="flex-1 text-center px-3">
                    <div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
                        style="border-color:rgba(123,92,245,.3);background:rgba(123,92,245,.08)"><svg width="20"
                            height="20" fill="none">
                            <circle cx="10" cy="10" r="7" stroke="#9B7DFF" stroke-width="1.5" />
                            <path d="M10 7V10L12 12" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round" />
                        </svg></div>
                    <p class="text-[11px] text-[#9B7DFF] font-semibold mb-1.5">3. Identify</p>
                    <p class="text-[12px] text-[#7a7a9a]">We identify where meaningful participation is forming and
                        gaining strength.</p>
                </div>
                <div class="sline mt-6"
                    style="background:linear-gradient(90deg,rgba(123,92,245,.35),rgba(245,158,11,.3))"></div>
                <div class="flex-1 text-center px-3">
                    <div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
                        style="border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.08)"><svg width="20"
                            height="20" fill="none">
                            <rect x="2" y="2" width="16" height="16" rx="3" stroke="#F59E0B" stroke-width="1.5" />
                            <path d="M6 14V10M10 14V8M14 14V6" stroke="#F59E0B" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg></div>
                    <p class="text-[11px] text-[#F59E0B] font-semibold mb-1.5">4. Visualize</p>
                    <p class="text-[12px] text-[#7a7a9a]">We present it all in a live terminal so users can see
                        formation as it happens.</p>
                </div>
                <div class="sline mt-6"
                    style="background:linear-gradient(90deg,rgba(245,158,11,.3),rgba(16,185,129,.3))"></div>
                <div class="flex-1 text-center px-3">
                    <div class="w-12 h-12 rounded-full border flex items-center justify-center mx-auto mb-3"
                        style="border-color:rgba(16,185,129,.3);background:rgba(16,185,129,.08)"><svg width="20"
                            height="20" fill="none">
                            <path d="M10 3L17 10L10 17M3 10H17" stroke="#10B981" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></div>
                    <p class="text-[11px] text-[#10B981] font-semibold mb-1.5">5. Act</p>
                    <p class="text-[12px] text-[#7a7a9a]">Users position during the build phases—not after expansion has
                        begun.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ WHAT MAKES SENFLUX DIFFERENT ═══ --}}
    <section class="py-20" style="background:rgba(8,8,18,.65)">
        <div class="max-w-[1180px] mx-auto px-6">
            <div class="text-center mb-10">
                <span class="pill mb-3.5 inline-block">Difference</span>
                <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">What makes <span
                        class="tg">Senflux</span> Different</h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 rv">
                <div class="card card-brand p-6">
                    <div class="ib mb-3.5"><svg width="18" height="18" fill="none">
                            <path d="M2 9L5 6L8 9L11 5L14 8L17 4" stroke="#9B7DFF" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">Focus on Participation</h3>
                    <p class="text-[13px] text-[#7a7a9a]">We track behavior, not just price.</p>
                </div>
                <div class="card card-brand p-6">
                    <div class="ib mb-3.5"><svg width="18" height="18" fill="none">
                            <circle cx="6" cy="6" r="2" stroke="#9B7DFF" stroke-width="1.5" />
                            <circle cx="12" cy="6" r="2" stroke="#9B7DFF" stroke-width="1.5" />
                            <circle cx="9" cy="12" r="2" stroke="#9B7DFF" stroke-width="1.5" />
                            <path d="M8 6H10M7 8L9 10M11 8L9 10" stroke="#9B7DFF" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">Formation First Approach</h3>
                    <p class="text-[13px] text-[#7a7a9a]">We identify early structure, not late reactions.</p>
                </div>
                <div class="card card-brand p-6">
                    <div class="ib mb-3.5"><svg width="18" height="18" fill="none">
                            <path d="M3 9L7 13L15 5" stroke="#9B7DFF" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <circle cx="9" cy="9" r="7" stroke="#9B7DFF" stroke-width="1.5" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">100% On-Chain Transparency</h3>
                    <p class="text-[13px] text-[#7a7a9a]">Everything we show is verifiable on-chain.</p>
                </div>
                <div class="card p-6" style="border-color:rgba(245,158,11,.2)">
                    <div class="ib ib-y mb-3.5"><svg width="18" height="18" fill="none">
                            <circle cx="9" cy="9" r="7" stroke="#F59E0B" stroke-width="1.5" />
                            <path d="M9 5V9L12 11" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">Real Time Intelligence</h3>
                    <p class="text-[13px] text-[#7a7a9a]">Our terminal is always live, always updating.</p>
                </div>
                <div class="card card-green p-6 sm:col-span-2">
                    <div class="ib ib-g mb-3.5"><svg width="18" height="18" fill="none">
                            <path d="M9 1L13 4V9C13 12 11.3 14.5 9 15.5C6.7 14.5 5 12 5 9V4L9 1Z" stroke="#10B981"
                                stroke-width="1.5" stroke-linejoin="round" />
                        </svg></div>
                    <h3 class="font-syne text-[14px] mb-2">Built for Trust</h3>
                    <p class="text-[13px] text-[#7a7a9a]">Transparency, performance, and community first.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ WHO WE ARE ═══ --}}
    <section class="py-20">
        <div class="max-w-[1180px] mx-auto px-6">
            <div class="text-center mb-12">
                <span class="pill mb-3.5 inline-block">Who We Are</span>
                <h2 class="font-syne font-bold text-[clamp(1.6rem,3vw,2.4rem)]">Built By <span class="tg">Operators.</span> Designed For Scale.</h2>
                <p class="text-[14px] text-[#7a7a9a] max-w-[520px] mx-auto mt-3 leading-[1.75]">
                    Senflux is led by a team focused on market intelligence, technology infrastructure, and deployment systems.
                </p>
            </div>
            <div class="max-w-[640px] mx-auto">
                <p class="text-[13px] text-[#4a4a6a] uppercase tracking-wider mb-4 text-center">Our Mission</p>
                <div class="p-6 rounded-2xl text-center" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07)">
                    <p class="text-[15px] text-[#c8c8e0] leading-[1.8]">
                        To create a smarter way to identify, validate, and act on market opportunities through
                        <span class="text-white font-medium">real-time participation intelligence.</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ CTA ═══ --}}
    <section class="py-20 text-center relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(ellipse 70% 60% at 50% 50%,rgba(123,92,245,.14),transparent 72%)"></div>
        <div class="max-w-[1180px] mx-auto px-6 relative z-10">
            <h2 class="font-syne font-extrabold text-[clamp(1.6rem,3.5vw,2.5rem)] leading-[1.2]">Senflux is more than a
                <span class="tg">platform</span>. It's a new way to understand <span
                    style="-webkit-text-fill-color:#06B6D4;color:#06B6D4">markets.</span>
            </h2>
            <p class="text-[14px] text-[#7a7a9a] max-w-[460px] mx-auto mt-4 mb-8 leading-[1.75]">We believe transparency
                creates trust. Intelligence creates opportunity, and participation creates the future.</p>
            <a href="{{ route('register') }}" class="btn-p mx-auto" style="padding:12px 28px;font-size:14px">Start
                Deployment Now</a>
        </div>
    </section>

</div>