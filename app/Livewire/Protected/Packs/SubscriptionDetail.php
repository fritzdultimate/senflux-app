<?php

namespace App\Livewire\Protected\Packs;

use App\Models\PackSlot;
use App\Models\PackSubscription;
use App\Models\PackTier;
use App\Services\PackLifecycleService;
use App\Services\PackPurchaseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class SubscriptionDetail extends Component
{
    #[Locked]
    public int $subscriptionId;

    public ?int $fundingSlotId = null;
    public float $fundAmount = 0;

    public ?int $upgradingToTierId = null;
    public bool $upgradeCompound = false;

    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(PackSubscription $subscription): void {
        abort_if($subscription->user_id !== Auth::id(), 403);
        $this->subscriptionId = $subscription->id;
    }

    public function getSubscriptionProperty(): PackSubscription {
        return PackSubscription::with(['packTier', 'slots'])->find($this->subscriptionId);
    }

    public function getUpgradeOptionsProperty()
    {
        return PackTier::active()->where('price', '>', $this->subscription->packTier->price)->get();
    }

    public function startFunding(int $slotId): void
    {
        $this->fundingSlotId = $slotId;
        $this->fundAmount = (float) $this->subscription->packTier->min_capital_per_slot;
        $this->errorMessage = '';
    }

    public function cancelFunding(): void
    {
        $this->fundingSlotId = null;
    }

    public function fundSlot(PackPurchaseService $service): void {
        $slot = PackSlot::find($this->fundingSlotId);
        if (!$slot) return;

        try {
            $service->fundSlot($slot, (float) $this->fundAmount);
            $this->fundingSlotId = null;
            $this->successMessage = "Slot #{$slot->slot_number} funded.";
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function earlyExit(int $slotId, PackLifecycleService $service): void
    {
        $slot = PackSlot::find($slotId);
        if (!$slot) return;

        try {
            $service->earlyExit($slot);
            $this->successMessage = "Slot #{$slot->slot_number} exited — capital returned, 8% fee applied.";
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function requestRefund(PackPurchaseService $service): void
    {
        try {
            $service->refund($this->subscription);
            $this->successMessage = 'Refund processed — funds returned to your wallet.';
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function withdraw(PackLifecycleService $service): void
    {
        try {
            $service->withdraw($this->subscription);
            $this->successMessage = 'Pack closed — capital returned to your wallet.';
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function continueCycle(PackLifecycleService $service): void {
        try {
            $new = $service->continueCycle($this->subscription);
            $this->redirect(route('dashboard.packs.show', $new), navigate: true);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function autoCompound(PackLifecycleService $service): void
    {
        try {
            $new = $service->autoCompound($this->subscription);
            $this->redirect(route('dashboard.packs.show', $new), navigate: true);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function startUpgrade(int $tierId): void
    {
        $this->upgradingToTierId = $tierId;
        $this->errorMessage = '';
    }

    public function cancelUpgrade(): void
    {
        $this->upgradingToTierId = null;
        $this->errorMessage = '';
    }

    public function confirmUpgrade(PackLifecycleService $service): void
    {
        $newTier = PackTier::find($this->upgradingToTierId);
        if (!$newTier) return;

        try {
            $new = $service->upgrade($this->subscription, $newTier, $this->upgradeCompound);
            $this->redirect(route('dashboard.packs.show', $new), navigate: true);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.protected.packs.subscription-detail');
    }
}