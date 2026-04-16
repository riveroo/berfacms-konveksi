<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $table = 'master_product_type';
    protected $fillable = ['name'];

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
}
