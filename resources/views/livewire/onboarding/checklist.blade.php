<div>
    @if(! $onboarding->completed)
    <div x-data="{ open: {{ $collapsed ? 'true' : 'false' }} }"
        class="rounded-2xl border border-white/[.07] bg-[#0d0d1a] overflow-hidden mb-2">

        {{-- ── Header ── --}}
        <div class="flex items-center justify-between px-5 py-4 cursor-pointer
                    hover:bg-white/[.02] transition-colors"
            @click="open = !open">

            <div class="flex items-center gap-3">
                {{-- Progress ring --}}
                <div class="relative w-9 h-9 shrink-0">
                    <svg width="36" height="36" viewBox="0 0 36 36" class="-rotate-90">
                        <circle cx="18" cy="18" r="14" fill="none"
                                stroke="rgba(255,255,255,.06)" stroke-width="3"/>
                        <circle cx="18" cy="18" r="14" fill="none"
                                stroke="#7B5CF5" stroke-width="3"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round(2 * M_PI * 14, 2) }}"
                                stroke-dashoffset="{{ round(2 * M_PI * 14 * (1 - $percent / 100), 2) }}"
                                style="transition:stroke-dashoffset .5s ease"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center
                                font-syne font-bold text-[9px] text-white">
                        {{ $percent }}%
                    </span>
                </div>

                <div>
                    <div class="font-syne font-bold text-white text-[13px]">Getting started</div>
                    <div class="text-[11.5px] text-[#4a4a6a] mt-0.5">
                        {{ $done }} of {{ $total }} steps complete
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button
                        @click.stop
                        x-on:click.stop="if(confirm('Dismiss the getting started guide? You can\'t undo this.')) $wire.skip()"
                        class="text-[11.5px] text-[#4a4a6a] hover:text-[#7a7a9a]
                            transition-colors cursor-pointer px-2 py-1">
                    Dismiss
                </button>

                <svg width="14" height="14" fill="none" viewBox="0 0 14 14"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                    class="text-[#4a4a6a] transition-transform duration-200"
                    :class="open ? 'rotate-180' : 'rotate-0'">
                    <path d="M3 5l4 4 4-4"/>
                </svg>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="h-[2px] bg-white/[.04] mx-5">
            <div class="h-full bg-gradient-to-r from-[#7B5CF5] to-[#9B7DFF] rounded-full transition-all duration-500"
                style="width:{{ $percent }}%"></div>
        </div>

        {{-- ── Steps ── --}}
        <div x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1">

            <div class="px-5 py-3 flex flex-col gap-1">
                @foreach($steps as $key => $step)
                    @php $isDone = $onboarding->{$key}; @endphp

                    <a href="{{ isset($step['route']) ? route($step['route']) : '#' }}"
                    wire:navigate
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl
                            transition-all duration-150 group no-underline
                            {{ $isDone
                                ? 'opacity-50 cursor-default pointer-events-none'
                                : 'hover:bg-white/[.04] cursor-pointer' }}">

                        <div class="w-6 h-6 rounded-full shrink-0 flex items-center justify-center
                                    border transition-all duration-200
                                    {{ $isDone
                                        ? 'bg-[#7B5CF5] border-[#7B5CF5]'
                                        : 'border-white/[.15] bg-transparent group-hover:border-[rgba(123,92,245,.4)]' }}">
                            @if($isDone)
                                <svg width="10" height="10" fill="none" viewBox="0 0 10 10"
                                    stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 5l2.5 2.5L8 3"/>
                                </svg>
                            @else
                                <span class="text-[9px] font-bold text-[#4a4a6a] group-hover:text-[#9B7DFF] transition-colors">
                                    {{ $loop->iteration }}
                                </span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="text-[13px] font-medium transition-colors
                                        {{ $isDone ? 'text-[#4a4a6a] line-through' : 'text-[#c8c8e0] group-hover:text-white' }}">
                                {{ $step['label'] }}
                            </div>
                            @unless($isDone)
                                <div class="text-[11.5px] text-[#4a4a6a] mt-0.5 leading-snug truncate">
                                    {{ $step['description'] }}
                                </div>
                            @endunless
                        </div>

                        @unless($isDone)
                            <span class="shrink-0 text-[10px] font-bold px-1.5 py-0.5 rounded
                                        bg-[rgba(123,92,245,.1)] text-[#7B5CF5] border border-[rgba(123,92,245,.2)]">
                                +{{ $step['xp'] }}xp
                            </span>
                            <svg width="12" height="12" fill="none" viewBox="0 0 12 12"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                class="shrink-0 text-[#4a4a6a] group-hover:text-[#9B7DFF]
                                        group-hover:translate-x-0.5 transition-all duration-150">
                                <path d="M2.5 6h7M6 2.5l3.5 3.5L6 9.5"/>
                            </svg>
                        @endunless
                    </a>
                @endforeach
            </div>

            @if($onboarding->isFullyComplete())
                <div class="mx-5 mb-4 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20
                            flex items-center gap-3">
                    <svg width="18" height="18" fill="none" viewBox="0 0 18 18"
                        stroke="#10B981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l4 4 8-8"/>
                    </svg>
                    <div>
                        <div class="text-[13px] font-semibold text-emerald-400">Setup complete!</div>
                        <div class="text-[11.5px] text-[#4a4a6a]">You've earned 100xp. You're all set.</div>
                    </div>
                </div>
            @endif

        </div>

    </div>
    @endif
</div>