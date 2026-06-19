<?php

namespace App\Livewire\Protected;

use App\Enums\RankLevel;
use App\Models\LeadershipMatchBonus;
use App\Models\RankAdvancement;
use App\Services\TeamVolumeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Rank & Rewards')]
class RankRewards extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function currentRank(): RankLevel
    {
        $rank = $this->user->rank;
        return $rank instanceof RankLevel ? $rank : RankLevel::from($rank ?? 'none');
    }

    #[Computed]
    public function nextRank(): ?RankLevel
    {
        return $this->currentRank->next();
    }

    #[Computed]
    public function teamVolume()
    {
        return app(TeamVolumeService::class)->computeForUser($this->user);
    }

    #[Computed]
    public function personalDeposit(): float
    {
        return app(TeamVolumeService::class)->getPersonalDepositVolume($this->user);
    }

    #[Computed]
    public function directReferrals(): int
    {
        return app(TeamVolumeService::class)->getDirectReferralCount($this->user);
    }

    #[Computed]
    public function progress(): array
    {
        $next = $this->nextRank;

        if (!$next) {
            return ['maxed' => true];
        }

        $tv      = (float) $this->teamVolume->weighted_total;
        $pd      = $this->personalDeposit;
        $dr      = $this->directReferrals;

        $tvReq = $next->teamVolumeRequired();
        $pdReq = $next->personalDepositRequired();
        $drReq = $next->directReferralsRequired();

        return [
            'maxed'  => false,
            'tv'     => $tv,
            'tv_req' => $tvReq,
            'tv_pct' => $tvReq > 0 ? min(100, round(($tv / $tvReq) * 100, 1)) : 100,
            'pd'     => $pd,
            'pd_req' => $pdReq,
            'pd_pct' => $pdReq > 0 ? min(100, round(($pd / $pdReq) * 100, 1)) : 100,
            'dr'     => $dr,
            'dr_req' => $drReq,
            'dr_pct' => $drReq > 0 ? min(100, round(($dr / $drReq) * 100, 1)) : 100,
            'qualified' => $tv >= $tvReq && $pd >= $pdReq && $dr >= $drReq,
        ];
    }

    #[Computed]
    public function allRanks(): array
    {
        return collect(RankLevel::cases())
            ->filter(fn($r) => $r !== RankLevel::NONE)
            ->map(fn($r) => [
                'rank'      => $r,
                'achieved'  => $r->order() <= $this->currentRank->order(),
                'current'   => $r === $this->currentRank,
            ])
            ->values()
            ->toArray();
    }

    #[Computed]
    public function rankHistory()
    {
        return RankAdvancement::where('user_id', $this->user->id)
            ->latest('achieved_at')
            ->get();
    }

    #[Computed]
    public function leadershipMatches()
    {
        return LeadershipMatchBonus::where('earner_id', $this->user->id)
            ->with('sourceUser:id,name', 'rankAdvancement:id,to_rank')
            ->latest()
            ->take(8)
            ->get();
    }

    #[Computed]
    public function totalRankBonuses(): float
    {
        return (float) RankAdvancement::where('user_id', $this->user->id)->sum('bonus_amount');
    }

    #[Computed]
    public function totalLeadershipMatches(): float
    {
        return (float) LeadershipMatchBonus::where('earner_id', $this->user->id)->sum('amount');
    }

    public function render()
    {
        return view('livewire.protected.rank-rewards');
    }
}
