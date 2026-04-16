<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmeItems extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sme_category_id',
        'sme_item_name',
        'sme_item_brand',
        'sme_item_description',
        'sme_item_price',
        'sme_item_image',
    ];

    public function category() : BelongsTo {
        return $this->belongsTo(SmeCategory::class, 'sme_category_id', 'id');
    }

    public function itemVariant() : HasMany {
        return $this->hasMany(SmeItemVariants::class, 'sme_item_id', 'id');
    }

    public function smePurchaseOrder() : HasMany {
        return $this->hasMany(SmePurchaseOrder::class, 'item_name', 'sme_item_name');
    }

     protected static function booted() : void {
        static::deleting(function (SmeItems $item) {
            $item->itemVariant()->each(fn ($variant) => $variant->delete()); // ✅ correct relation name
        });
        static::restoring(function (SmeItems $item) {
            $item->itemVariant()->withTrashed()->each(fn ($variant) => $variant->restore()); // ✅
        });
    }
}
