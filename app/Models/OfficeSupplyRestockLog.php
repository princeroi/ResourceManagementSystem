<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeSupplyRestockLog extends Model
{
    protected $fillable = [
        'office_supply_restock_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    public function officeSupplyRestock(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyRestock::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}