<?php

namespace App\Enums;

enum KycTier: string {
    case BASIC    = 'basic';
    case ENHANCED = 'enhanced';

    public function label(): string {
        return match($this) {
            self::BASIC    => 'Basic',
            self::ENHANCED => 'Enhanced',
        };
    }

    public function description(): string {
        return match($this) {
            self::BASIC    => 'Government ID and a selfie for identity verification.',
            self::ENHANCED => 'Adds proof of address to unlock higher withdrawal limits.',
        };
    }

    /**
     * Document fields required on a KycSubmission for this tier.
     * Used by both the upload form and the admin review screen.
     */
    public function requiredFields(): array {
        return match($this) {
            self::BASIC    => ['id_document_type', 'id_document_number', 'id_front_path', 'selfie_path'],
            self::ENHANCED => ['proof_of_address_path'],
        };
    }

    /** Numeric rank so "Enhanced implies Basic" comparisons are simple. */
    public function rank(): int {
        return match($this) {
            self::BASIC    => 1,
            self::ENHANCED => 2,
        };
    }

    public function color(): string {
        return match($this) {
            self::BASIC    => 'gray',
            self::ENHANCED => 'primary',
        };
    }
}
