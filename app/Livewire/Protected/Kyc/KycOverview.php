<?php

namespace App\Livewire\Protected\Kyc;

use App\Enums\KycStatus;
use App\Enums\KycTier;
use App\Models\KycSubmission;
use App\Services\KycService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.protected')]
#[Title('Identity Verification')]
class KycOverview extends Component
{
    use WithFileUploads;

    // ── Basic tier form ──────────────────────────────────────────────────
    public string $idDocumentType = 'passport';
    public string $idDocumentNumber = '';
    public $idFront;
    public $idBack;
    public $selfie;

    // ── Enhanced tier form ───────────────────────────────────────────────
    public $proofOfAddress;

    public string $errorMessage = '';
    public string $successMessage = '';

    #[Computed]
    public function user() {
        return Auth::user();
    }

    #[Computed]
    public function latestBasic(): ?KycSubmission {
        return $this->user->latestKycSubmission(KycTier::BASIC);
    }

    #[Computed]
    public function latestEnhanced(): ?KycSubmission {
        return $this->user->latestKycSubmission(KycTier::ENHANCED);
    }

    public function submitBasic(KycService $service): void {
        $this->errorMessage = '';

        $this->validate([
            'idDocumentType'   => 'required|in:passport,national_id,drivers_license',
            'idDocumentNumber' => 'required|string|min:4|max:60',
            'idFront'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:8192',
            'idBack'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:8192',
            'selfie'           => 'required|file|mimes:jpg,jpeg,png|max:8192',
        ]);

        try {
            $files = ['id_front_path' => $this->idFront, 'selfie_path' => $this->selfie];
            if ($this->idBack) {
                $files['id_back_path'] = $this->idBack;
            }

            $service->submit(
                user: $this->user,
                tier: KycTier::BASIC,
                files: $files,
                meta: [
                    'id_document_type'   => $this->idDocumentType,
                    'id_document_number' => $this->idDocumentNumber,
                ],
            );

            $this->reset(['idDocumentNumber', 'idFront', 'idBack', 'selfie']);
            $this->successMessage = 'Basic verification submitted — we\'ll review it shortly.';
            unset($this->latestBasic, $this->user);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function submitEnhanced(KycService $service): void {
        $this->errorMessage = '';

        $this->validate([
            'proofOfAddress' => 'required|file|mimes:jpg,jpeg,png,pdf|max:8192',
        ]);

        try {
            $service->submit(
                user: $this->user,
                tier: KycTier::ENHANCED,
                files: ['proof_of_address_path' => $this->proofOfAddress],
            );

            $this->reset(['proofOfAddress']);
            $this->successMessage = 'Enhanced verification submitted — we\'ll review it shortly.';
            unset($this->latestEnhanced, $this->user);
        } catch (\RuntimeException $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render() {
        return view('livewire.protected.kyc.kyc-overview');
    }
}
