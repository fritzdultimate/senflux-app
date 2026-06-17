<?php

namespace App\Livewire\Protected\Subscription;

use App\Models\PlanConfig;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
class Subscribe extends Component
{
    use WithPagination;

    public ?int   $planId   = null;
    public string $interval = 'monthly';

    public bool    $showModal        = false;
    public string  $errorMessage     = '';
    public ?string $payAddress       = null;
    public ?string $cryptoAmount     = null;
    public ?string $cryptoCurrency   = null;
    public float   $invoiceAmountUsd = 0;
    public ?string $expiresAt        = null;
    public ?int    $invoiceId        = null;

    public function selectPlan(int $planId): void
    {
        $this->planId = $planId;
        $this->errorMessage = '';
    }

    public function selectInterval(string $interval): void
    {
        if (in_array($interval, ['monthly', 'quarterly', 'yearly'])) {
            $this->interval = $interval;
        }
    }

    #[Computed]
    public function currentSubscription(): ?Subscription {
        return Auth::user()
            ->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->with('planConfig')
            ->latest()
            ->first();
    }

    /**
     * Any subscription currently pending payment — drives the gate UI
     * that blocks creating a duplicate.
     */
    #[Computed]
    public function pendingSubscription(): ?Subscription
    {
        return Auth::user()
            ->subscriptions()
            ->whereIn('status', ['pending', 'waiting'])
            ->with('planConfig')
            ->latest()
            ->first();
    }

    #[Computed]
    public function canCancelPending(): bool
    {
        $pending = $this->pendingSubscription;
        if (!$pending) return false;

        return $pending->created_at->diffInHours(now()) <= SubscriptionService::CANCEL_GRACE_HOURS;
    }

    public function getSelectedPlanPriceProperty(): float
    {
        if (!$this->planId) return 0;
        $plan = PlanConfig::find($this->planId);
        return $plan ? $plan->getPriceForInterval($this->interval) : 0;
    }

    /**
     * History list — paginated, refreshed on every poll while a pending record exists.
     */
    #[Computed]
    public function history()
    {
        return Auth::user()
            ->subscriptions()
            ->with('planConfig')
            ->latest()
            ->paginate(6, pageName: 'sub-page');
    }

    /**
     * Polls only while there's something pending to watch — avoids
     * needless requests once everything has settled.
     */
    #[Poll(10000)]
    public function refreshIfPending(): void
    {
        if (!$this->pendingSubscription) return;
        unset($this->pendingSubscription, $this->history, $this->currentSubscription);
    }

    public function subscribe(SubscriptionService $service): void
    {
        $this->validate([
            'planId'   => 'required|exists:plan_configs,id',
            'interval' => 'required|in:monthly,quarterly,yearly',
        ]);

        $this->errorMessage = '';

        try {
            $result = $service->createInvoice(
                user:     Auth::user(),
                plan:     PlanConfig::findOrFail($this->planId),
                interval: $this->interval,
            );

            $this->payAddress       = $result['pay_address'];
            $this->cryptoAmount     = $result['crypto_amount'];
            $this->cryptoCurrency   = $result['crypto_currency'];
            $this->invoiceAmountUsd = $result['amount_usd'];
            $this->expiresAt        = $result['expires_at'];
            $this->invoiceId        = $result['subscription_id'];
            $this->showModal        = true;

            unset($this->pendingSubscription, $this->history);

        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage() === 'PENDING_EXISTS'
                ? 'You already have a pending subscription payment. Resolve or cancel it before starting a new one.'
                : 'Could not create invoice. Please try again.';
        } catch (\Exception $e) {
            $this->errorMessage = 'Could not create invoice. Please try again.';
            \Log::error('Subscription failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
        }
    }

    public function cancelPending(SubscriptionService $service): void
    {
        $pending = $this->pendingSubscription;
        if (!$pending) return;

        try {
            $service->cancelPending($pending, Auth::user());
            unset($this->pendingSubscription, $this->history);
        } catch (\RuntimeException $e) {
            $this->errorMessage = match($e->getMessage()) {
                'GRACE_PERIOD_EXPIRED' => 'This invoice can no longer be cancelled — the grace period has passed.',
                default                => 'Could not cancel this invoice.',
            };
        }
    }

    public function resumeTracking(): void
    {
        $pending = $this->pendingSubscription;
        if ($pending) {
            $this->redirect(route('dashboard.subscription.track', $pending), navigate: true);
        }
    }

    public function goToTracker(): void
    {
        if ($this->invoiceId) {
            $this->redirect(route('dashboard.subscription.track', $this->invoiceId), navigate: true);
        }
    }

    public function render()
    {
        $plans = PlanConfig::active()->get()->map(fn($p) => [
            'id'              => $p->id,
            'label'           => $p->label,
            'daily_rate'      => (float) $p->daily_rate_max,
            'rate_pct'        => number_format($p->daily_rate_max * 100, 1),
            'monthly_price'   => (float) $p->monthly_price,
            'quarterly_price' => (float) $p->quarterly_price,
            'yearly_price'    => (float) $p->yearly_price,
            'min_deposit'     => (float) $p->min_deposit,
            'max_deposit'     => (float) $p->max_deposit,
            'is_popular'      => (bool) $p->is_popular,
        ])->values()->toArray();

        return view('livewire.protected.subscription.subscribe', [
            'plans'             => $plans,
            'selectedPlanPrice' => $this->selectedPlanPrice,
        ]);
    }
}
