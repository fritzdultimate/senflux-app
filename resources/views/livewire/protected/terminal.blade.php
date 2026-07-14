<div wire:poll.10000="refresh">
    @push('styles')
        @vite('resources/css/terminal.css')
        @vite('resources/css/dashboard.css')
    @endpush

    <div class="dash">

        <div class="panel__head" style="margin-bottom: 20px">
            <div>
                <div class="panel__title" style="font-size: 1.1rem">Live Formation Feed</div>
                <div class="panel__sub">Senflux Intelligence Engine · updated {{ now()->format('g:i A') }}</div>
            </div>
        </div>

        {{-- Feed + Heatmap --}}
        <div class="grid grid-cols-1 xl:grid-cols-[1fr_358px] gap-3.5 mb-3.5">

            <div class="terminal-feed-panel rounded-2xl overflow-hidden" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
                <div class="flex items-center justify-between px-5 py-3.5 border-b" style="border-color:rgba(255,255,255,.07)">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#10B981] ap block"></span>
                        <span class="text-[12px] font-semibold text-[#c8c8e0]">LIVE FORMATION FEED</span>
                    </div>
                    <span class="text-[11px] text-[#4a4a6a]">
                        {{ $this->formations->total() }} tracked · updated {{ now()->diffForHumans() }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="ftbl">
                        <thead>
                            <tr>
                                <th>Asset</th>
                                <th>Formation State</th>
                                <th>Participation (VS 24H)</th>
                                <th>Persistence Score</th>
                                <th>Velocity</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($this->formations as $formation)
                                <tr role="navigation" onclick="window.location='{{ route('dashboard.formations.detail', $formation) }}'" style="cursor:pointer" wire:navigate>
                                    <td>
                                        <span class="font-syne font-bold text-white text-[13px]">{{ $formation->token_symbol }}</span><br>
                                        <span class="text-[11px] text-[#4a4a6a]">{{ $formation->token_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:{{ $formation->state->color() }}22;color:{{ $formation->state->color() }};border:1px solid {{ $formation->state->color() }}55">
                                            {{ strtoupper($formation->state->label()) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="color:{{ $formation->state->color() }}" class="font-semibold">
                                            {{ $formation->price_change_24h >= 0 ? '+' : '' }}{{ number_format($formation->price_change_24h ?? 0, 0) }}%
                                        </span><br>
                                        <span class="text-[11px] text-[#4a4a6a]">{{ $formation->participationLevel() }}</span>
                                    </td>
                                    <td>
                                        <span class="text-white font-bold">{{ $formation->score }}</span><span class="text-[#4a4a6a] text-[12px]">/100</span><br>
                                        <span class="text-[11px] font-semibold" style="color:{{ $formation->state->color() }}">
                                            {{ $formation->persistenceLevel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="spark">
                                            @foreach ($formation->sparklineHeights() as $i => $h)
                                                <span style="height:{{ $h }}px; {{ $loop->last ? 'opacity:1;background:'.$formation->state->color() : '' }}"></span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td style="color:{{ $formation->state->color() }}">
                                        @switch ($formation->trendDirection())
                                            @case ('up')
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M7 17L17 7"/><path d="M8 7h9v9"/>
                                                </svg>
                                                @break
                                            @case ('down')
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M7 7l10 10"/><path d="M17 7v10H7"/>
                                                </svg>
                                                @break
                                            @default
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M5 12h14"/>
                                                </svg>
                                        @endswitch
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-8 text-[#4a4a6a]">No formations being monitored right now.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($this->formations->hasPages())
                    <div class="ff-pagination">
                        {{ $this->formations->links('vendor.pagination.senflux') }}
                    </div>
                @endif
            </div>

            {{-- Heatmap --}}
            <div class="heatmap-panel rounded-2xl overflow-hidden" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
                <div class="flex items-center justify-between px-4 py-3.5 border-b" style="border-color:rgba(255,255,255,.07)">
                    <span class="text-[12px] font-semibold text-[#c8c8e0]">PARTICIPATION HEATMAP</span>
                    <span class="text-[11px] text-[#9B7DFF] rounded-md px-2 py-1" style="background:rgba(123,92,245,.1);border:1px solid rgba(123,92,245,.2)">Solana Ecosystem</span>
                </div>
                <div class="grid grid-cols-3 gap-1.5 p-3.5">
                    @foreach ($this->sectorHeatmap as $sector)
                        <div class="hx hx-{{ $sector['strength'] }}">
                            <p class="text-[10px] font-bold text-white leading-tight">{{ $sector['label'] }}</p>
                            <p class="text-[9px] mt-0.5" style="color:rgba(255,255,255,.55)">{{ $sector['strengthLabel'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-4 border-t" style="border-color:rgba(255,255,255,.07)">
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        @php $totalWallets = \App\Models\Formation::active()->sum('active_wallets'); @endphp
                        <p class="font-syne font-bold text-[14px] text-white">
                            {{ $totalWallets > 0 ? number_format($totalWallets) : '—' }}
                        </p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Active Wallets</p>
                        @if ($totalWallets === 0)
                            <p class="text-[8px] text-[#4a4a6a] mt-0.5">Pending Birdeye</p>
                        @endif
                    </div>
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        <p class="font-syne font-bold text-[14px] text-white">{{ number_format($this->platformStats['active_participants']) }}</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Active Participants</p>
                    </div>
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        <p class="font-syne font-bold text-[14px] text-white">{{ number_format($this->platformStats['new_deployments_24h']) }}</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">New Deployments 24H</p>
                    </div>
                    <div class="p-3 text-center border-r" style="border-color:rgba(255,255,255,.07)">
                        <p class="font-syne font-bold text-[14px] text-white">${{ number_format($this->platformStats['capital_deployed']) }}</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Capital Deployed</p>
                    </div>
                    <div class="p-3 text-center">
                        <p class="font-syne font-bold text-[14px] text-white">{{ $this->platformStats['avg_formation_score'] }}/100</p>
                        <p class="text-[9.5px] text-[#4a4a6a] mt-0.5">Avg Formation Score</p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="formation-explained rounded-2xl p-5" style="background:rgba(8,8,18,.94);border:1px solid rgba(255,255,255,.07)">
            <p class="text-[12px] font-semibold text-[#c8c8e0] uppercase tracking-wider mb-4">Formation States Explained</p>
            <div class="grid grid-cols-2 md:grid-cols-6 gap-2">
                @foreach (\App\Enums\FormationState::cases() as $state)
                    <div class="fs" style="background:{{ $state->color() }}14;border:1px solid {{ $state->color() }}33">
                        <p class="text-[10px] font-bold uppercase tracking-wide leading-tight" style="color:{{ $state->color() }}">{{ $state->label() }}</p>
                        <p class="text-[10px] text-[rgba(255,255,255,.4)] mt-1 leading-tight">{{ $state->description() }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    
</div>