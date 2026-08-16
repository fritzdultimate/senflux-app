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
class NewSubscriptionDetail extends Component
{
    #[Locked]
    public int $subscriptionId;

    public float $deployAmount = 0;
    public float $topUpAmount = 0;

    public ?int $upgradingToTierId = null;
    public bool $upgradeCompound = false;

    public string $errorMessage = '';
    public string $successMessage = '';

    public function mount(PackSubscription $subscription): void {
        abort_if($subscription->user_id !== Auth::id(), 403);
        $this->subscriptionId = $subscription->id;
        // Pre-fill the deploy amount so opening the panel (now instant, via
        // Alpine) doesn't need a server round trip to populate a default.
        $this->deployAmount = (float) $subscription->packTier->min_capital_per_slot;
    }

    public function getSubscriptionProperty(): PackSubscription {
        return PackSubscription::with(['packTier', 'slots'])->find($this->subscriptionId);
    }

    /**
     * ASSUMPTION: exactly one slot per subscription under the new model.
     * Null means capital hasn't been deployed yet.
     */
    public function getSlotProperty(): ?PackSlot {
        return $this->subscription->slots->first();
    }

    public function getUpgradeOptionsProperty()
    {
        return PackTier::active()->where('price', '>', $this->subscription->packTier->price)->get();
    }

    public function getMinCapitalProperty(): float
    {
        return (float) $this->subscription->packTier->min_capital_per_slot;
    }

    /**
     * Source of truth for maturity is matures_at, not the `status` enum.
     * status flips to IN_RENEWAL_WINDOW via a scheduled job and can lag the
     * real timestamp by up to a cron cycle — computing it live here means
     * the UI is never stuck showing "active" (and allowing top-ups) past
     * the actual maturity moment just because that job hasn't run yet.
     */
    public function getIsMaturedProperty(): bool
    {
        return now()->gte($this->subscription->matures_at);
    }

    /**
     * Whether any capital-moving action (top-up, early exit, deploy,
     * renewal decisions) should be presented at all. Allow-listing
     * 'active' and 'in_renewal_window' rather than block-listing
     * 'refunded'/'closed'/etc means any future terminal status you add
     * is safe by default — the UI won't offer actions for a status it
     * doesn't recognize.
     */
    public function getIsActionableProperty(): bool
    {
        return in_array($this->subscription->status->value, ['active', 'in_renewal_window'], true);
    }

    public function deploy(PackPurchaseService $service): void
    {
        try {
            $service->deploySlot($this->subscription, (float) $this->deployAmount);
            $this->successMessage = 'Capital deployed — your position is now active.';
            unset($this->subscription, $this->slot);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function topUp(PackPurchaseService $service): void
    {
        if (!$this->slot) return;

        try {
            $service->topUp($this->slot, (float) $this->topUpAmount);
            $this->topUpAmount = 0;
            $this->successMessage = 'Capital added to your position.';
            unset($this->subscription, $this->slot);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function earlyExit(PackLifecycleService $service): void
    {
        if (!$this->slot) return;

        try {
            $service->earlyExit($this->slot);
            $this->successMessage = 'Position exited — capital returned, 8% fee applied.';
            unset($this->subscription, $this->slot);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function requestRefund(PackPurchaseService $service): void
    {
        try {
            $service->refund($this->subscription);
            $this->successMessage = 'Refund processed — funds returned to your wallet.';
            unset($this->subscription);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function withdraw(PackLifecycleService $service): void
    {
        try {
            $service->withdraw($this->subscription);
            $this->successMessage = 'Pack closed — capital returned to your wallet.';
            unset($this->subscription, $this->slot);
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function continueCycle(PackLifecycleService $service): void {
        try {
            $new = $service->continueCycle($this->subscription);
            $this->redirect(route('dashboard.packs.show', $new), navigate: true);
            return;
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function autoCompound(PackLifecycleService $service): void
    {
        try {
            $new = $service->autoCompound($this->subscription);
            $this->redirect(route('dashboard.packs.show', $new), navigate: true);
            return;
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function startUpgrade(int $tierId): void {
        $this->upgradingToTierId = $tierId;
        $this->errorMessage = '';
    }

    public function cancelUpgrade(): void
    {
        $this->upgradingToTierId = null;
        $this->errorMessage = '';
    }

    public function confirmUpgrade(PackLifecycleService $service): void {
        $newTier = PackTier::find($this->upgradingToTierId);
        if (!$newTier) return;

        try {
            $new = $service->upgradeNow($this->subscription, $newTier);
            $this->redirect(route('dashboard.packs.show', $new), navigate: true);
            return;
        } catch (\Throwable $e) {
            $this->errorMessage = $e->getMessage();
        }

        $this->dispatch('pack-action-completed');
    }

    public function render()
    {
        return view('livewire.protected.packs.new-subscription-detail');
    }
}