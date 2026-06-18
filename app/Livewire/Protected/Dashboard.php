<?php

namespace App\Livewire\Protected;

use App\Enums\DepositStatus;
use App\Enums\RankLevel;
use App\Enums\TransactionType;
use App\Models\Deposit;
use App\Models\MarketFormationStateModel;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class Dashboard extends Component {
    #[Computed]
    public function user() {
        return Auth::user()->load(['wallets', 'subscriptions']);
    }

    #[Computed]
    public function mainBalance(): float {
        return (float) $this->user->wallets()->where('type', 'main')->value('balance') ?? 0;
    }

    #[Computed]
    public function referralBalance(): float {
        return (float) $this->user->wallets()->where('type', 'referral')->value('balance') ?? 0;
    }

    #[Computed]
    public function rankBalance(): float {
        return (float) $this->user->wallets()->where('type', 'rank')->value('balance') ?? 0;
    }

    #[Computed]
    public function totalBalance(): float {
        return $this->mainBalance + $this->referralBalance + $this->rankBalance;
    }


    #[Computed]
    public function activeDeposits() {
        return $this->user->deposits()
            ->where('status', DepositStatus::ACTIVE->value)
            ->with('planConfig')
            ->latest('activated_at')
            ->get();
    }

    #[Computed]
    public function totalDeposited(): float {
        return (float) $this->user->deposits()
            ->whereIn('status', [DepositStatus::ACTIVE->value, DepositStatus::FINISHED->value])
            ->sum('actually_paid_usd');
    }

    #[Computed]
    public function totalEarned(): float {
        return (float) $this->user->deposits()
            ->whereIn('status', [DepositStatus::ACTIVE->value, DepositStatus::FINISHED->value])
            ->sum('total_earnings');
    }

    #[Computed]
    public function todayEarnings(): float {
        return (float) DB::table('deposit_earnings')
            ->where('user_id', $this->user->id)
            ->where('earned_date', now()->toDateString())
            ->sum('amount');
    }

    #[Computed]
    public function pendingDeposit(): ?Deposit {
        return $this->user->deposits()
            ->whereIn('status', [
                DepositStatus::PENDING->value,
                DepositStatus::WAITING->value,
                DepositStatus::CONFIRMING->value,
            ])
            ->with('planConfig')
            ->latest()
            ->first();
    }


    #[Computed]
    public function directReferralsCount(): int {
        return DB::table('referrals')
            ->where('referrer_id', $this->user->id)
            ->count();
    }

    #[Computed]
    public function totalNetworkVolume(): float {
        return (float) $this->user->teamVolume?->weighted_total ?? 0;
    }

    // ── Market Formation ─────────────────────────────────────────────────────

    #[Computed]
    public function formationState(): ?object {
        return DB::table('market_formation_states')
            ->where('is_current', true)
            ->first();
    }


    #[Computed]
    public function recentActivity() {
        $userId = $this->user->id;

        return WalletTransaction::where('user_id', $userId)
            ->whereIn('type', [
                TransactionType::DAILY_EARNING->value,
                TransactionType::REFERRAL_BONUS->value,
                TransactionType::RANK_BONUS->value,
                TransactionType::DEPOSIT->value,
                TransactionType::WITHDRAWAL->value,
            ])
            ->with('wallet')
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function activeSubscription() {
        return $this->user->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('planConfig')
            ->latest()
            ->first();
    }

    #[Computed]
    public function earningsChart(): array {
        $days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $days->push(now()->subDays($i)->toDateString());
        }

        $earnings = DB::table('deposit_earnings')
            ->where('user_id', $this->user->id)
            ->whereBetween('earned_date', [now()->subDays(6)->toDateString(), now()->toDateString()])
            ->selectRaw('earned_date, SUM(amount) as total')
            ->groupBy('earned_date')
            ->pluck('total', 'earned_date');

        return $days->map(fn($date) => [
            'date'  => $date,
            'day'   => now()->parse($date)->format('D'),
            'total' => (float) ($earnings[$date] ?? 0),
        ])->values()->toArray();
    }

    #[Computed]
    public function rankProgress(): array {
        $user = $this->user;
        $rank = $user->rank;
        $rankLevel = $rank instanceof RankLevel ? $rank : RankLevel::from($rank ?? 'none');
        $nextRank = $rankLevel->next();

        if (!$nextRank) {
            return ['label' => 'Max Rank', 'pct' => 100, 'next' => null];
        }

        $tv  = (float) ($user->teamVolume?->weighted_total ?? 0);
        $req = $nextRank->teamVolumeRequired();

        return [
            'label' => $rankLevel->label(),
            'next' => $nextRank->label(),
            'pct' => $req > 0 ? min(100, round(($tv / $req) * 100)) : 0,
            'tv' => $tv,
            'tv_req' => $req,
            'color' => $rankLevel->color(),
        ];
    }

    #[Poll(30000)]
    public function refresh(): void {
        // Unset cached computed properties to refresh them
        unset($this->mainBalance, $this->todayEarnings, $this->activeDeposits,
              $this->pendingDeposit, $this->formationState, $this->recentActivity);
    }

    public function render() {
        return view('livewire.protected.dashboard');
    }
}
