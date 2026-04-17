<?php
// app/Models/SoftwareSubscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class SoftwareSubscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'vendor',
        'category',
        'license_type',
        'plan_type',
        'total_seats',
        'used_seats',
        'cost',
        'billing_cycle',
        'currency',
        'start_date',
        'expiry_date',
        'auto_renew',
        'license_key',
        'account_email',
        'portal_url',
        'managed_by',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'expiry_date' => 'date',
        'auto_renew'  => 'boolean',
        'cost'        => 'decimal:2',
        'total_seats' => 'integer',
        'used_seats'  => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    /** All members ever added (includes removed) */
    public function members(): HasMany
    {
        return $this->hasMany(SubscriptionMember::class, 'software_subscription_id');
    }

    /** Only currently active members (removed_date IS NULL) */
    public function activeMembers(): HasMany
    {
        return $this->hasMany(SubscriptionMember::class, 'software_subscription_id')
            ->whereNull('removed_date');
    }

    /** Admin members of the plan */
    public function adminMembers(): HasMany
    {
        return $this->hasMany(SubscriptionMember::class, 'software_subscription_id')
            ->where('role', 'admin')
            ->whereNull('removed_date');
    }

    /** All install records across assets */
    public function assetInstalls(): HasMany
    {
        return $this->hasMany(AssetSoftware::class, 'software_subscription_id');
    }

    /** Currently installed (not yet uninstalled) */
    public function activeInstalls(): HasMany
    {
        return $this->hasMany(AssetSoftware::class, 'software_subscription_id')
            ->whereNull('uninstalled_date');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expiry_date
            && $this->expiry_date->between(Carbon::today(), Carbon::today()->addDays($days));
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isGroupBased(): bool
    {
        return in_array($this->plan_type, ['group', 'family', 'enterprise']);
    }

    public function hasAvailableSeats(): bool
    {
        if (is_null($this->total_seats)) {
            return true; // unlimited
        }

        return $this->used_seats < $this->total_seats;
    }

    public function remainingSeats(): ?int
    {
        if (is_null($this->total_seats)) {
            return null; // unlimited
        }

        return max(0, $this->total_seats - $this->used_seats);
    }
}