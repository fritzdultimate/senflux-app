<?php

namespace App\Services;

use App\Enums\KycStatus;
use App\Enums\KycTier;
use App\Models\KycSubmission;
use App\Models\User;
use App\Notifications\KycApprovedNotification;
use App\Notifications\KycRejectedNotification;
use App\Models\ActivityLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KycService
{
    /**
     * Submit (or resubmit after rejection) KYC documents for a tier.
     *
     * @param  array<string, UploadedFile>  $files  keyed by field name, e.g. id_front_path, selfie_path
     * @param  array<string, mixed>  $meta  tier-specific fields: id_document_type, id_document_number
     */
    public function submit(User $user, KycTier $tier, array $files, array $meta = []): KycSubmission
    {
        if ($tier === KycTier::ENHANCED && ! $user->hasApprovedTier(KycTier::BASIC)) {
            throw new \RuntimeException('Basic verification must be approved before submitting Enhanced verification.');
        }

        $existingPending = $user->kycSubmissions()
            ->where('tier', $tier->value)
            ->where('status', KycStatus::PENDING->value)
            ->exists();

        if ($existingPending) {
            throw new \RuntimeException("Your {$tier->label()} verification is already under review.");
        }

        return DB::transaction(function () use ($user, $tier, $files, $meta) {
            $submission = KycSubmission::create([
                'user_id'           => $user->id,
                'tier'              => $tier->value,
                'status'            => KycStatus::PENDING->value,
                'id_document_type'  => $meta['id_document_type'] ?? null,
                'id_document_number'=> $meta['id_document_number'] ?? null,
                'submitted_at'      => now(),
            ]);

            $paths = $this->storeDocuments($user, $submission, $files);
            $submission->update($paths);

            $user->update([
                'kyc_status'       => KycStatus::PENDING->value,
                'kyc_submitted_at' => now(),
            ]);

            ActivityLog::record(
                action:      'kyc_submitted',
                description: "Submitted {$tier->label()} KYC verification",
                subject:     $user,
                meta:        ['submission_id' => $submission->id, 'tier' => $tier->value],
            );

            return $submission->fresh();
        });
    }

    public function approve(KycSubmission $submission, int $adminId, ?string $note = null): void
    {
        if ($submission->status !== KycStatus::PENDING) {
            throw new \RuntimeException('Only submissions under review can be approved.');
        }

        DB::transaction(function () use ($submission, $adminId, $note) {
            $submission->update([
                'status'      => KycStatus::APPROVED->value,
                'reviewed_by' => $adminId,
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            $user = $submission->user;
            $tier = $submission->tier;

            // Only raise the user's tier if this approval is at least as high
            // as what they already have — a Basic approval after an Enhanced
            // approval (shouldn't normally happen) must never downgrade them.
            $currentRank = $user->kyc_tier_enum?->rank() ?? 0;
            $newTier = $tier->rank() > $currentRank ? $tier->value : $user->kyc_tier;

            $user->update([
                'kyc_status'          => KycStatus::APPROVED->value,
                'kyc_tier'            => $newTier,
                'kyc_verified_at'     => now(),
                'kyc_rejection_reason'=> null,
            ]);

            $user->notify(new KycApprovedNotification($submission));

            ActivityLog::record(
                action:      'kyc_approved',
                userId:      $adminId,
                description: "Approved {$tier->label()} KYC for {$user->name}" . ($note ? " — {$note}" : ''),
                subject:     $user,
                meta:        ['submission_id' => $submission->id, 'tier' => $tier->value],
            );
        });
    }

    public function reject(KycSubmission $submission, int $adminId, string $reason): void
    {
        if ($submission->status !== KycStatus::PENDING) {
            throw new \RuntimeException('Only submissions under review can be rejected.');
        }

        DB::transaction(function () use ($submission, $adminId, $reason) {
            $submission->update([
                'status'           => KycStatus::REJECTED->value,
                'reviewed_by'      => $adminId,
                'reviewed_at'      => now(),
                'rejection_reason' => $reason,
            ]);

            $user = $submission->user;

            // A rejected submission never revokes a tier the user already
            // holds from an earlier approval — only this submission's own
            // status reflects the rejection.
            $user->update([
                'kyc_status'           => KycStatus::REJECTED->value,
                'kyc_rejection_reason' => $reason,
            ]);

            $user->notify(new KycRejectedNotification($submission));

            ActivityLog::record(
                action:      'kyc_rejected',
                userId:      $adminId,
                description: "Rejected {$submission->tier->label()} KYC for {$user->name} — {$reason}",
                subject:     $user,
                meta:        ['submission_id' => $submission->id, 'tier' => $submission->tier->value],
            );
        });
    }

    /**
     * @param  array<string, UploadedFile>  $files
     * @return array<string, string>  field => stored path
     */
    private function storeDocuments(User $user, KycSubmission $submission, array $files): array
    {
        $basePath = "kyc/{$user->id}/{$submission->id}";
        $paths = [];

        foreach ($files as $field => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $paths[$field] = $file->storeAs($basePath, $filename, 'local');
        }

        return $paths;
    }
}
