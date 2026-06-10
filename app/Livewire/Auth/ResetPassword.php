<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Set New Password — Senflux')]
class ResetPassword extends Component {
    #[Url]
    public string $token = '';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $showPassword = false;
    public bool $done = false;

    public function mount(string $token): void {
        $this->token = $token;
        $this->email = request()->string('email')->value();
    }

    protected function rules(): array {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->numbers()->symbols()],
        ];
    }

    public function resetPassword(): void {
        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $this->done = true;
            $this->redirect(route('login') . '?reset=1', navigate: true);
            return;
        }

        $this->addError('email', __($status));
    }

    public function render(): \Illuminate\View\View {
        return view('livewire.auth.reset-password');
    }
}