<?php

namespace App\Services;

use App\Enums\FormationEventType;
use App\Models\Formation;
use App\Models\FormationEvent;

class FormationEventLogger {
    public function log(Formation $formation, FormationEventType $type, ?string $message = null, array $meta = []): FormationEvent {
        return FormationEvent::create([
            'formation_id' => $formation->id,
            'type' => $type->value,
            'message' => $message ?? $type->defaultMessage(),
            'meta' => $meta,
        ]);
    }
}