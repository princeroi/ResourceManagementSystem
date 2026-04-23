<?php
// app/Models/Asset.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_category_id',
        'property_tag',
        'name',
        'brand',
        'model',
        'serial_number',
        'specifications',
        'acquisition_date',
        'acquisition_cost',
        'supplier',
        'purchase_order_number',
        'warranty_expiry_date',
        'useful_life_years',
        'salvage_value',
        'location',
        'condition',
        'status',
        'lifecycle_stage',
        'image',
        'notes',
    ];

    protected $casts = [
        'acquisition_date'    => 'date',
        'warranty_expiry_date'=> 'date',
        'acquisition_cost'    => 'decimal:2',
        'salvage_value'       => 'decimal:2',
        'useful_life_years'   => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class, 'asset_id');
    }

    /** Currently active assignment (no returned_date yet) */
    public function activeAssignment(): HasOne
    {
        return $this->hasOne(AssetAssignment::class, 'asset_id')
            ->whereNull('returned_date')
            ->latestOfMany('assigned_date');
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class, 'asset_id');
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id');
    }

    public function disposals(): HasMany
    {
        return $this->hasMany(AssetDisposal::class, 'asset_id');
    }

    public function audits(): HasMany
    {
        return $this->hasMany(AssetAudit::class, 'asset_id');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(AssetIncident::class, 'asset_id');
    }

    /** All software install records for this asset */
    public function software(): HasMany
    {
        return $this->hasMany(AssetSoftware::class, 'asset_id');
    }

    /** Only currently installed software (not yet uninstalled) */
    public function installedSoftware(): HasMany
    {
        return $this->hasMany(AssetSoftware::class, 'asset_id')
            ->whereNull('uninstalled_date');
    }
}