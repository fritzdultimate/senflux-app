<?php

namespace App\Services;

use App\Enums\DepositStatus;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\Deposit;
use App\Models\PlanConfig;
use App\Models\User;
use App\Jobs\ProcessReferralBonus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositService
{
    public function __construct(
        private NowPaymentsService $nowPayments,
        private WalletService $wallet,
    ) {}

    /**
     * Create a new deposit and NowPayments invoice.
     */
    public function createInvoice(
        User $user,
        PlanConfig $plan,
        float $amountUsd,
        string $cryptoCurrency = 'sol',
    ): Deposit {
        return DB::transaction(function () use ($user, $plan, $amountUsd, $cryptoCurrency) {
            // Create deposit record first
            $deposit = Deposit::create([
                'user_id'          => $user->id,
                'plan_config_id'   => $plan->id,
                'amount_usd'       => $amountUsd,
                'crypto_currency'  => $cryptoCurrency,
                'status'           => DepositStatus::PENDING->value,
            ]);

            // Create NowPayments invoice
            $npResponse = $this->nowPayments->createPayment(
                priceAmount:       $amountUsd,
                priceCurrency:     'usd',
                payCurrency:       $cryptoCurrency,
                orderId:           "SENFLUX-{$deposit->id}",
                orderDescription:  "Senflux {$plan->label} Plan Deposit",
                successUrl:        route('dashboard.deposit.success', $deposit),
                cancelUrl:         route('dashboard.deposit.cancelled', $deposit),
            );

            $deposit->update([
                'nowpayments_id'       => $npResponse['payment_id'],
                'nowpayments_order_id' => $npResponse['order_id'] ?? null,
                'pay_address'          => $npResponse['pay_address'] ?? null,
                'crypto_amount'        => $npResponse['pay_amount'] ?? null,
                'network'              => $npResponse['network'] ?? null,
                'payment_url'          => $npResponse['invoice_url'] ?? null,
                'expires_at'           => isset($npResponse['expiration_estimate_date'])
                                          ? now()->parse($npResponse['expiration_estimate_date'])
                                          : now()->addHours(24),
                'status'               => DepositStatus::WAITING->value,
            ]);

            return $deposit->fresh();
        });
    }

    /**
     * Handle NowPayments IPN update. Idempotent.
     */
    public function handleIpnUpdate(array $ipnData): void
    {
        $paymentId = $ipnData['payment_id'] ?? null;
        if (!$paymentId) return;

        $deposit = Deposit::where('nowpayments_id', $paymentId)->first();
        if (!$deposit) {
            Log::warning("NowPayments IPN: deposit not found for payment_id {$paymentId}");
            return;
        }

        // Skip if already terminal
        if (DepositStatus::from($deposit->status)->isTerminal()) return;

        $newStatus = DepositStatus::fromNowPayments($ipnData['payment_status'] ?? '');

        $deposit->update([
            'status'               => $newStatus->value,
            'actually_paid'        => $ipnData['actually_paid'] ?? null,
            'actually_paid_usd'    => $ipnData['actually_paid_fiat'] ?? null,
            'confirmations'        => $ipnData['confirmations_count'] ?? 0,
            'required_confirmations' => $ipnData['required_confirmations'] ?? 0,
            'ipn_received_at'      => now(),
        ]);

        // Fully confirmed — activate
        if ($newStatus === DepositStatus::CONFIRMED) {
            $this->activate($deposit);
        }
    }

    /**
     * Activate a confirmed deposit — start earning, credit referral bonuses.
     */
    public function activate(Deposit $deposit): void
    {
        if ($deposit->status === DepositStatus::ACTIVE->value) return;

        DB::transaction(function () use ($deposit) {
            $plan = $deposit->planConfig;

            $deposit->update([
                'status'       => DepositStatus::ACTIVE->value,
                'daily_rate'   => $plan->daily_rate_max,
                'activated_at' => now(),
            ]);

            // Update user subscription if not already active
            $user = $deposit->user;
            if (!$user->subscription_plan || $user->subscription_expires_at?->isPast()) {
                // Monthly subscription bundled with deposit activation
                // (or handle subscription separately — depends on flow)
            }

            // Dispatch referral bonus job
            ProcessReferralBonus::dispatch($deposit);
        });
    }

    /**
     * Sync deposit status from NowPayments API (for polling).
     */
    public function syncStatus(Deposit $deposit): Deposit
    {
        if (DepositStatus::from($deposit->status)->isTerminal()) {
            return $deposit;
        }

        $npData = $this->nowPayments->getPaymentStatus($deposit->nowpayments_id);
        $this->handleIpnUpdate($npData);

        return $deposit->fresh();
    }
}

