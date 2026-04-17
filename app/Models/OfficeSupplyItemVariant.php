<?php
// app/Models/OfficeSupplyItemVariant.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeSupplyItemVariant extends Model
{
    protected $fillable = [
        'office_supply_item_id',
        'office_supply_variant',
        'office_supply_quantity',
    ];

    protected $casts = [
        'office_supply_quantity' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyItem::class, 'office_supply_item_id');
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(OfficeSupplyRequestItem::class, 'item_variant_id');
    }
}