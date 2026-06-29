<?php

namespace App\Jobs;

use App\Models\RankAdvancement;
use App\Models\User;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Services\WalletService;
use App\Models\LeadershipMatchBonus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessLeadershipMatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public RankAdvancement $advancement) {}

    public function handle(WalletService $wallet): void {
        $advancedUser = $this->advancement->user;
        $sponsor      = $advancedUser->referrer; 

        if (!$sponsor) return;

        $rate   = 0.15; // Leadership match rate (also stored in rank_requirements)
        $bonus  = round((float) $this->advancement->bonus_amount * $rate, 2);

        if ($bonus <= 0) return;

        DB::transaction(function () use ($sponsor, $advancedUser, $rate, $bonus, $wallet) {
            $tx = $wallet->credit(
                user:          $sponsor,
                walletType:    WalletType::RANK,
                amount:        $bonus,
                type:          TransactionType::LEADERSHIP_MATCH,
                description:   "Leadership match — {$advancedUser->name} achieved {$this->advancement->to_rank}",
                referenceId:   $this->advancement->id,
                referenceType: RankAdvancement::class,
                meta:          ['from_user' => $advancedUser->id, 'rate' => $rate],
            );

            LeadershipMatchBonus::create([
                'earner_id'             => $sponsor->id,
                'source_user_id'        => $advancedUser->id,
                'rank_advancement_id'   => $this->advancement->id,
                'rate'                  => $rate,
                'amount'                => $bonus,
                'wallet_transaction_id' => $tx->id,
                'processed_at'          => now(),
            ]);
        });
    }
}
