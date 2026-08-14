<div>

    <div class="dc">
        <div class="dc-console-header">
            <div class="dc-console-header__label">
                <span class="dc-console-header__eyebrow">Senflux Intelligence Engine</span>
                <h2>Live Formation Feed</h2>
            </div>
            <div class="dc-console-header__pulse">
                <span class="dc-pulse-dot"></span>
                {{ $this->formations->where('state', \App\Enums\FormationState::ACTIVE)->count() }} active
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px;">
            @foreach($this->formations as $formation)
                <a href="{{ route('dashboard.formations.show', $formation) }}" wire:navigate
                   class="dc-panel" style="text-decoration:none; display:block;" wire:key="formation-card-{{ $formation->id }}">

                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <div>
                            <h3 style="margin:0; font-size:16px;">{{ $formation->token_symbol }}</h3>
                            <span class="dc-active-row__since">{{ $formation->token_name }}</span>
                        </div>
                        <span class="dc-tier__tag" style="background:{{ $formation->state->color() }}22; color:{{ $formation->state->color() }};">
                            {{ $formation->state->label() }}
                        </span>
                    </div>

                    <div class="dc-readout" style="margin-top:12px;">
                        <div class="dc-readout__item">
                            <span class="dc-readout__label">Score</span>
                            <span class="dc-readout__value">{{ $formation->score }}/100</span>
                        </div>
                        <div class="dc-readout__divider"></div>
                        <div class="dc-readout__item">
                            <span class="dc-readout__label">Confidence</span>
                            <span class="dc-readout__value">{{ ucfirst($formation->confidence) }}</span>
                        </div>
                    </div>

                    <p class="dc-active-row__since" style="margin-top:8px;">Detected {{ $formation->detectedAgo() }}</p>

                    <div style="margin-top:14px; display:flex; flex-direction:column; gap:8px;">
                        @foreach([
                            ['Capital Concentration', $formation->capital_concentration],
                            ['Liquidity Migration', $formation->liquidity_migration],
                            ['Participation Growth', $formation->participation_growth],
                            ['Wallet Quality', $formation->wallet_quality],
                        ] as [$label, $value])
                            <div>
                                <div style="display:flex; justify-content:space-between; font-size:12px; color:#9ca3af;">
                                    <span>{{ $label }}</span>
                                    <span>{{ $value }}%</span>
                                </div>
                                <div style="height:6px; background:#1f2937; border-radius:3px; overflow:hidden;">
                                    <div style="height:100%; width:{{ $value }}%; background:{{ $formation->state->color() }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </a>
            @endforeach
        </div>

        @if($this->formations->isEmpty())
            <div class="history-empty">
                <p>No formations are currently being tracked.</p>
            </div>
        @endif
    </div>
</div>
