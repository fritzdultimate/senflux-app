@extends('layouts.legal')

@section('content')

<div>

    {{-- HERO --}}
    <section class="relative overflow-hidden pt-[125px] pb-16">

        <div
            class="absolute inset-0 pointer-events-none"
            style="
                background:
                    radial-gradient(
                        ellipse 60% 70% at 50% 20%,
                        rgba(123,92,245,.15),
                        transparent 70%
                    );
            "
        ></div>

        <div class="max-w-[1000px] mx-auto px-6 relative z-10">

            <div class="max-w-[760px]">

                <span class="pill mb-5 inline-block">
                    {{ $eyebrow }}
                </span>

                <h1 class="font-syne font-bold text-[clamp(2rem,4vw,3.5rem)] leading-[1.08] mb-5">
                    {{ $title }}
                </h1>

                <p class="text-[15px] md:text-[16px] text-[#8585a3] leading-[1.8] max-w-[700px]">
                    {{ $intro }}
                </p>

                <div class="flex flex-wrap items-center gap-2 mt-6 text-[11px] text-[#55556c]">
                    @isset($effective)
                        <span>Effective</span>
                        <span class="w-1 h-1 rounded-full bg-[#7B5CF5]"></span>
                        <span>{{ $effective }}</span>
                        <span class="w-1 h-1 rounded-full bg-[#7B5CF5]"></span>
                    @endisset
                    <span>Last updated</span>
                    <span class="w-1 h-1 rounded-full bg-[#7B5CF5]"></span>
                    <span>{{ $updated }}</span>
                </div>

            </div>

        </div>
    </section>


    {{-- CONTENT --}}
    <section class="pb-24">

        <div class="max-w-[1000px] mx-auto px-6">

            <div class="grid grid-cols-1 lg:grid-cols-[210px_1fr] gap-10">

                {{-- SIDE NAV --}}
                <aside class="hidden lg:block">

                    <div class="sticky top-28">

                        <p class="text-[10px] uppercase tracking-[.18em] text-[#55556c] mb-4">
                            On this page
                        </p>

                        <nav class="space-y-1">

                            @foreach($sections as $index => $section)

                                <a
                                    href="#section-{{ $index + 1 }}"
                                    class="block text-[11px] text-[#70708b] hover:text-[#9B7DFF] transition py-1.5"
                                >
                                    {{ $section['title'] }}
                                </a>

                            @endforeach

                        </nav>

                    </div>

                </aside>


                {{-- CONTENT --}}
                <article class="min-w-0">

                    <div class="card card-brand p-6 sm:p-9 lg:p-11">

                        <div class="space-y-11">

                            @foreach($sections as $index => $section)

                                <section
                                    id="section-{{ $index + 1 }}"
                                    class="scroll-mt-28"
                                >

                                    <h2 class="font-syne font-semibold text-[18px] sm:text-[20px] mb-4">
                                        {{ $section['title'] }}
                                    </h2>

                                    <div class="space-y-4">

                                        @foreach($section['body'] as $paragraph)

                                            <p class="text-[13px] sm:text-[14px] text-[#8585a3] leading-[1.85]">
                                                {{ $paragraph }}
                                            </p>

                                        @endforeach

                                    </div>

                                </section>

                                @if(!$loop->last)
                                    <div
                                        class="h-px"
                                        style="background:rgba(255,255,255,.055)"
                                    ></div>
                                @endif

                            @endforeach

                        </div>

                    </div>


                    {{-- BOTTOM CTA --}}
                    <div
                        class="mt-6 rounded-2xl p-6 sm:p-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5"
                        style="
                            background:
                                linear-gradient(
                                    135deg,
                                    rgba(123,92,245,.10),
                                    rgba(79,70,229,.035)
                                );
                            border:1px solid rgba(123,92,245,.15);
                        "
                    >

                        <div>

                            <p class="font-syne text-[14px] font-semibold mb-1">
                                Have a question about this policy?
                            </p>

                            <p class="text-[12px] text-[#77778f]">
                                Our team is happy to help clarify anything that isn't clear.
                            </p>

                        </div>

                        <a
                            href="{{ route('contact') }}"
                            class="btn-p whitespace-nowrap"
                        >
                            Contact Us →
                        </a>

                    </div>

                </article>

            </div>

        </div>

    </section>

</div>

@endsection