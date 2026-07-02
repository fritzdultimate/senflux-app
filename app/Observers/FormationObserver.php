<?php
// app/Observers/FormationObserver.php

namespace App\Observers;

use App\Enums\FormationEventType;
use App\Models\Formation;
use App\Services\FormationEventLogger;

class FormationObserver {
    public function __construct(private FormationEventLogger $logger) {}

    public function created(Formation $formation): void {
        $this->logger->log($formation, FormationEventType::DETECTED, "{$formation->token_symbol} formation detected");
    }

    public function updated(Formation $formation): void {
        if (!$formation->isDirty('state')) {
            return;
        }

        $to = $formation->state->label();
        $this->logger->log(
            $formation,
            FormationEventType::STATE_CHANGED,
            "Formation upgraded to {$to}",
            ['from' => $formation->getOriginal('state'), 'to' => $formation->state->value],
        );
    }
}