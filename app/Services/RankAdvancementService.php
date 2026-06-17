<?php

namespace App\Services;

use App\Enums\RankLevel;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\RankAdvancement;
use App\Models\RankRequirement;
use App\Models\User;
use App\Jobs\ProcessLeadershipMatch;
use Illuminate\Support\Facades\DB;

class RankAdvancementService {
    public function __construct(
        private WalletService $wallet,
        private TeamVolumeService $teamVolume,
    ) {}

    /**
     * Check if user qualifies for next rank and advance if so.
     * Can advance multiple ranks in one check.
     */
    public function checkAndAdvance(User $user): bool
    {
        $advanced = false;
        $currentRank = RankLevel::from($user->rank);

        while ($nextRank = $currentRank->next()) {
            if (!$this->qualifiesFor($user, $nextRank)) break;

            $this->advance($user, $currentRank, $nextRank);
            $currentRank = $nextRank;
            $advanced = true;

            $user->refresh();
        }

        return $advanced;
    }

    public function qualifiesFor(User $user, RankLevel $rank): bool
    {
        $req = RankRequirement::where('rank', $rank->value)->where('is_active', true)->first();
        if (!$req) return false;

        $tv       = $this->teamVolume->computeForUser($user);
        $personal = $this->teamVolume->getPersonalDepositVolume($user);
        $directs  = $this->teamVolume->getDirectReferralCount($user);

        return $tv->weighted_total >= $req->team_volume_usd
            && $personal >= $req->personal_deposit_usd
            && $directs  >= $req->direct_referrals;
    }

    private function advance(User $user, RankLevel $from, RankLevel $to): RankAdvancement
    {
        return DB::transaction(function () use ($user, $from, $to) {
            $req    = RankRequirement::where('rank', $to->value)->firstOrFail();
            $bonus  = (float) $req->cash_bonus;

            $tx = $this->wallet->credit(
                user:          $user,
                walletType:    WalletType::RANK,
                amount:        $bonus,
                type:          TransactionType::RANK_BONUS,
                description:   "Rank advancement bonus — {$to->label()}",
                referenceType: RankAdvancement::class,
                meta:          ['rank' => $to->value, 'from' => $from->value],
            );

            $advancement = RankAdvancement::create([
                'user_id'               => $user->id,
                'from_rank'             => $from->value,
                'to_rank'               => $to->value,
                'bonus_amount'          => $bonus,
                'wallet_transaction_id' => $tx->id,
                'achieved_at'           => now(),
            ]);

            $user->update([
                'rank'              => $to->value,
                'rank_achieved_at'  => now(),
            ]);

            // Update wallet_transaction reference
            $tx->update([
                'reference_id' => $advancement->id,
            ]);

            // Dispatch leadership match bonus for sponsor
            ProcessLeadershipMatch::dispatch($advancement);

            return $advancement;
        });
    }
}