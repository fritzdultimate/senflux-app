<div>

    {{-- HERO --}}
    <section class="relative overflow-hidden pt-[120px] pb-16">

        <div
            class="absolute inset-0 pointer-events-none"
            style="background:
                radial-gradient(ellipse 55% 65% at 15% 40%, rgba(123,92,245,.16), transparent 70%),
                radial-gradient(ellipse 45% 55% at 85% 55%, rgba(79,70,229,.10), transparent 70%);"
        ></div>

        <div class="max-w-[1180px] mx-auto px-6 relative z-10">

            <div class="max-w-[720px]">

                <span class="pill mb-5 inline-block">
                    Contact
                </span>

                <h1 class="font-syne font-bold text-[clamp(2rem,4vw,3.4rem)] leading-[1.08] mb-5">
                    Let's build a clearer view of
                    <span class="tg">market formation.</span>
                </h1>

                <p class="text-[15px] md:text-[16px] text-[#8585a3] leading-[1.8] max-w-[620px]">
                    Whether you're exploring Senflux, looking for product information,
                    interested in partnerships, or have a question about our data,
                    we'd like to hear from you.
                </p>

            </div>

        </div>
    </section>


    {{-- CONTACT AREA --}}
    <section class="pb-24">

        <div class="max-w-[1180px] mx-auto px-6">

            <div class="grid grid-cols-1 lg:grid-cols-[.72fr_1.28fr] gap-8 items-start">

                {{-- LEFT --}}
                <div class="space-y-4">

                    <div class="card card-brand p-7">

                        <div class="ib mb-5">
                            <svg width="19" height="19" fill="none">
                                <path
                                    d="M3 5.5A2.5 2.5 0 0 1 5.5 3h7A2.5 2.5 0 0 1 15 5.5v8a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 3 13.5v-8Z"
                                    stroke="#9B7DFF"
                                    stroke-width="1.5"
                                />
                                <path
                                    d="M6 7h6M6 10h4"
                                    stroke="#9B7DFF"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                />
                            </svg>
                        </div>

                        <h2 class="font-syne font-semibold text-[18px] mb-3">
                            Talk to Senflux
                        </h2>

                        <p class="text-[13px] text-[#7a7a9a] leading-[1.75]">
                            Have a question, partnership idea, or something you'd
                            like to explore with our team?
                        </p>

                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-4">

                        <div class="card p-5">

                            <div class="flex items-center gap-3 mb-3">

                                <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                                     style="background:rgba(123,92,245,.09);border:1px solid rgba(123,92,245,.18)">
                                    <svg width="17" height="17" fill="none">
                                        <path
                                            d="M2 4.5A2.5 2.5 0 0 1 4.5 2h8A2.5 2.5 0 0 1 15 4.5v8a2.5 2.5 0 0 1-2.5 2.5h-8A2.5 2.5 0 0 1 2 12.5v-8Z"
                                            stroke="#9B7DFF"
                                            stroke-width="1.3"
                                        />
                                        <path
                                            d="m4 5 4.5 3.5L13 5"
                                            stroke="#9B7DFF"
                                            stroke-width="1.3"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <h3 class="font-syne text-[13px] font-semibold">
                                    General enquiries
                                </h3>

                            </div>

                            <p class="text-[12px] text-[#77778f]">
                                Questions about Senflux, our platform, or our research.
                            </p>

                        </div>


                        <div class="card p-5">

                            <div class="flex items-center gap-3 mb-3">

                                <div class="w-9 h-9 rounded-lg flex items-center justify-center"
                                     style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.18)">
                                    <svg width="17" height="17" fill="none">
                                        <path
                                            d="M3 13L7 9L10 12L14 5"
                                            stroke="#10B981"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <h3 class="font-syne text-[13px] font-semibold">
                                    Partnerships
                                </h3>

                            </div>

                            <p class="text-[12px] text-[#77778f]">
                                Exploring integrations, research, data, or strategic partnerships.
                            </p>

                        </div>

                    </div>


                    <div class="card p-6"
                         style="background:linear-gradient(135deg,rgba(123,92,245,.08),rgba(79,70,229,.03))">

                        <p class="text-[11px] uppercase tracking-[.15em] text-[#9B7DFF] mb-3">
                            Our Approach
                        </p>

                        <p class="font-syne text-[15px] leading-[1.6]">
                            We believe better market decisions start with
                            understanding what participants are actually doing.
                        </p>

                    </div>

                </div>


                {{-- FORM --}}
                <div class="card card-brand p-6 sm:p-8">

                    @if($submitted)

                        <div class="min-h-[500px] flex items-center justify-center text-center">

                            <div class="max-w-[430px]">

                                <div
                                    class="w-[68px] h-[68px] rounded-2xl mx-auto mb-6 flex items-center justify-center"
                                    style="background:rgba(16,185,129,.09);border:1px solid rgba(16,185,129,.25);box-shadow:0 0 45px rgba(16,185,129,.12)"
                                >
                                    <svg width="28" height="28" fill="none">
                                        <path
                                            d="M5 14L11 20L23 7"
                                            stroke="#10B981"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </div>

                                <span class="pill mb-4 inline-block">
                                    Message received
                                </span>

                                <h2 class="font-syne font-bold text-[26px] mb-3">
                                    Thanks for reaching out.
                                </h2>

                                <p class="text-[13px] text-[#7a7a9a] leading-[1.8]">
                                    Your message has been received successfully.
                                    Someone from the Senflux team will review it
                                    and get back to you.
                                </p>

                                <button
                                    type="button"
                                    wire:click="$set('submitted', false)"
                                    class="btn-o mt-7"
                                >
                                    Send another message
                                </button>

                            </div>

                        </div>

                    @else

                        <div class="mb-7">

                            <span class="pill mb-4 inline-block">
                                Get in touch
                            </span>

                            <h2 class="font-syne font-bold text-[24px] mb-2">
                                Send us a message
                            </h2>

                            <p class="text-[13px] text-[#7a7a9a] leading-[1.7]">
                                Tell us a little about what you're looking for.
                                We'll route your message to the right person.
                            </p>

                        </div>


                        @if($errors->has('form'))

                            <div
                                class="mb-6 rounded-xl px-4 py-3 text-[12px]"
                                style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#fca5a5"
                            >
                                {{ $errors->first('form') }}
                            </div>

                        @endif


                        <form wire:submit="submit" class="space-y-5">

                            {{-- Honeypot --}}
                            <div class="hidden" aria-hidden="true">
                                <label>
                                    Website
                                    <input
                                        type="text"
                                        wire:model="website"
                                        tabindex="-1"
                                        autocomplete="off"
                                    >
                                </label>
                            </div>


                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                                <div>
                                    <label
                                        for="name"
                                        class="block text-[11px] font-semibold text-[#c5c5d5] mb-2"
                                    >
                                        Name
                                    </label>

                                    <input
                                        id="name"
                                        type="text"
                                        wire:model.live="name"
                                        autocomplete="name"
                                        placeholder="Your name"
                                        class="contact-input"
                                    >

                                    @error('name')
                                        <p class="contact-error">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div>
                                    <label
                                        for="email"
                                        class="block text-[11px] font-semibold text-[#c5c5d5] mb-2"
                                    >
                                        Email
                                    </label>

                                    <input
                                        id="email"
                                        type="email"
                                        wire:model.live="email"
                                        autocomplete="email"
                                        placeholder="you@company.com"
                                        class="contact-input"
                                    >

                                    @error('email')
                                        <p class="contact-error">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>


                            <div>
                                <label
                                    for="company"
                                    class="block text-[11px] font-semibold text-[#c5c5d5] mb-2"
                                >
                                    Company
                                    <span class="text-[#55556c] font-normal">(optional)</span>
                                </label>

                                <input
                                    id="company"
                                    type="text"
                                    wire:model.live="company"
                                    autocomplete="organization"
                                    placeholder="Your company"
                                    class="contact-input"
                                >

                                @error('company')
                                    <p class="contact-error">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label
                                    for="subject"
                                    class="block text-[11px] font-semibold text-[#c5c5d5] mb-2"
                                >
                                    Subject
                                </label>

                                <select
                                    id="subject"
                                    wire:model.live="subject"
                                    class="contact-input"
                                >
                                    <option value="">Select a topic</option>
                                    <option value="General enquiry">General enquiry</option>
                                    <option value="Product enquiry">Product enquiry</option>
                                    <option value="Partnership">Partnership</option>
                                    <option value="Data / Research">Data / Research</option>
                                    <option value="Media">Media</option>
                                    <option value="Other">Other</option>
                                </select>

                                @error('subject')
                                    <p class="contact-error">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label
                                    for="message"
                                    class="block text-[11px] font-semibold text-[#c5c5d5] mb-2"
                                >
                                    Message
                                </label>

                                <textarea
                                    id="message"
                                    wire:model.live.debounce.150ms="message"
                                    rows="7"
                                    placeholder="Tell us what you'd like to discuss..."
                                    class="contact-input resize-none"
                                ></textarea>

                                <div class="flex justify-between gap-3 mt-2">

                                    @error('message')
                                        <p class="contact-error mt-0">
                                            {{ $message }}
                                        </p>
                                    @else
                                        <p class="text-[10px] text-[#55556c]">
                                            Please don't include sensitive information.
                                        </p>
                                    @enderror

                                    <span class="text-[10px] text-[#55556c]">
                                        {{ $this->messageLength }}/5000
                                    </span>

                                </div>

                            </div>


                            <div class="pt-1">

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    wire:target="submit"
                                    class="btn-p w-full justify-center disabled:opacity-50 disabled:cursor-wait"
                                >

                                    <span wire:loading.remove wire:target="submit">
                                        Send Message →
                                    </span>

                                    <span wire:loading wire:target="submit">
                                        Sending...
                                    </span>

                                </button>

                                <p class="text-[10px] text-[#55556c] text-center mt-4 leading-[1.6]">
                                    By submitting this form, you acknowledge that your
                                    information will be handled according to our
                                    <a href="{{ route('privacy') }}"
                                       class="text-[#8d8daa] hover:text-white transition">
                                        Privacy Policy
                                    </a>.
                                </p>

                            </div>

                        </form>

                    @endif

                </div>

            </div>

        </div>

    </section>


    <style>
        .contact-input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(8,8,18,.72);
            color: #fff;
            padding: 12px 14px;
            font-size: 13px;
            outline: none;
            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .contact-input::placeholder {
            color: #4f4f66;
        }

        .contact-input:hover {
            border-color: rgba(123,92,245,.22);
        }

        .contact-input:focus {
            border-color: rgba(123,92,245,.6);
            background: rgba(10,10,22,.95);
            box-shadow: 0 0 0 3px rgba(123,92,245,.08);
        }

        .contact-error {
            margin-top: 7px;
            color: #f87171;
            font-size: 10px;
        }

        select.contact-input option {
            background: #111120;
            color: #fff;
        }
    </style>

</div>
