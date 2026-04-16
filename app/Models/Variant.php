<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $fillable = ['product_id', 'product_type_id', 'variant_code', 'variant_name', 'color', 'image'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
