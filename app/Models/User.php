<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail {
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balances' => 'array',
            'notify_email_notifications' => 'boolean'
        ];
    }

    public function sendEmailVerificationNotification(): void {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void {
        $this->notify(new ResetPasswordNotification($token));
    }
}
