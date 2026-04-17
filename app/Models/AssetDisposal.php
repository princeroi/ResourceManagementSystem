<?php
// app/Models/AssetDisposal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDisposal extends Model
{
    protected $fillable = [
        'asset_id',
        'disposal_type',
        'disposal_date',
        'disposed_by',
        'recipient',
        'disposal_value',
        'remarks',
    ];

    protected $casts = [
        'disposal_date'  => 'date',
        'disposal_value' => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}