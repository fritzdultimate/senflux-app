<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Protected\Security\TwoFactorSetup;
use App\Livewire\Protected\Withdraw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function verifiedUser(array $overrides = []): User
    {
        static $n = 0;
        $n++;

        return User::create(array_merge([
            'name' => 'twofatest' . $n,
            'email' => 'twofatest' . $n . '@example.com',
            'password' => Hash::make('secret123'),
            'affiliate_code' => 'TWOFAT' . str_pad((string) $n, 3, '0', STR_PAD_LEFT),
            'email_verified_at' => now(),
        ], $overrides));
    }

    public function test_full_enrollment_flow_via_livewire(): void
    {
        $user = $this->verifiedUser();

        $component = Livewire::actingAs($user)->test(TwoFactorSetup::class);
        $component->assertSee('Not Enabled');

        $component->call('beginEnrollment');
        $secret = $component->get('pendingSecret');
        $this->assertNotEmpty($secret);

        $code = (new Google2FA())->getCurrentOtp($secret);

        $component->set('confirmCode', $code)->call('confirmEnrollment');
        $component->assertHasNoErrors();

        $recoveryCodes = $component->get('recoveryCodes');
        $this->assertCount(10, $recoveryCodes);

        $user->refresh();
        $this->assertTrue((bool) $user->two_factor_enable);
        $this->assertNotNull($user->two_factor_confirmed_at);
    }

    public function test_login_requires_2fa_challenge_once_confirmed(): void
    {
        $user = $this->verifiedUser();
        $secret = (new \App\Services\TwoFactorService())->generateSecret();
        app(\App\Services\TwoFactorService::class)->confirmAndEnable($user, $secret, (new Google2FA())->getCurrentOtp($secret));

        $component = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'secret123')
            ->call('login');

        $component->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
        $this->assertEquals($user->id, session('2fa_user_id'));
    }

    public function test_disable_requires_password_and_step_up_code(): void
    {
        $user = $this->verifiedUser();
        $secret = (new \App\Services\TwoFactorService())->generateSecret();
        $codes = app(\App\Services\TwoFactorService::class)->confirmAndEnable($user, $secret, (new Google2FA())->getCurrentOtp($secret));

        $component = Livewire::actingAs($user)->test(TwoFactorSetup::class);
        $component->call('requestDisable');
        $component->set('disablePassword', 'wrong-password')->call('disable');
        $component->assertHasErrors('disablePassword');

        $component->set('disablePassword', 'secret123')->call('disable');
        // Correct password but no step-up code yet — should now show the step-up prompt.
        $this->assertTrue($component->get('stepUpRequired'));

        $code = (new Google2FA())->getCurrentOtp($secret);
        $component->set('stepUpCode', $code)->call('verifyStepUp');
        $component->assertSet('stepUpRequired', false);

        $component->set('disablePassword', 'secret123')->call('disable');
        $user->refresh();
        $this->assertFalse((bool) $user->two_factor_enable);
        $this->assertNull($user->two_factor_secret);
    }

    public function test_withdrawal_requires_step_up_when_2fa_enabled(): void
    {
        $user = $this->verifiedUser();
        $secret = (new \App\Services\TwoFactorService())->generateSecret();
        app(\App\Services\TwoFactorService::class)->confirmAndEnable($user, $secret, (new Google2FA())->getCurrentOtp($secret));

        // Approve KYC so the withdrawal gate itself doesn't block first.
        app(\App\Services\KycService::class)->submit(
            $user,
            \App\Enums\KycTier::BASIC,
            [
                'id_front_path' => \Illuminate\Http\UploadedFile::fake()->image('front.jpg'),
                'selfie_path' => \Illuminate\Http\UploadedFile::fake()->image('selfie.jpg'),
            ],
            ['id_document_type' => 'passport', 'id_document_number' => 'X1'],
        );
        $admin = $this->verifiedUser(['email' => 'wdadmin@example.com', 'is_admin' => true]);
        app(\App\Services\KycService::class)->approve($user->latestKycSubmission(\App\Enums\KycTier::BASIC), $admin->id);

        $component = Livewire::actingAs($user)->test(Withdraw::class);
        $component->set('amount', 50)
            ->set('walletAddress', '0x1234567890abcdef1234')
            ->call('requestConfirm');

        $component->call('submit');
        $this->assertTrue($component->get('stepUpRequired'));

        $code = (new Google2FA())->getCurrentOtp($secret);
        $component->set('stepUpCode', $code)->call('verifyStepUp');
        $component->assertSet('stepUpRequired', false);
    }
}
