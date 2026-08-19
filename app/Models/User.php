<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\KycTier;
use App\Enums\RankLevel;
use App\Enums\WalletType;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail {
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Only users flagged as admin may log into the Filament admin panel.
     * The panel currently has no other role/permission distinction.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) $this->is_admin;
    }

    public $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'rank_achieved_at' => 'datetime',
            'subscription_expires_at' => 'datetime',
            'kyc_verified_at' => 'datetime',
            'kyc_submitted_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enable' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'rank' => RankLevel::class,
            'balances' => 'array',
            'notification_preferences' => 'array',
            'notify_email_notifications' => 'boolean'
        ];
    }

    public function sendEmailVerificationNotification(): void {
        $this->notify(new VerifyEmailNotification());
    }

    public function sendPasswordResetNotification($token): void {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function onboarding(): HasOne {
        return $this->hasOne(OnboardingProgress::class);
    }

    public function getOnboardingAttribute(): OnboardingProgress {
        return $this->onboarding()->firstOrCreate(['user_id' => $this->id]);
    }

    protected static function boot(): void {
        parent::boot();

        static::creating(function (User $user) {
            if (empty($user->affiliate_code)) {
                $user->affiliate_code = static::generateUniqueAffiliateCode();
            }
        });

        // Initialize wallets on creation
        static::created(function (User $user) {
            foreach (WalletType::cases() as $type) {
                $user->wallets()->firstOrCreate(
                    ['type' => $type->value],
                    ['balance' => 0, 'locked_balance' => 0, 'currency' => 'USD', 'is_active' => true]
                );
            }
        });
    }

    public function referrals(): HasMany {
        return $this->hasMany(User::class, 'referrer_id');
    }

    /** Referral record (as the referred user) */
    public function referralRecord(): HasOne {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    public function referrer(): BelongsTo {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /** Referral records where this user is the referrer */
    public function referralsMade(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function slotEarnings(): HasMany {
        return $this->hasMany(SlotEarning::class);
    }

    public function deployedSlots(): HasManyThrough {
        return $this->hasManyThrough(
            PackSlot::class,
            PackSubscription::class,
            'user_id',
            'pack_subscription_id',
            'id',
            'id'
        )->whereNotNull('pack_slots.formation_id');
    }

    public function referralBonusesEarned(): HasMany
    {
        return $this->hasMany(ReferralBonus::class, 'earner_id');
    }

    public function rankAdvancements(): HasMany
    {
        return $this->hasMany(RankAdvancement::class);
    }

    public function leadershipMatchesEarned(): HasMany
    {
        return $this->hasMany(LeadershipMatchBonus::class, 'earner_id');
    }

    public function teamVolume(): HasOne {
        return $this->hasOne(TeamVolume::class);
    }

    public function activityLogs(): HasMany {
        return $this->hasMany(ActivityLog::class);
    }

    public function packSubscriptions(): HasMany {
        return $this->hasMany(PackSubscription::class);
    }

    // ── Wallet shortcuts ──────────────────────────────────────────────────────

    public function mainWallet(): ?Wallet {
        return $this->wallets->firstWhere('type', WalletType::MAIN->value);
    }

    public function referralWallet(): ?Wallet {
        return $this->wallets->firstWhere('type', WalletType::REFERRAL->value);
    }

    public function rankWallet(): ?Wallet {
        return $this->wallets->firstWhere('type', WalletType::RANK->value);
    }

    public function getWallet(WalletType $type): ?Wallet {
        return $this->wallets()->where('type', $type->value)->first();
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getActiveDepositAttribute(): ?Deposit
    {
        return $this->deposits()->where('status', 'active')->latest('activated_at')->first();
    }

    public function getHasActiveSubscriptionAttribute(): bool
    {
        return $this->subscription_plan !== null
            && $this->subscription_expires_at !== null
            && $this->subscription_expires_at->isFuture();
    }

    public function getTotalEarningsAttribute(): float {
        return (float) $this->slotEarnings()->sum('amount');
    }

    public function getTotalDepositsAttribute(): float {
        return (float) $this->deposits()
            ->whereIn('status', ['active', 'finished'])
            ->sum('actually_paid_usd');
    }

    public function getDirectReferralCountAttribute(): int
    {
        return $this->referrals()->count();
    }

    public function getRankLevelAttribute(): RankLevel {
        return $this->rank instanceof RankLevel ? $this->rank : RankLevel::from($this->rank ?? 'none');
    }

    public function getIsKycVerifiedAttribute(): bool
    {
        return $this->kyc_tier !== null;
    }

    // ── KYC ───────────────────────────────────────────────────────────────────

    public function kycSubmissions(): HasMany
    {
        return $this->hasMany(KycSubmission::class);
    }

    public function latestKycSubmission(?KycTier $tier = null): ?KycSubmission
    {
        return $this->kycSubmissions()
            ->when($tier, fn ($q) => $q->where('tier', $tier->value))
            ->latest()
            ->first();
    }

    public function getKycTierEnumAttribute(): ?KycTier
    {
        return $this->kyc_tier ? KycTier::from($this->kyc_tier) : null;
    }

    /** Enhanced approval implies Basic is also satisfied. */
    public function hasApprovedTier(KycTier $tier): bool
    {
        $current = $this->kyc_tier_enum;

        return $current !== null && $current->rank() >= $tier->rank();
    }


    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query) {
        return $query->where('is_active', true);
    }

    public function scopeWithFundedSlots($query) {
        return $query->whereHas('slots', fn($q) => $q->where('status', 'funded'));
    }

    public function scopeWithActiveDeposit($query) {
        return $query->whereHas('deposits', fn($q) => $q->where('status', 'active'));
    }

    public function scopeByRank($query, RankLevel $rank) {
        return $query->where('rank', $rank->value);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public static function generateUniqueAffiliateCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (static::where('affiliate_code', $code)->exists());

        return $code;
    }

    public function findUplineAtLevel(int $level): ?User
    {
        $current = $this;
        for ($i = 0; $i < $level; $i++) {
            $current = $current->referrer;
            if (!$current) return null;
        }
        return $current;
    }

    /** Get all upline users up to 8 levels */
    public function getUplineChain(int $maxLevels = 8): array
    {
        $chain   = [];
        $current = $this;

        for ($level = 1; $level <= $maxLevels; $level++) {
            $upline = $current->referrer;
            if (!$upline) break;
            $chain[$level] = $upline;
            $current = $upline;
        }

        return $chain;
    }

}
