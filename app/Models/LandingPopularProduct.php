<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPopularProduct extends Model
{
    protected $fillable = ['product_id', 'sort_order'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
