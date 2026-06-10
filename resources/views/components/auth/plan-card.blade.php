{{--
    Plan Card Component
    Usage:
        <x-auth.plan-card
            id="planFree"
            plan="free"
            label="Free Plan"
            subtitle="Get started immediately"
            price="$0"
            :selected="true"
            :features="[
                ['text' => '8 free signals per day',  'enabled' => true],
                ['text' => 'Basic formation feed',     'enabled' => true],
                ['text' => '1 trading bot',            'enabled' => true],
                ['text' => 'Pro terminal access',      'enabled' => false],
                ['text' => 'Whale cluster alerts',     'enabled' => false],
            ]"
        />
--}}

@props([
    'id'       => 'planCard',
    'plan'     => 'free',
    'label'    => 'Free Plan',
    'subtitle' => 'Get started immediately',
    'price'    => '$0',
    'popular'  => false,
    'selected' => false,
    'features' => [],
])

<div
    id="{{ $id }}"
    onclick="selectPlan('{{ $plan }}')"
    class="plan-card relative rounded-2xl border p-4 cursor-pointer transition-all duration-200
           {{ $selected ? 'border-[rgba(155,125,255,.55)] bg-[rgba(155,125,255,.08)] shadow-[0_0_0_1px_rgba(155,125,255,.25)]' : 'border-[rgba(255,255,255,.07)] bg-[rgba(255,255,255,.03)]' }}
           hover:border-[rgba(155,125,255,.4)]"
>
    {{-- "Most popular" badge --}}
    @if($popular)
        <div class="absolute -top-px right-4 bg-gradient-to-br from-amber-400 to-orange-500 text-white
                    font-syne text-[10px] font-bold px-2.5 py-0.5 rounded-b-lg tracking-wider uppercase">
            MOST POPULAR
        </div>
    @endif

    {{-- Header row --}}
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center gap-2.5">
            {{-- Radio dot --}}
            <div id="radio{{ ucfirst($plan) }}"
                 class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition-all duration-200
                        {{ $selected ? 'border-[#9B7DFF] bg-[#9B7DFF]' : 'border-[rgba(255,255,255,.2)] bg-transparent' }}">
                @if($selected)
                    <svg width="6" height="6" fill="white" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                @endif
            </div>

            <div>
                <div class="font-syne text-sm font-bold text-white">{{ $label }}</div>
                <div class="text-[11.5px] text-[#4a4a6a] mt-0.5">{{ $subtitle }}</div>
            </div>
        </div>

        {{-- Price --}}
        <div class="text-right">
            <div class="font-syne text-xl font-extrabold {{ $popular ? 'text-amber-400' : 'text-white' }}">
                {{ $price }}<span class="text-xs text-[#6b6b8a] font-normal">/mo</span>
            </div>
        </div>
    </div>

    {{-- Feature list --}}
    <div class="flex flex-col gap-1.5">
        @foreach($features as $feature)
            <div class="flex items-center gap-1.5 text-[12.5px] {{ $feature['enabled'] ? 'text-[#c8c8e0]' : 'text-[#4a4a6a]' }}">
                @if($feature['enabled'])
                    <svg width="13" height="13" fill="none" viewBox="0 0 13 13"
                         stroke="#10B981" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                        <path d="M1.5 6.5L5 10L11.5 3"/>
                    </svg>
                @else
                    <svg width="13" height="13" fill="none" viewBox="0 0 13 13"
                         stroke="#4a4a6a" stroke-width="1.4" stroke-linecap="round" class="shrink-0">
                        <path d="M2 2L11 11M11 2L2 11"/>
                    </svg>
                @endif
                {{ $feature['text'] }}
            </div>
        @endforeach
    </div>
</div>