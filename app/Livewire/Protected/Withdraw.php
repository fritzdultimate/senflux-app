<?php

namespace App\Livewire\Protected;

use App\Enums\WalletType;
use App\Enums\WithdrawalStatus;
use App\Livewire\Concerns\RequiresStepUp;
use App\Models\MainWallet;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Withdraw')]
class Withdraw extends Component
{
    use RequiresStepUp;

    // ── Form fields ───────────────────────────────────────────────────────

    public string $walletType    = 'main';
    public float  $amount        = 0;
    public string $walletAddress = '';
    public string $network       = 'sol';
    public string $cryptoCurrency = 'sol';

    // ── UI state ──────────────────────────────────────────────────────────

    public bool   $showConfirm  = false;
    public string $errorMessage = '';
    public string $successMessage = '';

    // ── Supported networks ────────────────────────────────────────────────

    // public array $networks = [
    //     ['code' => 'sol',    'label' => 'Solana',      'currency' => 'SOL'],
    //     ['code' => 'usdtsol','label' => 'Solana',      'currency' => 'USDT'],
    //     ['code' => 'trc20',  'label' => 'TRON (TRC-20)','currency' => 'USDT'],
    //     ['code' => 'bsc',    'label' => 'BSC (BEP-20)','currency' => 'USDT'],
    //     ['code' => 'erc20',  'label' => 'Ethereum',    'currency' => 'USDT'],
    //     ['code' => 'btc',    'label' => 'Bitcoin',     'currency' => 'BTC'],
    // ];

    #[Computed]
    public function networks() {
        $wallets = MainWallet::where('is_active', true)->get();

        return $wallets;
    }

    #[Computed]
    public function user() {
        return Auth::user()->load('wallets');
    }

    #[Computed]
    public function walletOptions(): array {
        return collect(WalletType::cases())->map(fn($type) => [
            'value'   => $type->value,
            'label'   => $type->label(),
            'balance' => (float) ($this->user->wallets->firstWhere('type', $type->value)?->balance ?? 0),
        ])->toArray();
    }

    #[Computed]
    public function availableBalance(): float {
        $wallet = $this->user->wallets->firstWhere('type', $this->walletType);
        if (!$wallet) return 0;
        return max(0, (float) $wallet->balance - (float) $wallet->locked_balance);
    }

    #[Computed]
    public function settings(): object {
        return app(WithdrawalService::class)->getSettings();
    }

    #[Computed]
    public function estimatedFee(): float {
        if ($this->amount <= 0) return 0;
        $s = $this->settings;
        if ((float) $s->fee_value <= 0) return 0;
        return match ($s->fee_type) {
            'percentage' => round($this->amount * ((float) $s->fee_value / 100), 2),
            'flat'       => (float) $s->fee_value,
            default      => 0,
        };
    }

    #[Computed]
    public function netAmount(): float {
        return max(0, $this->amount - $this->estimatedFee);
    }

    #[Computed]
    public function history() {
        return Withdrawal::where('user_id', $this->user->id)
            ->latest()
            ->take(10)
            ->get();
    }

    public function setMax(): void {
        $this->amount = $this->availableBalance;
    }

    public function selectNetwork(string $currency): void {
        $this->network = $currency;
        $found = MainWallet::where('currency', $currency)->first();
        $this->cryptoCurrency = strtolower($found->currency ?? $currency);
    }

    public function requestConfirm(): void {
        $this->errorMessage = '';

        $this->validate([
            'amount' => "required|numeric|min:{$this->settings->min_amount}|max:{$this->availableBalance}",
            'walletAddress' => 'required|string|min:10|max:200',
            'network' => 'required|string',
        ], [
            'amount.min' => "Minimum withdrawal is \${$this->settings->min_amount}.",
            'amount.max' => 'Amount exceeds your available balance.',
            'walletAddress.required' => 'Please enter your wallet address.',
            'walletAddress.min' => 'Wallet address appears too short.',
        ]);

        $this->showConfirm = true;
    }

    public function submit(WithdrawalService $service): void {
        $this->errorMessage = '';

        // Step-up re-auth: a fresh 2FA code is required before money moves,
        // unless the user already verified within the last 10 minutes.
        if (! $this->ensureStepUp()) {
            return;
        }

        try {
            $service->create(
                user:           $this->user,
                walletType:     WalletType::from($this->walletType),
                amount:         $this->amount,
                walletAddress:  $this->walletAddress,
                network:        $this->network,
                cryptoCurrency: $this->cryptoCurrency,
            );

            $this->successMessage = 'Withdrawal request submitted. Processing within '
                . $this->settings->processing_days
                . ' business day(s).';
            $this->showConfirm    = false;
            $this->reset(['amount', 'walletAddress']);
            unset($this->history, $this->availableBalance);

        } catch (\RuntimeException $e) {
            $this->showConfirm  = false;
            $this->errorMessage = $e->getMessage();
        }
    }

    public function cancelConfirm(): void
    {
        $this->showConfirm = false;
    }

    public function cancelWithdrawal(int $id, WithdrawalService $service): void {
        $withdrawal = Withdrawal::findOrFail($id);

        try {
            $service->cancel($withdrawal, $this->user);
            unset($this->history, $this->availableBalance);
            $this->successMessage = 'Withdrawal cancelled and funds returned to your wallet.';
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render() {
        return view('livewire.protected.withdraw');
    }
}
