<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StockMovementService;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'trx_date',
        'item_type',
        'product_id',
        'variant_id',
        'size_option_id',
        'item_id',
        'item_name',
        'old_stock',
        'new_stock',
        'difference',
        'reason',
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
        static::creating(function ($adjustment) {
            $service = new StockMovementService();
            $adjustment->cacheItemName();
            
            // Get current stock as old_stock
            $adjustment->old_stock = $service->getCurrentStock(
                $adjustment->item_type,
                $adjustment->variant_id,
                $adjustment->size_option_id,
                $adjustment->item_id
            );
            
            // Calculate difference
            $adjustment->difference = $adjustment->new_stock - $adjustment->old_stock;
        });

        static::created(function ($adjustment) {
            $service = new StockMovementService();
            $service->performAdjustment(
                $adjustment->item_type,
                $adjustment->variant_id,
                $adjustment->size_option_id,
                $adjustment->item_id,
                $adjustment->new_stock
            );
        });

        // Optional: Implement updated and deleted hooks if user allows editing/deleting adjustments
        // Requirements say: 
        // EDIT: Revert old adjustment, Apply new adjustment
        // DELETE: Restore stock to old_stock
        
        static::updating(function ($adjustment) {
            $service = new StockMovementService();
            
            // Revert to old stock of this adjustment first
            $original = $adjustment->getOriginal();
            $service->performAdjustment(
                $original['item_type'],
                $original['variant_id'],
                $original['size_option_id'],
                $original['item_id'],
                $original['old_stock']
            );
            
            // Re-calculate based on new values
            $adjustment->cacheItemName();
            $adjustment->old_stock = $service->getCurrentStock(
                $adjustment->item_type,
                $adjustment->variant_id,
                $adjustment->size_option_id,
                $adjustment->item_id
            );
            $adjustment->difference = $adjustment->new_stock - $adjustment->old_stock;
        });

        static::updated(function ($adjustment) {
            $service = new StockMovementService();
            $service->performAdjustment(
                $adjustment->item_type,
                $adjustment->variant_id,
                $adjustment->size_option_id,
                $adjustment->item_id,
                $adjustment->new_stock
            );
        });

        static::deleted(function ($adjustment) {
            $service = new StockMovementService();
            $service->performAdjustment(
                $adjustment->item_type,
                $adjustment->variant_id,
                $adjustment->size_option_id,
                $adjustment->item_id,
                $adjustment->old_stock
            );
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
