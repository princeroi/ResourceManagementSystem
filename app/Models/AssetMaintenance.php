<?php
// app/Models/AssetMaintenance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'maintenance_date',
        'completed_date',
        'performed_by',
        'description',
        'cost',
        'status',
        'remarks',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'completed_date'   => 'date',
        'cost'             => 'decimal:2',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['scheduled', 'in_progress']);
    }
}