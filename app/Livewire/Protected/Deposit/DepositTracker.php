<?php

namespace App\Livewire\Protected\Deposit;

use App\Enums\DepositStatus;
use App\Models\Deposit;
use App\Services\DepositService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Poll;
use Livewire\Component;

class DepositTracker extends Component {
    #[Locked]
    public int $depositId;

    public bool $confirmed = true;
    public bool $failed = false;
    public bool $expired = false;
    public bool $stopPolling = false;
    public int  $pollCount = 0;

    public function mount(Deposit $deposit): void
    {
        // Ensure deposit belongs to auth user
        abort_if($deposit->user_id !== Auth::id(), 403);

        $this->depositId = $deposit->id;
        $this->syncLocalState($deposit);
    }

    #[Computed]
    public function deposit(): Deposit
    {
        return Deposit::find($this->depositId);
    }

    /**
     * 3 steps now, not 4 — a deposit's job ends at "confirmed, wallet
     * credited." There's no deposit-level "active deployment" stage
     * anymore; deployment happens at the Pack/Slot level, after the
     * user separately buys a pack.
     */
    #[Computed]
    public function statusSteps(): array {
        $status = DepositStatus::from($this->deposit->status->value);

        $steps = [
            ['key' => 'waiting',    'label' => 'Awaiting Payment',    'icon' => 'clock'],
            ['key' => 'confirming', 'label' => 'Confirming On-Chain', 'icon' => 'shield-check'],
            ['key' => 'confirmed',  'label' => 'Wallet Credited',     'icon' => 'check-circle'],
        ];

        $order = ['pending' => 0, 'waiting' => 0, 'confirming' => 1, 'confirmed' => 2, 'active' => 2];
        $current = $order[$status->value] ?? 0;

        foreach ($steps as $i => &$step) {
            $step['completed'] = $i < $current;
            $step['active']    = $i === $current;
            $step['pending']   = $i > $current;
        }

        return $steps;
    }

    #[Computed]
    public function confirmationProgress(): int
    {
        $deposit = $this->deposit;
        if (!$deposit->required_confirmations || $deposit->required_confirmations === 0) return 0;
        return min(100, (int) round(($deposit->confirmations / $deposit->required_confirmations) * 100));
    }

    // Poll every 8 seconds — stops when terminal state reached
    #[Poll(8000)]
    public function poll(DepositService $service): void {
        if ($this->stopPolling) return;

        $this->pollCount++;

        // Stop polling after 30 minutes (225 polls × 8s)
        if ($this->pollCount > 225) {
            $this->stopPolling = true;
            return;
        }

        $deposit = $service->syncStatus($this->deposit);
        $this->syncLocalState($deposit);
    }

    private function syncLocalState(Deposit $deposit): void {
        $status = DepositStatus::from($deposit->status->value);

        $this->confirmed = in_array($status, [DepositStatus::CONFIRMED, DepositStatus::ACTIVE]);
        $this->failed = $status === DepositStatus::FAILED;
        $this->expired = $status === DepositStatus::EXPIRED;

        if ($this->confirmed || $this->failed || $this->expired) {
            $this->stopPolling = true;
        }
    }

    public function goToDashboard(): void {
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.protected.deposit.deposit-tracker')
            ->layout('layouts.protected', ['title' => 'Deposit Tracker']);
    }
}