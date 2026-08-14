<div>

    <div class="dc">
        <div class="dc-console-header">
            <div class="dc-console-header__label">
                <span class="dc-console-header__eyebrow">{{ $this->formation->ecosystem }}</span>
                <h2>{{ $this->formation->token_symbol }} — {{ $this->formation->token_name }}</h2>
            </div>
            <span class="dc-tier__tag" style="background:{{ $this->formation->state->color() }}22; color:{{ $this->formation->state->color() }};">
                {{ $this->formation->state->label() }}
            </span>
        </div>

        {{-- Overview --}}
        <div class="dc-readout">
            <div class="dc-readout__item">
                <span class="dc-readout__label">Formation Score</span>
                <span class="dc-readout__value">{{ $this->formation->score }}/100</span>
            </div>
            <div class="dc-readout__divider"></div>
            <div class="dc-readout__item">
                <span class="dc-readout__label">Confidence</span>
                <span class="dc-readout__value">{{ ucfirst($this->formation->confidence) }}</span>
            </div>
            <div class="dc-readout__divider"></div>
            <div class="dc-readout__item">
                <span class="dc-readout__label">Detected</span>
                <span class="dc-readout__value">{{ $this->formation->detectedAgo() }}</span>
            </div>
        </div>

        <p class="dc-subtext">{{ $this->formation->state->description() }}</p>

        {{-- Intelligence --}}
        <div class="dc-panel">
            <div class="dc-panel__header">
                <span class="dc-panel__title">Intelligence</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px;">
                @foreach([
                    ['Capital Concentration', $this->formation->capital_concentration],
                    ['Liquidity Flow', $this->formation->liquidity_migration],
                    ['Participation Growth', $this->formation->participation_growth],
                    ['Wallet Intelligence', $this->formation->wallet_quality],
                ] as [$label, $value])
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:13px; color:#9ca3af;">
                            <span>{{ $label }}</span>
                            <span>{{ $value }}%</span>
                        </div>
                        <div style="height:8px; background:#1f2937; border-radius:4px; overflow:hidden;">
                            <div style="height:100%; width:{{ $value }}%; background:{{ $this->formation->state->color() }};"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Timeline --}}
        <div class="dc-panel">
            <div class="dc-panel__header">
                <span class="dc-panel__title">Timeline</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:0;">
                @foreach($this->timelineSteps as $i => $step)
                    <div style="display:flex; align-items:center; gap:10px; padding:6px 0;">
                        <div style="width:10px; height:10px; border-radius:50%; background:{{ $step['completed'] || $step['active'] ? $this->formation->state->color() : '#374151' }};"></div>
                        <span style="color:{{ $step['active'] ? '#fff' : ($step['completed'] ? '#9ca3af' : '#4b5563') }};">
                            {{ $step['label'] }}
                        </span>
                    </div>
                    @if($i < count($this->timelineSteps) - 1)
                        <div style="width:1px; height:14px; background:#374151; margin-left:5px;"></div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Deployment status for the viewer's own slots --}}
        <div class="dc-panel">
            <div class="dc-panel__header">
                <span class="dc-panel__title">Your deployment status</span>
            </div>

            @if($this->mySlots->isEmpty())
                <p class="dc-subtext">You don't have any funded slots right now.</p>
            @else
                @foreach($this->mySlots as $slot)
                    <div class="dc-active-row" wire:key="myslot-{{ $slot->id }}">
                        <span>Slot #{{ $slot->slot_number }} ({{ $slot->subscription->packTier->name }})</span>
                        @php
                            $status = $slot->formation_id === $this->formation->id
                                ? 'already_deployed'
                                : ($slot->formation_id ? 'deployed_elsewhere' : $slot->deploymentStatus());
                        @endphp
                        <span class="dc-tier__tag">
                            {{ match($status) {
                                'already_deployed' => 'Already Deployed Here',
                                'deployed_elsewhere' => 'Deployed Elsewhere',
                                'eligible_for_deployment' => 'Eligible For Deployment',
                                default => 'Waiting For Qualification',
                            } }}
                        </span>
                    </div>
                @endforeach
            @endif
        </div>

        <a href="{{ route('dashboard.formations.index') }}" wire:navigate class="dc-subtext">&larr; Back to Formation Feed</a>
    </div>
</div>
