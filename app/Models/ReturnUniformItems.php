<?php
// App\Models\ReturnUniformItems.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnUniformItems extends Model
{
    protected $fillable = [
        'uniform_issuance_id',
        'site_id',
        'returned_by',
        'received_by',
        'notes',
        'status',
        'pending_at',
        'partial_at',
        'returned_at',
        'cancelled_at',
    ];

    protected $casts = [
        'pending_at'   => 'date',
        'partial_at'   => 'date',
        'returned_at'  => 'date',
        'cancelled_at' => 'date',
    ];

    public function uniformIssuance(): BelongsTo
    {
        return $this->belongsTo(UniformIssuances::class, 'uniform_issuance_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Sites::class, 'site_id');
    }

    public function returnUniformItemLine(): HasMany
    {
        return $this->hasMany(ReturnUniformItemLine::class, 'return_uniform_item_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ReturnUniformItemLog::class, 'return_uniform_item_id');
    }
}