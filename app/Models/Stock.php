<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['variant_id', 'size_option_id', 'stock', 'cogs', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
        'cogs' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function sizeOption()
    {
        return $this->belongsTo(SizeOption::class);
    }
}
