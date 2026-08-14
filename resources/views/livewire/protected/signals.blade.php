{{-- resources/views/livewire/protected/signals.blade.php --}}

<div class="sig" wire:poll.30000ms="refresh">

    <div class="sig-intro">
        <h2 class="sig-intro__title">Signals</h2>
        <p class="sig-intro__desc">Curated buy/sell/watch calls from the Senflux desk, based on formation strength and on-chain activity.</p>
    </div>

    @if($this->visibleSignals->isEmpty() && $this->lockedCount === 0)
        <div class="sig-empty">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M2 8.5h11M7.5 4L11 8.5L7.5 13M2 4v9"/></svg>
            <p>No active signals right now. Check back soon.</p>
        </div>
    @else
        <div class="sig-grid">
            @foreach($this->visibleSignals as $signal)
                @php $color = $signal->signal_type->color(); @endphp
                <div class="sig-card" style="--accent: {{ $color }}">
                    <div class="sig-card__head">
                        <div>
                            <span class="sig-card__asset">{{ $signal->trackedAsset->symbol }}</span>
                            <span class="sig-card__network">{{ $signal->trackedAsset->network }}</span>
                        </div>
                        <span class="sig-type-badge" style="color: {{ $color }}; background: {{ $color }}1a; border-color: {{ $color }}44">
                            {{ $signal->signal_type->label() }}
                        </span>
                    </div>

                    <div class="sig-confidence">
                        <div class="sig-confidence__track">
                            <div class="sig-confidence__fill" style="width: {{ $signal->confidence_score }}%; background: {{ $color }}"></div>
                        </div>
                        <span class="sig-confidence__val">{{ number_format($signal->confidence_score, 0) }}/100</span>
                    </div>

                    @if($signal->note)
                        <p class="sig-card__note">{{ $signal->note }}</p>
                    @endif

                    <div class="sig-card__footer">
                        <span>{{ $signal->created_at->diffForHumans() }}</span>
                        @if($signal->expires_at)
                            <span>Expires {{ $signal->expires_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($this->lockedCount > 0)
                <div class="sig-locked-card">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    <p class="sig-locked-card__title">
                        {{ $this->lockedCount }} more signal{{ $this->lockedCount !== 1 ? 's' : '' }} locked
                    </p>
                    <p class="sig-locked-card__desc">
                        Upgrade to {{ $this->highestLockedPlan?->label() }} to unlock these calls.
                    </p>
                    <a href="{{ route('dashboard.subscribe') }}" wire:navigate class="sig-locked-card__cta">
                        Upgrade Plan →
                    </a>
                </div>
            @endif
        </div>
    @endif

</div>
