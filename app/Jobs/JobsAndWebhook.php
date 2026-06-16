<?php
// ─── Jobs ────────────────────────────────────────────────────────────────────

namespace App\Jobs;

use App\Services\EarningsEngineService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDailyEarnings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 300;

    public function handle(EarningsEngineService $engine): void
    {
        $engine->processAllActiveDeposits();
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Jobs;

use App\Models\Deposit;
use App\Services\ReferralBonusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessReferralBonus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Deposit $deposit) {}

    public function handle(ReferralBonusService $service): void
    {
        $this->service->processForDeposit($this->deposit);
    }
}

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Jobs;

use App\Models\User;
use App\Services\RankAdvancementService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckRankAdvancement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 600;

    public function handle(RankAdvancementService $service): void
    {
        // Process all active users in chunks
        User::where('is_active', true)
            ->chunkById(50, function ($users) use ($service) {
                foreach ($users as $user) {
                    $service->checkAndAdvance($user);
                }
            });
    }
}

// ─────────────────────────────────────────────────────────────────────────────

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

    public function handle(WalletService $wallet): void
    {
        $advancedUser = $this->advancement->user;
        $sponsor      = $advancedUser->referredBy;

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

// ─────────────────────────────────────────────────────────────────────────────

namespace App\Jobs;

use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncNowPaymentsStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function handle(DepositService $service): void
    {
        // Sync all pending/confirming deposits
        Deposit::whereIn('status', ['waiting', 'confirming'])
            ->whereNotNull('nowpayments_id')
            ->chunkById(50, function ($deposits) use ($service) {
                foreach ($deposits as $deposit) {
                    try {
                        $service->syncStatus($deposit);
                    } catch (\Exception $e) {
                        \Log::error("Failed to sync deposit #{$deposit->id}: " . $e->getMessage());
                    }
                }
            });
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// Webhook Controller
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Http\Controllers\Webhook;

use App\Services\DepositService;
use App\Services\NowPaymentsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class NowPaymentsWebhookController extends Controller
{
    public function __construct(
        private NowPaymentsService $nowPayments,
        private DepositService $depositService,
    ) {}

    public function handle(Request $request): Response
    {
        $rawBody   = $request->getContent();
        $signature = $request->header('x-nowpayments-sig', '');

        // Always verify signature in production
        if (!app()->isLocal() && !$this->nowPayments->verifyIpnSignature($rawBody, $signature)) {
            Log::warning('NowPayments IPN signature verification failed', [
                'ip'        => $request->ip(),
                'signature' => $signature,
            ]);
            return response('Unauthorized', 401);
        }

        $payload = $request->json()->all();

        Log::info('NowPayments IPN received', [
            'payment_id'     => $payload['payment_id'] ?? null,
            'payment_status' => $payload['payment_status'] ?? null,
        ]);

        try {
            $this->depositService->handleIpnUpdate($payload);
        } catch (\Exception $e) {
            Log::error('NowPayments IPN processing error', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
            // Return 200 anyway to prevent NP retry storm
        }

        return response('OK', 200);
    }
}
