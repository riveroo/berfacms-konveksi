<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_name', 'description', 'thumbnail', 'is_active', 'sort_order', 'is_service'];

    protected static function booted()
    {
        static::deleting(function (Product $product) {
            $usages = [];

            // Check if product is referenced in transaction details
            if (\DB::table('transaction_details')->where('product_id', $product->id)->exists()) {
                $usages[] = 'Detail Transaksi (Sales / Orders)';
            }
            if (\DB::table('pre_order_details')->where('product_id', $product->id)->exists()) {
                $usages[] = 'Detail Pre Order';
            }
            if (\DB::table('stock_ins')->where('product_id', $product->id)->exists()) {
                $usages[] = 'Riwayat Stock In';
            }
            if (\DB::table('production_products')->where('product_id', $product->id)->exists()) {
                $usages[] = 'Riwayat Produksi';
            }

            // Also check if any variant of this product is referenced in tables using variant_id
            $variantIds = $product->variants()->pluck('id')->toArray();
            if (!empty($variantIds)) {
                if (\DB::table('stock_outs')->whereIn('variant_id', $variantIds)->exists()) {
                    $usages[] = 'Riwayat Stock Out';
                }
                if (\DB::table('stock_adjustments')->whereIn('variant_id', $variantIds)->exists()) {
                    $usages[] = 'Riwayat Stock Adjustment';
                }
            }

            if (!empty($usages)) {
                $uniqueUsages = array_unique($usages);
                throw new \Exception("Produk \"{$product->product_name}\" tidak dapat dihapus karena telah digunakan dalam riwayat transaksi atau modul lainnya (" . implode(', ', $uniqueUsages) . ").");
            }

            // If safe, cascade delete the variants and stocks
            foreach ($product->variants as $variant) {
                $variant->stocks()->delete();
                $variant->delete();
            }
        });
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
}
