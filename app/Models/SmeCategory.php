<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmeCategory extends Model
{
    protected $fillable = [
        'sme_category_name',
    ];

    public function smeItem() : HasMany
    {
        return $this->hasMany(SmeItem::class, 'sme_category_id', 'id');
    }
}
