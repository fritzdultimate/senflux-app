<?php

namespace App\Services;

use App\Enums\FormationEventType;
use App\Models\Formation;
use App\Models\PackSlot;
use App\Enums\PackSlotStatus;

class FormationDeploymentService {
    /**
     * Deploys a funded, undeployed slot into a formation. Manual (admin-
     * picked) for now — see Formation Feed admin notes for how this slots
     * into a future automated engine without this method's signature
     * needing to change.
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
     * deployment link changes.
     */
    public function undeploy(PackSlot $slot): PackSlot {
        $slot->update(['formation_id' => null, 'deployed_at' => null]);

        return $slot->fresh();
    }
}
