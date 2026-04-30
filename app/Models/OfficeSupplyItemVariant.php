<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeSupplyItemVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'office_supply_item_id',
        'office_supply_variant',
        'office_supply_quantity',
    ];

    protected $casts = [
        'office_supply_quantity' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(OfficeSupplyItem::class, 'office_supply_item_id')
            ->withTrashed();
    }

    public function requestItems(): HasMany
    {
        return $this->hasMany(OfficeSupplyRequestItem::class, 'item_variant_id');
    }

    public function getMoqAttribute(): int
    {
        $fromDate = now()->subMonths(3);

        $itemName = $this->item?->office_supply_name ?? '';
        $variant  = $this->office_supply_variant ?? '';
        $needle   = "{$itemName} ({$variant})";

        $totalUsed = OfficeSupplyRequestLog::where('action', 'approved')
            ->where('created_at', '>=', $fromDate)
            ->get()
            ->sum(function ($log) use ($needle, $itemName, $variant) {
                $noteData = is_array($log->note)
                    ? $log->note
                    : (json_decode($log->note ?? '[]', true) ?? []);

                return collect($noteData)
                    ->filter(fn ($row) =>
                        isset($row['label']) &&
                        (strcasecmp(trim($row['label']), trim($needle)) === 0 ||
                            (str_contains($row['label'], $itemName) &&
                            str_contains($row['label'], $variant)))
                    )
                    ->sum(fn ($row) => (int) ($row['qty'] ?? 0));
            });

        $monthlyAvg  = $totalUsed / 3;
        $safetyStock = 1.3;
        $leadTime    = 1;

        return (int) ceil(($monthlyAvg * $leadTime) * $safetyStock);
    }
}