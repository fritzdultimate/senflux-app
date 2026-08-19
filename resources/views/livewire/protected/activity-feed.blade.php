@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">
@endpush

@php
    $categoryColor = [
        'financial' => '#22c55e',
        'security' => '#F0A93D',
        'compliance' => '#60a5fa',
        'account' => '#8B7CF6',
    ];
@endphp

<div class="relative min-h-screen overflow-hidden bg-[#07080C]">

    {{-- ── Ambient backdrop glow ──────────────────────────────────────── --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[420px] overflow-hidden">
        <div class="absolute left-1/2 top-[-180px] h-[420px] w-[680px] -translate-x-1/2 rounded-full blur-3xl opacity-[0.12]" style="background: #8B7CF6"></div>
    </div>
    <div class="pointer-events-none absolute inset-0 opacity-[0.4]" style="background-image: radial-gradient(rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 28px 28px;"></div>

    <div class="relative mx-auto w-full max-w-3xl px-4 py-10 sm:px-6 lg:px-0" style="font-family: 'Inter', sans-serif;">

        {{-- ── Header ────────────────────────────────────────────────── --}}
        <div class="mb-6">
            <p class="font-['Sora'] text-[20px] font-semibold text-white">Activity Log</p>
            <p class="mt-1 text-[13px] text-gray-500">A full history of everything that's happened on your account.</p>
        </div>

        {{-- ── Category filters ─────────────────────────────────────── --}}
        <div class="mb-5 flex flex-wrap gap-2">
            @foreach($this->categories as $key => $label)
                <button
                    type="button"
                    wire:click="setFilter('{{ $key }}')"
                    class="rounded-full border px-3.5 py-1.5 text-[12px] font-medium transition
                        {{ $filter === $key
                            ? 'border-[#8B7CF6]/50 bg-[#8B7CF6]/15 text-[#c4b5fd]'
                            : 'border-white/10 bg-white/[0.03] text-gray-400 hover:border-white/20 hover:text-gray-300' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- ── Feed ──────────────────────────────────────────────────── --}}
        <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02]">
            @forelse($this->activity as $log)
                @php $meta = $log->display_meta; @endphp
                <div class="flex items-start gap-3.5 border-b border-white/[0.05] px-4 py-4 last:border-b-0 sm:px-5">
                    <span
                        class="mt-1 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full"
                        style="background: color-mix(in srgb, {{ $categoryColor[$meta['category']] ?? '#6b7280' }} 15%, transparent);"
                    >
                        <span
                            class="h-2 w-2 rounded-full"
                            style="background: {{ $categoryColor[$meta['category']] ?? '#6b7280' }}"
                        ></span>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                            <p class="text-[13.5px] font-medium text-white">{{ $meta['label'] }}</p>
                            <p class="text-[11px] text-gray-600" style="font-family: 'IBM Plex Mono', monospace;">
                                {{ $log->created_at->format('M j, Y \a\t g:i A') }}
                            </p>
                        </div>
                        @if($log->description)
                            <p class="mt-1 text-[12.5px] text-gray-500">{{ $log->description }}</p>
                        @endif
                        <span class="mt-2 inline-block rounded-full border border-white/10 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-gray-500">
                            {{ ucfirst($meta['category']) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center gap-3 px-4 py-16 text-center">
                    <svg width="30" height="30" fill="none" viewBox="0 0 24 24" stroke="#4b5563" stroke-width="1.3">
                        <path d="M12 3C7.5 3 4 6.5 4 11v6l-2 3h20l-2-3v-6c0-4.5-3.5-8-8-8z"/>
                        <path d="M9.5 20c0 1.4 1.1 2.5 2.5 2.5s2.5-1.1 2.5-2.5"/>
                    </svg>
                    <p class="text-[13px] text-gray-500">No activity in this category yet.</p>
                </div>
            @endforelse
        </div>

        @if($this->activity->hasPages())
            <div class="mt-5">
                {{ $this->activity->links() }}
            </div>
        @endif

    </div>
</div>
