<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmeItemVariants extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sme_item_id',
        'sme_item_size',
        'sme_item_quantity',
    ];

    protected $casts = [
        'sme_item_quantity' => 'integer',
    ];

    public function smeItem(): BelongsTo
    {
        return $this->belongsTo(SmeItems::class, 'sme_item_id', 'id')
                    ->withTrashed();
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(SmePurchaseOrder::class, 'sme_item_variant_id', 'id');
    }

    /**
     * Minimum Order Quantity — mirrors the Uniform MOQ logic.
     * Based on quantity approved via purchase orders in the last 3 months.
     */
    public function getMoqAttribute(): int
    {
        $fromDate = now()->subMonths(3);

        // Sum qty from approved purchase order logs whose note contains this variant
        $itemName = $this->smeItem?->sme_item_name ?? '';
        $size     = $this->sme_item_size ?? '';
        $needle   = "{$itemName} ({$size})";

        $totalUsed = SmePurchaseOrderLog::where('action', 'approved')
            ->where('created_at', '>=', $fromDate)
            ->get()
            ->sum(function ($log) use ($needle, $itemName, $size) {
                $noteData = is_array($log->note)
                    ? $log->note
                    : (json_decode($log->note ?? '[]', true) ?? []);

                return collect($noteData)
                    ->filter(fn ($row) =>
                        isset($row['label']) &&
                        (strcasecmp(trim($row['label']), trim($needle)) === 0 ||
                            (str_contains($row['label'], $itemName) &&
                             str_contains($row['label'], $size)))
                    )
                    ->sum(fn ($row) => (int) ($row['qty'] ?? 0));
            });

        $monthlyAvg  = $totalUsed / 3;
        $safetyStock = 1.3;
        $leadTime    = 1; // months

        return (int) ceil(($monthlyAvg * $leadTime) * $safetyStock);
    }
}