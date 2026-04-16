<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnSmeItemLog extends Model
{
    protected $fillable = [
        'return_sme_item_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    public function returnSmeItem(): BelongsTo
    {
        return $this->belongsTo(ReturnSmeItems::class, 'return_sme_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
