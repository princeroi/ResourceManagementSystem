<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnSmeItemLine extends Model
{
    protected $fillable = [
        'return_sme_item_id',
        'sme_item_id',
        'sme_item_variant_id',
        'sme_purchase_order_item_id',
        'employee_name',
        'condition',
        'reason',
        'remarks',
        'add_to_stock',          // ← the key flag
        'quantity',
        'returned_quantity',
        'remaining_quantity',
    ];

    protected $casts = [
        'add_to_stock' => 'boolean',
    ];

    public function returnSmeItem() : BelongsTo
    {
        return $this->belongsTo(ReturnSmeItems::class, 'return_sme_item_id');
    }

    public function smeItem() : BelongsTo
    {
        return $this->belongsTo(SmeItems::class, 'sme_item_id');
    }

    public function smeItemVariant() : BelongsTo
    {
        return $this->belongsTo(SmeItemVariants::class, 'sme_item_variant_id');
    }

    public function smePurchaseOrderItem() : BelongsTo
    {
        return $this->belongsTo(SmePurchaseOrderItems::class, 'sme_purchase_order_item_id');
    }


}
