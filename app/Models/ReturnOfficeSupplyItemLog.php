<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnOfficeSupplyItemLog extends Model
{
    protected $fillable = [
        'return_office_supply_item_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    public function returnOfficeSupplyItem(): BelongsTo
    {
        return $this->belongsTo(ReturnOfficeSupplyItems::class, 'return_office_supply_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}