<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_id',
        'product_id',
        'variant_id',
        'size_option_id',
        'quantity',
    ];

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function sizeOption()
    {
        return $this->belongsTo(SizeOption::class);
    }
}
