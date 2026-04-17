<?php
// app/Models/OfficeSupplyRequestItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeSupplyRequestItem extends Model
{
    protected $fillable = [
        'office_supply_request_id',
        'item_id',
        'item_variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyRequest::class, 'office_supply_request_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyItem::class, 'item_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyItemVariant::class, 'item_variant_id');
    }
}