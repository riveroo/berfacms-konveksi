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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            if (
                \DB::table('stock_ins')->where('item_id', $item->id)->exists() ||
                \DB::table('stock_outs')->where('item_id', $item->id)->exists() ||
                \DB::table('stock_adjustments')->where('item_id', $item->id)->exists() ||
                \DB::table('production_materials')->where('item_id', $item->id)->exists()
            ) {
                throw new \Exception('Item cannot be deleted because it is referenced in transactions or production.');
            }
        });
    }

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
