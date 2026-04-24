<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\UniformItems;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\SoftDeletes;

class UniformItemVariants extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'uniform_item_id',
        'uniform_item_size',
        'uniform_item_quantity'
    ];

    protected $casts = [
        'uniform_item_quantity'         => 'integer'
    ];

    public function uniformItem() : BelongsTo {
        return $this->belongsTo(UniformItems::class, 'uniform_item_id', 'id')
                    ->withTrashed();
    }

    public function issuance() :HasMany {
        return $this->hasMany(UniformIssuanceItems::class, 'uniform_item_variant_id', 'id');
    }

    public function getMoqAttribute() {
        $fromDate = now()->subMonth(3);
        
        $totalIssued = $this->issuance()
            ->where('created_at', '>=', $fromDate)
            ->sum('quantity');
        
        $monthlyAvrg = $totalIssued / 3;

        $safetyStock = 1.3;

        $leadTime = 1; // in months

        return (int) ceil(($monthlyAvrg * $leadTime) * $safetyStock);
    }
    
}


