<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departments extends Model
{
    protected $fillable = [
        'department_name'
    ] ;

    public function users() : HasMany
    {
        return $this->hasMany(User::class);
    }
}
