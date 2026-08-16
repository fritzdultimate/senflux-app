<?php

namespace App\Livewire\Protected;

use App\Enums\TransactionType;
use App\Enums\WalletType;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.protected')]
#[Title('Wallet')]
class Wallet extends Component
{
    use WithPagination;

    #[Url]
    public string $walletFilter = 'all';

    #[Url]
    public string $typeFilter = 'all';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatedWalletFilter(): void { $this->resetPage(); }
    public function updatedTypeFilter(): void { $this->resetPage(); }
    public function updatedDateFrom(): void { $this->resetPage(); }
    public function updatedDateTo(): void { $this->resetPage(); }

    #[Computed]
    public function user()
    {
        return Auth::user()->load('wallets');
    }

    #[Computed]
    public function wallets(): array {
        return collect(WalletType::cases())->map(function ($type) {
            $wallet = $this->user->wallets->firstWhere('type', $type->value);
            return [
                'type'      => $type->value,
                'label'     => $type->label(),
                'balance'   => (float) ($wallet?->balance ?? 0),
                'locked'    => (float) ($wallet?->locked_balance ?? 0),
                'available' => max(0, (float) ($wallet?->balance ?? 0) - (float) ($wallet?->locked_balance ?? 0)),
            ];
        })->toArray();
    }

    #[Computed]
    public function totalBalance(): float
    {
        return array_sum(array_column($this->wallets, 'balance'));
    }

    #[Computed]
    public function totalLocked(): float
    {
        return array_sum(array_column($this->wallets, 'locked'));
    }

    #[Computed]
    public function totalAvailable(): float
    {
        return array_sum(array_column($this->wallets, 'available'));
    }

    #[Computed]
    public function typeOptions(): array {
        return collect(TransactionType::cases())->map(fn($t) => [
            'value' => $t->value,
            'label' => $t->label(),
        ])->toArray();
    }

    #[Computed]
    public function transactions() {
        $query = WalletTransaction::where('user_id', $this->user->id)
            ->with('wallet');

        if ($this->walletFilter !== 'all') {
            $query->whereHas('wallet', fn($q) => $q->where('type', $this->walletFilter));
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->latest()->paginate(15);
    }

    #[Computed]
    public function thisMonthCredits(): float
    {
        return (float) WalletTransaction::where('user_id', $this->user->id)
            ->whereIn('type', [
                TransactionType::DEPOSIT->value,
                TransactionType::DAILY_EARNING->value,
                TransactionType::REFERRAL_BONUS->value,
                TransactionType::RANK_BONUS->value,
                TransactionType::LEADERSHIP_MATCH->value,
            ])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    #[Computed]
    public function thisMonthDebits(): float
    {
        return (float) WalletTransaction::where('user_id', $this->user->id)
            ->whereIn('type', [TransactionType::WITHDRAWAL->value, TransactionType::FEE->value])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    public function resetFilters(): void
    {
        $this->reset(['walletFilter', 'typeFilter', 'dateFrom', 'dateTo']);
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.protected.wallet');
    }
}
