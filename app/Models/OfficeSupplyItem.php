<?php
// app/Models/OfficeSupplyItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeSupplyItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'office_supply_category_id',
        'office_supply_name',
        'office_supply_description',
        'office_supply_price',
        'office_supply_image',
    ];

    protected $casts = [
        'office_supply_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyCategory::class, 'office_supply_category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(OfficeSupplyItemVariant::class, 'office_supply_item_id');
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(OfficeSupplyRequestItem::class, 'item_id');
    }
}