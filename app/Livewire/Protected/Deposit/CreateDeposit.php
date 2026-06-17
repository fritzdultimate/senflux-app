<?php

namespace App\Livewire\Protected\Deposit;

use App\Models\Deposit;
use App\Models\PlanConfig;
use App\Services\DepositService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
class CreateDeposit extends Component
{
    use WithPagination;

    public ?int   $planId         = null;
    public float  $amountUsd      = 0;
    public string $cryptoCurrency = 'sol';

    public bool   $showModal    = false;
    public ?int   $depositId    = null;
    public string $errorMessage = '';

    public function selectPlan(int $planId): void
    {
        $plan = PlanConfig::find($planId);
        if (!$plan) return;

        $this->planId    = $planId;
        $this->amountUsd = (float) $plan->min_deposit;
        $this->errorMessage = '';
    }

    public function selectCurrency(string $code): void
    {
        $this->cryptoCurrency = $code;
    }

    public function getSelectedPlanProperty(): ?PlanConfig
    {
        return $this->planId ? PlanConfig::find($this->planId) : null;
    }

    public function getEstimatedDailyProperty(): float
    {
        $plan = $this->selectedPlan;
        if (!$plan || $this->amountUsd <= 0) return 0;
        return round($this->amountUsd * (float) $plan->daily_rate_max, 2);
    }

    public function getEstimatedMonthlyProperty(): float
    {
        return round($this->estimatedDaily * 30, 2);
    }

    /**
     * Any deposit currently pending/waiting/confirming — drives the gate UI.
     */
    #[Computed]
    public function pendingDeposit(): ?Deposit
    {
        return Auth::user()
            ->deposits()
            ->whereIn('status', ['pending', 'waiting', 'confirming'])
            ->with('planConfig')
            ->latest()
            ->first();
    }

    #[Computed]
    public function canCancelPending(): bool
    {
        $pending = $this->pendingDeposit;
        if (!$pending) return false;

        return $pending->created_at->diffInHours(now()) <= DepositService::CANCEL_GRACE_HOURS;
    }

    #[Computed]
    public function activeDeposits()
    {
        return Auth::user()
            ->deposits()
            ->where('status', 'active')
            ->with('planConfig')
            ->latest('activated_at')
            ->get();
    }

    #[Computed]
    public function history()
    {
        return Auth::user()
            ->deposits()
            ->with('planConfig')
            ->latest()
            ->paginate(8, pageName: 'dep-page');
    }

    #[Poll(10000)]
    public function refreshIfPending(): void
    {
        if (!$this->pendingDeposit) return;
        unset($this->pendingDeposit, $this->history, $this->activeDeposits);
    }

    public function submit(DepositService $depositService): void
    {
        if (!Auth::user()->has_active_subscription) {
            $this->errorMessage = 'An active subscription is required before depositing.';
            return;
        }

        $plan = $this->selectedPlan;
        if (!$plan) {
            $this->errorMessage = 'Select a plan first.';
            return;
        }

        $this->validate([
            'planId'         => 'required|exists:plan_configs,id',
            'amountUsd'      => "required|numeric|min:{$plan->min_deposit}|max:{$plan->max_deposit}",
            'cryptoCurrency' => 'required|string',
        ]);

        $this->errorMessage = '';

        try {
            $deposit = $depositService->createInvoice(
                user:           Auth::user(),
                plan:           $plan,
                amountUsd:      $this->amountUsd,
                cryptoCurrency: $this->cryptoCurrency,
            );

            $this->depositId = $deposit->id;
            $this->showModal  = true;

            unset($this->pendingDeposit, $this->history);

        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage() === 'PENDING_EXISTS'
                ? 'You already have a pending deposit. Resolve or cancel it before starting a new one.'
                : 'Could not create payment invoice. Please try again.';
        } catch (\Exception $e) {
            $this->errorMessage = 'Could not create payment invoice. Please try again.';
            \Log::error('Deposit creation failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
        }
    }

    public function cancelPending(DepositService $service): void
    {
        $pending = $this->pendingDeposit;
        if (!$pending) return;

        try {
            $service->cancelPending($pending, Auth::user());
            unset($this->pendingDeposit, $this->history);
        } catch (\RuntimeException $e) {
            $this->errorMessage = match($e->getMessage()) {
                'GRACE_PERIOD_EXPIRED' => 'This invoice can no longer be cancelled — the grace period has passed.',
                default                => 'Could not cancel this invoice.',
            };
        }
    }

    public function resumeTracking(): void
    {
        $pending = $this->pendingDeposit;
        if ($pending) {
            $this->redirect(route('dashboard.deposit.track', $pending), navigate: true);
        }
    }

    public function goToTracker(): void
    {
        if ($this->depositId) {
            $this->redirect(route('dashboard.deposit.track', $this->depositId), navigate: true);
        }
    }

    public function render()
    {
        $plans   = PlanConfig::active()->get();
        $deposit = $this->depositId ? Deposit::find($this->depositId) : null;

        return view('livewire.protected.deposit.create-deposit', [
            'plans'   => $plans,
            'deposit' => $deposit,
        ]);
    }
}
