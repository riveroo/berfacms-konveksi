<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pre_order_id',
        'product_id',
        'variant_id',
        'size_option_id',
        'price',
        'quantity',
        'discount',
        'subtotal',
    ];

    public function preOrder(): BelongsTo
    {
        return $this->belongsTo(PreOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    public function sizeOption(): BelongsTo
    {
        return $this->belongsTo(SizeOption::class);
    }
}
