<?php
// app/Models/AssetSoftware.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSoftware extends Model
{
    protected $fillable = [
        'asset_id',
        'software_subscription_id',
        'installed_date',
        'uninstalled_date',
        'installed_by',
        'version',
        'notes',
    ];

    protected $casts = [
        'installed_date'   => 'date',
        'uninstalled_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(SoftwareSubscription::class, 'software_subscription_id');
    }

    public function isCurrentlyInstalled(): bool
    {
        return is_null($this->uninstalled_date);
    }
}