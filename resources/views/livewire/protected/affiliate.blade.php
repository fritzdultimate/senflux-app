{{-- resources/views/livewire/protected/affiliate.blade.php --}}
<div>
    @push('styles')
        @vite('resources/css/affiliate.css')
    @endpush

    <div class="aff">

        {{-- ── Referral link card ──────────────────────────────────────────── --}}
        <div class="aff-link-card">
            <div class="aff-link-card__head">
                <div>
                    <p class="aff-link-card__label">Your Referral Link</p>
                    <p class="aff-link-card__sub">Share this to start earning across 8 levels</p>
                </div>
                <div class="aff-code-pill">{{ $this->user->affiliate_code }}</div>
            </div>

            <div
                class="aff-link-box"
                x-data="{ link: @js($this->referralLink) }"
            >
                <span class="aff-link-text" x-text="link"></span>
                <button
                    type="button"
                    class="aff-copy-btn"
                    wire:click="flashCopied"
                    x-on:click="navigator.clipboard.writeText(link)"
                >
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Copy
                </button>
            </div>

            @if($copiedFlash)
                <p class="aff-copied-flash" wire:poll.2500ms="$set('copiedFlash', '')">{{ $copiedFlash }}</p>
            @endif
        </div>

        {{-- ── Stat strip ───────────────────────────────────────────────────── --}}
        <div class="aff-stats">
            <div class="aff-stat">
                <p class="aff-stat__label">Direct Referrals</p>
                <p class="aff-stat__value">{{ $this->directReferralsCount }}</p>
            </div>
            <div class="aff-stat">
                <p class="aff-stat__label">Network Size</p>
                <p class="aff-stat__value">{{ $this->totalNetworkSize }}</p>
            </div>
            <div class="aff-stat">
                <p class="aff-stat__label">Total Earned</p>
                <p class="aff-stat__value aff-stat__value--green">${{ number_format($this->totalReferralEarnings, 2) }}</p>
            </div>
            <div class="aff-stat">
                <p class="aff-stat__label">This Month</p>
                <p class="aff-stat__value aff-stat__value--green">${{ number_format($this->thisMonthEarnings, 2) }}</p>
            </div>
        </div>

        <div class="aff-layout">

            {{-- ── Level breakdown ─────────────────────────────────────────── --}}
            <div class="aff-panel">
                <p class="aff-panel__title">8-Level Commission Structure</p>
                <div class="aff-levels">
                    @foreach($this->levelBreakdown as $lvl)
                        <div class="aff-level-row {{ $lvl['count'] > 0 ? 'aff-level-row--active' : '' }}">
                            <div class="aff-level-row__badge">L{{ $lvl['level'] }}</div>
                            <div class="aff-level-row__rate">{{ number_format($lvl['rate'] * 100, 2) }}%</div>
                            <div class="aff-level-row__count">
                                {{ $lvl['count'] }} {{ Str::plural('member', $lvl['count']) }}
                            </div>
                            <div class="aff-level-row__earned">
                                ${{ number_format($lvl['earned'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── Recent bonuses ──────────────────────────────────────────── --}}
            <div class="aff-panel">
                <p class="aff-panel__title">Recent Bonuses</p>

                @if($this->recentBonuses->isEmpty())
                    <div class="aff-empty">
                        <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3"><circle cx="9" cy="7" r="3"/><path d="M3 20v-1a5 5 0 0 1 10 0v1M16 11a3 3 0 0 1 0 6M21 20v-.5a4.5 4.5 0 0 0-4.5-4.5"/></svg>
                        <p>No referral bonuses yet.</p>
                    </div>
                @else
                    <div class="aff-bonus-list">
                        @foreach($this->recentBonuses as $bonus)
                            <div class="aff-bonus-row">
                                <div class="aff-bonus-row__level">L{{ $bonus->level }}</div>
                                <div class="aff-bonus-row__body">
                                    <span class="aff-bonus-row__name">{{ $bonus->sourceUser->name ?? 'Network member' }}</span>
                                    <span class="aff-bonus-row__time">{{ $bonus->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="aff-bonus-row__amount">+${{ number_format($bonus->amount, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        {{-- ── Direct referrals table ──────────────────────────────────────── --}}
        <div class="aff-panel">
            <p class="aff-panel__title">Direct Referrals</p>

            @if($this->directReferrals->isEmpty())
                <div class="aff-empty">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><circle cx="9" cy="7" r="3"/><path d="M3 20v-1a5 5 0 0 1 10 0v1M16 11a3 3 0 0 1 0 6M21 20v-.5a4.5 4.5 0 0 0-4.5-4.5"/></svg>
                    <p>No direct referrals yet. Share your link to start building your network.</p>
                </div>
            @else
                <div class="aff-table-scroll">
                    <table class="aff-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Joined</th>
                                <th>Plan</th>
                                <th>Deposited</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->directReferrals as $ref)
                                <tr>
                                    <td>
                                        <span class="aff-table__name">{{ $ref->name }}</span>
                                    </td>
                                    <td class="aff-table__muted">{{ $ref->created_at->format('M j, Y') }}</td>
                                    <td>
                                        @if($ref->subscription_plan)
                                            <span class="aff-plan-badge">{{ ucfirst($ref->subscription_plan) }}</span>
                                        @else
                                            <span class="aff-table__muted">—</span>
                                        @endif
                                    </td>
                                    <td class="aff-table__amount">${{ number_format($ref->total_deposited ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="aff-pagination">
                    {{ $this->directReferrals->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
