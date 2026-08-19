<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KycAndTwoFactorSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser(array $overrides = []): User
    {
        static $n = 0;
        $n++;

        return User::create(array_merge([
            'name' => 'smoketest' . $n,
            'email' => 'smoke' . $n . '@example.com',
            'password' => Hash::make('secret123'),
            'affiliate_code' => 'SMOKE' . str_pad((string) $n, 3, '0', STR_PAD_LEFT),
            'email_verified_at' => now(),
        ], $overrides));
    }

    public function test_kyc_page_renders(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->get(route('dashboard.kyc'));

        $response->assertOk();
        $response->assertSee('Identity Verification');
    }

    public function test_security_page_renders(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->get(route('dashboard.security'));

        $response->assertOk();
        $response->assertSee('Two-Factor Authentication');
    }

    public function test_withdraw_page_renders_with_kyc_banner(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->get(route('dashboard.withdraw'));

        $response->assertOk();
        $response->assertSee('Identity verification required');
    }

    public function test_settings_page_renders(): void
    {
        $user = $this->verifiedUser();

        $response = $this->actingAs($user)->get(route('dashboard.settings'));

        $response->assertOk();
    }

    public function test_non_admin_cannot_access_filament_panel(): void
    {
        $user = $this->verifiedUser(['email' => 'nonadmin@example.com']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_can_access_filament_panel(): void
    {
        $admin = $this->verifiedUser(['email' => 'admin@example.com', 'is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_admin_can_view_kyc_submissions_resource(): void
    {
        $admin = $this->verifiedUser(['email' => 'admin3@example.com', 'is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/kyc-submissions');

        $response->assertOk();
    }

    public function test_admin_can_view_users_resource(): void
    {
        $admin = $this->verifiedUser(['email' => 'admin4@example.com', 'is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
    }

    public function test_full_kyc_and_withdrawal_gate_flow(): void
    {
        $user = $this->verifiedUser(['email' => 'flowuser@example.com']);
        $admin = $this->verifiedUser(['email' => 'flowadmin@example.com', 'is_admin' => true]);

        $service = app(\App\Services\KycService::class);
        $submission = $service->submit(
            $user,
            \App\Enums\KycTier::BASIC,
            [
                'id_front_path' => \Illuminate\Http\UploadedFile::fake()->image('front.jpg'),
                'selfie_path' => \Illuminate\Http\UploadedFile::fake()->image('selfie.jpg'),
            ],
            ['id_document_type' => 'passport', 'id_document_number' => 'X1'],
        );

        $withdrawalService = app(\App\Services\WithdrawalService::class);

        // Before approval — must be blocked.
        try {
            $withdrawalService->create($user, \App\Enums\WalletType::MAIN, 50, 'addr', 'sol', 'sol');
            $this->fail('Expected RuntimeException for unverified KYC');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('Identity verification', $e->getMessage());
        }

        $service->approve($submission, $admin->id);
        $user->refresh();
        $this->assertEquals('basic', $user->kyc_tier);
        $this->assertTrue($user->is_kyc_verified);
    }
}
