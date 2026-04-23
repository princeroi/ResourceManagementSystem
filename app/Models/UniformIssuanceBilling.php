<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class UniformIssuanceBilling extends Model
{
    protected $fillable =[
        'uniform_issuance_id', 
        'billed_to', 
        'billing_type', 
        'billing_items', 
        'total_price', 
        'status',
        'billed_at',
        'created_by'
    ];

    protected $casts = [
        'billing_items'         => 'array',
        'billed_at'            => 'date',
        'total_price'           => 'decimal:2'
    ];

    public function uniformIssuance(): BelongsTo
    {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id', 'id');
    }

    public function issuance() : BelongsTo
    {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id');
    }

    public function creator() : BelongsTo 
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function billingAtds() : HasMany
    {
        return $this->hasMany(BillingAtd::class, 'uniform_issuance_billing_id');
    }

    // public function billingDrs() : HasMany
    // {
    //     return $this->hasMany(BillingDr::class, 'uniform_issuance_billing_id');
    // }

    public function billingInclude(): MorphOne
    {
        return $this->morphOne(BillingInclude::class, 'includeable');
    }

    public function billingDrs(): MorphMany
    {
        return $this->morphMany(BillingDr::class, 'billable');
    }
}
