<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SmePurchaseOrder extends Model
{
    protected $fillable = [
        'site_id',
        'note',
        'status',
        'po_number',
        'po_file_path',
        'po_date',
        'dr_number',
        'dr_file_path',
        'pending_at',
        'approved_at',
        'rejected_at',
    ];

    protected $casts = [
        'po_date'     => 'date',
        'pending_at'  => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (SmePurchaseOrder $order) {
            if ($order->status === 'pending' && blank($order->pending_at)) {
                $order->pending_at = now();
            }
        });

        static::updating(function (SmePurchaseOrder $order) {
            if ($order->isDirty('status')) {
                match ($order->status) {
                    'approved' => $order->approved_at = now(),
                    'rejected' => $order->rejected_at = now(),
                    'pending'  => $order->pending_at  = now(),
                };
            }
        });
    }
    
    public function site() : BelongsTo {
        return $this->belongsTo(Sites::class, 'site_id', 'id');
    }

    public function purchaseOrderItems() : HasMany {
        return $this->hasMany(SmePurchaseOrderItems::class, 'sme_purchase_order_id', 'id');
    }

    public function billingDrs(): MorphMany
{
    return $this->morphMany(BillingDr::class, 'sourceable');
}
}

    