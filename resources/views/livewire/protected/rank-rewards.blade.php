{{-- resources/views/livewire/protected/rank-rewards.blade.php --}}
<div>

    <div class="rnk">

        {{-- ── Current rank hero ───────────────────────────────────────────── --}}
        <div class="rnk-hero" style="border-color: {{ $this->currentRank->color() }}33; background: linear-gradient(155deg, {{ $this->currentRank->color() }}14, transparent)">
            <div class="rnk-hero__badge" style="background: {{ $this->currentRank->color() }}22; border-color: {{ $this->currentRank->color() }}55">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="{{ $this->currentRank->color() }}" stroke-width="1.6"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01z"/></svg>
            </div>
            <div class="rnk-hero__body">
                <p class="rnk-hero__label">Current Rank</p>
                <p class="rnk-hero__rank" style="color: {{ $this->currentRank->color() }}">{{ $this->currentRank->label() }}</p>
            </div>
            @if($this->nextRank)
                <div class="rnk-hero__next">
                    <p class="rnk-hero__label">Next Rank</p>
                    <p class="rnk-hero__next-rank">{{ $this->nextRank->label() }}</p>
                </div>
            @else
                <div class="rnk-hero__next">
                    <p class="rnk-hero__max">Maximum rank achieved</p>
                </div>
            @endif
        </div>

        {{-- ── Stat strip ───────────────────────────────────────────────────── --}}
        <div class="rnk-stats">
            <div class="rnk-stat">
                <p class="rnk-stat__label">Team Volume</p>
                <p class="rnk-stat__value">${{ number_format($this->teamVolume->weighted_total, 0) }}</p>
            </div>
            <div class="rnk-stat">
                <p class="rnk-stat__label">Personal Deposit</p>
                <p class="rnk-stat__value">${{ number_format($this->personalDeposit, 0) }}</p>
            </div>
            <div class="rnk-stat">
                <p class="rnk-stat__label">Direct Referrals</p>
                <p class="rnk-stat__value">{{ $this->directReferrals }}</p>
            </div>
            <div class="rnk-stat">
                <p class="rnk-stat__label">Total Rank Bonuses</p>
                <p class="rnk-stat__value rnk-stat__value--green">${{ number_format($this->totalRankBonuses, 2) }}</p>
            </div>
        </div>

        {{-- ── Progress toward next rank ───────────────────────────────────── --}}
        @if(!$this->progress['maxed'])
            <div class="rnk-panel">
                <div class="rnk-panel__head">
                    <p class="rnk-panel__title">Progress to {{ $this->nextRank->label() }}</p>
                    @if($this->progress['qualified'])
                        <span class="rnk-qualified-badge">Qualified — advancing soon</span>
                    @endif
                </div>

                <div class="rnk-progress-rows">
                    {{-- Team Volume --}}
                    <div class="rnk-progress-row">
                        <div class="rnk-progress-row__head">
                            <span>Team Volume</span>
                            <span>${{ number_format($this->progress['tv'], 0) }} / ${{ number_format($this->progress['tv_req'], 0) }}</span>
                        </div>
                        <div class="rnk-bar">
                            <div class="rnk-bar__fill" style="width: {{ $this->progress['tv_pct'] }}%; background: {{ $this->nextRank->color() }}"></div>
                        </div>
                    </div>

                    {{-- Personal Deposit --}}
                    <div class="rnk-progress-row">
                        <div class="rnk-progress-row__head">
                            <span>Personal Deposit</span>
                            <span>${{ number_format($this->progress['pd'], 0) }} / ${{ number_format($this->progress['pd_req'], 0) }}</span>
                        </div>
                        <div class="rnk-bar">
                            <div class="rnk-bar__fill" style="width: {{ $this->progress['pd_pct'] }}%; background: {{ $this->nextRank->color() }}"></div>
                        </div>
                    </div>

                    {{-- Direct Referrals --}}
                    <div class="rnk-progress-row">
                        <div class="rnk-progress-row__head">
                            <span>Direct Referrals</span>
                            <span>{{ $this->progress['dr'] }} / {{ $this->progress['dr_req'] }}</span>
                        </div>
                        <div class="rnk-bar">
                            <div class="rnk-bar__fill" style="width: {{ $this->progress['dr_pct'] }}%; background: {{ $this->nextRank->color() }}"></div>
                        </div>
                    </div>
                </div>

                <p class="rnk-bonus-preview">
                    Reaching {{ $this->nextRank->label() }} unlocks a <strong style="color: {{ $this->nextRank->color() }}">${{ number_format($this->nextRank->cashBonus(), 0) }}</strong> cash bonus.
                </p>
            </div>
        @endif

        {{-- ── Rank ladder ──────────────────────────────────────────────────── --}}
        <div class="rnk-panel">
            <p class="rnk-panel__title" style="margin-bottom: .9rem">Rank Ladder</p>
            <div class="rnk-ladder">
                @foreach($this->allRanks as $r)
                    @php $rank = $r['rank']; @endphp
                    <div class="rnk-ladder-item {{ $r['achieved'] ? 'rnk-ladder-item--achieved' : '' }} {{ $r['current'] ? 'rnk-ladder-item--current' : '' }}">
                        <div class="rnk-ladder-item__dot" style="background: {{ $r['achieved'] ? $rank->color() : 'transparent' }}; border-color: {{ $rank->color() }}"></div>
                        <div class="rnk-ladder-item__body">
                            <span class="rnk-ladder-item__name" style="color: {{ $r['achieved'] ? $rank->color() : '#6b7280' }}">{{ $rank->label() }}</span>
                            <span class="rnk-ladder-item__req">${{ number_format($rank->teamVolumeRequired() / 1000, 0) }}k TV · {{ $rank->directReferralsRequired() }} DR</span>
                        </div>
                        <span class="rnk-ladder-item__bonus">${{ number_format($rank->cashBonus(), 0) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rnk-layout">

            {{-- ── Rank history ─────────────────────────────────────────────── --}}
            <div class="rnk-panel">
                <p class="rnk-panel__title">Rank History</p>
                @if($this->rankHistory->isEmpty())
                    <div class="rnk-empty">
                        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87L18.18 21 12 17.77 5.82 21 7 14.14 2 9.27l6.91-1.01z"/></svg>
                        <p>No rank advancements yet.</p>
                    </div>
                @else
                    <div class="rnk-history-list">
                        @foreach($this->rankHistory as $adv)
                            <div class="rnk-history-row">
                                <div class="rnk-history-row__body">
                                    <span class="rnk-history-row__rank" style="color: {{ $adv->to_rank->color() }}">
                                        {{ $adv->from_rank->label() }} → {{ $adv->to_rank->label() }}
                                    </span>
                                    <span class="rnk-history-row__date">{{ $adv->achieved_at->format('M j, Y') }}</span>
                                </div>
                                <span class="rnk-history-row__bonus">+${{ number_format($adv->bonus_amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Leadership matches ───────────────────────────────────────── --}}
            <div class="rnk-panel">
                <div class="rnk-panel__head">
                    <p class="rnk-panel__title">Leadership Matches</p>
                    <span class="rnk-panel__total">${{ number_format($this->totalLeadershipMatches, 2) }} total</span>
                </div>
                @if($this->leadershipMatches->isEmpty())
                    <div class="rnk-empty">
                        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3"><circle cx="9" cy="7" r="3"/><path d="M3 20v-1a5 5 0 0 1 10 0v1M16 11a3 3 0 0 1 0 6M21 20v-.5a4.5 4.5 0 0 0-4.5-4.5"/></svg>
                        <p>No leadership match bonuses yet.</p>
                        <p class="rnk-empty__sub">Earn 15% when your direct referrals rank up.</p>
                    </div>
                @else
                    <div class="rnk-history-list">
                        @foreach($this->leadershipMatches as $match)
                            <div class="rnk-history-row">
                                <div class="rnk-history-row__body">
                                    <span class="rnk-history-row__rank">
                                        {{ $match->sourceUser->name ?? 'Network member' }} → {{ $match->rankAdvancement->to_rank->label() }}
                                    </span>
                                    <span class="rnk-history-row__date">{{ $match->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="rnk-history-row__bonus">+${{ number_format($match->amount, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
