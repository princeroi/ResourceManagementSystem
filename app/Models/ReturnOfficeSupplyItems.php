<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnOfficeSupplyItems extends Model
{
    protected $fillable = [
        'returned_by',
        'received_by',
        'status',
        'pending_at',
        'partial_at',
        'returned_at',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'pending_at'   => 'date',
        'partial_at'   => 'date',
        'returned_at'  => 'date',
        'cancelled_at' => 'date',
    ];

    public function returnOfficeSupplyItemLine(): HasMany
    {
        return $this->hasMany(ReturnOfficeSupplyItemLine::class, 'return_office_supply_item_id', 'id');
    }

    public function returnOfficeSupplyItemLog(): HasMany
    {
        return $this->hasMany(ReturnOfficeSupplyItemLog::class, 'return_office_supply_item_id', 'id');
    }
}