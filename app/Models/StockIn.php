<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StockMovementService;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'trx_date',
        'item_type',
        'product_id',
        'variant_id',
        'size_option_id',
        'item_name',
        'item_id',
        'quantity',
        'user_id',
    ];

    protected $casts = [
        'trx_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    protected static function booted()
    {
        static::creating(function ($stockIn) {
            $stockIn->cacheItemName();
        });

        static::created(function ($stockIn) {
            $service = new StockMovementService();
            $service->adjustStock($stockIn, 1);
        });

        static::updating(function ($stockIn) {
            $service = new StockMovementService();
            // Rollback old stock (decrease it)
            $oldStockIn = $stockIn->getOriginal();
            $service->adjustStock((object)$oldStockIn, -1);
            
            $stockIn->cacheItemName();
        });

        static::updated(function ($stockIn) {
            $service = new StockMovementService();
            $service->adjustStock($stockIn, 1);
        });

        static::deleted(function ($stockIn) {
            $service = new StockMovementService();
            $service->adjustStock($stockIn, -1);
        });
    }

    public function cacheItemName()
    {
        if ($this->item_type === 'product') {
            $name = ($this->product?->product_name ?? '') . ' - ' . ($this->variant?->variant_name ?? '');
            if ($this->sizeOption) {
                $name .= ' (' . $this->sizeOption->name . ')';
            }
            $this->item_name = $name;
        } else {
            $this->item_name = $this->item?->item_name;
        }
    }
}
