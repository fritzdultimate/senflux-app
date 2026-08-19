@php
    $categoryDot = [
        'financial' => '#22c55e',
        'security' => '#F0A93D',
        'compliance' => '#60a5fa',
        'account' => '#8B7CF6',
    ];
@endphp

<div
    class="relative"
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    @click.outside="open = false"
>
    <button
        type="button"
        class="tb-ico"
        aria-label="Activity"
        x-on:click="open = !open; if (open) $wire.markSeen()"
    >
        <svg width="14" height="14" fill="none" viewBox="0 0 14 14" stroke="#c8c8e0" stroke-width="1.3">
            <path d="M7 1.5C4.5 1.5 2.5 3.5 2.5 6v3.5l-1 1.5h11l-1-1.5V6C11.5 3.5 9.5 1.5 7 1.5z"/>
            <path d="M5.5 11.5c0 .8.7 1.5 1.5 1.5s1.5-.7 1.5-1.5"/>
        </svg>
        @if($this->hasUnseen)
            <div class="ndot"></div>
        @endif
    </button>

    <style>[x-cloak] { display: none !important; }</style>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed right-3 top-[58px] z-50 w-[calc(100vw-24px)] max-w-[360px] origin-top-right rounded-2xl border border-white/10 bg-[#0B0C14] shadow-2xl shadow-black/50 sm:right-4"
        style="font-family: 'Inter', sans-serif;"
    >
        <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
            <p class="font-['Sora'] text-[13px] font-semibold text-white">Activity</p>
            <a
                href="{{ route('dashboard.activity') }}"
                wire:navigate
                class="text-[11px] font-medium text-[#8B7CF6] hover:text-[#a493ff]"
            >
                View all
            </a>
        </div>

        <div class="max-h-[380px] overflow-y-auto">
            @forelse($this->recentActivity as $log)
                @php $meta = $log->display_meta; @endphp
                <div class="flex items-start gap-3 border-b border-white/[0.05] px-4 py-3 last:border-b-0">
                    <span
                        class="mt-1.5 h-1.5 w-1.5 flex-shrink-0 rounded-full"
                        style="background: {{ $categoryDot[$meta['category']] ?? '#6b7280' }}"
                    ></span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[12.5px] font-medium text-white">{{ $meta['label'] }}</p>
                        @if($log->description)
                            <p class="mt-0.5 truncate text-[11px] text-gray-500">{{ $log->description }}</p>
                        @endif
                        <p class="mt-1 text-[10.5px] text-gray-600" style="font-family: 'IBM Plex Mono', monospace;">
                            {{ $log->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center gap-2 px-4 py-8 text-center">
                    <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#4b5563" stroke-width="1.3">
                        <path d="M12 3C7.5 3 4 6.5 4 11v6l-2 3h20l-2-3v-6c0-4.5-3.5-8-8-8z"/>
                        <path d="M9.5 20c0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5"/>
                    </svg>
                    <p class="text-[11.5px] text-gray-500">No recent activity yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
