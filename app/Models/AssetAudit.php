<?php
// app/Models/AssetAudit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAudit extends Model
{
    protected $fillable = [
        'asset_id',
        'audited_by',
        'audit_date',
        'condition_found',
        'location_found',
        'matches_records',
        'remarks',
    ];

    protected $casts = [
        'audit_date'      => 'date',
        'matches_records' => 'boolean',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function hasMismatch(): bool
    {
        return !$this->matches_records;
    }
}