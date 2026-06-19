<?php

namespace App\Livewire\Protected;

use App\Enums\DepositStatus;
use App\Enums\PlanType;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Poll;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('My Bots')]
class MyBots extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function bots()
    {
        // Each active/finished deposit IS a bot — the plan it's running is the bot identity.
        return $this->user->deposits()
            ->whereIn('status', [DepositStatus::ACTIVE->value, DepositStatus::FINISHED->value])
            ->with('planConfig')
            ->latest('activated_at')
            ->get()
            ->map(function ($deposit) {
                $plan = $deposit->planConfig->plan instanceof PlanType
                    ? $deposit->planConfig->plan
                    : PlanType::from($deposit->planConfig->plan);

                $principal = (float) ($deposit->actually_paid_usd ?? $deposit->amount_usd);

                return [
                    'deposit_id'   => $deposit->id,
                    'bot_name'     => $plan->label() . ' Bot',
                    'plan'         => $plan,
                    'status'       => $deposit->status,
                    'is_running'   => $deposit->status === DepositStatus::ACTIVE->value,
                    'principal'    => $principal,
                    'earned'       => (float) $deposit->total_earnings,
                    'daily_rate'   => (float) $deposit->daily_rate,
                    'deployed_at'  => $deposit->activated_at,
                    'days_running' => $deposit->activated_at?->diffInDays(now()) ?? 0,
                ];
            });
    }

    #[Computed]
    public function totalEarningRunning(): float
    {
        return (float) $this->bots->where('is_running', true)->sum('earned');
    }

    #[Computed]
    public function activeBotCount(): int
    {
        return $this->bots->where('is_running', true)->count();
    }

    #[Poll(30000)]
    public function refresh(): void
    {
        unset($this->bots, $this->totalEarningRunning, $this->activeBotCount);
    }

    public function render()
    {
        return view('livewire.protected.my-bots');
    }
}
