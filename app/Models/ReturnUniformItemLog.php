<?php
// App\Models\ReturnUniformItemLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnUniformItemLog extends Model
{
    protected $fillable = [
        'return_uniform_item_id',
        'user_id',
        'action',
        'status_from',
        'status_to',
        'note',
    ];

    public function returnUniformItem(): BelongsTo
    {
        return $this->belongsTo(ReturnUniformItems::class, 'return_uniform_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}