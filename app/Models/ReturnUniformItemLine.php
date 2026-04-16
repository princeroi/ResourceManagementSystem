<?php
// App\Models\ReturnUniformItemLine.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnUniformItemLine extends Model
{
    protected $fillable = [
        'return_uniform_item_id',
        'uniform_item_id',
        'uniform_item_variant_id',
        'uniform_issuance_item_id',
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

    public function returnUniformItem(): BelongsTo
    {
        return $this->belongsTo(ReturnUniformItems::class, 'return_uniform_item_id');
    }

    public function uniformItem(): BelongsTo
    {
        return $this->belongsTo(UniformItems::class, 'uniform_item_id');
    }

    public function uniformItemVariant(): BelongsTo
    {
        return $this->belongsTo(UniformItemVariants::class, 'uniform_item_variant_id');
    }

    public function uniformIssuanceItem(): BelongsTo
    {
        return $this->belongsTo(UniformIssuanceItems::class, 'uniform_issuance_item_id');
    }
}