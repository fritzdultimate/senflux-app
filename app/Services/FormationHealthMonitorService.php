<?php

namespace App\Services;

use App\Enums\FormationEventType;
use App\Enums\PackSlotStatus;
use App\Models\Formation;

/**
 * Scheduler-driven sweep, same cadence as the deployment pass. Does NOT
 * delete Formation rows — a formation has FormationEvent, SlotEarning,
 * FormationTradeActivity, and FormationLiquiditySnapshot history hanging
 * off it via FK, so removing the row would orphan or cascade-delete real
 * financial/audit history. "Delete" here means: stop it from holding or
 * receiving capital, and log why.
 */
class FormationHealthMonitorService {
    public function __construct(
        private FormationDeploymentService $deployer,
        private FormationEventLogger $eventLogger,
        private SlotAutoDeploymentService $autoDeployer,
    ) {}

    /**
     * Force-exits every slot deployed into a formation scoring below
     * exit_score_threshold, then immediately runs the auto-deployment
     * sweep so freed slots get a shot at re-matching in the same pass
     * instead of sitting idle until the next cron tick.
     *
     * Returns [formations_exited, slots_undeployed, slots_redeployed].
     */
    public function sweep(): array {
        $threshold = config('packs.exit_score_threshold', 20);

        $decayingFormations = Formation::query()
            ->where('score', '<', $threshold)
            ->whereHas('deployedSlots', fn ($q) => $q->where('status', PackSlotStatus::FUNDED->value)->whereNotNull('formation_id'))
            ->with(['deployedSlots' => fn ($q) => $q->where('status', PackSlotStatus::FUNDED->value)->whereNotNull('formation_id')])
            ->get();

        $formationsExited = 0;
        $slotsUndeployed = 0;

        foreach ($decayingFormations as $formation) {
            $slots = $formation->deployedSlots;

            foreach ($slots as $slot) {
                $this->deployer->undeploy($slot);
                $slotsUndeployed++;
            }

            $this->eventLogger->log(
                $formation,
                FormationEventType::EXPOSURE_REDUCED,
                "Score fell to {$formation->score} (below threshold {$threshold}) — {$slots->count()} slot(s) exited",
                ['score' => $formation->score, 'threshold' => $threshold, 'slot_ids' => $slots->pluck('id')],
            );

            $formationsExited++;
        }

        $slotsRedeployed = $slotsUndeployed > 0
            ? $this->autoDeployer->deployEligibleSlots()
            : 0;

        return [
            'formations_exited' => $formationsExited,
            'slots_undeployed' => $slotsUndeployed,
            'slots_redeployed' => $slotsRedeployed,
        ];
    }
}