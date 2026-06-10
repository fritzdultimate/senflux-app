{{--
    Ticker Strip Component
    Usage: <x-auth.ticker />
    Optional: pass $items array to override defaults.
--}}

@props([
    'items' => [
        ['sym' => 'BTC',  'price' => '$69,174',     'change' => '+2.4%',  'up' => true],
        ['sym' => 'ETH',  'price' => '$3,482',      'change' => '+1.8%',  'up' => true],
        ['sym' => 'SOL',  'price' => '$187.32',     'change' => '-0.6%',  'up' => false],
        ['sym' => 'WIF',  'price' => '$2.84',       'change' => '+14.2%', 'up' => true],
        ['sym' => 'BONK', 'price' => '$0.0000281',  'change' => '+8.6%',  'up' => true],
        ['sym' => 'XRP',  'price' => '$0.6423',     'change' => '-1.5%',  'up' => false],
    ]
])

<div class="relative overflow-hidden">
    {{-- Fade edges --}}
    <div class="absolute inset-y-0 left-0 w-6 bg-gradient-to-r from-[#0b0f1a] to-transparent z-10 pointer-events-none"></div>
    <div class="absolute inset-y-0 right-0 w-6 bg-gradient-to-l from-[#0b0f1a] to-transparent z-10 pointer-events-none"></div>

    <div id="ticker" class="ticker-strip flex gap-5 overflow-x-auto no-scrollbar py-2">
        @foreach($items as $item)
            <div class="flex items-center gap-2 shrink-0">
                <span class="font-syne text-[11px] font-bold text-[#9B7DFF] tracking-widest">{{ $item['sym'] }}</span>
                <span class="text-[11.5px] text-[#c8c8e0] font-medium">{{ $item['price'] }}</span>
                <span class="text-[10.5px] font-semibold {{ $item['up'] ? 'text-emerald-400' : 'text-red-400' }}">
                    {{ $item['change'] }}
                </span>
            </div>
        @endforeach
    </div>
</div>