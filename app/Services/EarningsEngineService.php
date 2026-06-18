<?php

namespace App\Services;

use App\Enums\MarketFormationState;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\Deposit;
use App\Models\DepositEarning;
use App\Models\MarketFormationStateModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EarningsEngineService
{
    public function __construct(private WalletService $wallet) {}

    /**
     * Process daily earnings for all active deposits.
     * Called by scheduler — idempotent via unique(deposit_id, earned_date).
     */
    public function processAllActiveDeposits(): void {
        $today = now()->toDateString();
        $formation = $this->getCurrentFormationState();

        Deposit::where('status', 'active')
            ->whereNotNull('daily_rate')
            ->whereNotNull('activated_at')
            ->chunkById(100, function ($deposits) use ($today, $formation) {
                foreach ($deposits as $deposit) {
                    $this->processDepositEarning($deposit, $today, $formation);
                }
            });
    }

    public function processDepositEarning(Deposit $deposit, string $date, ?object $formation = null): ?DepositEarning {
        // Already processed today?
        if (DepositEarning::where('deposit_id', $deposit->id)->where('earned_date', $date)->exists()) {
            return null;
        }

        $formation ??= $this->getCurrentFormationState();
        $multiplier = $formation ? (float) $formation->earnings_multiplier : 1.0;
        $state = $formation?->state ?? 'active';

        $baseRate = (float) $deposit->daily_rate;
        $principal = (float) $deposit->actually_paid_usd;
        $earning = round($principal * $baseRate * $multiplier, 8);

        return DB::transaction(function () use ($deposit, $date, $earning, $baseRate, $multiplier, $state) {
            $user = $deposit->user;

            $tx = $this->wallet->credit(
                user: $user,
                walletType: WalletType::MAIN,
                amount: $earning,
                type: TransactionType::DAILY_EARNING,
                description: "Daily earning — {$date}",
                referenceId: $deposit->id,
                referenceType: Deposit::class,
                meta: ['date' => $date, 'rate' => $baseRate, 'multiplier' => $multiplier],
            );

            $record = DepositEarning::create([
                'deposit_id' => $deposit->id,
                'user_id' => $user->id,
                'wallet_transaction_id' => $tx->id,
                'amount' => $earning,
                'rate_applied' => $baseRate,
                'formation_state' => $state,
                'formation_multiplier' => $multiplier,
                'earned_date' => $date,
                'processed_at' => now(),
            ]);

            $deposit->increment('total_earnings', $earning);
            $deposit->update(['last_earnings_at' => now()]);

            return $record;
        });
    }

    private function getCurrentFormationState(): ?object {
        return DB::table('market_formation_states')
            ->where('is_current', true)
            ->first();
    }
}