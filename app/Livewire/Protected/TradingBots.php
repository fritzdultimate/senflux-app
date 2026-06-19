<?php

namespace App\Livewire\Protected;

use App\Enums\PlanType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.protected')]
#[Title('Trading Bots')]
class TradingBots extends Component
{
    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function bots()
    {
        return DB::table('plan_configs')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($plan) {
                $type = $plan->plan instanceof PlanType ? $plan->plan : PlanType::from($plan->plan);

                return [
                    'plan'          => $type,
                    'name'          => $type->label() . ' Bot',
                    'label'         => $plan->label,
                    'daily_rate_max'=> (float) $plan->daily_rate_max,
                    'min_deposit'   => (float) $plan->min_deposit,
                    'max_deposit'   => (float) $plan->max_deposit,
                    'monthly_price' => (float) $plan->monthly_price,
                    'features'      => json_decode($plan->features ?? '[]', true) ?: [],
                    'is_user_plan'  => $this->user->subscription_plan === $plan->plan,
                ];
            });
    }

    #[Computed]
    public function userHasActiveBot(): bool
    {
        return $this->user->deposits()->where('status', 'active')->exists();
    }

    public function render()
    {
        return view('livewire.protected.trading-bots');
    }
}
