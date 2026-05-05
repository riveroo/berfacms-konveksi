<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StockMovementService;

class StockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'trx_date',
        'item_type',
        'production_id',
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
        static::creating(function ($stockOut) {
            $stockOut->cacheItemName();
            
            // Validate stock before creating
            $service = new StockMovementService();
            if (!$service->checkSufficiency(
                $stockOut->item_type,
                $stockOut->product_id,
                $stockOut->variant_id,
                $stockOut->size_option_id,
                $stockOut->item_id,
                $stockOut->quantity
            )) {
                throw new \Exception("Stock not sufficient");
            }
        });

        static::created(function ($stockOut) {
            $service = new StockMovementService();
            $service->adjustStock($stockOut, -1);
        });

        static::updating(function ($stockOut) {
            $service = new StockMovementService();
            // Rollback old stock first
            $oldStockOut = $stockOut->getOriginal();
            // We use the original record data but with a virtual object or pass params
            // For simplicity, let's just reverse the old and apply the new
            $service->adjustStock((object)$oldStockOut, 1);
            
            // Check sufficiency for the new quantity
            if (!$service->checkSufficiency(
                $stockOut->item_type,
                $stockOut->product_id,
                $stockOut->variant_id,
                $stockOut->size_option_id,
                $stockOut->item_id,
                $stockOut->quantity
            )) {
                // Rollback the reversal if failed
                $service->adjustStock((object)$oldStockOut, -1);
                throw new \Exception("Stock not sufficient");
            }
            
            $stockOut->cacheItemName();
        });

        static::updated(function ($stockOut) {
            $service = new StockMovementService();
            $service->adjustStock($stockOut, -1);
        });

        static::deleted(function ($stockOut) {
            $service = new StockMovementService();
            $service->adjustStock($stockOut, 1);
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
