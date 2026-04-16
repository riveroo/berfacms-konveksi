<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_name', 'description', 'thumbnail', 'is_active'];

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
}