// ─────────────────────────────────────────────────────────────────────────────

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
    public function processAllActiveDeposits(): void
    {
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

    public function processDepositEarning(Deposit $deposit, string $date, ?object $formation = null): ?DepositEarning
    {
        // Already processed today?
        if (DepositEarning::where('deposit_id', $deposit->id)->where('earned_date', $date)->exists()) {
            return null;
        }

        $formation ??= $this->getCurrentFormationState();
        $multiplier = $formation ? (float) $formation->earnings_multiplier : 1.0;
        $state      = $formation?->state ?? 'active';

        $baseRate   = (float) $deposit->daily_rate;
        $principal  = (float) $deposit->amount_usd;
        $earning    = round($principal * $baseRate * $multiplier, 8);

        return DB::transaction(function () use ($deposit, $date, $earning, $baseRate, $multiplier, $state) {
            $user = $deposit->user;

            $tx = $this->wallet->credit(
                user:          $user,
                walletType:    WalletType::MAIN,
                amount:        $earning,
                type:          TransactionType::DAILY_EARNING,
                description:   "Daily earning — {$date}",
                referenceId:   $deposit->id,
                referenceType: Deposit::class,
                meta:          ['date' => $date, 'rate' => $baseRate, 'multiplier' => $multiplier],
            );

            $record = DepositEarning::create([
                'deposit_id'           => $deposit->id,
                'user_id'              => $user->id,
                'wallet_transaction_id'=> $tx->id,
                'amount'               => $earning,
                'rate_applied'         => $baseRate,
                'formation_state'      => $state,
                'formation_multiplier' => $multiplier,
                'earned_date'          => $date,
                'processed_at'         => now(),
            ]);

            $deposit->increment('total_earnings', $earning);
            $deposit->update(['last_earnings_at' => now()]);

            return $record;
        });
    }

    private function getCurrentFormationState(): ?object
    {
        return DB::table('market_formation_states')
            ->where('is_current', true)
            ->first();
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Services;

use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\Deposit;
use App\Models\Referral;
use App\Models\ReferralBonus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReferralBonusService
{
    // Referral bonus rates per level (8 levels)
    private const RATES = [
        1 => 0.08,
        2 => 0.04,
        3 => 0.025,
        4 => 0.02,
        5 => 0.015,
        6 => 0.0125,
        7 => 0.01,
        8 => 0.0075,
    ];

    public function __construct(private WalletService $wallet) {}

    /**
     * Walk 8 levels of upline and credit referral bonuses.
     */
    public function processForDeposit(Deposit $deposit): void
    {
        $depositor  = $deposit->user;
        $amount     = (float) $deposit->actually_paid_usd ?? (float) $deposit->amount_usd;
        $currentUser = $depositor;

        for ($level = 1; $level <= 8; $level++) {
            $upline = $this->getDirectUpline($currentUser);
            if (!$upline) break;

            $rate   = self::RATES[$level];
            $bonus  = round($amount * $rate, 8);

            if ($bonus > 0) {
                DB::transaction(function () use ($upline, $depositor, $deposit, $level, $rate, $bonus) {
                    $tx = $this->wallet->credit(
                        user:          $upline,
                        walletType:    WalletType::REFERRAL,
                        amount:        $bonus,
                        type:          TransactionType::REFERRAL_BONUS,
                        description:   "Level {$level} referral bonus from deposit #{$deposit->id}",
                        referenceId:   $deposit->id,
                        referenceType: Deposit::class,
                        meta:          ['level' => $level, 'rate' => $rate, 'from_user' => $depositor->id],
                    );

                    ReferralBonus::create([
                        'earner_id'              => $upline->id,
                        'source_user_id'         => $depositor->id,
                        'deposit_id'             => $deposit->id,
                        'level'                  => $level,
                        'rate'                   => $rate,
                        'amount'                 => $bonus,
                        'wallet_transaction_id'  => $tx->id,
                        'processed_at'           => now(),
                    ]);
                });
            }

            $currentUser = $upline;
        }
    }

    private function getDirectUpline(User $user): ?User
    {
        return $user->referredBy ?? null;
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Services;

use App\Models\Deposit;
use App\Models\TeamVolume;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeamVolumeService
{
    // Team volume distribution weights per level
    private const WEIGHTS = [
        1 => 1.00,
        2 => 0.75,
        3 => 0.50,
        4 => 0.25,
        5 => 0.15,
        6 => 0.10,
        7 => 0.05,
        8 => 0.025,
    ];

    /**
     * Compute and cache team volume for a user.
     */
    public function computeForUser(User $user): TeamVolume
    {
        $levels = [];
        $currentLevel = [$user->id];
        $visited = [$user->id => true];

        for ($level = 1; $level <= 8; $level++) {
            $nextLevel = [];
            $levelVolume = 0;

            foreach ($currentLevel as $uid) {
                $directReferrals = DB::table('referrals')
                    ->where('referrer_id', $uid)
                    ->pluck('referred_id')
                    ->toArray();

                foreach ($directReferrals as $rid) {
                    if (isset($visited[$rid])) continue;
                    $visited[$rid] = true;
                    $nextLevel[] = $rid;

                    // Sum active deposit volumes for this person
                    $vol = Deposit::where('user_id', $rid)
                        ->whereIn('status', ['active', 'finished'])
                        ->sum('actually_paid_usd');

                    $levelVolume += (float) $vol;
                }
            }

            $levels[$level] = $levelVolume;
            $currentLevel = $nextLevel;

            if (empty($currentLevel)) break;
        }

        $rawTotal      = array_sum($levels);
        $weightedTotal = 0;
        foreach ($levels as $l => $vol) {
            $weightedTotal += $vol * (self::WEIGHTS[$l] ?? 0);
        }

        $record = TeamVolume::updateOrCreate(
            ['user_id' => $user->id],
            [
                'level_1'          => $levels[1] ?? 0,
                'level_2'          => $levels[2] ?? 0,
                'level_3'          => $levels[3] ?? 0,
                'level_4'          => $levels[4] ?? 0,
                'level_5'          => $levels[5] ?? 0,
                'level_6'          => $levels[6] ?? 0,
                'level_7'          => $levels[7] ?? 0,
                'level_8'          => $levels[8] ?? 0,
                'raw_total'        => $rawTotal,
                'weighted_total'   => $weightedTotal,
                'last_computed_at' => now(),
            ]
        );

        return $record;
    }

    /**
     * Get direct referral count (level 1 only).
     */
    public function getDirectReferralCount(User $user): int
    {
        return DB::table('referrals')->where('referrer_id', $user->id)->count();
    }

    /**
     * Get total personal deposit volume for a user.
     */
    public function getPersonalDepositVolume(User $user): float
    {
        return (float) Deposit::where('user_id', $user->id)
            ->whereIn('status', ['active', 'finished'])
            ->sum('actually_paid_usd');
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Services;

use App\Enums\RankLevel;
use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\RankAdvancement;
use App\Models\RankRequirement;
use App\Models\User;
use App\Jobs\ProcessLeadershipMatch;
use Illuminate\Support\Facades\DB;

class RankAdvancementService
{
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
