<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmeItemVariants extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'sme_item_id',
        'sme_item_size',
        'sme_item_quantity'
    ];

    protected $casts = [
        'sme_item_quantity'         => 'integer'
    ];

    public function smeItem() : BelongsTo {
        return $this->belongsTo(SmeItems::class, 'sme_item_id', 'id')
                    ->withTrashed();
    }
}
