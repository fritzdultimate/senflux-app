<?php

namespace App\Livewire\Protected\Deposit;

use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Deposits are now a standalone "fund your wallet" action — no Plan
 * selection, no subscription gate. The only gate is "no unsettled
 * deposit in flight." Earning happens entirely through Packs now
 * (PackPurchase / PackSlot), bought separately with the funded balance.
 */
#[Layout('components.layouts.protected')]
class CreateDeposit extends Component
{
    use WithPagination;

    public float  $amountUsd      = 0;
    public string $cryptoCurrency = 'sol';

    public bool   $showModal    = false;
    public ?int   $depositId    = null;
    public string $errorMessage = '';

    public function selectCurrency(string $code): void
    {
        $this->cryptoCurrency = $code;
    }

    public function getMinDepositProperty(): float
    {
        return (float) config('senflux.deposit.min_amount', 50);
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
    public function history()
    {
        return Auth::user()
            ->deposits()
            ->latest()
            ->paginate(8, pageName: 'dep-page');
    }

    #[Poll(10000)]
    public function refreshIfPending(): void
    {
        if (!$this->pendingDeposit) return;
        unset($this->pendingDeposit, $this->history);
    }

    public function submit(DepositService $depositService): void
    {
        $this->validate([
            'amountUsd'      => "required|numeric|min:{$this->minDeposit}",
            'cryptoCurrency' => 'required|string',
        ]);

        $this->errorMessage = '';

        try {
            $deposit = $depositService->createInvoice(
                user:           Auth::user(),
                amountUsd:      $this->amountUsd,
                cryptoCurrency: $this->cryptoCurrency,
            );

            $this->depositId = $deposit->id;
            $this->showModal  = true;

            unset($this->pendingDeposit, $this->history);

        } catch (\RuntimeException $e) {
            $this->errorMessage = match ($e->getMessage()) {
                'PENDING_EXISTS' => 'You already have a pending deposit. Resolve or cancel it before starting a new one.',
                'BELOW_MINIMUM'  => "Minimum deposit is \${$this->minDeposit}.",
                default          => 'Could not create payment invoice. Please try again.',
            };
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
                default => 'Could not cancel this invoice.'
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
        $deposit = $this->depositId ? Deposit::find($this->depositId) : null;

        return view('livewire.protected.deposit.create-deposit', [
            'deposit' => $deposit,
        ]);
    }
}