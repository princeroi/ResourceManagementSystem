<?php
// app/Models/SmePurchaseOrderLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmePurchaseOrderLog extends Model
{
    protected $fillable = [
        'sme_purchase_order_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    protected $casts = [
        'note' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function smePurchaseOrder(): BelongsTo
    {
        return $this->belongsTo(SmePurchaseOrder::class);
    }
}