<?php
// app/Models/OfficeSupplyCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeSupplyCategory extends Model
{
    protected $fillable = [
        'office_supply_category_name',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OfficeSupplyItem::class, 'office_supply_category_id');
    }
}