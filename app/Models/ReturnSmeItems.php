<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnSmeItems extends Model
{
    protected $fillable = [
        'sme_purchase_order_id',
        'site_id',
        'returned_by',
        'received_by',
        'notes',
        'status',
        'pending_at',
        'partial_at',
        'returned_at',
        'cancelled_at',
    ];

    protected $casts = [
        'pending_at'   => 'date',
        'partial_at'   => 'date',
        'returned_at'  => 'date',
        'cancelled_at' => 'date',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(SmePurchaseOrder::class, 'sme_purchase_order_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Sites::class, 'site_id');
    }

    public function returnSmeItemLine(): HasMany
    {
        return $this->hasMany(ReturnSmeItemLine::class, 'return_sme_item_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReturnSmeItemLog::class, 'return_sme_item_id');
    }
}
