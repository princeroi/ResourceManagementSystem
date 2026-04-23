<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingDr extends Model
{
    protected $table = 'billing_dr';
    protected $fillable = [
        // Morph: the billing record (SmeBilling or UniformIssuanceBilling)
        'billable_id',
        'billable_type',

        // Morph: the source (SmePurchaseOrder or UniformIssuances)
        'sourceable_id',
        'sourceable_type',

        // Shared
        'employee_name',
        'dr_number',
        'date_signed',
        'dr_image',
        'remarks',
        'uploaded_by',
    ];

    protected $casts = [
        'date_signed' => 'date',
    ];

    /**
     * The billing this DR belongs to.
     * Morphs to: SmeBilling | UniformIssuanceBilling
     */
    public function billable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The source document this DR came from.
     * Morphs to: SmePurchaseOrder | UniformIssuances
     */
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // ── Scopes ──

    public function scopeForSme($query)
    {
        return $query->where('sourceable_type', SmePurchaseOrder::class);
    }

    public function scopeForUniform($query)
    {
        return $query->where('sourceable_type', UniformIssuances::class);
    }
}