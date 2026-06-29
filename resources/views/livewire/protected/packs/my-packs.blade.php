<div>
    @vite('resources/css/dc.css')

    <div class="dc">
        <div class="dc-console-header">
            <div class="dc-console-header__label">
                <span class="dc-console-header__eyebrow">My Packs</span>
                <h2>Your active and past pack subscriptions</h2>
            </div>
            <a href="{{ route('dashboard.packs.browse') }}" wire:navigate class="dc-submit">Buy a Pack</a>
        </div>

        @if($this->subscriptions->isEmpty())
            <div class="history-empty">
                <p>You haven't bought a pack yet.</p>
            </div>
        @else
            <div class="dc-panel">
                @foreach($this->subscriptions as $sub)
                    <a href="{{ route('dashboard.packs.show', $sub) }}" wire:navigate
                       class="dc-active-row" wire:key="sub-{{ $sub->id }}" style="display:flex; text-decoration:none;">
                        <div>
                            <span class="dc-active-row__plan">{{ $sub->packTier->name }}</span>
                            <span class="dc-active-row__since">
                                {{ $sub->slots->where('status', 'funded')->count() }}/{{ $sub->slots->count() }} slots funded
                                · matures {{ $sub->matures_at->format('M j, Y') }}
                            </span>
                        </div>
                        <div class="dc-active-row__amounts">
                            <span class="dc-tier__tag">{{ $sub->status->label() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
