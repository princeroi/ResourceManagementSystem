<?php
// app/Models/OfficeSupplyRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeSupplyRequest extends Model
{
    protected $fillable = [
        'request_number',
        'requested_by',
        'request_date',
        'note',
        'status',
    ];

    protected $casts = [
        'request_date' => 'date',
        'status' => 'string',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->request_number)) {
                $model->request_number = static::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber(): string
    {
        $prefix = 'REQ-' . now()->timezone('Asia/Manila')->format('Ymd') . '-';

        $last = static::where('request_number', 'like', $prefix . '%')
            ->orderByDesc('request_number')
            ->value('request_number');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfficeSupplyRequestItem::class, 'office_supply_request_id');
    }
}