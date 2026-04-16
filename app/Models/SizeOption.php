<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeOption extends Model
{
    protected $fillable = ['name', 'order'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}
