{{-- resources/views/livewire/protected/packs/my-packs.blade.php --}}
@push('styles')
    @vite('resources/css/my-bots.css')
@endpush

<div class="myb" wire:poll.30000ms="refresh">

    {{-- ── Stat strip ───────────────────────────────────────────────────── --}}
    <div class="myb-stats">
        <div class="myb-stat">
            <p class="myb-stat__label">Active Packs</p>
            <p class="myb-stat__value">{{ $this->activePackCount }}</p>
        </div>
        <div class="myb-stat">
            <p class="myb-stat__label">Total Earned (Active)</p>
            <p class="myb-stat__value myb-stat__value--green">+${{ number_format($this->totalEarningActive, 2) }}</p>
        </div>
        <div class="myb-stat myb-stat--cta">
            <a href="{{ route('dashboard.packs.browse') }}" wire:navigate class="myb-deploy-link">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Buy a Pack
            </a>
        </div>
    </div>

    {{-- ── Pack list ────────────────────────────────────────────────────── --}}
    @if($this->subscriptions->isEmpty())
        <div class="myb-empty">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path d="M21 8l-9-5-9 5 9 5 9-5z"/>
                <path d="M3 8v8l9 5 9-5V8"/>
                <path d="M12 13v8"/>
            </svg>
            <p>No packs bought yet.</p>
            <p class="myb-empty__sub">Buy a pack to unlock allocation slots and start deploying capital.</p>
            <a href="{{ route('dashboard.packs.browse') }}" wire:navigate class="myb-empty__cta">Buy a pack &rarr;</a>
        </div>
    @else
        <div class="myb-grid">
            @php $tierColors = ['#60a5fa', '#9B7DFF', '#fbbf24']; @endphp
            @foreach($this->subscriptions as $sub)
                @php
                    $color = $tierColors[$sub->packTier->sort_order ?? 0] ?? '#6b7280';
                    $isOpen = in_array($sub->status->value, ['active', 'in_renewal_window'], true);
                    $isRenewal = $sub->status->value === 'in_renewal_window';
                    $statusColor = $isRenewal ? '#f59e0b' : ($isOpen ? '#22c55e' : '#6b7280');
                    $fundedCount = $sub->slots->where('status.value', 'funded')->count();
                    $totalSlots = $sub->slots->count();
                    $capitalDeployed = $sub->slots->sum('capital_amount');
                    $earned = $sub->slots->sum('realized_profit');
                @endphp
                <a href="{{ route('dashboard.packs.show', $sub) }}" wire:navigate
                   class="myb-card {{ $isOpen ? 'myb-card--running' : '' }}" style="text-decoration:none;"
                   wire:key="sub-{{ $sub->id }}">

                    <div class="myb-card__head">
                        <div class="myb-card__icon" style="background: {{ $color }}1a; border-color: {{ $color }}44">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="{{ $color }}" stroke-width="1.6">
                                <path d="M21 8l-9-5-9 5 9 5 9-5z"/>
                                <path d="M3 8v8l9 5 9-5V8"/>
                                <path d="M12 13v8"/>
                            </svg>
                        </div>
                        <div class="myb-card__title-wrap">
                            <span class="myb-card__title">{{ $sub->packTier->name }}</span>
                            <span class="myb-card__status" style="color: {{ $statusColor }}">
                                <span class="myb-card__dot" style="background: {{ $statusColor }}"></span>
                                {{ $isRenewal ? 'Renewal Window' : $sub->status->label() }}
                            </span>
                        </div>
                    </div>

                    <div class="myb-card__stats">
                        <div class="myb-card__stat">
                            <span>Deployed</span>
                            <strong>${{ number_format($capitalDeployed, 2) }}</strong>
                        </div>
                        <div class="myb-card__stat">
                            <span>Earned</span>
                            <strong class="myb-card__stat--green">+${{ number_format($earned, 2) }}</strong>
                        </div>
                        <div class="myb-card__stat">
                            <span>Slots</span>
                            <strong>{{ $fundedCount }}/{{ $totalSlots }}</strong>
                        </div>
                    </div>

                    <div class="myb-card__footer">
                        <span>Purchased {{ $sub->purchased_at->format('M j, Y') }}</span>
                        <span>Matures {{ $sub->matures_at->format('M j, Y') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
 
</div>