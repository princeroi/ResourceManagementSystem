<?php
// app/Models/AssetTransfer.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetTransfer extends Model
{
    protected $fillable = [
        'asset_id',
        'transferred_from',
        'transferred_to',
        'from_location',
        'to_location',
        'transfer_date',
        'transferred_by',
        'reason',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}