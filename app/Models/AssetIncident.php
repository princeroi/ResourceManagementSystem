<?php
// app/Models/AssetIncident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetIncident extends Model
{
    protected $fillable = [
        'asset_id',
        'reported_by',
        'reported_date',
        'description',
        'severity',
        'status',
        'resolved_date',
        'resolution_notes',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'resolved_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }
}