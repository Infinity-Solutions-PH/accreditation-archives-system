<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'code',
        'name',
        'slug',
        'description',
        'order_no'
    ];

    // public function parameters()
    // {
    //     return $this->hasMany(Parameter::class);
    // }
}
