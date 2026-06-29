<?php

namespace App\Livewire\Protected\Packs;

use App\Models\PackTier;
use App\Models\PackSubscription;
use App\Services\PackPurchaseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.protected')]
class BrowsePacks extends Component
{
    public string $errorMessage = '';

    #[Computed]
    public function tiers()
    {
        return PackTier::active()->get();
    }

    #[Computed]
    public function walletBalance(): float
    {
        return (float) (Auth::user()->mainWallet()?->balance ?? 0);
    }

    #[Computed]
    public function userHasActivePack(): bool
    {
        return PackSubscription::where('user_id', Auth::id())
            ->whereIn('status', ['active', 'in_renewal_window'])
            ->exists();
    }

    public function buy(int $tierId, PackPurchaseService $service): void
    {
        $tier = PackTier::find($tierId);
        if (!$tier) return;

        $this->errorMessage = '';

        if ($this->walletBalance < (float) $tier->price) {
            $this->errorMessage = "Insufficient wallet balance — you need \${$tier->price} to buy {$tier->name}.";
            return;
        }

        try {
            $subscription = $service->buyPack(Auth::user(), $tier);
        } catch (\Throwable $e) {
            $this->errorMessage = 'Could not complete purchase. Please try again.';
            \Log::error('Pack purchase failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return;
        }

        $this->redirect(route('dashboard.packs.show', $subscription), navigate: true);
    }

    public function render()
    {
        return view('livewire.protected.packs.browse-packs');
    }
}