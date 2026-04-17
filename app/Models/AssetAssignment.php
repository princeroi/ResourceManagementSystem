<?php
// app/Models/AssetAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id',
        'user_id',
        'assigned_to',
        'department',
        'location',
        'assigned_date',
        'returned_date',
        'remarks',
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'returned_date' => 'date',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isActive(): bool
    {
        return is_null($this->returned_date);
    }
}