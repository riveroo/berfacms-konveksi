<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item_id',
        'item_name',
        'item_code',
        'product_type_id',
        'unit_id',
        'minimum_stock',
        'price',
        'stock',
        'supplier_id',
    ];

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
