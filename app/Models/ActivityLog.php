<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    /**
     * Action → display metadata used by the activity bell / activity feed.
     * Falls back to a generic "account" entry for anything not listed here.
     */
    private const DISPLAY_META = [
        'admin_debit_user' => ['label' => 'Balance Adjusted', 'category' => 'financial', 'icon' => 'minus-circle', 'color' => 'red'],
        'admin_fund_user' => ['label' => 'Balance Funded', 'category' => 'financial', 'icon' => 'plus-circle', 'color' => 'green'],
        'admin_reactivate_user' => ['label' => 'Account Reactivated', 'category' => 'account', 'icon' => 'user-check', 'color' => 'green'],
        'admin_reset_2fa' => ['label' => 'Two-Factor Reset', 'category' => 'security', 'icon' => 'shield', 'color' => 'amber'],
        'admin_suspend_user' => ['label' => 'Account Suspended', 'category' => 'account', 'icon' => 'user-x', 'color' => 'red'],
        'changed_password' => ['label' => 'Password Changed', 'category' => 'security', 'icon' => 'lock', 'color' => 'amber'],
        'deposit.manual_activation' => ['label' => 'Deposit Activated', 'category' => 'financial', 'icon' => 'plus-circle', 'color' => 'green'],
        'disabled_2fa' => ['label' => 'Two-Factor Disabled', 'category' => 'security', 'icon' => 'shield-off', 'color' => 'red'],
        'enabled_2fa' => ['label' => 'Two-Factor Enabled', 'category' => 'security', 'icon' => 'shield-check', 'color' => 'green'],
        'kyc_approved' => ['label' => 'Verification Approved', 'category' => 'compliance', 'icon' => 'badge-check', 'color' => 'green'],
        'kyc_rejected' => ['label' => 'Verification Rejected', 'category' => 'compliance', 'icon' => 'badge-x', 'color' => 'red'],
        'kyc_submitted' => ['label' => 'Verification Submitted', 'category' => 'compliance', 'icon' => 'file-text', 'color' => 'blue'],
        'regenerated_2fa_recovery_codes' => ['label' => 'Recovery Codes Regenerated', 'category' => 'security', 'icon' => 'key', 'color' => 'amber'],
        'updated_notification_preferences' => ['label' => 'Notification Preferences Updated', 'category' => 'account', 'icon' => 'bell', 'color' => 'blue'],
        'updated_profile' => ['label' => 'Profile Updated', 'category' => 'account', 'icon' => 'user', 'color' => 'blue'],
    ];
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo('subject');
    }

    // ── Static helpers ────────────────────────────────────────────────────────

    public static function record(
        string $action,
        ?int $userId = null,
        ?string $description = null,
        mixed $subject = null,
        array $meta = [],
    ): self {
        return static::create([
            'user_id'      => $userId ?? auth()->id(),
            'action'       => $action,
            'description'  => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject?->id,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'meta'         => $meta ?: null,
        ]);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Everything a given user should see in their own activity feed.
     *
     * ActivityLog::record() stamps user_id as the ACTOR, not necessarily the
     * affected account — admin-initiated actions (fund/debit/suspend/KYC
     * review/2FA reset) are recorded with user_id = the admin and
     * subject = the affected User. This scope reunites both halves so a
     * user sees things done TO their account as well as things they did
     * themselves, plus deposit-scoped entries (subject = Deposit, not User).
     */
    public function scopeVisibleTo($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhere(function ($q2) use ($userId) {
                    $q2->where('subject_type', User::class)
                        ->where('subject_id', $userId);
                })
                ->orWhere(function ($q3) use ($userId) {
                    $q3->where('subject_type', Deposit::class)
                        ->whereIn('subject_id', function ($sub) use ($userId) {
                            $sub->select('id')
                                ->from('deposits')
                                ->where('user_id', $userId);
                        });
                });
        });
    }

    public function scopeInCategory($query, string $category)
    {
        $actionsInCategory = collect(self::DISPLAY_META)
            ->filter(fn ($meta) => $meta['category'] === $category)
            ->keys()
            ->all();

        if ($category === 'account') {
            // "account" is also the fallback category for any action not
            // explicitly mapped, so include unmapped actions too rather
            // than only the handful explicitly tagged 'account' above.
            $mappedElsewhere = collect(self::DISPLAY_META)
                ->reject(fn ($meta) => $meta['category'] === 'account')
                ->keys()
                ->all();

            return $query->where(function ($q) use ($actionsInCategory, $mappedElsewhere) {
                $q->whereIn('action', $actionsInCategory)
                    ->orWhereNotIn('action', $mappedElsewhere);
            });
        }

        return $query->whereIn('action', $actionsInCategory);
    }

    /** Human-friendly label/icon/color for this entry's action, with a safe fallback. */
    public function getDisplayMetaAttribute(): array
    {
        return self::DISPLAY_META[$this->action] ?? [
            'label' => \Illuminate\Support\Str::of($this->action)->replace(['_', '.'], ' ')->title()->toString(),
            'category' => 'account',
            'icon' => 'activity',
            'color' => 'gray',
        ];
    }
}