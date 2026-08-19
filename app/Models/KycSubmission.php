<?php

namespace App\Models;

use App\Enums\KycStatus;
use App\Enums\KycTier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KycSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'tier',
        'status',
        'id_document_type',
        'id_document_number',
        'id_front_path',
        'id_back_path',
        'selfie_path',
        'proof_of_address_path',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'provider',
        'provider_reference_id',
        'provider_status',
        'submitted_at',
    ];

    protected function casts(): array {
        return [
            'tier'                => KycTier::class,
            'status'              => KycStatus::class,
            'id_document_number'  => 'encrypted',
            'reviewed_at'         => 'datetime',
            'submitted_at'        => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool {
        return $this->status === KycStatus::PENDING;
    }

    /**
     * The document fields on this submission that actually have a file,
     * keyed field => human label, for rendering document viewer links.
     */
    public function documentFields(): array {
        $labels = [
            'id_front_path'          => 'ID — Front',
            'id_back_path'           => 'ID — Back',
            'selfie_path'            => 'Selfie',
            'proof_of_address_path'  => 'Proof of Address',
        ];

        $fields = [];
        foreach ($labels as $field => $label) {
            if (! empty($this->{$field})) {
                $fields[$field] = $label;
            }
        }

        return $fields;
    }
}
