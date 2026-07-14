<?php

namespace App\Services;

use App\Enums\FormationEventType;
use App\Models\Formation;
use App\Models\PackSlot;
use App\Enums\PackSlotStatus;

class FormationDeploymentService {
    /**
     * Deploys a funded, undeployed slot into a formation. Also starts
     * this slot's own rolling earning clock — first payout eligible at
     * (24h + jitter) from the moment of deployment, not from a shared
     * calendar cutoff.
     */
    public function deploy(PackSlot $slot, Formation $formation): PackSlot {
        if ($slot->status !== PackSlotStatus::FUNDED) {
            throw new \DomainException('Only a funded slot can be deployed.');
        }

        if ($slot->formation_id !== null) {
            throw new \DomainException('This slot is already deployed.');
        }

        if (!$formation->state->acceptsNewDeployments()) {
            throw new \DomainException("{$formation->token_symbol} isn't in a deployable state ({$formation->state->label()}).");
        }

        $slot->update([
            'formation_id' => $formation->id,
            'deployed_at' => now(),
            'next_earning_at' => $this->nextEarningTime(),
        ]);

        app(FormationEventLogger::class)->log(
            $formation,
            FormationEventType::DEPLOYMENT_INITIATED,
            "Deployment initiated — {$formation->token_symbol}",
            ['slot_id' => $slot->id],
        );

        return $slot->fresh();
    }

    /**
     * Pulls a slot out of a weakening/closed formation without closing
     * the slot itself — it goes back to "Waiting For Qualification"
     * until matched to a new one. Capital is untouched; only the
     * deployment link changes. next_earning_at is cleared too — a slot
     * sitting undeployed isn't earning, so its clock resets and starts
     * fresh from whenever it next gets redeployed, rather than carrying
     * over a stale eligibility timestamp from its old formation.
     */
    public function undeploy(PackSlot $slot): PackSlot {
        $slot->update([
            'formation_id' => null,
            'deployed_at' => null,
            'next_earning_at' => null,
        ]);

        return $slot->fresh();
    }

    private function nextEarningTime(): \Carbon\CarbonInterface {
        $minHours = config('packs.earning_min_interval_hours', 24);
        $jitterMaxMinutes = config('packs.earning_jitter_max_minutes', 180);

        return now()
            ->addHours($minHours)
            ->addMinutes(random_int(0, max(0, $jitterMaxMinutes)));
    }
}