<?php

namespace App\Livewire\Protected\Subscription;

use App\Models\Subscription;
use App\Services\SubscriptionService;
use App\Services\NowPaymentsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Poll;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class SubscriptionTracker extends Component
{
    #[Locked]
    public int $subscriptionId;

    public bool $confirmed   = false;
    public bool $failed      = false;
    public bool $stopPolling = false;
    public int  $pollCount   = 0;

    public function mount(Subscription $subscription): void
    {
        abort_if($subscription->user_id !== Auth::id(), 403);

        $this->subscriptionId = $subscription->id;
        $this->syncLocalState($subscription);
    }

    #[Computed]
    public function subscription(): Subscription
    {
        return Subscription::with('planConfig')->find($this->subscriptionId);
    }

    #[Poll(8000)]
    public function poll(NowPaymentsService $nowPayments, SubscriptionService $service): void
    {
        if ($this->stopPolling) return;

        $this->pollCount++;
        if ($this->pollCount > 225) { // ~30 min cap
            $this->stopPolling = true;
            return;
        }

        $subscription = $this->subscription;
        if (!$subscription->nowpayments_id) return;

        try {
            $npData = $nowPayments->getPaymentStatus($subscription->nowpayments_id);
            $service->handleIpnUpdate($npData);
        } catch (\Exception $e) {
            \Log::error('Subscription poll failed', ['error' => $e->getMessage()]);
        }

        $this->syncLocalState($subscription->fresh());
    }

    private function syncLocalState(Subscription $subscription): void
    {
        $this->confirmed = $subscription->status === 'active';
        $this->failed    = $subscription->status === 'cancelled';

        if ($this->confirmed || $this->failed) {
            $this->stopPolling = true;
        }
    }

    public function goToDashboard(): void
    {
        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.protected.subscription.subscription-tracker');
    }
}