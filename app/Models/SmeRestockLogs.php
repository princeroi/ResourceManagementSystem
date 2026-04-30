<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeRestockLogs extends Model
{
    protected $fillable = [
        'sme_restock_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    public function smeRestock(): BelongsTo
    {
        return $this->belongsTo(SmeRestocks::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}