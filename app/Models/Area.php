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

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
