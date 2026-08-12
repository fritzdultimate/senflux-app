{{-- resources/views/livewire/pages/why-solana.blade.php --}}

<div class="bg-[#05050d] text-white overflow-hidden">

    {{-- ═══════════════════════════════════════════════════════════════
        HERO — WHY SOLANA
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="relative min-h-[760px] flex items-center overflow-hidden">

        {{-- Background atmosphere --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute w-[800px] h-[800px] rounded-full -top-[300px] -right-[200px]"
                 style="background:radial-gradient(circle,rgba(123,92,245,.18),transparent 68%);filter:blur(20px)">
            </div>

            <div class="absolute w-[600px] h-[600px] rounded-full bottom-[-350px] left-[-200px]"
                 style="background:radial-gradient(circle,rgba(6,182,212,.07),transparent 70%)">
            </div>

            <div class="absolute inset-0"
                 style="background-image:linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px);background-size:80px 80px;mask-image:linear-gradient(to bottom,black,transparent 90%)">
            </div>
        </div>

        <div class="max-w-[1180px] mx-auto px-6 w-full relative z-10 pt-[110px] pb-24">

            <div class="max-w-[900px]">

                <div class="flex items-center gap-3 mb-7">
                    <span class="w-2 h-2 rounded-full bg-[#7B5CF5] shadow-[0_0_14px_rgba(123,92,245,.8)]"></span>
                    <span class="text-[11px] uppercase tracking-[.25em] text-[#7a7a9a]">
                        Why Solana
                    </span>
                </div>

                <h1 class="font-syne font-extrabold text-[clamp(1.5rem,3.5vw,2.6rem)] leading-[1.1] max-w-[950px]">
                    CAPITAL MOVES FAST.
                    <br>
                    <span class="tg">INTELLIGENCE</span>
                    <br>
                    NEEDS TO MOVE FASTER.
                </h1>

                <div class="mt-9 max-w-[650px]">
                    <p class="text-[15px] leading-[1.75] text-[#9a9ab5]">
                        Senflux begins on Solana because the ecosystem provides the
                        <span class="text-white">speed, transparency, liquidity</span>
                        and observable participation needed to build a Capital Intelligence
                        layer around on-chain behaviour.
                    </p>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('home') }}"
                       class="btn-p"
                       style="padding:13px 24px">
                        Explore Senflux →
                    </a>

                    <a href="{{ route('how-it-works') }}"
                       class="btn-o"
                       style="padding:13px 24px">
                        How Senflux Works
                    </a>
                </div>
            </div>

            {{-- Hero bottom thesis --}}
            <div class="mt-24 grid grid-cols-1 md:grid-cols-3 gap-px rounded-2xl overflow-hidden"
                 style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.07)">

                <div class="p-6 md:p-7" style="background:rgba(8,8,18,.88)">
                    <p class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a] mb-3">
                        The premise
                    </p>
                    <p class="font-syne text-[17px] font-bold leading-[1.35]">
                        Price is visible.
                        <span class="text-[#9B7DFF]">Capital behaviour can come earlier.</span>
                    </p>
                </div>

                <div class="p-6 md:p-7" style="background:rgba(8,8,18,.88)">
                    <p class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a] mb-3">
                        The environment
                    </p>
                    <p class="font-syne text-[17px] font-bold leading-[1.35]">
                        Observable activity creates
                        <span class="text-[#10B981]">measurable behaviour.</span>
                    </p>
                </div>

                <div class="p-6 md:p-7" style="background:rgba(8,8,18,.88)">
                    <p class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a] mb-3">
                        The objective
                    </p>
                    <p class="font-syne text-[17px] font-bold leading-[1.35]">
                        Transform activity into
                        <span class="text-[#06B6D4]">capital intelligence.</span>
                    </p>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        ENVIRONMENT
    ═══════════════════════════════════════════════════════════════ --}}
    <section id="environment"
             class="py-28 relative"
             style="background:rgba(8,8,18,.72)">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="grid grid-cols-1 lg:grid-cols-[.75fr_1.25fr] gap-16 lg:gap-24">

                <div>
                    <p class="text-[10px] uppercase tracking-[.25em] text-[#7B5CF5] mb-4">
                        The Environment Matters
                    </p>

                    <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15] tracking-[-.03em]">
                        INFORMATION
                        <br>
                        <span class="tg">CREATES EDGE.</span>
                    </h2>
                </div>

                <div>
                    <p class="text-[17px] leading-[1.8] text-[#b0b0c5] mb-8">
                        Capital Intelligence depends on information.
                        The more observable the environment, the more effectively
                        a system can evaluate:
                    </p>

                    <div class="space-y-0 border-t border-white/[.07]">

                        @foreach([
                            'Where capital is moving.',
                            'Who is participating.',
                            'How participation is changing.',
                            'Whether a formation is strengthening.',
                            'Whether that activity is persistent.'
                        ] as $item)

                            <div class="flex items-center gap-5 py-5 border-b border-white/[.07]">
                                <span class="font-mono text-[11px] text-[#7B5CF5]">
                                    0{{ $loop->iteration }}
                                </span>

                                <span class="text-[15px] text-[#d4d4e5]">
                                    {{ $item }}
                                </span>
                            </div>

                        @endforeach

                    </div>

                    <p class="text-[15px] leading-[1.8] text-[#7a7a9a] mt-8">
                        Solana provides an environment where these behaviours can be
                        observed through public on-chain activity.
                        That makes it a natural starting point for Senflux.
                    </p>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        01 — OBSERVABLE CAPITAL ACTIVITY
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

                <div class="lg:sticky lg:top-28">

                    <div class="flex items-center gap-3 mb-5">
                        <span class="font-mono text-[11px] text-[#7B5CF5]">01</span>
                        <span class="h-px w-10 bg-[#7B5CF5]/40"></span>
                        <span class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a]">
                            Observable Capital Activity
                        </span>
                    </div>

                    <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15] tracking-[-.035em]">
                        SEE WHERE
                        <br>
                        PARTICIPATION
                        <br>
                        <span class="tg">IS BUILDING.</span>
                    </h2>

                    <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mt-7 max-w-[430px]">
                        Traditional market analysis often begins with price.
                        Senflux begins with participation.
                    </p>
                </div>

                <div>

                    <p class="text-[16px] text-[#b0b0c5] leading-[1.8] mb-8">
                        Solana’s transparent on-chain environment allows Senflux
                        to observe activity across wallets, assets, liquidity and
                        transactions.
                    </p>

                    <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mb-8">
                        This provides the raw information needed to evaluate changes
                        in participation before they necessarily become obvious
                        through price alone.
                    </p>

                    <div class="rounded-2xl p-6 md:p-8"
                         style="background:linear-gradient(145deg,rgba(123,92,245,.09),rgba(8,8,18,.9));border:1px solid rgba(123,92,245,.18)">

                        <p class="text-[10px] uppercase tracking-[.2em] text-[#7B5CF5] mb-6">
                            Observable Signals
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            @foreach([
                                'Wallet activity',
                                'Capital concentration',
                                'Liquidity movement',
                                'Participation density',
                                'Transaction behaviour',
                                'Wallet clusters',
                                'Formation persistence',
                                'Capital rotation'
                            ] as $signal)

                                <div class="flex items-center gap-3 p-3.5 rounded-xl"
                                     style="background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.05)">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#7B5CF5] shadow-[0_0_8px_rgba(123,92,245,.7)]"></span>
                                    <span class="text-[12px] text-[#c8c8e0]">{{ $signal }}</span>
                                </div>

                            @endforeach

                        </div>

                        <div class="mt-7 pt-6 border-t border-white/[.06]">
                            <p class="text-[13px] leading-[1.7] text-[#7a7a9a]">
                                The objective is not to react to every transaction.
                                <span class="text-white">
                                    It is to distinguish meaningful activity from noise.
                                </span>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        02 — CAPITAL MOBILITY
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28"
             style="background:rgba(8,8,18,.72)">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">

                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="font-mono text-[11px] text-[#06B6D4]">02</span>
                        <span class="h-px w-10 bg-[#06B6D4]/40"></span>
                        <span class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a]">
                            High Capital Mobility
                        </span>
                    </div>

                    <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15]">
                        CAPITAL
                        <br>
                        DOESN’T
                        <br>
                        <span style="color:#06B6D4">STAND STILL.</span>
                    </h2>
                </div>

                <div class="space-y-7">

                    <p class="text-[16px] text-[#b0b0c5] leading-[1.8]">
                        Digital assets move quickly.
                        Capital can rotate between assets, sectors, protocols
                        and emerging formations in a relatively short period of time.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <div class="rounded-2xl p-6"
                             style="background:rgba(244,63,94,.045);border:1px solid rgba(244,63,94,.12)">
                            <p class="text-[10px] uppercase tracking-[.2em] text-[#F43F5E] mb-3">
                                The Challenge
                            </p>
                            <p class="font-syne font-bold text-[18px] leading-[1.35]">
                                There is an enormous amount of activity.
                            </p>
                        </div>

                        <div class="rounded-2xl p-6"
                             style="background:rgba(16,185,129,.045);border:1px solid rgba(16,185,129,.12)">
                            <p class="text-[10px] uppercase tracking-[.2em] text-[#10B981] mb-3">
                                The Opportunity
                            </p>
                            <p class="font-syne font-bold text-[18px] leading-[1.35]">
                                That activity can be observed.
                            </p>
                        </div>

                    </div>

                    <p class="text-[15px] text-[#7a7a9a] leading-[1.8]">
                        Senflux is designed to continuously evaluate these movements
                        and identify when multiple participation factors begin to align.
                    </p>

                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        03 — WALLET VISIBILITY
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="text-center max-w-[760px] mx-auto mb-16">

                <div class="flex justify-center items-center gap-3 mb-5">
                    <span class="font-mono text-[11px] text-[#9B7DFF]">03</span>
                    <span class="h-px w-10 bg-[#9B7DFF]/40"></span>
                    <span class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a]">
                        Wallet Visibility
                    </span>
                </div>

                <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15]">
                    PARTICIPATION
                    <br>
                    <span class="tg">LEAVES A TRACE.</span>
                </h2>

                <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mt-6">
                    One of the most important advantages of an on-chain environment
                    is that participation is not entirely hidden behind traditional
                    financial reporting.
                </p>

            </div>

            {{-- Intelligence chain --}}
            <div class="max-w-[920px] mx-auto">

                @foreach([
                    ['Wallet Activity', '#7B5CF5'],
                    ['Capital Concentration', '#9B7DFF'],
                    ['Participation Density', '#06B6D4'],
                    ['Persistence', '#10B981'],
                    ['Formation Strength', '#10B981']
                ] as $index => $item)

                    <div class="relative flex items-center gap-5 md:gap-8">

                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl flex items-center justify-center flex-shrink-0 font-mono text-[11px]"
                             style="background:{{ $item[1] }}12;border:1px solid {{ $item[1] }}35;color:{{ $item[1] }}">
                            0{{ $index + 1 }}
                        </div>

                        <div class="flex-1 rounded-xl p-4 md:p-5"
                             style="background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.06)">
                            <span class="font-syne font-bold text-[14px] md:text-[16px]">
                                {{ $item[0] }}
                            </span>
                        </div>

                    </div>

                    @if(!$loop->last)
                        <div class="ml-[20px] md:ml-[24px] h-6 w-px bg-gradient-to-b from-white/10 to-[#7B5CF5]/30"></div>
                    @endif

                @endforeach

            </div>

            <p class="text-center text-[14px] text-[#7a7a9a] leading-[1.8] max-w-[680px] mx-auto mt-10">
                This does not mean every wallet or transaction is meaningful.
                It means the underlying activity provides another layer of information
                that can be analyzed systematically.
            </p>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        04 — FORMATION CYCLES
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28"
             style="background:rgba(8,8,18,.72)">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="grid grid-cols-1 lg:grid-cols-[.8fr_1.2fr] gap-16">

                <div>

                    <div class="flex items-center gap-3 mb-5">
                        <span class="font-mono text-[11px] text-[#10B981]">04</span>
                        <span class="h-px w-10 bg-[#10B981]/40"></span>
                        <span class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a]">
                            Rapid Formation Cycles
                        </span>
                    </div>

                    <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15]">
                        FORMATIONS
                        <br>
                        CAN DEVELOP
                        <br>
                        <span class="text-[#10B981]">QUICKLY.</span>
                    </h2>

                    <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mt-7">
                        New market formations can emerge, strengthen, rotate
                        and weaken rapidly.
                    </p>

                </div>

                <div>

                    <p class="text-[15px] text-[#b0b0c5] leading-[1.8] mb-9">
                        Solana’s active ecosystem provides a large and continuously
                        changing environment in which these participation patterns
                        can develop.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        @php
                            $states = [
                                ['EARLY', 'Initial meaningful participation begins to emerge.', '#06B6D4'],
                                ['BUILDING', 'Participation density and capital concentration increase.', '#9B7DFF'],
                                ['ACTIVE', 'The formation demonstrates stronger and more persistent participation.', '#10B981'],
                                ['STRENGTHENING', 'Multiple intelligence factors continue to support the formation.', '#22C55E'],
                                ['WEAKENING', 'Participation or supporting conditions begin to deteriorate.', '#F43F5E'],
                            ];
                        @endphp

                        @foreach($states as $state)

                            <div class="rounded-2xl p-5"
                                 style="background:{{ $state[2] }}08;border:1px solid {{ $state[2] }}22">

                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-1.5 h-1.5 rounded-full"
                                          style="background:{{ $state[2] }};box-shadow:0 0 8px {{ $state[2] }}">
                                    </span>

                                    <span class="font-syne font-bold text-[11px] tracking-[.12em]"
                                          style="color:{{ $state[2] }}">
                                        {{ $state[0] }}
                                    </span>
                                </div>

                                <p class="text-[12px] text-[#7a7a9a] leading-[1.65]">
                                    {{ $state[1] }}
                                </p>

                            </div>

                        @endforeach

                    </div>

                    <div class="mt-7 p-5 rounded-xl"
                         style="background:rgba(123,92,245,.05);border:1px solid rgba(123,92,245,.14)">
                        <p class="text-[13px] text-[#b0b0c5] leading-[1.7]">
                            The objective is to identify when a formation is becoming
                            meaningful — and continuously reassess it as conditions change.
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        05 — DYNAMIC ECOSYSTEM
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="text-center max-w-[760px] mx-auto mb-14">

                <div class="flex justify-center items-center gap-3 mb-5">
                    <span class="font-mono text-[11px] text-[#F59E0B]">05</span>
                    <span class="h-px w-10 bg-[#F59E0B]/40"></span>
                    <span class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a]">
                        A Dynamic Ecosystem
                    </span>
                </div>

                <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15]">
                    CAPITAL ROTATES.
                    <br>
                    <span class="tg">ECOSYSTEMS CHANGE.</span>
                </h2>

                <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mt-6">
                    Solana is not a single market. It contains multiple sectors,
                    assets, protocols and participation communities.
                </p>

            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                @foreach([
                    ['DeFi', '#7B5CF5'],
                    ['Infrastructure', '#06B6D4'],
                    ['Memecoins', '#F59E0B'],
                    ['AI', '#9B7DFF'],
                    ['DePIN', '#10B981'],
                    ['Gaming', '#F43F5E'],
                    ['Consumer Applications', '#22C55E'],
                    ['Emerging Areas', '#7B5CF5']
                ] as $sector)

                    <div class="group rounded-2xl p-6 min-h-[125px] flex flex-col justify-between transition-all duration-300 hover:-translate-y-1"
                         style="background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.06)">

                        <span class="w-2 h-2 rounded-full"
                              style="background:{{ $sector[1] }};box-shadow:0 0 10px {{ $sector[1] }}">
                        </span>

                        <div class="mt-7">
                            <p class="font-syne font-bold text-[14px]">
                                {{ $sector[0] }}
                            </p>

                            <p class="text-[10px] uppercase tracking-wider text-[#4a4a6a] mt-1">
                                Capital Rotation
                            </p>
                        </div>

                    </div>

                @endforeach

            </div>

            <p class="text-center text-[14px] text-[#7a7a9a] max-w-[680px] mx-auto mt-9 leading-[1.8]">
                Senflux is designed to monitor these changes and evaluate where
                participation is concentrating and where formations are gaining
                or losing strength.
            </p>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        06 — SPEED
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28"
             style="background:rgba(8,8,18,.72)">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="max-w-[800px]">

                <div class="flex items-center gap-3 mb-5">
                    <span class="font-mono text-[11px] text-[#7B5CF5]">06</span>
                    <span class="h-px w-10 bg-[#7B5CF5]/40"></span>
                    <span class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a]">
                        Speed Matters
                    </span>
                </div>

                <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15] tracking-[-.035em]">
                    INTELLIGENCE IS ONLY
                    <br>
                    USEFUL IF IT CAN
                    <br>
                    <span class="tg">LEAD TO ACTION.</span>
                </h2>

                <p class="text-[16px] text-[#8e8ea5] leading-[1.8] mt-7">
                    Markets can move faster than manual decision-making.
                    By continuously processing observable activity, Senflux is
                    designed to reduce the delay between observation, validation
                    and deployment.
                </p>

            </div>

            {{-- Process --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-14">

                @foreach([
                    ['01', 'OBSERVATION', 'Continuously monitor observable activity.', '#7B5CF5'],
                    ['02', 'VALIDATION', 'Determine whether qualifying conditions are present.', '#06B6D4'],
                    ['03', 'DEPLOYMENT', 'Automate participation when conditions are met.', '#10B981']
                ] as $step)

                    <div class="rounded-2xl p-7"
                         style="background:{{ $step[3] }}08;border:1px solid {{ $step[3] }}20">

                        <div class="flex items-center justify-between mb-10">
                            <span class="font-mono text-[11px]"
                                  style="color:{{ $step[3] }}">
                                {{ $step[0] }}
                            </span>

                            <span class="w-2 h-2 rounded-full"
                                  style="background:{{ $step[3] }};box-shadow:0 0 10px {{ $step[3] }}">
                            </span>
                        </div>

                        <h3 class="font-syne font-bold text-[17px] mb-3">
                            {{ $step[1] }}
                        </h3>

                        <p class="text-[12px] text-[#7a7a9a] leading-[1.7]">
                            {{ $step[2] }}
                        </p>

                    </div>

                @endforeach

            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        07 — TRANSPARENCY
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                <div>

                    <div class="flex items-center gap-3 mb-5">
                        <span class="font-mono text-[11px] text-[#10B981]">07</span>
                        <span class="h-px w-10 bg-[#10B981]/40"></span>
                        <span class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a]">
                            Built Around On-Chain Transparency
                        </span>
                    </div>

                    <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15]">
                        VERIFIABLE
                        <br>
                        <span class="text-[#10B981]">BY DESIGN.</span>
                    </h2>

                    <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mt-7 max-w-[470px]">
                        Senflux is built around observable blockchain activity.
                        As the infrastructure develops, transparency remains
                        a core design principle.
                    </p>

                </div>

                <div class="rounded-2xl p-7 md:p-9"
                     style="background:linear-gradient(145deg,rgba(16,185,129,.07),rgba(8,8,18,.9));border:1px solid rgba(16,185,129,.16)">

                    <p class="text-[10px] uppercase tracking-[.2em] text-[#10B981] mb-7">
                        The Intelligence Loop
                    </p>

                    @foreach([
                        'Observe the activity.',
                        'Understand the formation.',
                        'Validate the conditions.',
                        'Monitor the deployment.'
                    ] as $item)

                        <div class="flex items-center gap-4 py-4 border-b border-white/[.06] last:border-0">

                            <span class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-mono text-[#10B981]"
                                  style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.16)">
                                {{ $loop->iteration }}
                            </span>

                            <span class="text-[14px] text-[#d2d2df]">
                                {{ $item }}
                            </span>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        WHY SOLANA — SUMMARY
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-28"
             style="background:rgba(8,8,18,.72)">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="text-center max-w-[760px] mx-auto mb-14">

                <p class="text-[10px] uppercase tracking-[.25em] text-[#7B5CF5] mb-4">
                    Why Start With Solana?
                </p>

                <h2 class="font-syne font-extrabold text-[clamp(1.4rem,3vw,2.2rem)] leading-[1.15]">
                    THE RIGHT ENVIRONMENT
                    <br>
                    FOR <span class="tg">CAPITAL INTELLIGENCE.</span>
                </h2>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                @php
                    $advantages = [
                        ['OBSERVABILITY', 'On-chain participation can be monitored and analyzed.', '#7B5CF5'],
                        ['CAPITAL MOBILITY', 'Capital can move rapidly across assets and sectors.', '#06B6D4'],
                        ['WALLET VISIBILITY', 'Wallet behaviour provides another layer of participation intelligence.', '#9B7DFF'],
                        ['ACTIVE FORMATION ENVIRONMENT', 'New formations can emerge and evolve rapidly.', '#10B981'],
                        ['ECOSYSTEM DIVERSITY', 'Multiple sectors create opportunities for capital rotation analysis.', '#F59E0B'],
                        ['SPEED', 'A high-performance environment supports continuous monitoring and automated infrastructure.', '#F43F5E']
                    ];
                @endphp

                @foreach($advantages as $item)

                    <div class="rounded-2xl p-7 min-h-[190px]"
                         style="background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.06)">

                        <div class="flex items-center justify-between mb-10">
                            <span class="w-2 h-2 rounded-full"
                                  style="background:{{ $item[2] }};box-shadow:0 0 10px {{ $item[2] }}">
                            </span>

                            <span class="text-[9px] uppercase tracking-[.2em] text-[#4a4a6a]">
                                Senflux
                            </span>
                        </div>

                        <h3 class="font-syne font-bold text-[14px] tracking-wide mb-3">
                            {{ $item[0] }}
                        </h3>

                        <p class="text-[12px] text-[#7a7a9a] leading-[1.7]">
                            {{ $item[1] }}
                        </p>

                    </div>

                @endforeach

            </div>

            <p class="text-center text-[14px] text-[#8e8ea5] leading-[1.8] max-w-[700px] mx-auto mt-10">
                Together, these characteristics make Solana a strong initial operating
                environment for Senflux Capital Intelligence.
            </p>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        SOLANA IS THE BEGINNING
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-32 relative overflow-hidden">

        <div class="absolute inset-0 pointer-events-none"
             style="background:radial-gradient(ellipse 65% 70% at 50% 50%,rgba(123,92,245,.12),transparent 70%)">
        </div>

        <div class="max-w-[1000px] mx-auto px-6 relative z-10 text-center">

            <p class="text-[10px] uppercase tracking-[.25em] text-[#7B5CF5] mb-5">
                The Long-Term Vision
            </p>

            <h2 class="font-syne font-extrabold text-[clamp(1.5rem,3.5vw,2.6rem)] leading-[.98] tracking-[-.04em]">
                SOLANA IS
                <br>
                THE <span class="tg">BEGINNING.</span>
                <br>
                <span class="text-[#4a4a6a]">NOT THE LIMIT.</span>
            </h2>

            <p class="text-[16px] text-[#8e8ea5] leading-[1.85] max-w-[680px] mx-auto mt-9">
                Senflux is being built with a broader vision.
                The objective is not to create intelligence that exists only within
                one blockchain.
            </p>

            <p class="text-[15px] text-[#7a7a9a] leading-[1.85] max-w-[680px] mx-auto mt-5">
                The long-term vision is to develop a multi-chain Capital Intelligence
                layer capable of mapping capital movement, participation behaviour
                and formation development across multiple digital-asset ecosystems.
            </p>

            {{-- Expansion visual --}}
            <div class="mt-16 flex flex-col md:flex-row items-center justify-center gap-3">

                <div class="px-7 py-4 rounded-xl text-[12px] font-semibold"
                     style="background:rgba(123,92,245,.1);border:1px solid rgba(123,92,245,.25);color:#9B7DFF">
                    SOLANA
                </div>

                <div class="text-[#4a4a6a] text-xl hidden md:block">→</div>
                <div class="text-[#4a4a6a] text-xl md:hidden">↓</div>

                <div class="px-7 py-4 rounded-xl text-[12px] font-semibold"
                     style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);color:#b0b0c5">
                    EXPANSION
                </div>

                <div class="text-[#4a4a6a] text-xl hidden md:block">→</div>
                <div class="text-[#4a4a6a] text-xl md:hidden">↓</div>

                <div class="px-7 py-4 rounded-xl text-[12px] font-semibold"
                     style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.22);color:#10B981">
                    MULTI-CHAIN CAPITAL INTELLIGENCE
                </div>

            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        CAPITAL INTELLIGENCE PIPELINE
    ═══════════════════════════════════════════════════════════════ --}}
    <section id="architecture"
             class="py-28"
             style="background:rgba(8,8,18,.72)">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="max-w-[760px] mb-16">

                <p class="text-[10px] uppercase tracking-[.25em] text-[#06B6D4] mb-4">
                    From Blockchain Activity To Capital Intelligence
                </p>

                <h2 class="font-syne font-extrabold text-[clamp(1.5rem,3.5vw,2.6rem)] leading-[1.02]">
                    RAW ACTIVITY
                    <br>
                    IS NOT <span class="tg">INTELLIGENCE.</span>
                </h2>

                <p class="text-[15px] text-[#7a7a9a] leading-[1.8] mt-6">
                    Thousands of transactions can happen every minute.
                    Most are noise. The Senflux model is designed to transform
                    observable activity into increasingly meaningful layers of information.
                </p>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                @php
                    $pipeline = [
                        ['01', 'OBSERVE', 'Monitor on-chain activity and participation.', '#7B5CF5'],
                        ['02', 'ANALYZE', 'Evaluate capital movement, wallets, liquidity and behavioural patterns.', '#9B7DFF'],
                        ['03', 'IDENTIFY', 'Detect emerging market formations.', '#06B6D4'],
                        ['04', 'VALIDATE', 'Determine whether the formation demonstrates qualifying strength and persistence.', '#F59E0B'],
                        ['05', 'DEPLOY', 'Automate participation when qualifying conditions are met.', '#10B981'],
                        ['06', 'MONITOR', 'Continuously reassess formation and deployment conditions.', '#22C55E']
                    ];
                @endphp

                @foreach($pipeline as $step)

                    <div class="rounded-2xl p-7 min-h-[210px]"
                         style="background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.06)">

                        <div class="flex items-center justify-between mb-12">

                            <span class="font-mono text-[11px]"
                                  style="color:{{ $step[3] }}">
                                {{ $step[0] }}
                            </span>

                            <span class="w-2 h-2 rounded-full"
                                  style="background:{{ $step[3] }};box-shadow:0 0 10px {{ $step[3] }}">
                            </span>

                        </div>

                        <h3 class="font-syne font-bold text-[16px] mb-3">
                            {{ $step[1] }}
                        </h3>

                        <p class="text-[12px] text-[#7a7a9a] leading-[1.7]">
                            {{ $step[2] }}
                        </p>

                    </div>

                @endforeach

            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        FINAL THESIS
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-36 relative overflow-hidden">

        <div class="absolute inset-0 pointer-events-none"
             style="background:radial-gradient(ellipse 55% 65% at 50% 50%,rgba(123,92,245,.14),transparent 72%)">
        </div>

        <div class="max-w-[900px] mx-auto px-6 relative z-10 text-center">

            <div class="w-12 h-12 rounded-xl mx-auto mb-8 flex items-center justify-center"
                 style="background:rgba(123,92,245,.1);border:1px solid rgba(123,92,245,.25)">
                <span class="w-2 h-2 rounded-full bg-[#9B7DFF] shadow-[0_0_14px_rgba(155,125,255,.9)]"></span>
            </div>

            <h2 class="font-syne font-extrabold text-[clamp(1.5rem,3.5vw,2.6rem)] leading-[1.15] tracking-[-.04em]">
                CAPITAL MOVES
                <br>
                <span class="tg">BEFORE MARKETS DO.</span>
            </h2>

            <p class="text-[17px] text-[#9a9ab5] leading-[1.85] max-w-[680px] mx-auto mt-9">
                The premise behind Senflux is simple:
            </p>

            <div class="my-8">
                <p class="font-syne font-bold text-[22px] md:text-[28px]">
                    Price is visible.
                </p>

                <p class="font-syne font-bold text-[22px] md:text-[28px] text-[#9B7DFF] mt-2">
                    Capital behaviour can come earlier.
                </p>
            </div>

            <p class="text-[15px] text-[#7a7a9a] leading-[1.85] max-w-[650px] mx-auto">
                By building within an observable on-chain environment, Senflux seeks
                to identify the participation and capital movements that may precede
                broader market visibility.
            </p>

            <div class="mt-12 flex flex-col sm:flex-row justify-center items-center gap-4">

                <div class="px-5 py-3 rounded-lg text-[12px] text-[#9B7DFF]"
                     style="background:rgba(123,92,245,.07);border:1px solid rgba(123,92,245,.18)">
                    Solana gives us the environment.
                </div>

                <div class="hidden sm:block text-[#4a4a6a]">→</div>

                <div class="px-5 py-3 rounded-lg text-[12px] text-[#10B981]"
                     style="background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.18)">
                    Senflux provides the intelligence layer.
                </div>

            </div>

            {{-- Final CTA --}}
            <div class="mt-14">

                <a href="{{ route('register') }}"
                   class="btn-p inline-flex"
                   style="padding:14px 30px;font-size:14px">
                    Explore Senflux →
                </a>

                <div class="mt-7 flex items-center justify-center gap-4 text-[10px] uppercase tracking-[.25em] text-[#4a4a6a]">
                    <span>Observe</span>
                    <span class="text-[#7B5CF5]">•</span>
                    <span>Validate</span>
                    <span class="text-[#7B5CF5]">•</span>
                    <span>Deploy</span>
                </div>

            </div>

        </div>
    </section>


    {{-- ═══════════════════════════════════════════════════════════════
        FOOTER BRAND STATEMENT
    ═══════════════════════════════════════════════════════════════ --}}
    <section class="py-12 border-t border-white/[.05]">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="flex flex-col md:flex-row items-center justify-between gap-5">

                <div>
                    <p class="font-syne font-bold text-[15px]">
                        SENFLUX AI
                    </p>

                    <p class="text-[10px] uppercase tracking-[.2em] text-[#4a4a6a] mt-1">
                        Capital Intelligence for Digital Markets
                    </p>
                </div>

                <div class="text-center md:text-right">

                    <p class="text-[11px] text-[#7a7a9a]">
                        Built initially on Solana.
                    </p>

                    <p class="text-[10px] text-[#4a4a6a] mt-1">
                        Intelligence. Validation. Automated Deployment.
                    </p>

                </div>

            </div>

        </div>
    </section>

</div>
