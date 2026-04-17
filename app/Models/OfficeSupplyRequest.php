<?php
// app/Models/OfficeSupplyRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeSupplyRequest extends Model
{
    protected $fillable = [
        'requested_by',
        'request_date',
        'note',
        'status',
    ];

    protected $casts = [
        'request_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OfficeSupplyRequestItem::class, 'office_supply_request_id');
    }
}