<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnOfficeSupplyItemLine extends Model
{
    protected $fillable = [
        'return_office_supply_item_id',
        'office_supply_item_id',
        'office_supply_item_variant_id',
        'employee_name',
        'quantity',
        'returned_quantity',
        'remaining_quantity',
        'condition',
        'reason',
        'add_to_stock',
        'remarks',
    ];

    protected $casts = [
        'quantity'           => 'integer',
        'returned_quantity'  => 'integer',
        'remaining_quantity' => 'integer',
        'add_to_stock'       => 'boolean',
    ];

    public function returnOfficeSupplyItem(): BelongsTo
    {
        return $this->belongsTo(ReturnOfficeSupplyItems::class, 'return_office_supply_item_id');
    }

    public function officeSupplyItem(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyItem::class, 'office_supply_item_id');
    }

    public function officeSupplyItemVariant(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyItemVariant::class, 'office_supply_item_variant_id');
    }
}