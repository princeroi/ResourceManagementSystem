<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingInclude extends Model
{
    protected $fillable = [
        'billing_id',
        'includeable_type',
        'includeable_id',
        'amount',
        'label',
        'included_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'included_at' => 'datetime',
    ];

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class);
    }

    public function includeable(): MorphTo
    {
        return $this->morphTo();
    }
}