<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeSupplyRequestLog extends Model
{
    protected $fillable = [
        'office_supply_request_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    protected $casts = [
        'note' => 'array',
    ];

    public function officeSupplyRequest(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyRequest::class, 'office_supply_request_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}