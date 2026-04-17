<?php
// app/Models/SubscriptionMember.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionMember extends Model
{
    protected $fillable = [
        'software_subscription_id',
        'user_id',
        'member_name',
        'member_email',
        'department',
        'role',
        'added_date',
        'removed_date',
        'remarks',
    ];

    protected $casts = [
        'added_date'   => 'date',
        'removed_date' => 'date',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(SoftwareSubscription::class, 'software_subscription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isActive(): bool
    {
        return is_null($this->removed_date);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Display name: prefer linked user's name, fall back to stored member_name */
    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->member_name;
    }
}